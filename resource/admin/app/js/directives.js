angular.module('xenon.directives', []).

    directive('setTitle', function($rootScope){
        return {
            restrict: 'E',
            replace: true,
            link: function(scope, el, attr){
                $rootScope.currentPage.title = attr.title;
            }
        }        
    }).
	// Layout Related Directives
	directive('settingsPane', function(){
		return {
			restrict: 'E',
			templateUrl: tinyConfig.templatePathAjax('layout/settings-pane'),
			controller: 'SettingsPaneCtrl'
		};
	}).
	directive('sidebarMenu', function(){
		return {
			restrict: 'E',
			templateUrl: tinyConfig.templatePathAjax('layout/sidebar-menu'),
			controller: 'SidebarMenuCtrl'
		};
	}).
	directive('sidebarChat', function(){
		return {
			restrict: 'E',
			replace: true,
			templateUrl: tinyConfig.templatePathAjax('layout/sidebar-chat')
		};
	}).
	directive('footerChat', function(){
		return {
			restrict: 'E',
			replace: true,
			controller: 'FooterChatCtrl',
			templateUrl: tinyConfig.templatePathAjax('layout/footer-chat')
		};
	}).
	directive('sidebarLogo', function(){
		return {
			restrict: 'E',
			replace: true,
			templateUrl: tinyConfig.templatePathAjax('layout/sidebar-logo')
		};
	}).
	directive('sidebarProfile', function(){
		return {
			restrict: 'E',
			replace: true,
			templateUrl: tinyConfig.templatePathAjax('layout/sidebar-profile')
		};
	}).
	directive('userInfoNavbar', function(){
		return {
			restrict: 'E',
			replace: true,
			templateUrl: tinyConfig.templatePathAjax('layout/user-info-navbar')
		};
	}).
	directive('pageTitle', function(){
		return {
			restrict: 'E',
			replace: true,
			templateUrl: tinyConfig.templatePath('layout/page-title'),
			link: function(scope, el, attr){
				scope.title = attr.title;
				scope.description = attr.description;
			}
		};
	}).
	directive('siteFooter', function(){
		return {
			restrict: 'E',
			templateUrl: tinyConfig.templatePathAjax('layout/footer')
		};
	}).
	directive('xeBreadcrumb', function(){
		return {
			restrict: 'A',
			link: function(scope, el)
			{
				var $bc = angular.element(el);

				if($bc.hasClass('auto-hidden'))
				{
					var $as = $bc.find('li a'),
						collapsed_width = $as.width(),
						expanded_width = 0;

					$as.each(function(i, el)
					{
						var $a = $(el);

						expanded_width = $a.outerWidth(true);
						$a.addClass('collapsed').width(expanded_width);

						$a.hover(function()
						{
							$a.removeClass('collapsed');
						},
						function()
						{
							$a.addClass('collapsed');
						});
					});
				}
			}
		}
	}).

	// Widgets Directives
	directive('xeCounter', function(){

		return {
			restrict: 'EAC',
			link: function(scope, el, attrs)
			{
				var $el = angular.element(el),
					sm = scrollMonitor.create(el);
                    
				sm.fullyEnterViewport(function()
				{
					var opts = {
						useEasing: 		attrDefault($el, 'easing', true),
						useGrouping:	attrDefault($el, 'grouping', true),
						separator: 		attrDefault($el, 'separator', ','),
						decimal: 		attrDefault($el, 'decimal', '.'),
						prefix: 		attrDefault($el, 'prefix', ''),
						suffix:			attrDefault($el, 'suffix', ''),
					},
					$count		= attrDefault($el, 'count', 'this') == 'this' ? $el : $el.find($el.data('count')),
					from        = attrDefault($el, 'from', 0),
					to          = attrDefault($el, 'to', 100),
					duration    = attrDefault($el, 'duration', 2.5),
					delay       = attrDefault($el, 'delay', 0),
					decimals	= new String(to).match(/\.([0-9]+)/) ? new String(to).match(/\.([0-9]+)$/)[1].length : 0,
					counter 	= new countUp($count.get(0), from, to, decimals, duration, opts);

					setTimeout(function(){ counter.start(); }, delay * 1000);

					sm.destroy();
				});
			}
		};
	}).
	directive('xeFillCounter', function(){

		return {
			restrict: 'EAC',
			link: function(scope, el, attrs)
			{
				var $el = angular.element(el),
					sm = scrollMonitor.create(el);

				sm.fullyEnterViewport(function()
				{
					var fill = {
						current: 	null,
						from: 		attrDefault($el, 'fill-from', 0),
						to: 		attrDefault($el, 'fill-to', 100),
						property: 	attrDefault($el, 'fill-property', 'width'),
						unit: 		attrDefault($el, 'fill-unit', '%'),
					},
					opts 		= {
						current: fill.to, onUpdate: function(){
							$el.css(fill.property, fill.current + fill.unit);
						},
						delay: attrDefault($el, 'delay', 0),
					},
					easing 		= attrDefault($el, 'fill-easing', true),
					duration 	= attrDefault($el, 'fill-duration', 2.5);

					if(easing)
					{
						opts.ease = Sine.easeOut;
					}

					// Set starting point
					fill.current = fill.from;

					TweenMax.to(fill, duration, opts);

					sm.destroy();
				});
			}
		};
	}).
	directive('xeStatusUpdate', function(){

		return {
			restrict: 'EAC',
			link: function(scope, el, attrs)
			{
				var $el          	= angular.element(el),
					$nav            = $el.find('.xe-nav a'),
					$status_list    = $el.find('.xe-body li'),
					index           = $status_list.filter('.active').index(),
					auto_switch     = attrDefault($el, 'auto-switch', 0),
					as_interval		= 0;

				if(auto_switch > 0)
				{
					as_interval = setInterval(function()
					{
						goTo(1);

					}, auto_switch * 1000);

					$el.hover(function()
					{
						window.clearInterval(as_interval);
					},
					function()
					{
						as_interval = setInterval(function()
						{
							goTo(1);

						}, auto_switch * 1000);;
					});
				}

				function goTo(plus_one)
				{
					index = (index + plus_one) % $status_list.length;

					if(index < 0)
						index = $status_list.length - 1;

					var $to_hide = $status_list.filter('.active'),
						$to_show = $status_list.eq(index);

					$to_hide.removeClass('active');
					$to_show.addClass('active').fadeTo(0,0).fadeTo(320,1);
				}

				$nav.on('click', function(ev)
				{
					ev.preventDefault();

					var plus_one = $(this).hasClass('xe-prev') ? -1 : 1;

					goTo(plus_one);
				});
			}
		};
	}).

	// Extra (Section) Directives
	directive('tocify', function(){
		return {
			restrict: 'AC',
			link: function(scope, el, attr)
			{
				if( ! jQuery.isFunction(jQuery.fn.tocify))
					return false;

				var $this = angular.element(el),
					watcher = scrollMonitor.create($this.get(0));

				$this.tocify({
					context: '.tocify-content',
					selectors: "h2,h3,h4,h5"
				});


				$this.width( $this.parent().width() );

				watcher.lock();

				watcher.stateChange(function()
				{
					$($this.get(0)).toggleClass('fixed', this.isAboveViewport)
				});
			}
		}
	}).
	directive('scrollable', function(){
		return {
			restrict: 'AC',
			link: function(scope, el, attr)
			{
				if( ! jQuery.isFunction(jQuery.fn.perfectScrollbar))
					return false;

				var $this = angular.element(el),
					max_height = parseInt(attrDefault($this, 'max-height', 200), 10);

				max_height = max_height < 0 ? 200 : max_height;

				$this.css({maxHeight: max_height}).perfectScrollbar({
					wheelPropagation: true
				});
			}
		}
	}).

	// Forms Directives
	directive('tagsinput', function(){
		return {
			restrict: 'AC',
			link: function(scope, el, attr)
			{
				var $el = angular.element(el);

				if( ! jQuery.isFunction(jQuery.fn.tagsinput))
					return false;
                setTimeout(function(){
                    $el.tagsinput();    
                }, 100)
				
			}
		}
	}).
	directive('validate', function(){
		return {
			restrict: 'AC',
			link: function(scope, el, attr)
			{
				if( ! jQuery.isFunction(jQuery.fn.validate))
					return false;

				var $this = angular.element(el),
					opts = {
						rules: {},
						messages: {},
						errorElement: 'span',
						errorClass: 'validate-has-error',
						highlight: function (element) {
							$(element).closest('.form-group').addClass('validate-has-error');
						},
						unhighlight: function (element) {
							$(element).closest('.form-group').removeClass('validate-has-error');
						},
						errorPlacement: function (error, element)
						{
							if(element.closest('.has-switch').length)
							{
								error.insertAfter(element.closest('.has-switch'));
							}
							else
							if(element.parent('.checkbox, .radio').length || element.parent('.input-group').length)
							{
								error.insertAfter(element.parent());
							}
							else
							{
								error.insertAfter(element);
							}
						}
					},
					$fields = $this.find('[data-validate]');


				$fields.each(function(j, el2)
				{
					var $field = $(el2),
						name = $field.attr('name'),
						validate = attrDefault($field, 'validate', '').toString(),
						_validate = validate.split(',');

					for(var k in _validate)
					{
						var rule = _validate[k],
							params,
							message;

						if(typeof opts['rules'][name] == 'undefined')
						{
							opts['rules'][name] = {};
							opts['messages'][name] = {};
						}

						if($.inArray(rule, ['required', 'url', 'email', 'number', 'date', 'creditcard']) != -1)
						{
							opts['rules'][name][rule] = true;

							message = $field.data('message-' + rule);

							if(message)
							{
								opts['messages'][name][rule] = message;
							}
						}
						// Parameter Value (#1 parameter)
						else
						if(params = rule.match(/(\w+)\[(.*?)\]/i))
						{
							if($.inArray(params[1], ['min', 'max', 'minlength', 'maxlength', 'equalTo']) != -1)
							{
								opts['rules'][name][params[1]] = params[2];


								message = $field.data('message-' + params[1]);

								if(message)
								{
									opts['messages'][name][params[1]] = message;
								}
							}
						}
					}
				});

				$this.validate(opts);
			}
		}
	}).
	// Other Directives
	directive('loginForm', function(){
		return {
			restrict: 'AC',
			link: function(scope, el){

				jQuery(el).find(".form-group:has(label)").each(function(i, el)
				{
					var $this = angular.element(el),
						$label = $this.find('label'),
						$input = $this.find('.form-control');

						$input.on('focus', function()
						{
							$this.addClass('is-focused');
						});

						$input.on('keydown', function()
						{
							$this.addClass('is-focused');
						});

						$input.on('blur', function()
						{
							$this.removeClass('is-focused');

							if($input.val().trim().length > 0)
							{
								$this.addClass('is-focused');
							}
						});

						$label.on('click', function()
						{
							$input.focus();
						});

						if($input.val().trim().length > 0)
						{
							$this.addClass('is-focused');
						}
				});
			}
		};
	})
    
    
    .directive('tinyModal', function($modal, $rootScope, $controller){
            return {
                restrict: 'A',
                scope: {
                    onOpened: '&',
                    htmlContent: '='
                },
                link: function($scope, el, attrs){
                    var fn = $scope.$parent.fn,
                        t = angular.element(el),
                        id = 'myModal';
                        
                    if(fn === undefined && typeof $scope.$parent.$parent.fn != 'undefined'){
                        fn = angular.extend($scope.$parent.$parent.fn);
                    }
                    
                    if(t.data('id')){
                        id = t.data('id').match(new RegExp('.htm')) ? tinyConfig.dirTempHtm+t.data('id') : t.data('id');
                    }
                    t.click(function(){
                        var htmlContent = $scope.htmlContent ? {htmlContent: $scope.htmlContent} : {};
                        $rootScope.currentModal = $modal.open({
            				templateUrl: id,
            				size: t.data('size') || null,
            				backdrop: t.data('backdrop') || 'static',
                            animation: true,
                            controller: function($scope){
                                $scope = angular.extend($scope, fn, t.data(), htmlContent);
                            }
            			});
                    })
                }
            }
        })
        .directive('tinyCheckbox', function($ocLazyLoad, ASSETS){
            $ocLazyLoad.load([
    			ASSETS.forms.icheck
    		]);
            return {
                restrict: 'E',
                templateUrl: tinyConfig.templatePath('forms/icheck'),
                controller: function($scope, $element){
                    $scope.checkboxID = tn.randomUidd();
                    $scope.checkboxs = $element.attr('data');
                },
                link: function($scope){
                    tn.delayBeforeLoaded('.'+$scope.checkboxID, function(){
                        jQuery('.'+$scope.checkboxID).iCheck({
                    		checkboxClass: 'icheckbox_square-blue',
                            radioClass: 'iradio_square-yellow'
                    	});
                     }, 2, 'iCheck');
                }
            }
        })
        .directive('tinyRadio', function($ocLazyLoad, ASSETS){
            $ocLazyLoad.load([
    			ASSETS.forms.icheck
    		]);
            return {
                restrict: 'AC',
                link: function(scope, el){
                    tn.delayBeforeLoaded('iCheck', function(){
                        jQuery(el).iCheck({
                    		checkboxClass: 'icheckbox_square-blue',
                            radioClass: 'iradio_square-yellow'
                    	});
                     }, 1);
                }
            }
        })
        .directive('tinySelectbox', function($ocLazyLoad, $timeout, ASSETS){
            $ocLazyLoad.load([
				ASSETS.core.jQueryUI,
                ASSETS.forms.selectboxit
			]);
            return {
                restrict: 'AC',
                link: function(scope, el, attrs){
                    $timeout(function(){
                        tn.delayBeforeLoaded('selectBoxIt', function(){
                            jQuery(el).selectBoxIt().on('open', function()
    						{
    							// Adding Custom Scrollbar
    							$(this).data('selectBoxSelectBoxIt').list.perfectScrollbar();
    						});
                        }, 1);
                    }, 0)
                }
            }
        })
        .directive('tinySelect2', function($ocLazyLoad, $timeout, ASSETS){
            $ocLazyLoad.load([
				ASSETS.forms.select2
			]);
            return {
                restrict: 'AC',
                link: function(scope, el, attrs){
                    $timeout(function(){
                        tn.delayBeforeLoaded('select2', function(){
                            jQuery(el).select2({
								placeholder: 'Select your country...',
								allowClear: true
							}).on('select2-open', function()
							{
								// Adding Custom Scrollbar
								$(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();
							});
                        }, 1);
                    }, 0)
                }
            }
        })
        .directive('tinyPicker', function($ocLazyLoad, ASSETS){
            $ocLazyLoad.load([
				ASSETS.forms.datepicker
			]);
            return {
                restrict: 'AC',
                link: function(scope, el, attrs){
                    var t = jQuery(el);
                    tn.delayBeforeLoaded('datepicker', function(){
                        t.datepicker(t.data());
                        var $this = angular.element(el),
                            $n = $this.next(),
        					$p = $this.prev();
        
            				if($n.is('.input-group-addon') && $n.has('a'))
            				{
            					$n.on('click', function(ev)
            					{
            						ev.preventDefault();
            
            						$this.datepicker('show');
            					});
            				}
            
            				if($p.is('.input-group-addon') && $p.has('a'))
            				{
            					$p.on('click', function(ev)
            					{
            						ev.preventDefault();
            
            						$this.datepicker('show');
            					});
            				}
                    }, 1);
                }
            }
        })
        .directive('tinyCbr', function($timeout){
            return {
                scope: {
                    cbrModel: '=',
                    select: '&'
                },
    			restrict: 'AC',
    			link: function(scope, el, attrs){
    			 $timeout(function(){
                    var parent = scope.$parent.$parent; 
                    var $input = cbr_replace(el);
                    var $el    = angular.element(el);
                    
                    if(attrs.tinyModel)
                        angular.element($input).click(function(){
                            var t = $(this);
                            var model = attrs.tinyModel;
                            
                            if($el.is(':checked')){
                                if(parent[model]){
                                    parent[model].push($el.attr('value'));
                                }else{
                                    parent[model] = [$el.attr('value')];
                                }
                                if(scope.select){
                                    scope.select({input: tn.parseElement($el)})
                                }
                            }else
                                for(var i = 0; i < parent[model].length; i++){
                                    if(parent[model][i] == $el.attr('value')){
                                        parent[model].splice(i, 1);
                                    }
                                }
                                
                            parent.$apply();
                        })
                 }, 100);
                }
            }
        })
        .directive('tinySort', function(){
            return {
                restrict: 'AC',
                scope: {
                    onSave: '&'
                },
                link: function(scope, el, attrs){
                    if(!$.ui || !attrs.element) {
                        console.warn('Can\'t find element');
                        return false;
                    }
                    
                    var $this   = angular.element(el),
                        opts    = {
                            items: attrDefault($this, 'items', '> div'),
                            containment: attrDefault($this, 'containment', 'parent')
                        };
                    
                    $this.on('click', function(){
                        $el     = jQuery('#'+attrs.element);
                        if(attrs.cancelBtn){
                            jQuery('#'+attrs.cancelBtn).stop().slideDown(300).find('a').one('click', function(){
                                $(this).parents('#'+attrs.cancelBtn).stop().slideUp(300);
                                $el.sortable('destroy');
                                
                                var sdata = {};
                                $el.find(opts.items).each(function(){
                                    var t = $(this);
                                    sdata['sort_'+t.data('id')] = t.index();
                                })
                                scope.onSave({serilaze: sdata});
                            })
                        }
                        if($el.length)
                            $el.sortable(opts);
                    })
                }
            }
        })
        .directive('tinySelectall', function(){
            return {
                restrict: 'AC',
                link: function(scope, el, attrs){
                    var $el = angular.element(el);
                    
                    $el.on('change', function(){
                        var is_checked = $(this).is(':checked');
                            
                        $(attrs.target).prop('checked', is_checked).trigger('change');
                    })
                }
            }
        })
        .directive('logout', function(){
            return {
                restrict: 'AC',
                link: function(scope, el, attrs){
                    jQuery(el).click(function(){
                        jQuery.ajax({
                            url: URL_SERVER+'login/logout',
                            success: function(){
                                window.location.href = URL_SERVER+'login';
                            }
                        })
                    }).css('cursor', 'pointer');
                }
            }
        });