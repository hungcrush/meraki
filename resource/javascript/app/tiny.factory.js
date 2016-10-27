angular.module('tinyfw.factory',[])
    .factory('$tiny', function($http, $timeout, $ocLazyLoad, $rootScope, $stateParams){    
        
        return {
            getLoad: function(){
                var $$this = this;
                return $state.current.resolve;
            },
            ajax: function(options, notForm){
                var $$this = this;
                if(typeof tinyConfig.tinyToken == 'undefined') return;
                
                var _data = angular.extend({},options.data,{
                    json: 'yes'
                }, JSON.parse(Base64.decode(tinyConfig.tinyToken)));
                //-- Delete data when extended
                delete options.data;
                var _options = angular.extend({},{
                    method: 'POST',
                    url: URL_SERVER,
                    data: $.param(_data),
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                },options);
                
                return $http(_options)
                        .success(function(data){
                            if(!notForm){
                                var $form = jQuery('body').find('form[tiny-submit]');
                                
                                    $form.find(':button[type=submit]').prop('disabled', true);
                                    $timeout(function(){
                                        $form.find(':button[type=submit]').prop('disabled', false);
                                    }, 2000);
                            }    
                            if(data.error){
                                $$this.alert(data.error, 'Error', 'error');
                            }
                        })
                        .error(function(data, status, headers, config){
                            var errorStatus;
                            
                            if (status === 0) {
                               errorStatus = 'No connection. Verify application is running.';
                            } else if (status == 401) {
                               errorStatus = 'Unauthorized';
                            } else if (status == 405) {
                               errorStatus = 'HTTP verb not supported [405]';
                            } else if (status == 500) {
                               errorStatus = 'Internal Server Error [500].';
                            } else if (status == 403) {
                               errorStatus = 'The action you have requested is not allowed..';
                            }
                            $$this.alert(errorStatus, 'Error', 'error');
                        })
            },
            alert: function(mess, title, objOptions){
                if(typeof objOptions != 'object'){
                    objOptions = {
                        type: objOptions
                    }
                }
                objOptions = angular.extend({
                    type: 'success',
                    html: false,
                    showConfirmButton: true,
                    callback: function(){}
                }, objOptions);
                
                var init = function(){
                    if(mess){
                        swal({
                            title: title || '',
                            text: mess,
                            type: objOptions.type,
                            showCancelButton: objOptions.type == "warning" ? true : false,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: objOptions.type == "warning" ? "Yes, delete it!" : false,
                            cancelButtonText: objOptions.type == "warning" ? "No, cancel plx!" : false,
                            closeOnConfirm: false,
                            closeOnCancel: false,
                            html: objOptions.html ? true : false,
                            showConfirmButton: objOptions.showConfirmButton ? true : false
                        }, function(isConfirm) {
                            if(objOptions.type != "warning") {
                                swal.close();
                                return;
                            }
                            if (isConfirm) {
                                swal({
                                    title: "Deleted!",
                                    text: "Your imaginary file has been deleted.",
                                    type: "success",
                                    timer: 1000
                                })
                                if(objOptions.callback){
                                    objOptions.callback.call();
                                }
                            } else {
                                swal({
                                    title: "Cancelled",
                                    text: "Your imaginary file is safe :)",
                                    type: "error",
                                    timer: 1000
                                })
                            }
                        });
                    }
                }
                
                //-- load resource for call in feature
                $ocLazyLoad.load([
        			tinyConfig.dirTemp+'/sweetalert/sweetalert.min.js?v=1',
                    tinyConfig.dirTemp+'/sweetalert/sweetalert.css?v=1'
        		]).then(function(){
  		            init();
        		});
            },
            confirm: function(options, mess){
                var $$this = this;
                var default__ = {
                    mess: 'Are you sure?',
                    link: tn.makeURL('remove'),
                    data: {},
                    callback: function(){}
                }
                options     = angular.extend(default__, options);
                
                $$this.alert(options.mess, 'Are you sure?', {
                    callback: function(){
                        $$this.ajax({
                            url: options.link,
                            data: options.data
                        }).success(function(){
                            var m  = mess || 'Deleted'; 
                            toastr.success('<i class="fa fa-check"></i> '+m);
                            options.callback.call();
                        })
                    },
                    type: 'warning'
                });
                
            },
            loadData: function(state){
                var ext = "?";
                if(state.match(/[?]/)){
                    ext = "&";
                }
                return $http.get(state+ext+'load_data=yes&json=yes').then(function(response){
                    return response.data; 
                })
            }
        }
    })
    .factory('tinyajaxHanding', function($q, $rootScope) {
          return {
            request: function(config) {
              return config;
            },
        
            // optional method
           requestError: function(rejection) {
              return $q.reject(rejection);
            },
            
            response: function(response) {
              if(response.data.hash) 
                    tinyConfig.tinyToken = response.data.hash;
              
              if(response.data.noLogin && response.data.noLogin == 1){
                    if(typeof $rootScope.isAdmin == 'function') $rootScope.isAdmin();
              }
              return response;
            },
        
           responseError: function responseError(rejection) {
                return $q.reject(rejection);
            }
          };
    })
    .filter('mkurl', function($sce) {
        return function(val) {
            return val.replace('.html', '');
        };
    })
    .filter('unsafe', function($sce) {
        return function(val) {
            return $sce.trustAsHtml(val);
        };
    })
    .filter('tinyimage', function(){
        return function(val) {
            if($.trim(val) == '') return '';

            var $path   = val.split('|'),
                $file   = $path[1],
                $folder = $path[0];

            return [URL_SERVER + 'uploads', $folder, 'thumbs', $file].join('/');

        }
    })