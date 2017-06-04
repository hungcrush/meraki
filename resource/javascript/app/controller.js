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

        var _filterIdOthers = function(id)
        {   
            var _obj = [];
            forEach($rootScope._privateCartIds, function(obj, keys){
                if(keys.match(new RegExp(id, 'gi')))
                {
                    _obj.push(obj);
                }
            })

            return _obj;
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
                            var items = _filterIdOthers(itemData.product_item_id);
                            items.forEach(function(item){
                                $rootScope.shopCart.push({quantity: item.quantity, size: item.size, product_id: itemData.product_item_id, name: itemData.name, price: tn.parseInt(itemData.price), image: itemData.thumb});
                            })
                            
                        })
                    })
                }
                
            }
        }

        $rootScope.loadShopCartViaCookies();

        
        $rootScope.textAddtoCart = 'Add to Bag';
        $rootScope.addToCart = function(item)
        {
            $rootScope.textAddtoCart = 'Processing...';
            
            $tiny.loadData('/products/load-data-item/0/' + item.product_id).then(function(data){
                var itemData = data.item_data.product;
                var _product_id;
                if(!$rootScope._privateCartIds[item.product_id]){
                    _product_id = itemData.product_item_id;
                    $rootScope._privateCartIds[_product_id] = {
                        quantity: item.quantity,
                        size: item.size
                    }
                }
                else
                {
                    // is exist in the bag
                    _product_id = itemData.product_item_id + '__' + new Date().getTime();
                    $rootScope._privateCartIds[_product_id] = {
                        quantity: item.quantity,
                        size: item.size
                    }
                }

                $rootScope.shopCart.push({quantity: item.quantity, size: item.size, product_id: _product_id, name: itemData.name, price: tn.parseInt(itemData.price), image: itemData.thumb});
            
                $cookies.put('tiny_carts', JSON.stringify($rootScope._privateCartIds), {path: '/'});
                $timeout(function(){
                    $rootScope.toggleCart(true);
                    $rootScope.textAddtoCart = 'Add to Bag';
                }, 500);

                
            })
           
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
                total += (obj.price * (obj.quantity));
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
    .controller('PaymentCtrl', function($scope, $tiny, $state){
        $scope.submitPayment = function(data){
            $tiny.ajax({
                url: '/payment/submit',
                data: {payment_data: data}
            })
            .then(function(data){
                if(data.success)
                {
                    $state.go('home.payment.responsive', {params: 'complete'});
                }
            })
        }
    })
    .controller('PhotobookCtrl', function($scope, $rootScope, productData, $stateParams)
    {
        $scope.category_data = productData.category.product;
        $scope.products = productData.items.product;

        console.log('Debug products', $scope.products);
    })
    .controller('ProductDetailCtrl', function($scope, $rootScope, itemsData, $tiny)
    {
        $scope.itemData = itemsData.item_data.product;
        $scope.first_image = $scope.itemData.images[0];

        $scope.product_related = [];

        $scope.loadRelatedProducts = function()
        {
            $tiny.ajax({
                url: '/products/load-related/',
                data: {
                    item_id: $scope.itemData.product_item_id, 
                    product_id: $scope.itemData.product_id
                }
            })
            .then(function(data){
                $scope.product_related = data.products.product;
                console.log('Debug product related', $scope.product_related);
            })
        }

        $scope.loadRelatedProducts();
    })
    .controller('ProductDetailCtrl2', function($rootScope, $scope)
    {
        
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
    .controller('SearchCtrl', function(){

    })

    