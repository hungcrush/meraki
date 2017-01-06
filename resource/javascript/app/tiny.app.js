'use strict';

var tinyfw = angular.module('tinyfw',     
[
    "ui.router", 
    "ngAnimate", 
    "tinyfw.factory", 
    "tinyfw.directive", 
    "oc.lazyLoad", 
    "chieffancypants.loadingBar",
    "yuyu.directive",
    "yuyu.controller"
]);

tinyfw.run(function($rootScope, cfpLoadingBar, $state)
        {
        
        $rootScope.isContainer = true;
        $rootScope.currentPage = {};
        $rootScope.tinyDestroy = [];
        
        $rootScope.$on('$stateChangeStart', function() {
          cfpLoadingBar.start();
          $rootScope.isActive = '';
          
        })
    
        $rootScope.$on('$stateChangeSuccess', function(a,b,c,d,e) {
          cfpLoadingBar.complete();
          
          jQuery('html, body').animate({ scrollTop: 0 }, 500);
          
          setTimeout(function(){
            jQuery('.drag-target').trigger('click');
          }, 200)
        })
        
        $rootScope.$on('$stateChangeError', function(){
            alert('Error!');
            //window.location.reload();
        })
})
tinyfw.config(function($stateProvider, $urlRouterProvider, $locationProvider, $httpProvider, cfpLoadingBarProvider){
  //-- Url default
  cfpLoadingBarProvider.includeSpinner = true;
  $httpProvider.interceptors.push('tinyajaxHanding');
  $urlRouterProvider.otherwise(PATH_)
  
  $stateProvider
    .state('home', {
        url: PATH_,
        resolve: {
			// owlSlider: function($ocLazyLoad){
			// 	return $ocLazyLoad.load([
   //                  tinyConfig.dirTemp+'/owl-carousel/owl.carousel.css',
   //                  tinyConfig.dirTemp+'/owl-carousel/owl.theme.css',
   //                  tinyConfig.dirTemp+'/owl-carousel/owl-carousel-custom.css',
   //                  tinyConfig.dirTemp+'/owl-carousel/owl.carousel.js'
			// 	]);
			// }
        },
        templateUrl: URL_SERVER,
        controller: 'HomeCtrl'
    })
        
    //------------------------------
    // PRODUCTS
    //------------------------------
    
    .state('home.products', {
        url: 'products',
        templateUrl: URL_SERVER+'products',
        controller: 'ProductCtrl'
    })
    
    //-- PHOTOBOOK
    
    .state('home.photobook', {
        url: 'product/{id:[0-9a-fA-F]{1,8}}:SEO',
        resolve: {
            productData: function($tiny, $stateParams) {
                return $tiny.loadData(URL_SERVER + 'products/load-data/' + $stateParams.id);
            }
        },
        templateUrl: URL_SERVER+'products/product',
        controller: 'PhotobookCtrl'
    })
    
    //-- CHILD OF PHOTOBOOK
    
    .state('home.photobook.child', {
        url: '/{photobook_id:[0-9a-fA-F]{1,8}}:SEO_2',
        resolve: {
            itemsData: function($tiny, $stateParams){
                return $tiny.loadData(URL_SERVER + 'products/load-data-item/' + $stateParams.id + '/' + $stateParams.photobook_id);
            }
        },
        templateUrl: URL_SERVER+'products/photobook',
        controller: 'ChildPhotobookCtrl'
    })
    
    //-- DETAIL PHOTOBOOK
    
    .state('home.photobook.child.detail', {
        url: '/{detail_id:[0-9a-fA-F]{1,8}}:SEO_3',
        templateUrl: function($stateParams){
            return URL_SERVER+'product/'+$stateParams.id+'/'+$stateParams.photobook_id+'/'+$stateParams.detail_id
        },
        controller: 'DetailPhotobookCtrl'
    })
    
    //------------------------------
    // BLOG
    //------------------------------
    
    .state('home.blog', {
        url: 'blog',
        templateUrl: URL_SERVER+'blog',
        controller: 'BlogCtrl'
    })
        .state('home.blog.detail', {
            url: '/{blog_id:[0-9a-fA-F]{1,8}}:SEO',
            templateUrl: function($stateParams){
                return URL_SERVER+'blog/'+$stateParams.blog_id
            }
        })
    
    //------------------------------
    // ABOUT
    //------------------------------
    
    .state('home.aboutus', {
        url: 'aboutus',
        templateUrl: URL_SERVER+'aboutus',
        controller: 'AboutCtrl',
        templateConlection: true,
        pageClass: 'our-story'
    })

    //------------------------------
    // CONTACT - GET IN TOUCH
    //------------------------------
    
    .state('home.contact', {
        url: 'contact',
        templateUrl: URL_SERVER+'contact',
        controller: 'ContactCtrl',
        pageClass: 'contact-us'
    })

    //------------------------------
    // ANOTHER PAGES
    //------------------------------
    
    .state('home.pages', {
        url: 'pages/*page',
        templateUrl: function($stateParams){
            var $page = $stateParams.page.split('-')[0];
            return URL_SERVER+'pages/' + $page;
        },
        controller: 'PagesCtrl',
        pageClass: 'contact-us'
    })

    //------------------------------
    // ACCOUNT
    //------------------------------

    .state('home.account', {
        url: 'account',
        templateUrl: URL_SERVER+'account',
        controller: 'AboutCtrl',
        templateConlection: true,
        pageClass: 'template-customers-login '
    })

    //------------------------------
    // PAYMENT
    //------------------------------

    .state('home.payment', {
        url: 'payment',
        templateUrl: URL_SERVER+'payment',
        controller: 'AboutCtrl'
    })
    
    $locationProvider.html5Mode({
      enabled: true,
      requireBase: false
    });
})