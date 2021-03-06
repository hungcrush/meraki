'use strict';

angular.module('tiny.admin.controllers', []).
    controller('HomeCtrl', function($scope, $tiny, statusPunch){
         $scope.yutest = 99.9;
         $scope.wasPunchIn = statusPunch.check;

         $scope.punchIn = function()
         {
            $tiny.ajax({
                url: '/admin/hr/punch-in'
            })
            .then(function(data){
                toastr.success('<i class="fa fa-check"></i> Success');
                $scope.wasPunchIn = 1;
            })
         }

         $scope.punchOut = function()
         {
            $tiny.ajax({
                url: '/admin/hr/punch-out'
            })
            .then(function(data){
                toastr.success('<i class="fa fa-check"></i> Success');
                $scope.wasPunchIn = 2;
            })
         }
    }).
    controller('PermissionCtrl', function($stateParams, $rootScope, $tiny, $scope, Load, g){
        $scope.lists        = Load.list_1;
        $scope.listsUser    = Load.list_0;
        
        $scope.Remove       = function(id){
            var al = $tiny.confirm({
                data: {id: id},
                callback: function(){
                    g.Load($stateParams, $tiny ,g).then(function(response){
                        $scope.lists        = response.list_1;
                        $scope.listsUser    = response.list_0;
                    })     
                }           
                            
              })
        }
        
        $scope.fn = {};
        $scope.fn.for = 1;
        $scope.group_id = 0;
        $scope.fn.list_group = Load.list_group;
        $scope.fn.user_group = Load.user_group;
        $scope.fn.createPermission = function(t){
            var ajax = $tiny.ajax({
                url: tn.makeURL('Add'),
                data: t
            })
            ajax.then(function(data){
                if(data.content == 'OK'){
                    toastr.success('<i class="fa fa-check"></i> Success');
                    $rootScope.currentModal.close();
                    g.Load($stateParams, $tiny ,g).then(function(response){
                        $scope.lists        = response.list_1;
                        $scope.listsUser    = response.list_0;
                    })
                }
            })
        }
        
        $scope.fn.createGroup = function(t){
            var ajax = $tiny.ajax({
                url: tn.makeURL('AddGroup'),
                data: t
            })
            ajax.then(function(data){
                if(data.content == 'OK'){
                    toastr.success('<i class="fa fa-check"></i> Success');
                    $rootScope.currentModal.close();
                    g.Load($stateParams, $tiny ,g).then(function(response){
                        $scope.lists         = response.list_1;
                        $scope.listsUser     = response.list_0;
                        $scope.fn.list_group = response.list_group;
                    })
                }
            })
        }
        
    }).
    controller('GroupuserCtrl', function($scope, Load, $tiny, $rootScope, $stateParams, g){
        $scope.list = Load.lists;
        $scope.fn           = {};
        $scope.fn.listAdmin = {};
        $scope.fn.listUser  = {};
        $scope.fn.createGroup = function(t){
            
            if(t.permission && typeof t.permission == 'object')
                t.permissions = t.permission.join(',');
            else if(typeof t.permission == 'string')
                t.permissions = t.permission;
                
            var ajax = $tiny.ajax({
                url: tn.makeURL('add'),
                data: t
            })
            ajax.then(function(data){
                if(data.content == 'OK'){
                    toastr.success('<i class="fa fa-check"></i> Success');
                    $rootScope.currentModal.close();
                    g.Load($stateParams, $tiny ,g).then(function(response){
                        $scope.list        = response.lists;
                    })
                }
            })
        }
        
        $scope.Remove       = function(id){
            $tiny.confirm({
                data: {id: id},
                callback: function(){
                    g.Load($stateParams, $tiny ,g).then(function(response){
                        $scope.list        = response.lists;
                    })    
                }           
                            
            })
        }
        $tiny.loadData(tinyConfig.urlAdminTemplate('/administrator/permission')).then(function(response){
            $scope.fn.listAdmin    = response.list_1;
            $scope.fn.listUser     = response.list_0;
        })
    }).
    controller('PeoplesCtrl', function($scope, Load, $tiny, $stateParams, $rootScope, $state, g, $timeout){
        var m = ($stateParams.module).split('/');
        
        if(m[0] == 'peoples' && m[1] == 'edit'){
            $scope.isEdit = true;
        }else if(m[1] == 'add'){
            $scope.isEdit = false;
        }
        
        if(Load.lists){
            $scope.lists = Load.lists;
        }
        else{
            if(Load.permission){
                $scope.permissions  = Load.permission.list;
                $scope.groups       = Load.groups;
            }
            $scope = angular.extend($scope, Load.user_info);
            if(!$scope.avatar || $scope.avatar == '')
                $scope.avatar = avatar_default;
                
        }
        
        $scope.totalItems  = Load.total;
        $scope.changePage  = function(page, group_id){
            $tiny.ajax({
                url: g.URL($stateParams),
                data: {page: page, group_id: group_id, load_data: true}
            }).then(function(data){
                //-- data return only have 1 object
                angular.forEach($scope.lists, function(obj, i){
                    if(obj.user_group_id == group_id){
                        $scope.lists[i].list_user = data.lists[0].list_user;
                    }
                })
            })
        }
        
        tinyConfig.thumbSize = {w: 128, h: 128};
        
        $scope.uploaded = function(res, file){
            var r = angular.fromJson(res);
            $('#avatar_link').val(r.folder+'|'+r.filename);
        }
        $scope.save_edit = function(data){
            data.denied = [];
            $('#permissions').find('input[name="permission_denied"]').each(function(){
                var t = $(this);
                if(!t.is(':checked')){
                    data.denied.push(t.val());
                }
            })
            $('#permissions').find('input[name="permission_group"]:not(":checked")').each(function(){
                data.denied.push($(this).val());
            })
            
            $tiny.ajax({
                url: tn.makeURL('save'),
                data: data
            }).then(function(response){
                if(response.content == 'OK'){
                    toastr.success('<i class="fa fa-check"></i> Success');
                    $rootScope.changeRoute('admin.administrator', {module: 'peoples'});
                }
            })
        }
        
        $scope.Remove       = function(id){
            var al = $tiny.confirm({
                data: {user_id: id},
                callback: function(){
                    g.Load($stateParams, $tiny, g).then(function(response){
                        console.log($scope.lists);
                        $scope.lists = response.lists;
                    })  
                }           
                            
            })
        }
        
        $scope.check = function(){
            console.log($scope);
        }
    }).
    controller('MenuCtrl', function(Load, $scope, $tiny, $rootScope, $stateParams, g){
        $scope.menus = Load.menus;
        $scope.isUpdate = false;
        $scope.json = {};
        $scope.fn = {
            options: Load.options
        };
        $scope.$on('ngRepeatFinished', function(){
            jQuery.UIkit.nestable(jQuery('#nestable-list-1'));
            $scope.$apply();
            $("#nestable-list-1").on('nestable-stop', function(ev)
    		{
    			var serialized = $(this).data('nestable').serialize();
    			json = {};
    			toJson(serialized, 0);
    			
                $scope.json = json;
                
                $scope.isUpdate = true;
                $scope.$apply();
    		});
        })
        
        $scope.sortMenu = function(){
            $tiny.ajax({
                url: tn.makeURL('sort'),
                data: {data: JSON.stringify($scope.json)}
            }).then(function(data){
                if(data.content == 'OK'){
                    $scope.isUpdate = false;
                    $scope.$apply();
                    
                    toastr.success('<i class="fa fa-check"></i> Success');
                }
            })
        }
        
        $scope.Remove = function(id){
            $tiny.confirm({
                data: {menu_id: id},
                callback: function(){
                    g.Load($stateParams, $tiny ,g).then(function(response){
                        $scope.menus = response.menus;
                        $scope.fn.options = response.options;
                    })
                }           
                            
            })
        }
        
        $scope.fn.createMenu = function(data){
            $tiny.ajax({
                url: tn.makeURL('Add'),
                data: data
            }).then(function(data){
                if(data.content == 'OK'){
                    $rootScope.currentModal.close();
                    toastr.success('<i class="fa fa-check"></i> Success');
                    g.Load($stateParams, $tiny ,g).then(function(response){
                        $scope.menus = response.menus;
                        $scope.fn.options = response.options;
                    })
                    
                    public_vars.$body.trigger('UpdateMenu');
                }
            })
        }
        $scope.fn.pages = Load.pages;
        $scope.fn.isChecked = function(id, str)
        {
            //-- use indexOf to get id is checked
            str = str.toString();
            id  = id.toString();
            return (str.indexOf(id + ',') != -1 || str.indexOf(',' + id) != -1 || str == id);
        }
        
        $tiny.loadData(tinyConfig.urlAdminTemplate('/administrator/permission')).then(function(response){
            $scope.fn.listAdmin    = response.list_1;
        })
    }).
    controller('AboutCtrl', function($scope, $tiny, Load, $rootScope, $stateParams, g, $sce){
        $scope.required_title = true;
        $scope.required_des = true;
        $scope.required_another_images = true;
        $scope.required_cover = true;

        $scope.success = false;
        $scope.formSubmit = function(t){
            
            $tiny.ajax({
                url: URL_SERVER+'admin/content/save/about_content',
                data: t
            }).then(function(){
                $scope.success = true;
            })
        }
        
        $scope.loadHomeSetup = function(){
            g.Load($tiny, $stateParams).then(function(Load){
                if(Load.home.content_title){
                    $scope.home['indexType'] = Load.home.content_title;
                    var obj = jQuery.parseJSON(Load.home.content_html);
                    save_data = obj;
                    $scope.home = angular.extend({}, $scope.home, Load.home, obj);
                }
            })
        }
        
        $scope.home = {
            media_size: 80,
            font_size: 50,
            opacity: 50
        };
        
        var save_data = {};
        
        $scope.uploadComplete = function(respon, file){
            var r = angular.fromJson(respon);
            $scope.home['background'] = tn.getURLUploaded(r.folder, r.filename).origin;
            $scope.home['path'] = r.folder+'|'+r.filename;
            var dataUpdate = {
                content: JSON.stringify(angular.extend(save_data, {
                    path: r.folder+'|'+r.filename,
                    content: $scope.home.content || null
                })),
                title: $scope.home.indexType || 'null'
            }
            
            $scope.fn.formSubmit(dataUpdate, true);
        }
        
        $scope.saveHomeSetting = function(t){
            if(!t.show_title){
                delete save_data.show_title;
            }
            
            if(!t.show_control){
                delete save_data.show_control;
            }
            
            if(!t.show_suggest){
                delete save_data.show_suggest;
            }
            
            var dataUpdate = {
                content: JSON.stringify(angular.extend(save_data, t)),
                title: $scope.home.indexType || 'null'
            }
            
            $scope.fn.formSubmit(dataUpdate, true);
        }
                   
        if($stateParams.module == 'home'){
            
            if(Load.home.content_title){
                $scope.home['indexType'] = Load.home.content_title;
                var obj = angular.fromJson(Load.home.content_html);
                save_data = obj;
                $scope.home = angular.extend({}, $scope.home, Load.home, obj);
            }
        }
        
        $scope.toTrustedHTML = function( html ){
            return $sce.trustAsHtml( html );
        }
        
        $scope.ChangeURL = function(){
            var t = angular.element('#setting-form').serializeObject();
            var params = {
                rel: t.show_suggest ? 1 : 0,
                controls: t.show_control ? 1 : 0,
                showinfo: t.show_title ? 1 : 0
            }
            $scope.home.src = $scope.home.src_cur+'?'+jQuery.param(params);
        }
        
        $scope.removeMedia = function(){
            var dataUpdate = {
                content: '',
                title: 'null'
            }
            $scope.fn.formSubmit(dataUpdate);
        }
        
        $scope.fn = {
            formSubmit: function(t, check){
                
                if(!check){
                    t['content'] = JSON.stringify(angular.extend(save_data, {
                        path: $scope.home.path,
                        content: t.content
                    }))
                }
                console.log(t);
                $tiny.ajax({
                    url: tn.makeURL('save'),
                    data: t
                }).then(function(data){
                    toastr.success('<i class="fa fa-check"></i> Success');
                    
                    if(!check){
                        $scope.loadHomeSetup();
                        $rootScope.currentModal.close();
                    }
                })
            },
            uploadComplete: function(respon, file){
                
                var r = angular.fromJson(respon);
             
                var dataUpdate = {
                    content: tn.getURLUploaded(r.folder, r.filename).origin,
                    title: 'image'
                }
                this.formSubmit(dataUpdate);
            }
        }
    }).
    controller('ProductCtrl', function($scope, $tiny, $rootScope, $stateParams, Load, g){
        $scope._module = URL_SERVER+'admin/product/';
        $scope.isLarge = Load.product.length > 0 ? false : true;

        $scope.parent_current = {};
        var initParentCurrent = function($product_id)
        {
            var outObj = {};
            Load.product.forEach(function(obj){
                if(obj.product_id == $product_id)
                {
                    outObj = obj;

                    if(obj.parent != 0)
                    {
                        outObj['prevParent'] = initParentCurrent(obj.parent);
                    }else if($product_id > 0){
                        outObj['prevParent'] = {'product_id': 0};
                    }
                    return;
                }
            })

            if(!outObj.prevParent)
            {
                outObj = {product_id: 0};
            }

            return outObj;
        }

        
        $scope.selectParent = function(obj)
        {
            if(obj.product_id > 0)
                $scope.parent_current = angular.extend(obj, {prevParent: $scope.parent_current});
        }
        $scope.backPrevParent = function()
        {
            $scope.parent_current = $scope.parent_current.prevParent;
            $rootScope.changeRoute('admin.product.list.items', {module: $scope.parent_current.product_id});
        }

        $scope.filterByParent = function(item)
        {
            return !!(item.parent == $scope.parent_current.product_id);
        }


        var productn = Load.productn;
        $scope.listProduct  = Load.product;
        $scope.listItems    = [];
        $scope.listPosts    = [];
        
        $scope.item_name = '';
        
        $scope.toggleProduct = function(){
            $('.album-sorting-info').find('a').trigger('click');
            $scope.isLarge = !$scope.isLarge;
        }
        
        $scope.Moved = false;
        $scope.isMoved = function(value){
            if(!value){
            $scope.Moved = true;
            }else{
                $scope.Moved = 2;
            }
        }
        
        
        $scope.product_id = 0;
        $scope.toProduct_id = function($id){
            if(Object.keys($scope.parent_current).length == 0)
            {
                $scope.parent_current = initParentCurrent($id);
            }
            $scope.product_id = $id;
        }
        
        $scope.item_id = 0;
        $scope.toItem_id = function($id){
            $scope.item_id = $id;
        }
        
        $scope.imagethumb = tinyConfig.dirTemp+ '/admin/assets/images/thumb-upload.jpg?2';
        $scope.fn = {imagethumb: $scope.imagethumb};
        
        $scope.fn.action = 'add-product';
        
        $scope.fn.formSubmit = function(t, s){
            
            if( (t.item_image === '' || t.image === '') && s != 'add-post'){
                console.log($scope.itemImages);
                $tiny.alert('Please upload product image');
                return false;
            }
            
            switch(s){
                case 'add-product':
                    $tiny.ajax({
                        url: tn.makeURL('add', $scope._module),
                        data: t
                    }).then(function(data){
                        if(data.content == 'OK'){
                            $rootScope.currentModal.close();
                            toastr.success('<i class="fa fa-check"></i> Success');
                            g.Load($tiny).then(function(response){
                                $scope.listProduct = response.product;
                                productn = response.productn;
                                
                                if($scope.listProduct.length == 1 && t.product_id === undefined){
                                    $scope.isLarge = false;
                                    
                                    setTimeout(function(){
                                        $rootScope.changeRoute('admin.product.list.items', {module: $scope.listProduct[0].product_id});
                                    }, 300)
                                    
                                }else if(t.product_id == $scope.product_id){
                                    $scope.item_name = t.name;
                                }
                            })
                        }
                    })
                break;
            
                case 'add-item':
                    $tiny.ajax({
                        url: tn.makeURL('add/item', $scope._module),
                        data: t
                    }).then(function(data){
                        if(data.content == 'OK'){
                            $rootScope.currentModal.close();
                            toastr.success('<i class="fa fa-check"></i> Success');
                            
                            $scope.loadItems($scope.parent_current.product_id);
                        }
                    })
                    break;
                    
                case 'add-post':
                    $tiny.ajax({
                        url: tn.makeURL('add/post', $scope._module),
                        data: t
                    }).then(function(data){
                        if(data.content == 'OK'){
                            $rootScope.currentModal.close();
                            toastr.success('<i class="fa fa-check"></i> Success');
                            
                            $scope.loadPosts(data.product_id);
                        }
                    })
                    break;
                    
                default:
                    $tiny.alert('Not found your action!');
            }
            return true;
        }
        
        $scope.fn.uploadComplete = function(respon, file){
            var r = angular.fromJson(respon);
            angular.element('#image_src').val(r.folder+'|'+r.filename);
        }

        $scope.fn.contains = function(arr, value)
        {
            return arr.indexOf(value) !== -1;
        }
        
        $scope.loadItems = function($product_id, callback){
            //if($product_id == 0) return false;
            $scope.item_name = productn[$product_id];
            
            $tiny.loadData($scope._module+$product_id).then(function(response){
                $scope.listItems = response.product;
                if(callback)
                    callback.call();
            })
        }
        
        $scope.loadPosts = function($product_id){
            //if(!$product_id || $product_id == 0) return false;
            
            $tiny.loadData($scope._module+'post/'+$product_id).then(function(response){
                $scope.listPosts = response.posts;
            })
        }
        
        $scope.saveSort = function(data, type){
            $tiny.ajax({
                url: tn.makeURL('sort/' + type, $scope._module),
                data: {sortarr: JSON.stringify(data)}
            }).then(function(data){
                if(data.content == 'OK'){
                    toastr.success('<i class="fa fa-check"></i> Success');
                }else{
                    $tiny.alert('Sorry. Please contact admin :D..', 'Error', {type: 'error'});
                }
            })
        }
        
        $scope.Remove = function(id, type){
            $tiny.confirm({
                data: {id: id},
                link: tn.makeURL('Remove/'+type, $scope._module),
                callback: function(){
                    if(id == $scope.product_id && type == ''){
                        $scope.listItems = [];
                        $scope.item_name = '';
                    }
                    if($scope.listProduct.length > 1 || type != ''){
                        if(type == '')
                            g.Load($tiny).then(function(response){
                                $scope.listProduct = response.product;
                                productn = response.productn;
                                $rootScope.changeRoute('admin.product.list.items', {module: $scope.listProduct[0].product_id});
                            })
                        else if(type == 'item')
                            $scope.loadItems($scope.product_id);
                        else if(type == 'post')
                            $scope.loadPosts($scope.product_id);
                    }else{
                        $scope.listProduct = [];
                        $rootScope.changeRoute('admin.product.list', {});
                        $scope.isLarge = true;
                    }
                }           
                            
            })
        }
        
        $scope.$on('$stateChangeSuccess', function(evt, toState){
            if(toState.name == 'admin.product.list' && $scope.listProduct.length){
                $rootScope.changeRoute('admin.product.list.items', {module: 0});
            }
        })
    }).
    controller('ProductItemCtrl', function($scope, $tiny, $stateParams, $rootScope){
        if($stateParams.module != ''){

            $scope.isMoved();
            $scope.toProduct_id($stateParams.module);
            
            $scope.loadItems($scope.product_id);
            $scope.loadPosts($scope.product_id);

            var categories = new convertCategories($scope.listProduct);
            $scope.productCategories = categories.parseCategory(0);
        }
        
    }).
    controller('ProductItemDetailCtrl', function($scope, $tiny, $stateParams, $timeout, $rootScope, itemData){
        
        $scope.isMoved(2);
        $scope.success = false;
        
        $scope.toProduct_id($stateParams.module);
        if($scope.listItems.length == 0)
            $scope.loadItems($scope.product_id);
        
        $scope.itemData   = itemData.product;
        $scope.itemImages = itemData.product.images;
        $scope.toItem_id($stateParams.detail_id);
        
        $scope.backToList = function(){
            $rootScope.changeRoute('admin.product.list.items', {module: $scope.listProduct[0].product_id})
        }
        
        $scope.updateSubmit = function(t){
            if($scope.itemImages.length == 0){
                $tiny.alert('Please upload product image');
                return false;
            }
            t.content = tinymce.get('detail_content').getContent();
            t.images  = $scope.itemImages;
            
            $tiny.ajax({
                url: tn.makeURL('add/detail', $scope._module),
                data: t
            }).then(function(data){
                if(data.content == 'OK'){
                    //toastr.success('<i class="fa fa-check"></i> Success');
                    $scope.success = true;
                }
            })
        }
        
        $scope.onAddfile = function(up, files){
            angular.forEach(files, function(value, key){
                var image = tn.initLoading(tinyConfig.thumbSize, value.id);
                $timeout(function(){
                    $scope.itemImages.push({
                        src: image.src,
                        id: image.iid,
                        path: null
                    })
                }, 10);
            }) 
        }
        
        $scope.onUploaded = function(respon, file){
            respon = angular.fromJson(respon);
            $timeout(function(){
                var el = jQuery('#'+file.id),
                    img= tn.getURLUploaded(respon.folder, respon.filename);
                
                $scope.itemImages[el.data('index')].src = img.src;
                $scope.itemImages[el.data('index')].path= img.path;
            },10);
        }
        
        //-- delete one element
        $scope.DeleteImage = function(path, id){
            angular.forEach($scope.itemImages, function(obj, i){
                if(obj.id == id)
                    $scope.itemImages.splice(i, 1);
            })
            
            path = path.split('|');
            $tiny.ajax({
                url: URL_SERVER + 'welcome/Delete?path=uploads',
                data: {
                    folder: path[0],
                    filename: path[1]
                }
            })
        }
    }).
    controller('BlogCtrl', function($scope, Categories, $tiny){
        $scope.listBlogs = [];
        $scope.countBlog = 0;
        $scope.categories = Categories.cate;
        $scope.listcates = Categories.cate_list;
        $scope.currentPage = 1;
        var search_obj = {};
        
        $scope.Load = function(t){
            t = angular.extend({
                page: 1,
                load_data: true,
                keyword: '',
                category_id: 'all'
            }, t, search_obj);
            
            
            $tiny.ajax({
                url: URL_SERVER+'admin/blog',
                data: t
            }).then(function(data){
                $scope.listBlogs = data.lists;
                $scope.countBlog = data.count;
                $scope.currentPage = t.page;
            })
        }
        $scope.Load();
        
        $scope.changePage  = function(page){
            $scope.Load({page: page});
        }
        
        $scope.Search = function(t){
            search_obj = t;
            $scope.Load();
        }
        
        $scope.Delete = function(id, cate_id){
            $tiny.confirm({
                data: {blog_id: id, category_id: cate_id},
                callback: function(){
                    $scope.Load({page: $scope.currentPage});
                }           
                            
            })
        }
    }).
    controller('BlogAddCtrl', function($scope, $tiny, Categories, $rootScope){
        $scope.success = false;
        $scope.listcates = Categories.cate_list;
        
        if(!$scope.listcates.length){
            setTimeout(function(){
                $tiny.alert('<p>No Categories</p> <br /> <a style="margin-top: 8px;" href="'+URL_SERVER+'admin/blog/categories" onclick="swal.close();" class="btn btn-success">Add Categories</a> <span style="font-size: 14px; padding: 0 10px;">OR</span> <a style="margin-top: 8px" onclick="swal.close();" class="btn btn-danger">Continue</a>', '', {
                    html: true,
                    type: false,
                    showConfirmButton: false
                });
            }, 200)
        }
        
        $scope.backToList = function(){
            $rootScope.changeRoute('admin.blog',{module: ''});
        }
        
        $scope.addBlog = function(t){
            
            $tiny.ajax({
                url: tn.makeURL('add'),
                data: t
            }).then(function(data){
                if(data.content == 'OK')
                    $scope.success = true;
            })
        }
    }).
    controller('BlogEditCtrl', function($scope, loadSingle, Categories, $tiny, $rootScope){
        $scope.success = false;
        $scope.listcates = Categories.cate_list;
        $scope.single = loadSingle.lists[0];
        
        $scope.backToList = function(){
            $rootScope.changeRoute('admin.blog',{module: ''});
        }
        
        $scope.addBlog = function(t){
            $tiny.ajax({
                url: URL_SERVER + 'admin/blog/add',
                data: t
            }).then(function(){
                $scope.success = true;
            })
        }
    }).
    controller('BlogCategoriesCtrl', function($scope, $tiny, Categories, g, $rootScope){
        $scope.listcates = Categories.cate_list;
        
        $scope.addCategory = function(t){
            $tiny.ajax({
                url: tn.makeURL('add'),
                data: t
            }).then(function(data){
                g.Categories($tiny).then(function(respon){
                    $scope.listcates = respon.cate_list;
                    if($rootScope.currentModal.close)
                        $rootScope.currentModal.close();
                })
            })
        }
        
        $scope.fn = {
            saveCategory: $scope.addCategory
        }
        
        $scope.Delete = function(id){
            $tiny.confirm({
                data: {category_id: id},
                callback: function(){
                    g.Categories($tiny).then(function(respon){
                        $scope.listcates = respon.cate_list;
                    })
                }           
                            
            })
        }
    }).
    /*
        Controller for Profile, Timline
    */
    controller('ProfileCtrl', function($scope, $tiny, timelines){
        $scope.feeds = timelines.data;
        $scope.comments = [];

        $scope.save_edit = function(data){
            $tiny.ajax({
                url: tn.makeURL('save', '/admin/profile'),
                data: data
            }).then(function(data){
                if(data.content == 'OK')
                {
                    toastr.success('<i class="fa fa-check"></i> Success');
                }
            })
        }

        $scope.uploaded = function(res, file){
            var r = angular.fromJson(res);
            $('#avatar_link').val(r.folder+'|'+r.filename);
        }

        $scope.postTimeLine = function(data){
            $tiny.ajax({
                url: tn.makeURL('postfeed', '/admin/profile'),
                data: data
            }).then(function(data){
                if(data.content == 'OK')
                {
                    toastr.success('<i class="fa fa-check"></i> Success');
                    $scope.reloadTimelines();
                }
            })
        }

        $scope.reloadTimelines = function()
        {
            $tiny.loadData('/admin/profile/load-timelines').then(function(res){
                $scope.feeds = res.data;
            })
        }

        $scope.postComment = function(feed_id, index)
        {
            $tiny.ajax({
                url: tn.makeURL('post-feed-comment', 'admin/profile'),
                data: {
                    content: $scope.feeds[index].commentContent,
                    feed_id: feed_id
                }
            })
        }
    }).
    /*
        Controller for CRM module
    */
    controller('CRMCtrl', function($scope, $rootScope){
        $scope.crm_title  = 'CRM';
        $scope.crmCurrent = '';
        $scope.setCrmCurrent = function(page)
        {
            $scope.crmCurrent = page;
        }

        $scope.parentobj = {
            listchecked : []
        };

        $scope.checkHasSelected = function(){
            console.log($scope.parentobj.listchecked);
            return $scope.parentobj.listchecked.length;
        }

        $scope.selectUser = function(users){
            console.log(users);
        }
    }).
    controller('CRMListCtrl', function($rootScope, $scope, $state, $tiny, $location){
        var match = $state.current.name.match(/(stream|contacts)/);
        if(match.length > 0) $scope.setCrmCurrent(match[0]);

        $scope.accessToAction = 1; // 1: Save 2: Save and Add More
        $scope.changeAccessTo = function(n)
        {
            $scope.accessToAction = n;
        }

        $scope.contactDeleteAll = function(){
            $scope.contactDelete($scope.parentobj.listchecked);
        }

        $scope.contactDelete = function(customer_id){
            $tiny.confirm({
                data: {customer_id: customer_id},
                link: '/admin/crm/contact-delete',
                callback: function(){
                    public_vars.$window.trigger('dataTable-reload');

                    if(typeof customer_id == 'object')
                        $scope.parentobj.listchecked = [];
                }                        
            })
        }

        $scope.Search = function(data) {
            public_vars.$window.trigger('dataTable-reload', [data.keyword]);
        }

        $scope.contactSubmit = function(data){
            $tiny.ajax({
                url: tn.makeURL('contact-save', '/admin/crm/'),
                data: {
                    form: data,
                    access: $scope.accessToAction
                }
            }).then(function(data){
                if(data.content == 'OK')
                {
                    if($scope.accessToAction == 1)
                        $location.url('/admin/crm/contact/');
                    else
                        $rootScope.reload();
                }
            })
            return false;
        }
    }).
    controller('ProjectBaseCtrl', function($scope, $stateParams){
        $scope.objCondition = {
            is_template: false,
            isView: false
        };
        $scope.formData = {};
        $scope.parentobj = {
            listchecked : []
        };
    }).
    controller('ProjectCtrl', function($scope, $stateParams, $tiny, $rootScope){
        var baseURL = '/admin/project/';
        $scope.titleAdd                 = $stateParams.is_template ? 'Add templete' : 'Add project';
        $scope.objCondition.is_template = $stateParams.is_template ? true : false;
        $scope.objCondition.isView      = false;

        $scope.parentobj.taskData = false;

        $scope.selectParticipant = function(obj)
        {
            $scope.formData.participants = obj;
        }

        $scope.addProject = function(data)
        {
            // check condition

            // Ajax
            $tiny.ajax({
                url: tn.makeURL('save-project', baseURL),
                data: {
                    project_info: data
                }
            })
            .then(function(data){
                $rootScope.changeRoute('admin.task.add', {project_id: data.project_id});
            })
        }
    }).
    controller('ProjectViewCtrl', function($scope, projectData, tasks, $tiny){
        var baseURL = '/admin/project/';
        $scope.projectData              = projectData.data;
        $scope.processing               = tasks.processing;
        $scope.parentobj.currentStatus  = $scope.processing.task_id;
        $scope.parentobj.taskData       = tasks.data;
        $scope.parentobj.todoCompleted  = tasks.is_complete == 1 ? true : false;
        $scope.objCondition.isView      = true;

        var getTodoIDs = function()
        {
            var s = [];
            $scope.processing.todo_list.forEach(function(entry, k){
                s.push(entry.todo_id);
            })

            return s;
        },
        getNextTask = function()
        {
            var _obj = false;
            $scope.parentobj.taskData.some(function(obj, k){
                if(obj.status == 0)
                {
                    _obj = obj;
                    console.log('fdsfds')
                    return true;
                }
            })

            return _obj;
        }

        $scope.commentForTodo = function(index)
        {
            $tiny.ajax({
                url: tn.makeURL('load-comments', baseURL),
                data: {
                    todo_id: typeof index != 'undefined' ? $scope.processing.todo_list[index].todo_id : getTodoIDs()
                }
            })
            .then(function(data){
                console.log('Debug comments', data);
                angular.forEach(data.comments, function(comment, key){
                    $scope.processing.todo_list.forEach(function(obj, k){
                        if(obj.todo_id == key)
                        {
                            $scope.processing.todo_list[k].comments = comment;
                        }
                    })
                })
            })
        }

        $scope.commentForTodo();

        $scope.todoComplete = function(item)
        {
            $tiny.ajax({
                url: tn.makeURL('todo-complete', baseURL),
                data: {
                    todo_id: item.todo_id,
                    is_complete: item.completed ? 1 : 0,
                    task_id: item.task_id
                }
            })
            .then(function(data){
                $scope.parentobj.todoCompleted = data.is_complete == 1 ? true : false;
            })
        }

        $scope.postComment = function(todo_id, index)
        {
            $tiny.ajax({
                url: tn.makeURL('post-comment', baseURL),
                data: {
                    todo_id: todo_id,
                    content: $scope.formData.comment[todo_id]
                }
            })
            .then(function(data){
                if(data.content == 'OK')
                {
                    $scope.commentForTodo(index);
                }
            })
        }

        $scope.nextTask = function()
        {
            $tiny.ajax({
                url: tn.makeURL('next-task', baseURL),
                data: {
                    task_id: $scope.processing.task_id,
                    next_task_id: getNextTask().task_id,
                    project_id: $scope.projectData.project_id
                }
            })
            .then(function(data){
                if(data.status == 'OK')
                {
                    $scope.processing               = data.processing;
                    $scope.parentobj.taskData       = data.data;
                    $scope.parentobj.todoCompleted  = false;

                    $scope.parentobj.currentStatus  = $scope.processing.task_id;

                }
            })
        }

        $scope.parentobj.changeTabtoTask = function(task)
        {
            if(task.status == 0) return;
            $tiny.ajax({
                url: tn.makeURL('change-to-task', baseURL),
                data: {
                    task_id: task.task_id
                }
            })
            .then(function(data){
                $scope.processing               = data.content;
                $scope.parentobj.currentStatus  = task.task_id;
                // reload comment for the above todos
                $scope.commentForTodo();
            })
        }
    }).
    controller('taskBaseCtrl', function($scope){
        var defaultdata = {
            participants: [],
            listTodo : [],
            info: {},
            options: {},
            opt: {}
        };
        $scope.dataTask = [defaultdata];
        $scope.dataProject = {
            title: 'Add new Task',
            description: 'Add new task for someone'
        };
        //--------------------
        $scope.formData = angular.copy(defaultdata);
        $scope.currentTab = 0;

        $scope.changeTabtoTask = function(type){
            var number = (type == 'next') ? $scope.currentTab + 1 : parseInt(type);
            $scope.dataTask[$scope.currentTab] = $scope.formData;
            if(typeof $scope.dataTask[number] == 'undefined')
            {
                $scope.dataTask[number] = angular.copy(defaultdata);
            }
            //-- set formData to next/prev tab
            $scope.formData = $scope.dataTask[number]; 
            //-- set CurrentTab
            $scope.currentTab = number;

            console.log(number, $scope.formData);
        }

    }).
    controller('taskCtrl', function($scope, $tiny, projectData){
        console.log(projectData)
        if(projectData){
            $scope.dataProject.title        = projectData.data.title;
            $scope.dataProject.description  = projectData.data.description;
        }

        $scope.selectUser = function(obj, id, index)
        {
            $scope.formData.listTodo[index].assign      = [obj[0].user_id, obj[0].full_name]
            $scope.formData.listTodo[index].assign_to   = obj[0].user_id;
        }

        $scope.selectParticipant = function(obj)
        {
            console.log(obj);
            $scope.formData.participants = obj;
        }

        $scope.addTodo = function(type)
        {
            switch(type)
            {
                case 1: // check list
                    $scope.formData.listTodo.push({
                        type: 'check_list',
                        text: '',
                        assign: []
                    });
                    break;
                case 2: // note
                    $scope.formData.listTodo.push({
                        type: 'note',
                        text: '',
                        assign: []
                    });
                    break;
            }
        }

        $scope.SaveTask  = function()
        {
            $scope.changeTabtoTask($scope.currentTab);
            console.log($scope.dataTask)
            var dataForm = [];
            angular.copy($scope.dataTask).forEach(function(obj, k){
                dataForm.push({
                    title           : obj.info.title,
                    description     : obj.info.description,
                    participants    : obj.participants,
                    options         : obj.options,
                    project_id      : projectData ? projectData.data.project_id : 0,
                    todos           : obj.listTodo,
                    //-- miss condition
                    status          : k == 0 ? 1 : 0
                })
            });
            $tiny.ajax({
                url: tn.makeURL('save', '/admin/task/'),
                data: {tasks: dataForm}
            })
        }

        
    }).
    controller('dailyreportCtrl', function($scope){
        $scope.parentObj = {panelActive: ''};
    }).
    controller('dailyreportAddCtrl', function($scope, $tiny){
        $scope.parentObj.panelActive = 'add';
        $scope.addReport = function(data)
        {
            $tiny.ajax({
                url: '/admin/dailyreport/save',
                data: data
            })
        }
    })

