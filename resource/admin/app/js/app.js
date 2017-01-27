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

app.run(function($rootScope, $uibModal, $state)
{
    $rootScope.tinyDestroy = [];
    $rootScope.currentPage = {};
    $rootScope.item_page   = tinyConfig.item_page;
    
    $rootScope.isAdmin = function(){
        $rootScope.currentModal = $uibModal.open({
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
                        }, true).then(function(data){
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
				},
                statusPunch: function($tiny) {
                    return $tiny.loadData('/admin/hr/check-status-punch');
                }
			},
			controller: 'HomeCtrl'
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
        state('admin.profile', {
            url: '/profile/*module',
            templateUrl: function($stateParams){
                if($stateParams && jQuery.trim($stateParams.module) != '')
                    return URL_SERVER + 'admin/profile/'+$stateParams.module;
                else
                    return URL_SERVER+'admin/profile';
            },
            resolve: {
                profile: function($ocLazyLoad){
                    return $ocLazyLoad.load([
                        ASSETS.core.googleMapsLoader,
                        ASSETS.icons.elusive,
                    ]);
                },
                timelines: function($tiny) {
                    return $tiny.loadData('/admin/profile/load-timelines');
                }
            },
            controller: 'ProfileCtrl'
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
        }).
        //------------------------------
        // CRM MODULE
        //------------------------------
        state('admin.crm', {
            url: '/crm',
            templateUrl: URL_SERVER+'admin/crm',
            controller: 'CRMCtrl',
            redirectTo: 'admin.crm.stream'
        }).
        state('admin.crm.stream', {
            url: '/stream/',
            views: {
                content: {
                    templateUrl: URL_SERVER + 'admin/crm/stream',
                    controller: 'CRMListCtrl'
                }
            }
        }).
        state('admin.crm.contacts', {
            url: '/contact/*module',
            resolve: {
                deps: function($ocLazyLoad){
                    return $ocLazyLoad.load([
                        ASSETS.tables.datatables,
                    ]);
                },
            },
            views: {
                content: {
                    templateUrl: function($stateParams){
                        if($stateParams && jQuery.trim($stateParams.module) != '')
                            return URL_SERVER + 'admin/crm/'+$stateParams.module;
                        else
                            return URL_SERVER + 'admin/crm/contact';
                    },
                    controller: 'CRMListCtrl'
                }
            }
        }).
        //------------------------------
        // PRODUCT MODULE
        //------------------------------
        state('admin.project', {
            url: '/project',
            templateUrl: URL_SERVER + 'admin/project',
            resolve: {
                iCheck: function($ocLazyLoad){
                    return $ocLazyLoad.load([
                        ASSETS.forms.icheck,
                    ]);
                },
                deps: function($ocLazyLoad){
                    return $ocLazyLoad.load([
                        ASSETS.tables.datatables,
                    ]);
                }
            },
            controller: 'ProjectBaseCtrl'
        }).
        state('admin.project.add', {
            url: '/add?is_template',
            templateUrl: URL_SERVER + 'admin/project/add',
            controller: 'ProjectCtrl'
        }).
        state('admin.project.detail', {
            url: '/project-view/{project_id:[0-9]}',
            templateUrl: URL_SERVER + 'admin/project/project-view',
            resolve: {
                projectData: function($stateParams, $tiny){
                    return $tiny.loadData('/admin/project/load-project-info/' + $stateParams.project_id);
                },
                tasks: function($stateParams, $tiny){
                    return $tiny.loadData('/admin/project/load-tasks/' + $stateParams.project_id);
                }
            },
            controller: 'ProjectViewCtrl'
        }).
        //------------------------------
        // ADD TASK MODULE
        //------------------------------
        state('admin.task', {
            url: '/task',
            templateUrl: URL_SERVER + 'admin/task',
            controller: 'taskBaseCtrl'
        }).
        state('admin.task.add', {
            url: '/add?project_id',
            resolve: {
                projectData: function($stateParams, $tiny){
                    if($stateParams.project_id)
                    {
                        return $tiny.loadData('/admin/project/load-project-info/' + $stateParams.project_id);
                    }
                    return false;
                }
            },
            templateUrl: URL_SERVER + 'admin/task/add',
            controller: 'taskCtrl'
        }).
        //------------------------------
        // DAILY REPORTS
        //------------------------------
        state('admin.dailyreport', {
            url: '/dailyreport',
            templateUrl: URL_SERVER + 'admin/dailyreport',
            controller: 'dailyreportCtrl',
            resolve: {
                deps: function($ocLazyLoad){
                    return $ocLazyLoad.load([
                        ASSETS.tables.datatables,
                    ]);
                }
            },
        }).
        state('admin.dailyreport.add', {
            url: '/report-writer',
            templateUrl: URL_SERVER + 'admin/dailyreport/reportWriter',
            controller: 'dailyreportAddCtrl'
        })
        
    $locationProvider.html5Mode({
      enabled: true,
      requireBase: false
    });
});

app
.filter('trustAsResourceUrl', ['$sce', function($sce) {
    return function(val) {
        return $sce.trustAsResourceUrl(val);
    };
}])
.filter('keytolabel', function(){
    return function(val) {
        var str = '';
        if(typeof val == 'string')
        {
            val = val.split(/_/);
            val.forEach(function(v, k){
                str += (k > 0) ? ' ' + tn.firstToUpperCase(v) : tn.firstToUpperCase(v);
            })
        }

        return str;
    }
})
