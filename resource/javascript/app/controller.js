'use strict';
angular.module('yuyu.controller', [])
    //-- Basic Controler, not related to the controlers below
    .controller('tinyWapper', function($scope, $rootScope, $state, $tiny, $cookies, $timeout){
        $scope.searchOpen = false;
        $scope.toggleSearch = function(value){
            $scope.searchOpen = value !== undefined ? value : !$scope.searchOpen;
            if($scope.searchOpen){
                _documentClickSet();
            }
        }

        $scope.cartOpen = false;
        $rootScope.toggleCart = function(value){
            $scope.cartOpen = value !== undefined ? value : !$scope.cartOpen;
            if($scope.cartOpen){
                _documentClickSet();
            }
        }

        var _documentClickSet = function(){
            setTimeout(function(){
                $(document).off('click._documentClickSet').on('click._documentClickSet', function(e){
                    if(!$(e.target).parents('.tiny-handle-drop').length){
                        $timeout(function(){
                            $scope.searchOpen = false;
                            $scope.cartOpen   = false;
                            $(document).off('click._documentClickSet');
                        })
                    }
                })
            }, 500);
        }

        _window.on('addToCart', function(e, data){
            console.log(data);
        })

        $rootScope.templateConlection = false;
        $rootScope.$on('$stateChangeStart', 
        function(event, toState, toParams, fromState, fromParams){ 
            console.log(toState)
            $scope.templateConlection = !!(toState.templateConlection);
            $scope.pageClass = toState.pageClass;
        })

        $rootScope.$on('$stateChangeSuccess', function(){
            $scope.showMobileNav(false);
        })

        //-- load menu
        var sortByFeature = function(data)
        {   
            var feature = [];
            angular.copy(data).forEach(function(obj, k){
                if(obj.feature == 1)
                {
                    feature.push(obj);
                    data.splice(k, 1);
                }
            })

            return feature.concat(data);
        }
        $tiny.loadData(URL_SERVER + 'welcome/get-navbar').then(function(data){
            //$scope.navbarShop = sortByFeature(data.product);
            var categories = new convertCategories(data.product);
            $scope.navbarShop = categories.parseObjCategory(0);
            $rootScope.productn = data.productn;
        });

        $scope.mobileNavShow = false;
        $scope.showMobileNav = function(value){
            if(typeof value != 'undefined') return $scope.mobileNavShow = value;
            $scope.mobileNavShow = !$scope.mobileNavShow;
        }

        /* SHOP CART */
        $rootScope.shopCart = [];
        $rootScope._privateCartIds = {};

        $rootScope.loadShopCartViaCookies = function(){
            var $data = $cookies.get('tiny_carts');
            console.log($data);
            if($data)
            {
                $rootScope._privateCartIds = $.parseJSON($data);
                if(Object.keys($rootScope._privateCartIds).length)
                {
                    $tiny.ajax({
                        url: '/products/load-data-items',
                        data: {items: Object.keys($rootScope._privateCartIds)}
                    })
                    .then(function(data){

                        data.product.forEach(function(itemData, k){
                            var item = $rootScope._privateCartIds[itemData.product_item_id];
                            $rootScope.shopCart.push({quantity: item.quantity, size: item.size, product_id: itemData.product_item_id, name: itemData.name, price: tn.parseInt(itemData.price), image: itemData.thumb});
                        })
                    })
                }
                
            }
        }

        $rootScope.loadShopCartViaCookies();

        
        $rootScope.addToCart = function(item)
        {
            if(!$rootScope._privateCartIds[item.product_id]){
                $tiny.loadData('/products/load-data-item/0/' + item.product_id).then(function(data){
                    var itemData = data.item_data.product;
                    $rootScope._privateCartIds[item.product_id] = {
                        quantity: item.quantity,
                        size: item.size
                    }

                    $rootScope.shopCart.push({quantity: item.quantity, size: item.size, product_id: itemData.product_item_id, name: itemData.name, price: tn.parseInt(itemData.price), image: itemData.thumb});
                
                    $cookies.put('tiny_carts', JSON.stringify($rootScope._privateCartIds), {path: '/'});
                    $timeout(function(){
                        $rootScope.toggleCart(true);
                    }, 500);
                })
            }
           
        }
        $rootScope.removeItemCart = function(key)
        {
            var item = $rootScope.shopCart[key];
            delete $rootScope._privateCartIds[item.product_id];
            $rootScope.shopCart.splice(key, 1);

            $cookies.put('tiny_carts', JSON.stringify($rootScope._privateCartIds), {path: '/'});
        }

        $rootScope.calcSubTotal = function(){
            var total = 0;
            $rootScope.shopCart.forEach(function(obj){
                total += (obj.price * (obj.quantity/2));
            })

            return total;
        }
    })
    .controller('HomeCtrl', function($rootScope, $state, $tiny, $scope)
    {
        $rootScope.isContainer = false;
        $rootScope.bannerSlide = [
            {
                src: URL_SERVER+'slide_2.jpg',
                link: '#'
            }
        ];
        $scope.instagrams = [];
        $tiny.loadData(URL_SERVER + 'welcome/get-recent-instagram').then(function(data){
            console.log(data);
            $scope.instagrams = data.medias;
        })
    })
    .controller('ProductCtrl', function($rootScope)
    {
        $rootScope.isActive = 'product';
    })
    .controller('PaymentCtrl', function($scope, $tiny){
        $scope.submitPayment = function(data){
            $tiny.ajax({
                url: '/payment/submit',
                data: {payment_data: data}
            })
            .then(function(data){

            })
        }
    })
    .controller('PhotobookCtrl', function($scope, $rootScope, productData, $stateParams)
    {
        $scope.category_data = productData.category.product;
        $scope.products = productData.items.product;

        console.log('Debug products', $scope.products);
    })
    .controller('ChildPhotobookCtrl', function($scope, $rootScope, itemsData)
    {
        $scope.itemData = itemsData.item_data.product;
        $scope.first_image = $scope.itemData.images[0];
    })
    .controller('DetailPhotobookCtrl', function($rootScope, $scope)
    {
        $rootScope.isActive = 'product';
        $scope.listImage = [
            {
                src: URL_SERVER+'detail_1.jpg',
                hash: 'one'
            },
            {
                src: URL_SERVER+'detail_2.jpg',
                hash: 'two'
            },
            {
                src: URL_SERVER+'detail_3.jpg',
                hash: 'three'
            },
            {
                src: URL_SERVER+'detail_4.jpg',
                hash: 'four'
            },
            {
                src: URL_SERVER+'detail_5.jpg',
                hash: 'five'
            },
            {
                src: URL_SERVER+'detail_6.jpg',
                hash: 'six'
            }
        ];
        
        $scope.res = {
            0: {
                items: 2  
            },
            500:{
                items: 3  
            },
            787: {
                items: 4
            },
            1024: {
                items: 5
            },
        };
        
        $scope.res_bottom = {
            0: {
                items: 3  
            },
            787: {
                items: 4
            },
            1024: {
                items: 5
            },
        };
        
        $scope.listThumbnail = [
            {
                src: URL_SERVER+'detail_1_1.jpg',
                link: '#'
            },
            {
                src: URL_SERVER+'detail_2_2.jpg',
                link: '#'
            },
            {
                src: URL_SERVER+'detail_3_3.jpg',
                link: '#'
            },
            {
                src: URL_SERVER+'detail_4_4.jpg',
                link: '#'
            },
            {
                src: URL_SERVER+'detail_5_5.jpg',
                link: '#'
            },
            {
                src: URL_SERVER+'detail_6_6.jpg',
                link: '#'
            }
        ];
    })
    .controller('BlogCtrl', function($rootScope)
    {
        $rootScope.isActive = 'blog';
    })
    .controller('AboutCtrl', function($rootScope)
    {
        $rootScope.isActive = 'about';
    })
    .controller('ContactCtrl', function(){

    })
    .controller('PagesCtrl', function(){
        
    })