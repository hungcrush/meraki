'use strict';

angular.module('xenon.controllers', []).
	controller('LoginCtrl', function($scope, $rootScope)
	{
		$rootScope.isLoginPage        = true;
		$rootScope.isLightLoginPage   = false;
		$rootScope.isLockscreenPage   = false;
		$rootScope.isMainPage         = false;
	}).
	controller('LoginLightCtrl', function($scope, $rootScope)
	{
		$rootScope.isLoginPage        = true;
		$rootScope.isLightLoginPage   = true;
		$rootScope.isLockscreenPage   = false;
		$rootScope.isMainPage         = false;
	}).
	controller('LockscreenCtrl', function($scope, $rootScope)
	{
		$rootScope.isLoginPage        = false;
		$rootScope.isLightLoginPage   = false;
		$rootScope.isLockscreenPage   = true;
		$rootScope.isMainPage         = false;
	}).
	controller('MainCtrl', function($scope, $rootScope, $location, $layout, $layoutToggles, $pageLoadingBar, Fullscreen, $tiny, $timeout, $state)
	{
		$rootScope.isLoginPage        = false;
		$rootScope.isLightLoginPage   = false;
		$rootScope.isLockscreenPage   = false;
		$rootScope.isMainPage         = true;

		$rootScope.layoutOptions = {
			horizontalMenu: {
				isVisible		: false,
				isFixed			: true,
				minimal			: false,
				clickToExpand	: false,

				isMenuOpenMobile: false
			},
			sidebar: {
				isVisible		: true,
				isCollapsed		: false,
				toggleOthers	: true,
				isFixed			: true,
				isRight			: false,

				isMenuOpenMobile: false,

				// Added in v1.3
				userProfile		: true
			},
			chat: {
				isOpen			: false,
			},
			settingsPane: {
				isOpen			: false,
				useAnimation	: true
			},
			container: {
				isBoxed			: false
			},
			skins: {
				sidebarMenu		: '',
				horizontalMenu	: '',
				userInfoNavbar	: ''
			},
			pageTitles: true,
			userInfoNavVisible	: false
		};
        
        $rootScope.reload = function reload() {
            $state.transitionTo($state.current, $stateParams, { reload: true, inherit: true, notify: true });
        };

		$layout.loadOptionsFromCookies(); // remove this line if you don't want to support cookies that remember layout changes


		$scope.updatePsScrollbars = function()
		{
			var $scrollbars = jQuery(".ps-scrollbar:visible");

			$scrollbars.each(function(i, el)
			{
				if(typeof jQuery(el).data('perfectScrollbar') == 'undefined')
				{
					jQuery(el).perfectScrollbar();
				}
				else
				{
					jQuery(el).perfectScrollbar('update');
				}
			})
		};


		// Define Public Vars
		public_vars.$body = jQuery("body");


		// Init Layout Toggles
		$layoutToggles.initToggles();


		// Other methods
		$scope.setFocusOnSearchField = function()
		{
			public_vars.$body.find('.search-form input[name="s"]').focus();

			setTimeout(function(){ public_vars.$body.find('.search-form input[name="s"]').focus() }, 100 );
		};


		// Watch changes to replace checkboxes
        /*
		$scope.$watch(function()
		{
			cbr_replace();
		});
        */
		// Watch sidebar status to remove the psScrollbar
		$rootScope.$watch('layoutOptions.sidebar.isCollapsed', function(newValue, oldValue)
		{
			if(newValue != oldValue)
			{
				if(newValue == true)
				{
					public_vars.$sidebarMenu.find('.sidebar-menu-inner').perfectScrollbar('destroy')
				}
				else
				{
					public_vars.$sidebarMenu.find('.sidebar-menu-inner').perfectScrollbar({wheelPropagation: public_vars.wheelPropagation});
				}
			}
		});


		// Page Loading Progress (remove/comment this line to disable it)
		$pageLoadingBar.init();

		$scope.showLoadingBar = showLoadingBar;
		$scope.hideLoadingBar = hideLoadingBar;


		// Set Scroll to 0 When page is changed
		$rootScope.$on('$stateChangeStart', function(evt, to, params)
		{
			var obj = {pos: jQuery(window).scrollTop()};

			TweenLite.to(obj, .25, {pos: 0, ease:Power4.easeOut, onUpdate: function()
			{
				$(window).scrollTop(obj.pos);
			}});
            
            delete $rootScope.currentPage.title;
   
        });
        

		// Full screen feature added in v1.3
		$scope.isFullscreenSupported = Fullscreen.isSupported();
		$scope.isFullscreen = Fullscreen.isEnabled() ? true : false;

		$scope.goFullscreen = function()
		{
			if (Fullscreen.isEnabled())
				Fullscreen.cancel();
			else
				Fullscreen.all();

			$scope.isFullscreen = Fullscreen.isEnabled() ? true : false;
		}
        
        $scope.getUrlCurrent = function(){
            return {
                name    : $state.current.name,
                params  : JSON.stringify($state.params)
            }
        }
        $rootScope.changeRoute = function(url, params, forceReload) {
            if(url == 'login') url = URL_SERVER + url;
            params = params || {};
            $scope = $scope || angular.element(document).scope();
            if(forceReload) { // that's right TWO dollar signs: $$phase
                window.location = url;
            } else {
                $state.go(url, params);
            }
        };
	}).
	controller('SidebarMenuCtrl', function($scope, $rootScope, $menuItems, $timeout, $location, $state, $layout, $tiny)
	{

		// Menu Items
		var $sidebarMenuItems = $menuItems.instantiate();
        
		//$scope.menuItems = $sidebarMenuItems.prepareSidebarMenu().getAll();
        
        $scope.menuItems = [];
        $tiny.ajax({
            url: URL_SERVER+'admin/administrator/menu/Navigation'
        }).success(function(data){
            $scope.menuItems = $sidebarMenuItems.prepareSidebarMenu(data.menu).getAll();
            setActice();
            
            // Trigger menu setup
    		public_vars.$sidebarMenu = public_vars.$body.find('.sidebar-menu');
    		$timeout(setup_sidebar_menu, 1);
    
    		ps_init(); // perfect scrollbar for sidebar
        })
        
		// Set Active Menu Item
		//$sidebarMenuItems.setActive( $location.path() );
        
        var setActice = function(){
            var module___   = '';
            
            if($state.params.module && jQuery.trim($state.params.module) != ''){
                module___ = ($state.params.module).split('/');
            }
            
            var location = $location.path();
            if(module___.length > 2){
                location = location.replace(new RegExp('/'+module___[2]), '');
                location = location.replace(new RegExp(module___[1]), '');
            }
            
			var item = $sidebarMenuItems.setActive(location);
            if(!$rootScope.currentPage.title && item != null)
                $rootScope.currentPage.title = item.title;
        };

		$rootScope.$on('$stateChangeSuccess', function()
		{
            setActice();
		});
	}).
	controller('SettingsPaneCtrl', function($rootScope)
	{
		// Define Settings Pane Public Variable
		public_vars.$settingsPane = public_vars.$body.find('.settings-pane');
		public_vars.$settingsPaneIn = public_vars.$settingsPane.find('.settings-pane-inner');
	}).
	controller('ChatCtrl', function($scope, $element)
	{
		var $chat = jQuery($element),
			$chat_conv = $chat.find('.chat-conversation');

		$chat.find('.chat-inner').perfectScrollbar(); // perfect scrollbar for chat container


		// Chat Conversation Window (sample)
		$chat.on('click', '.chat-group a', function(ev)
		{
			ev.preventDefault();

			$chat_conv.toggleClass('is-open');

			if($chat_conv.is(':visible'))
			{
				$chat.find('.chat-inner').perfectScrollbar('update');
				$chat_conv.find('textarea').autosize();
			}
		});

		$chat_conv.on('click', '.conversation-close', function(ev)
		{
			ev.preventDefault();

			$chat_conv.removeClass('is-open');
		});
	}).
	controller('UIModalsCtrl', function($scope, $rootScope, $modal, $sce)
	{
		// Open Simple Modal
		$scope.openModal = function(modal_id, modal_size, modal_backdrop)
		{
			$rootScope.currentModal = $modal.open({
				templateUrl: modal_id,
				size: modal_size,
				backdrop: typeof modal_backdrop == 'undefined' ? true : modal_backdrop
			});
		};

		// Loading AJAX Content
		$scope.openAjaxModal = function(modal_id, url_location)
		{
			$rootScope.currentModal = $modal.open({
				templateUrl: modal_id,
				resolve: {
					ajaxContent: function($http)
					{
						return $http.get(url_location).then(function(response){
							$rootScope.modalContent = $sce.trustAsHtml(response.data);
						}, function(response){
							$rootScope.modalContent = $sce.trustAsHtml('<div class="label label-danger">Cannot load ajax content! Please check the given url.</div>');
						});
					}
				}
			});

			$rootScope.modalContent = $sce.trustAsHtml('Modal content is loading...');
		}
	}).
	controller('PaginationDemoCtrl', function($scope)
	{
		$scope.totalItems = 64;
		$scope.currentPage = 4;

		$scope.setPage = function (pageNo)
		{
			$scope.currentPage = pageNo;
		};

		$scope.pageChanged = function()
		{
			console.log('Page changed to: ' + $scope.currentPage);
		};

		$scope.maxSize = 5;
		$scope.bigTotalItems = 175;
		$scope.bigCurrentPage = 1;
	});