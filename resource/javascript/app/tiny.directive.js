'use strict';
angular.module('tinyfw.directive', [])
        .directive('tinySubmit', function($timeout){
            return {
                restrict: 'AC',
                scope: { onSubmit : '&tinySubmit' },
                link: function(scope, elem, attrs){
                    var $form  = $(elem), 
                        __vali = $form.hasClass('validate'),
                        __time = __vali ? 100 : 0;
                    $form.on('submit', function(){
                
                        setTimeout(function(){
                            if(__vali && $form.find('.form-group.validate-has-error').length){
                                    return false;
                            }
                            var dataForm = $form.serializeObject();
                            var r = scope.onSubmit({dataForm: dataForm});
                            
                            if(r !== false){
                                $form.find('[type="submit"]').prop('disabled', true);
                                // if(typeof attrs.noReset == 'undefined'){
                                //     $form.trigger('reset');
                                // }else
                                //     console.log('No Reset');
                            }
                        }, __time);
                    
                    });
                }
            }
        })
        .directive('tinyDrag', function($rootScope, $timeout){
            return {
                restrict: 'A',
                link: function(scope, el, attrs){
                    if(attrs.tinyDrag == 'true'){
                        angular.element(el).attr("draggable", "true");
                        el.bind('dragstart', function(){
                            if(el.hasClass('imgLoading')) return false;
                            $rootScope.el = el;
                            $rootScope.$emit('tinystart-drag');
                        })
                        el.bind("dragend", function (e){
                            //$rootScope.el = null;
                            $rootScope.$emit('tinyend-drag');
                        });
                    }else{
                        tn.delayBeforeLoaded('draggable', function(){
                            el.draggable({ containment: "parent", cursor: 'move' });
                        }, 1);
                    }
                },
                controller: function($scope){
                    $scope.$evalAsync( function() {
                        $rootScope.$emit('tinycomplete');
                    });
                    
                }
            }
        })
        .directive('tinyDragTarget', function($rootScope, $timeout){
            return {
                restrict: 'A',
                scope: {
    	            onDrop: '&'
    	        },
                link: function(scope, el, attrs){
                    el.bind("dragover", function (e) {
                        if (e.preventDefault) {
                            e.preventDefault(); // Necessary. Allows us to drop.
                        }
                        return false;
                    });
                    el.bind("dragenter", function (e) {
                        var t = el;
                        if(t.attr('tiny-drag-target'))
                            t.css('border','1px dashed #ccc');
                        
                    })
                    el.bind("dragleave", function(e){
                        var t = el;
                        if(t.attr('tiny-drag-target'))
                            t.css('border','none');
                    })
                    el.bind("drop", function(e){
                        var t = el;
                        if (e.preventDefault) {
                            e.preventDefault(); // Necessary. Allows us to drop.
                        }
                        if (e.stopPropogation) {
        	                e.stopPropogation(); // Necessary. Allows us to drop.
 	                    }
                        
                        if(t.attr('tiny-drag-target')){
                            if($rootScope.el != null){
                                scope.onDrop({xEl: tn.parseElement($rootScope.el), dEl: tn.parseElement(el), index: $rootScope.el.closest('div').index()});
                            }
                            t.css('border','1px dashed #ccc');
                            el.removeAttr('tiny-drag-target');
                        }
                        return false;
                    })
                    
                    $rootScope.$on('tinystart-drag', function(){
                        if(el.attr('tiny-drag-target'))
                            el.css('background','#F2F2F2'); 
                    });
                    
                    $rootScope.$on('tinyend-drag', function(){
                            el.css('background','#fff'); 
                    });
                }
            }
        })
        .directive('tinyShow', function(){
            return {
                restrict: 'A',
                link: function(scope, el, attrs){
                    scope.$watch(attrs.tinyShow, function ngShowWatchAction(value) {
                        if(value == 'block' || value.toString() == 'false'){
                            el.show();
                        }else{
                            el.hide();
                        }
                    })
                }   
            }
        })
        .directive('tinyAddmore', function($compile){
            return {
                restrict: 'A',
                link: function(scope, el, attrs){
                    var start = attrs.start ? parseInt(attrs.start) : 0;

                    if(el.is('input') && el.attr('type') != 'button')
                    {
                        el.bind("keydown keypress", function(event) {
                            var $target, $area, html;
                            if(event.which === 13) {
                                if (!event.shiftKey) {
                                    if(attrs.partner)
                                    {
                                        $target = $(attrs.partner);
                                    }
                                    else
                                    {
                                        $target = el.parent().prev();
                                    }

                                    $area = !attrs.area ? el.prev() : $(attrs.area);

                                    html = $compile(
                                        $target.html()
                                        .replace(new RegExp('{number}', 'g'), start)
                                        .replace(new RegExp('{text}', 'g'), el.val())
                                    )( scope );
                                    start++;

                                    $area.append(html);
                                    el.val('');
                                    
                                    event.preventDefault();
                                }
                            }
                        });
                    }
                    else
                    {
                        el.on('click', function(){
                            var $target, html;
                            if(attrs.partner)
                            {
                                $target = $(attrs.partner);
                            }
                            else
                            {
                                $target = el.parents(attrs.parent).prev();
                            }

                            html = $compile(
                                    $('<div />').html($target.clone()).html().replace(new RegExp('name="([^"]+)['+start+']', 'g'), function(match){
                                        return match.replace(new RegExp(start, 'g'), (start + 1));
                                    })
                                )( scope );
                            start++;

                            $target.after(html);
                        })
                    }
                }
            }
        })
        .directive('tinyUpload', function($timeout, $rootScope, $tiny){
            return {
                restrict: 'A',
                scope: {
                    elFileList: '=',
                    elProcess: '=',
                    onProcess: '&',
                    onAddfile: '&',
                    onUploaded: '&',
                    uploadInit: '&',
                    onRemove: '&'
                },
                link: function(scope, el, attrs){
                    if(attrs.id === undefined) //-- if element not have id
                        angular.element(el).attr("id", tn.randomUidd());
                        
                        var $main           = angular.element(el).parents('#main'),
                            $file_list      = null,
                            $image_target   = null;
                        var docReady;  
                        var checkSize       = function(image){
                            jQuery.each(attrs.init.files, function(i, obj){
                    		      var img = new o.Image(),
                                      blob = obj.getSource();
                                      img.onload = function(){
                                         if(this.width != image[0] || this.height != image[1]){
                                            attrs.init.files.splice(i, 1);
                                            attrs.init.removeFile(obj);
                                            $tiny.alert('Image size required is '+image[0]+'x'+image[1]);
                                            return false;
                                         }
                                         if(i == attrs.init.files.length - 1){
                                            docReady.resolve();
                                         } 
                                      }
                                      img.load(blob);
                              })
                        }
                        
                        var thumbSize       = tinyConfig.thumbSize;
                        if(attrs.thumbSize){
                            var thumb = attrs.thumbSize.split(',');
                            thumbSize = {
                                w: thumb[0],
                                h: thumb[1]
                            } 
                        }
                        var initUploader    = function(){
                            var init = {
                            	runtimes : 'html5,flash,silverlight,html4',
                            	browse_button : angular.element(el).attr("id"), // you can pass in id...
                                drop_element: $main.find('.tiny-drag-target').length ? 'tiny-drag-target' : angular.element(el).attr("id"),
                            	container: $main[0], // ... or DOM Element itself
                            	url : URL_SERVER+'upload.php',
                            	flash_swf_url : tinyConfig.dirTemp+'/plupload/Moxie.swf',
                            	silverlight_xap_url : tinyConfig.dirTemp+'/plupload/Moxie.xap',
                                chunk_size: '1mb',
                                multi_selection: attrs.multi || false,
                                multipart_params : {folder: attrs.folder || 'tiny', thumSize: JSON.stringify(thumbSize)},
                            	filters : {
                            		max_file_size : '10mb',
                            		mime_types: [
                            			{title : "Image files", extensions : "jpg,gif,png"}
                            		]
                            	},
                            
                            	init: {
                            		PostInit: function() {
                            			scope.uploadInit();
                            		},
                            
                            		FilesAdded: function(up, files) {
                            		  docReady = $.Deferred(); 
                                      if(attrs.sizeRequired){
                                		checkSize(attrs.sizeRequired.split(','));  
                                      }else
                                        docReady.resolve();
                                        
                                      $.when(docReady).then(function(){
                                		  if(attrs.onAddfile) 
                                                scope.onAddfile({up: JSON.stringify(up), files: files});
                                          else{
                                                if($main.find('[file_list]').length){
                                                    $file_list = $main.find('[file_list]');
                                                    plupload.each(files, function(file) {
                                        				$file_list.append('<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>');
                                        			});
                                                }
                                                if($main.find('[image_target]').length){
                                                    $image_target = $main.find('[image_target]');
                                                    plupload.each(files, function(file) {
                                                        $image_target.after('<div class="progress progress-striped">\
                                							<div id="p_'+file.id+'" class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">\
                                								<span class="sr-only">40% Complete (success)</span>\
                                							</div>\
                                						</div>');
                                                    });
                                                }

                                                //-- 27/10/2016 : When lazy...
                                                console.log('LAzy', $image_target, $file_list);
                                                if($image_target == null && $file_list == null)
                                                {
                                                    $main.append('<img class="tiny_image_target" />');
                                                    $image_target = $main.find('img.tiny_image_target');
                                                }
                                          }
                                          attrs.init.start();
                                      });
                            		},
                            
                            		UploadProgress: function(up, file) {
                            		  if(attrs.onProcess) 
                                            scope.onProcess({up: JSON.stringify(up), files: JSON.stringify(files)});
                                      else{
                                            var elementFile = jQuery('#'+file.id);
                                            if(!jQuery('#p_'+file.id).length){
                                                elementFile.after(
                                                  $('<div class="progress tinyProcess">\
                                                      <div id="p_'+file.id+'" class="determinate" style="width: '+ file.percent +'%"></div>\
                                                     </div>')
                                                  .css('width', elementFile.width())
                                                );
                                            }else
                                                jQuery('#p_'+file.id).width(file.percent+'%').attr('aria-valuenow', file.percent);
                                      }
                            		},
                                    
                                    FileUploaded: function(up, file, dataJson){
                                        var respon = angular.fromJson(dataJson.response);
                                        if(respon.error){
                                            alert('Error!');
                                            $timeout(function(){
                                                jQuery('#'+file.id).parent('div').remove();
                                            },50)
                                            return false;
                                        }
                                        
                                        jQuery('#p_'+file.id).parent('div').remove();
                                        
                                        if($image_target != null){
                                            var src = tn.getURLUploaded(respon.folder, respon.filename);
                                            $image_target.attr('src', attrs.notThumb ? src.origin : src.src);
                                        }
                                        
                                        $timeout(function(){
                                            if(attrs.onUploaded){
                                                scope.onUploaded({respon: dataJson.response, file: file});
                                            }else{
                                                var r = angular.fromJson(dataJson.response);
                                                if(attrs.inputId)
                                                {
                                                    $(attrs.inputId).val(r.folder+'|'+r.filename);
                                                }
                                                else if($main.find('[input_target]').length)
                                                {
                                                    $main.find('[input_target]').val(r.folder+'|'+r.filename);
                                                }
                                            }

                                            setTimeout(function(){
                                                //-- add delete button to image
                                                if(attrs.onRemove){
                                                    var el = angular.element('#'+file.id);
                                                    if(el.is('img') || el.is('a')){
                                                        el.addClass('imgLoading');
                                                        var img = new Image();
                                                        img.onload = function () {
                                                           el.removeClass('imgLoading');
                                                           var $i = $('<i class="mdi-navigation-close fa fa-times removeDraged"></i>').click(function(){
                                                                scope.onRemove({path: el.data('path'), id: el.attr('id')})
                                                           });
                                                           if(el.is('img'))
                                                                el.after($i);
                                                           else
                                                                el.append($i);
                                                        }
                                                        img.src = el.attr('origin-src');
                                                    }
        
                                                }
                                            }, 100)
                                        },500)
                                    },
                            
                            		Error: function(up, err) {
                            			document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
                            		}
                            	}
                            };
                            attrs.init = new plupload.Uploader(init);
                            attrs.init.init();
                            
                            scope.$on(
            					"$destroy",
            					function handleDestroyEvent() {
            						console.log('Destroy plupload');
                                    attrs.init.destroy();
            					}
            				);
                        }
                        
                        
                        //-- init upload
                        if(typeof plupload == 'undefined'){
                            tn.loadJs(tinyConfig.dirTemp+'/plupload/plupload.full.min.js', function(){
                                initUploader();
                            });
                        }else{
                            initUploader();
                        }
                }
            }
        })
        
        .directive('tinyEditor', function(){
            tn.loadJs(tinyConfig.dirTemp+'/tinymce/tinymce.min.js');
            return {
                restrict: 'AC',
                link: function(scope, element, attr){
                    var $el = angular.element(element);
                    if(!attr.id) {
                        console.warn('Textare don\'t have ID');
                        $el.attr('id', tn.randomUidd());
                        attr['timeout'] = 500;
                    }
                    
                    var init = function(){
                        tinymce.init({
                            selector: "#"+attr.id,
                            relative_urls : false,
                            remove_script_host : false,
                            convert_urls : true,
                            height: attr.height || 300,
                            setup : function(ed) {
                              
                            },
                            plugins: [
                                "advlist autolink lists link image preview",
                                "searchreplace visualblocks code fullscreen",
                                "insertdatetime media table contextmenu paste responsivefilemanager"
                            ],
                            toolbar: "responsivefilemanager undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
                            
                            external_filemanager_path: tinyConfig.dirTemp+"/tinymce/filemanager/",
                            filemanager_title:"Responsive Filemanager" ,
                            external_plugins: { "filemanager" : tinyConfig.dirTemp+"/tinymce/filemanager/plugin.min.js"}
                        });
                    }
                    //-- init upload
                    var timeout = attr.timeout || 100;
                    tn.delayBeforeLoaded('tinymce', function(){
                        setTimeout(function(){
                            init();
                        }, timeout)
                    }, 3);
                    
                    scope.$on(
    					"$destroy",
    					function handleDestroyEvent() {
    					   if(tinymce.get(attr.id) != null){
    					       console.log('Remove tinymce');
    					       tinymce.get(attr.id).remove();
    					   }                               
    					}
    				);
                }
            }
        })
        
        .directive('backPage', function($window){
            return {
                restrict: 'AC',
                link: function(scope, element, attr){
                    element.on('click', function() {
                         $window.history.back();
                    });
                }
            }
        })
        
        .directive('scrollToTop', function(){
            return {
                restrict: 'AC',
                link: function(scope, element, attr){
                    angular.element(element).on('click', function(){
                        jQuery('html, body').animate({ scrollTop: 0 }, 200);
                    })
                }
            }
        })
        
        .directive('repeatComplete', function($timeout){
            return {
                restrict: 'A',
                link: function (scope, element, attrs) {
                    if (scope.$last === true) {
                        $timeout(function () {
                            scope.$emit('ngRepeatFinished');
                        });

                        if(attrs.repeatComplete != '')
                        {
                            _window.trigger('ngRepeatFinished.' + attrs.repeatComplete);
                        }
                    }
                }
            }
        })
        
        .directive('tinyHtml', function($sce){
    		return {
    			restrict: 'A',
    			scope: {
    				htmlCode: '=tinyHtml'
    			},
    			link: function(scope, element, attributes){
    				angular.element(element).html(scope.htmlCode);
    			}
    		}
    	})
        
        .directive('mobileTinyVideo', function($window){
            return {
                restrict: 'AC',
                link: function(scope, el, attrs){
                    if($window.outerWidth < 560){
                        angular.element(el).addClass('video-container no-controls');
                    }
                }
            }
        })
        
        .directive('tinySelected', function(){
            return {
                restrict: 'A',
                scope: {
                    tinySelected: '=tinySelected'
                },
                link: function(scope, el, attrs){
                    setTimeout(function(){
                        angular.element(el).val(scope.tinySelected);
                    }, 100)
                }
            }
        })

        .directive('ngEnter', function() {
            return function(scope, element, attrs) {
                element.bind("keydown keypress", function(event) {
                    if(event.which === 13) {
                        if (!event.shiftKey) {
                            scope.$apply(function(){
                                    scope.$eval(attrs.ngEnter);
                            });
                        event.preventDefault();
                        }
                    }
                });
            };
        });