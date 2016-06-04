'use strict';

angular.module('xenon.services', []).
	service('$menuItems', function()
	{
		this.menuItems = [];

		var $menuItemsRef = this;
        var $i__ = null;
        
        this.avalibleMenu = [];

		var menuItemObj = {
			parent: null,

			title: '',
			link: '', // starting with "./" will refer to parent link concatenation
			state: '', // will be generated from link automatically where "/" (forward slashes) are replaced with "."
			icon: '',

			isActive: false,
			label: null,

			menuItems: [],

			setLabel: function(label, color, hideWhenCollapsed)
			{
				if(typeof hideWhenCollapsed == 'undefined')
					hideWhenCollapsed = true;

				this.label = {
					text: label,
					classname: color,
					collapsedHide: hideWhenCollapsed
				};

				return this;
			},

			addItem: function(title, link, icon)
			{
				var parent = this,
					item = angular.extend(angular.copy(menuItemObj), {
						parent: parent,

						title: title,
						link: link,
						icon: icon
					});
                    
				if(item.link)
				{
					item.link = parent.link + '/' + item.link.substring(2, link.length);

					item.state = $menuItemsRef.toStatePath(item.link);
                    
                    $menuItemsRef.avalibleMenu.push(item.state);
				}
				this.menuItems.push(item);

				return item;
			}
		};

		this.addItem = function(title, link, icon)
		{
		    var state = this.toStatePath(link);
            
			var item = angular.extend(angular.copy(menuItemObj), {
				title: title,
				link: URL_SERVER+link,
				state: state,
				icon: icon
			});
            
            this.avalibleMenu.push(state);
                
			this.menuItems.push(item);

			return item;
		};

		this.getAll = function()
		{
			return this.menuItems;
		};

        this.prepareSidebarMenu = function(menuItems){
            var menu = [],
	           _self = this;
               
            var loop = function(menuItems, c){
            	jQuery.each(menuItems, function(i, obj){
            		c++;
                    menu[c] = _self.addItem(obj.title, obj.link, obj.icon);
            		if(obj.child != null)
            			loop_child(obj.child, c);
            	})
            }
            var loop_child = function(menuItems, c){
            	jQuery.each(menuItems, function(i, obj){
            	    var cc = c + 1;
            		menu[cc] = menu[c].addItem(obj.title, obj.link, obj.icon);
            		if(obj.child != null)
            			loop_child(obj.child, cc);
            	})
            }
            
            loop(menuItems, 0);
            return this;
        }

		this.instantiate = function()
		{
			return angular.copy( this );
		}

		this.toStatePath = function(path)
		{
            path = path.replace(new RegExp(URL_SERVER), '');
			return path.replace(/\//g, '.').replace(/^\./, '');
		};

		this.setActive = function(path)
		{
		    $i__ = null;  
		    path = path.replace(new RegExp(PATH_), '');
            
            var doState = this.toStatePath(path),
                avalibleMenu = $menuItemsRef.avalibleMenu + this.avalibleMenu,
                s, n;
            
            if(!tn.isInArray(doState, avalibleMenu)){
                if(doState.slice('-1') == '.')
                doState = doState.substring(1, doState.length-1);
                
                console.log('start find parent of menu');
                while(!tn.isInArray(doState, avalibleMenu) || !tn.isInArray(doState+'.', avalibleMenu)){
                     console.log(doState);   
                     if(doState.indexOf('.') === -1) break;
                     n = doState.lastIndexOf(".");
                     doState = doState.substr(0, n);
                }
            }
            this.iterateCheck(this.menuItems, doState);
            
            
			return $i__;
		};

		this.setActiveParent = function(item)
		{
			item.isActive = true;
			item.isOpen = true;

			if(item.parent)
				this.setActiveParent(item.parent);
		};

		this.iterateCheck = function(menuItems, currentState)
		{
			angular.forEach(menuItems, function(item)
			{
				if(item.state == currentState || item.state == currentState+'.')
				{
					item.isActive = true;

					if(item.parent != null)
						$menuItemsRef.setActiveParent(item.parent);
                        
                    $i__ = item;
				}
				else
				{
					item.isActive = false;
					item.isOpen = false;

					if(item.menuItems.length)
					{
						$menuItemsRef.iterateCheck(item.menuItems, currentState);
					}
				}
			});
		}
	});