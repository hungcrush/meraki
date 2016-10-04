'use strict';
angular.module('yuyu.directive', [])
        .directive('tnSlider', function($timeout, $rootScope, $state){
            return {
                restrict: 'E',
                replace: true,
                templateUrl: tinyConfig.templatePath('layout/slider-banner'),
                scope: {
                    objSlider: '=',
                    responsive: '='
                },
                controller: function($scope, $element){
                    $scope.id = tn.randomUidd();
                    $element.attr('id', $scope.id);
                    
                    if($scope.responsive === undefined)
                        $scope.responsive = {};
                },
                link: function(scope, el, attr){
                    if(attr.image)
                        scope.image = true;
                    
                    $timeout(function(){
                        var t = $('#'+scope.id);
                        
                        var init = function(){
                            $rootScope.tinyDestroy.push({type: 'owlSlide', data: scope.id, state: Object.keys($state.$current.includes)});
                            var init = {
                                items: attr.items || 1,
                                dots: false,
                                nav:true,
                                center: attr.center || false,
                                margin: parseInt(attr.margin) || 0,
                                loop: attr.loop || false,
                                navText: attr.nav === undefined ? ['', ''] : ['<i class="ion-ios-arrow-left"></i>', '<i class="ion-ios-arrow-right"></i>'],
                                responsive: scope.responsive,
                                onInitialized: function(){
                                    
                                    //-- if slide is dots nav clone.
                                    if(attr.isnav){
                                        var isNav = $('['+attr.isnav+']'),
                                            sc    = t;
                                        
                                        isNav.on('changed.owl.carousel', function(){
                                            sc.find('.item.s').removeClass('s');
                                            var n = sc.find('.item').eq(isNav.data('owlCarousel')._current).addClass('s').parent('.owl-item');
                                            if(!n.hasClass('active')){
                                                if(!n.next().hasClass('active'))
                                                    n.parents('.owl-carousel').trigger('next.owl.carousel');
                                                else
                                                    n.parents('.owl-carousel').trigger('prev.owl.carousel');
                                            }
                                        })
                                        
                                        sc.find('.item:first').addClass('s');
                                        sc.find('.item').on('click', function(){
                                            var t = $(this);
                                            isNav.trigger('to.owl.carousel', [t.parent('.owl-item').index(), 300, true])  
                                        })
                                    }
                                    t.parent().css('opacity', 1);
                                    t.find('.owl-stage').css('width', t.find('.owl-stage').outerWidth() + 5);
                                }
                            };
                            
                            if(attr.hash){
                                init.URLhashListener = true;
                                init.startPosition = 'URLHash';
                            }
                            t.owlCarousel(init);
                            
                        }
                        if(!attr.scrollTo){
                            init();
                        }else{
                            if($('#'+scope.id).length){
                                var tOut;
                                var $div = $('#'+scope.id);
                                var initScroll = function(){
                                    clearTimeout(tOut);
                                    tOut = setTimeout(function(){
                                        if(tn.isScrolledIntoView($div.closest('.parentScroll'))){
                                            init();
                                            $(window).unbind('scroll', initScroll);
                                        }
                                    }, 200);
                                };
                                if(tn.isScrolledIntoView($div.closest('.parentScroll'))){
                                    setTimeout(function(){
                                        init();
                                    }, 500)
                                }
                                $(window).bind('scroll', initScroll);
                            }
                        }
                        
                        scope.$on(
        					"$destroy",
        					function handleDestroyEvent() {
        						console.log('Destroy Slider');
                                t.trigger('destroy.owl.carousel');
        					}
        				);
                    }, 100);
                }
            }
        })
        .directive('slideHome', function(){
            return {
                restrict: 'AC',
                link: function(scope, el, attr){
                    angular.element(el).slider();
                }
            }
        })
        .directive('tnTitle', function($rootScope){
            return {
                restrict: 'E',
    			replace: true,
                scope: true,
    			template: '<div class="title-page"><h1>{{title}}</h1><small>{{description}}</small></div>',
    			link: function(scope, el, attr){
    				scope.title = attr.title;
    				scope.description = attr.description;
                    setTimeout(function() {
                        scope.$destroy();
                        el.find('.ng-binding').removeClass('ng-binding');
                    }, 100);
                    if(attr.description)
                        $rootScope.currentPage.title = attr.title;
    			}
            }
        })
        .directive('productThumbnails', function(){
            return {
                restrict: 'AC',
                scope: true,
                link: function(scope, el, attrs){
                    var ProductThumbs = el.find('#ProductThumbs'),
                        ProductPhoto  = el.find('#ProductPhoto');

                    ProductThumbs.find('li > a').on('click', function(e){
                        e.preventDefault();
                        var href = $(this).attr('href');
                        ProductPhoto.css('background-image', 'url('+href+')');
                    })
                }
            }
        })
        .directive('tinySelectDropdown', function(){
            return {
                restrict: 'AC',
                link: function(scope, el, attrs){
                    el.tinyDropdown();
                }
            }
        })