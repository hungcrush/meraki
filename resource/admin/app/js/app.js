'use strict';

var app = angular.module('xenon-app', [
	'ngCookies',

	'ui.router',
	'ui.bootstrap',

	'oc.lazyLoad',

	'xenon.controllers',
	'xenon.directives',
	'xenon.factory',
	'xenon.services',

	'FBAngular',
    'tinyfw.directive',
    'tinyfw.factory',
    'tiny.services',
    'tiny.admin.controllers',
    
    'xenon.constant'
]);

app.run(function($rootScope, $modal, $state)
{
    $rootScope.tinyDestroy = [];
    $rootScope.currentPage = {};
    $rootScope.item_page   = tinyConfig.item_page;
    
    $rootScope.isAdmin = function(){
        $rootScope.currentModal = $modal.open({
			templateUrl: tinyConfig.templatePathAjax('lost-session'),
			size: null,
			backdrop: 'static'
		});
        $rootScope.isAdmin = null;
    };
	// Page Loading Overlay
	public_vars.$pageLoadingOverlay = jQuery('.page-loading-overlay');
	jQuery(window).load(function()
	{
		public_vars.$pageLoadingOverlay.addClass('loaded');
	})
});


app.config(function($stateProvider, $urlRouterProvider, $ocLazyLoadProvider, ASSETS, $locationProvider){

	$urlRouterProvider.otherwise(PATH_+'admin/home');

	$stateProvider.
        //------------------------------
        // LOGIN PAGE - SINGLE
        //------------------------------
        state('login', {
            url: PATH_+'login',
            templateUrl: URL_SERVER+'login',
			controller: 'LoginCtrl',
			resolve: {
				resources: function($ocLazyLoad){
					return $ocLazyLoad.load([
						ASSETS.forms.jQueryValidate,
						ASSETS.extra.toastr,
					]);
				},
			}
        }).
		// Main Layout Structure
        //------------------------------
        // MAIN LAYOUT STRUCTURE
        //------------------------------
        state('admin', {
            abstract: true,
            url: PATH_+'admin',
            templateUrl: tinyConfig.templatePath('layout/app-body'),
            resolve:{
                userinfo: function($tiny){
                    return $tiny.loadData(URL_SERVER + 'login/getUserdata');
                }
            },
            controller: function($rootScope, $tiny, $scope, userinfo){
                var $u = userinfo.userdata;
                
                if($u.avatar_link && $u.avatar_link != ''){
                    var a = $u.avatar_link.split('|');
                    $u.avatar = tn.getURLUploaded(a[0], a[1]).src;
                }else
                    $u.avatar = tinyConfig.dirTemp+'/admin/assets/images/user-4.png';
    
                $rootScope.userinfo           = $u;
                $rootScope.p                  = $u.permissions;
                
                delete $rootScope.userinfo.permissions;
                
                $rootScope.avatar_default     = tinyConfig.dirTemp+'/admin/assets/images/user-1.png';
                
				$rootScope.isLoginPage        = false;
				$rootScope.isLightLoginPage   = false;
				$rootScope.isLockscreenPage   = false;
				$rootScope.isMainPage         = true;
                $rootScope.dirTem             = tinyConfig.dirTemp+'/admin'; 
                
                $scope.CheckLogin = function(){
                    var a = function(){
                        $tiny.ajax({
                            url: URL_SERVER+'admin/session',
                        }, true).success(function(data){
                            if(!data.noLogin)
                                setTimeout(function(){
                                    a();
                                }, 10000)
                            else if(data.noLogin == 1){
                                if(typeof $rootScope.isAdmin == 'function') $rootScope.isAdmin();
                            }  
                        })
                    }
                    a();
                }
                $scope.CheckLogin();
			}
        }).
        //------------------------------
        // HOME PAGE - DASHBOARD
        //------------------------------
		state('admin.home', {
			url: '/home',
			templateUrl: URL_SERVER+'admin/home',
            resolve: {
				resources: function($ocLazyLoad){
					return $ocLazyLoad.load([
						ASSETS.icons.meteocons
					]);
				}
			},
			controller: function($scope, $tiny){
			     $scope.yutest = 99.9;
			}
		}).
        //------------------------------
        // ADMINISTRATOR - MANAGE WEBSITE
        //--------------
        // multi module
        //------------------------------
        state('admin.administrator', {
            url: '/administrator/*module',
            templateUrl: function($stateParams){
                
                if($stateParams && jQuery.trim($stateParams.module) != '')
                    return URL_SERVER + 'admin/administrator/'+$stateParams.module;
                else
                    return URL_SERVER+'admin/administrator';
            },
            resolve: {
                URL: function($stateParams){
                    return URL_SERVER + 'admin/administrator/'+$stateParams.module;
                },
                Load: function($stateParams, $tiny, g){
                    var m = ($stateParams.module).split('/');
                    
                    switch(m[0]){
                        case 'permission':
                        case 'groupuser':
                        case 'peoples':
                        case 'menu':
                            return $tiny.loadData(g.URL($stateParams));
                    }
                    return null;
                },
                g: function(){
                    return this.resolve;
                },
                resources: function($stateParams, $ocLazyLoad){
                    var m = ($stateParams.module).split('/');
                    
                    switch(m[0]){
                        case 'menu':
                            return $ocLazyLoad.load([
        						ASSETS.uikit.base,
        						ASSETS.uikit.nestable,
        					]);
                    }
                }
            },
            controllerProvider: function($stateParams){
                var m = ($stateParams.module).split('/');
                var Ctrl = tn.capitalizeFirstLetter(m[0])+'Ctrl';
                try{
                    return Ctrl;
                }catch(err){
                    return null;
                }
            }
        }).
        //------------------------------
        // CONTENT MANAGER
        //------------------------------
        state('admin.content', {
            url: '/content/*module',
            templateUrl: function($stateParams){
                if($stateParams && jQuery.trim($stateParams.module) != '')
                    return URL_SERVER + 'admin/content/'+$stateParams.module;
                else
                    return URL_SERVER+'admin/content';
            },
            controller: 'AboutCtrl',
            resolve: {
                Load: function($tiny, $stateParams){
                    if($stateParams.module == 'home'){
                        return $tiny.loadData(URL_SERVER + 'admin/content/home/load');
                    }
                },
                g: function(){
                    return this.resolve;
                }
            }
        }).
        //------------------------------
        // PRODUCT MANAGER
        //------------------------------
        state('admin.product', {
            url: '/product',
            resolve: {
				resources: function($ocLazyLoad){
				    tn.loadJs(tinyConfig.dirTemp+'/tinymce/tinymce.min.js');
					return $ocLazyLoad.load([
						ASSETS.core.jQueryUI,
                        ASSETS.forms.jQueryValidate
					]);
				},
                Load: function($tiny){
                      return $tiny.loadData(URL_SERVER + 'admin/product/');
                },
                g: function(){
                    return this.resolve;
                }
			},
            templateUrl: URL_SERVER+'admin/product',
            controller: 'ProductCtrl'
        }).
        state('admin.product.list', {
            url: '/',
            views: {
                products: {
                    templateUrl: URL_SERVER+'admin/product/getTemplate/product'
                }
                
            },
            isRedirect: true
        }).
        state('admin.product.list.items', {
            url: ':module',
            views: {
                items: {
                    templateUrl: URL_SERVER+'admin/product/getTemplate/items',
                    controller: 'ProductItemCtrl'
                }
            }
        }).
        state('admin.product.list.detail', {
            url: ':module/:detail_id',
            resolve: {
                itemData: function($tiny, $stateParams){
                    return $tiny.loadData(URL_SERVER + 'admin/product/'+$stateParams.module+'/'+$stateParams.detail_id);
                }
            },
            views: {
                items: {
                    templateUrl: URL_SERVER+'admin/product/getTemplate/items',
                    controller: 'ProductItemCtrl'
                },
                detail: {
                    templateUrl: URL_SERVER+'admin/product/getTemplate/detail',
                    controller: 'ProductItemDetailCtrl'
                }
            }
        }).
        //------------------------------
        // BLOG MANAGER
        //------------------------------
        state('admin.blog', {
            url: '/blog/*module',
            templateUrl: function($stateParams){
                if($stateParams && jQuery.trim($stateParams.module) != '')
                    return URL_SERVER + 'admin/blog/'+$stateParams.module;
                else
                    return URL_SERVER+'admin/blog';
            },
            resolve: {
                tagsinput: function($ocLazyLoad){
					return $ocLazyLoad.load([
						ASSETS.forms.tagsinput,
					]);
				},
                Categories: function($tiny){
                    return $tiny.loadData(URL_SERVER + 'admin/blog/categories');
                },
                loadSingle: function($stateParams, $tiny){
                    var m = ($stateParams.module).split('/');
                    if(m[0] == 'edit' && m[1] != ''){
                        return $tiny.loadData(URL_SERVER + 'admin/blog/?post_id='+m[1])
                    }
                },
                g: function(){
                    return this.resolve;
                }
            },
            controllerProvider: function($stateParams){
                var m = ($stateParams.module).split('/');
                var Ctrl = 'Blog'+tn.capitalizeFirstLetter(m[0])+'Ctrl';
                try{
                    return Ctrl;
                }catch(err){
                    return null;
                }
            }
        })        
        
    $locationProvider.html5Mode({
      enabled: true,
      requireBase: false
    });
});

app.filter('unsafe', function($sce) {
    return function(val) {
        console.log(val);
        return $sce.trustAsHtml(val);
    };
})
.filter('trustAsResourceUrl', ['$sce', function($sce) {
    return function(val) {
        return $sce.trustAsResourceUrl(val);
    };
}])
