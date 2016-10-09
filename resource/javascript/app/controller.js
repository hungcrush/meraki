'use strict';
angular.module('yuyu.controller', [])
    //-- Basic Controler, not related to the controlers below
    .controller('tinyWapper', function($scope, $rootScope, $state){
        $scope.searchOpen = false;
        $scope.toggleSearch = function(value){
            $scope.searchOpen = value !== undefined ? value : !$scope.searchOpen;
        }

        $scope.cartOpen = false;
        $scope.toggleCart = function(value){
            $scope.cartOpen = value !== undefined ? value : !$scope.cartOpen;
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
    })
    .controller('HomeCtrl', function($rootScope, $state)
    {
        $rootScope.isContainer = false;
        $rootScope.bannerSlide = [
            {
                src: URL_SERVER+'slide_2.jpg',
                link: '#'
            }
        ];
    })
    .controller('ProductCtrl', function($rootScope)
    {
        $rootScope.isActive = 'product';
    })
    .controller('PhotobookCtrl', function($scope, $rootScope)
    {
        $rootScope.isActive = 'product';
        $scope.objExplore = [
            {
                src: URL_SERVER+'explore_1.jpg',
                link: '#'
            },
            {
                src: URL_SERVER+'explore_2.jpg',
                link: '#'
            },
            {
                src: URL_SERVER+'explore_3.jpg',
                link: '#'
            },
            {
                src: URL_SERVER+'explore_4.jpg',
                link: '#'
            },
            {
                src: URL_SERVER+'explore_5.jpg',
                link: '#'
            }
        ];
    })
    .controller('ChildPhotobookCtrl', function($scope, $rootScope)
    {
        $rootScope.isActive = 'product';
        $scope.objPhotobook = [
            {
                src: URL_SERVER+'photobook_1.jpg',
                link: '#'
            },
            {
                src: URL_SERVER+'photobook_2.jpg',
                link: '#'
            },
            {
                src: URL_SERVER+'photobook_3.jpg',
                link: '#'
            },
            {
                src: URL_SERVER+'photobook_4.jpg',
                link: '#'
            },
            {
                src: URL_SERVER+'photobook_5.jpg',
                link: '#'
            },
            {
                src: URL_SERVER+'photobook_6.jpg',
                link: '#'
            }
        ];
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