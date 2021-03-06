/*
- All plugin write for tiny project
- 10/01/2016 - Tran Vinh Hung <hungtranqt93@gmail.com>
- 
*/
;(function($, window, document, undefined){
	var _window 	= $(window),
		_document	= $(document);

	function tinyDropdown(element, options)
	{
		var default__ = {};

		this.el 		= $(element);
		this.options 	= $.extend({}, default__, options, this.el.data());

		// Input clone name send to form
		this.$input 	= $('<input type="hidden" />').attr('name', this.el.attr('name')).appendTo(this.el.parents('form'));

		// remove attr name
		this.el.removeAttr('name');

		this.events 	= {
			click: 'click.' + this.el.data('tinyDropdownId')
		}

		if(this.el.is('select'))
		{
			this.$el 	= this.el.clone();
			this.init();
		}
	}

	tinyDropdown.prototype = {
		template: '<div class="dropdown"><span class="old"></span><span class="carat"></span></div>',
		init: function(){
			this.$tpl 		= $(this.template);
			this.$selected	= this.$el.find('option[selected]').length ? this.$el.find('option[selected]') : this.$el.find('option:first');
			this.$el.appendTo(this.$tpl.find('.old'));

			this.$tpl.find('.old').after(this.renderFeature());
			this.$tpl.append(this.renderOptions(this.$selected));

			this.el.replaceWith(this.$tpl);

			this.$input.val(this.$selected.attr('value'));
		},
		renderOptions: function()
		{
			this.ul 		= $('<ul />');
			var _self 		= this;
			this.el.find('option').each(function(){
				var opt = $(this);
				_self.ul.append('<li data-value="'+opt.attr('value')+'" class="'+ ((opt.text() == _self.$selected.text()) ? "active" : "")+ '">' + opt.text() + '</li>');
			})

			_self.ul.find('li').on('click', $.context(this, 'onChange'));

			this.$div = $('<div />').append(_self.ul);

			return this.$div;
		},
		onChange: function(e){
			var t = $(e.target);
			this.ul.find('.active').removeClass('active');
			t.addClass('active');
			this.$tpl.find('.selected').text(t.text());

			this.onToggle(true);
			this.$input.val(t.text());
		},
		onToggle: function(_value){
			var _self = this;
			if(!_value)
				_self.$tpl.toggleClass('open');
			else
				_self.$tpl.removeClass('open');

			if(_self.$tpl.hasClass('open'))
			{
				_self.$div.css('height', _self.$div.find('ul > li').length * 40);
			}
			else
			{
				_self.$div.attr('style', null);
			}

			_document.on(_self.events.click, function(e){
				var $target = $(e.target);
				if(!$target.is(_self.$tpl) && !$target.parents('.dropdown').length)
				{
					_document.off(_self.events.click);
					_self.onToggle(true);
				}
			})
		},
		renderFeature: function(){
			var $el 	= $('<span class="selected">'+this.$selected.text()+'</span>'),
				_self	= this;

			$el.on('click', function(e){_self.onToggle()})

			return $el;
		}
	}

	$.fn.tinyDropdown = function(params)
	{
		var lists  	= this,
			retval	= this;

		lists.each(function()
		{
			var plugin = $.data(this, 'tinyDropdown');
			if (!plugin) {
				$.data(this, 'tinyDropdown', new tinyDropdown(this, params));
				$.data(this, 'tinyDropdown-id', new Date().getTime());
			} else {
				if (typeof params === 'string' && typeof plugin[params] === 'function') {
					retval = plugin[params]();
				}
			}
		})

		return retval || lists;
	}

	function tinyHeader(element, options)
	{
		var $el 	= $(element),
			event 	= 'scroll.' + $el.data('tinyHeaderId');
		_window.on(event, function(){
			console.log('AAAAA')
			if(_window.scrollTop() > 100)
			{
				$el.addClass('background');
			}
			else
			{
				$el.removeClass('background');
			}
		})

		$el.one('$destroy', function(){
			_window.off(event);
			$el.data('tinyHeader', null);
		})
	}

	$.fn.tinyHeader = function(params)
	{
		var lists  	= this,
			retval	= this;

		lists.each(function(){
			var plugin = $.data(this, 'tinyHeader');
			if (!plugin) {
				$.data(this, 'tinyHeader', new tinyHeader(this, params));
				$.data(this, 'tinyHeader-id', new Date().getTime());
			}
		})

		return retval || lists;
	}

	function tinyTabs(element, options)
	{
		this.$el = $(element);
		this.tabHeader 	= this.$el.find('> div:first');
		this.tabBody 	= this.$el.find('> div:last');

		this.init();
	}

	tinyTabs.prototype = {
		init: function() {
			var _self = this;

			_self.tabHeader.find('> .visisble').on('click', function(){
				var t = $(this);

				_self.tabHeader.find('.active').removeClass('active');
				_self.tabBody.find('.active').removeClass('active');

				t.addClass('active');
				_self.tabBody.find('> .'+ t.data('change')).addClass('active');
			})
		}
	}

	$.fn.tinyTabs = function(params)
	{
		var lists  	= this,
			retval	= this;

		lists.each(function(){
			var plugin = $.data(this, 'tinyTabs');
			if (!plugin) {
				$.data(this, 'tinyTabs', new tinyTabs(this, params));
				$.data(this, 'tinyTabs-id', new Date().getTime());
			}
		})

		return retval || lists;
	}

	/*
		Input placeholder to label
	*/

	function tinyInput(element, options)
	{
		this.$el = $(element);
		this.init();
	}

	tinyInput.prototype = {
		className: 'field--show-floating-label',
		init: function() {
			var _self = this, el = this.$el;

			el.find('input').not('[type="hidden"]')
			.on('keyup', function(e){
				var t = $(this);
				t.off('blur');
				if($.trim(t.val()) != '')
				{
					el.addClass(_self.className);
				}
				else
				{
					t.on('blur', function(){
						t.off('blur');
						el.removeClass(_self.className);
					})
				}
			})
		}
	}

	$.fn.tinyInput = function(params)
	{
		var lists  	= this,
			retval	= this;

		lists.each(function(){
			var plugin = $.data(this, 'tinyInput');
			if (!plugin) {
				$.data(this, 'tinyInput', new tinyInput(this, params));
				$.data(this, 'tinyInput-id', new Date().getTime());
			}
		})

		return retval || lists;
	}

	/*
		Our Story
		Scroll to element and add class animate-image animate
	*/
	function tinyAnimateScroll($element)
	{
		this.$el = $($element);

		this.classes = 'animate-image animate';
		this.elOffset = this.$el.offset().top - 650;

		var _self = this;
		_self.event = 'scroll.' + new Date().getTime() + this.$el.index();
		_self.init();
		
	}

	tinyAnimateScroll.prototype = {
		init: function() {
			var _self = this;
			_window.on(_self.event, function(){

				if(_window.scrollTop() >= _self.elOffset)
				{
					_self.$el.addClass(_self.classes);
					_window.off(_self.event);
				}
			})
		}
	}

	$.fn.tinyAnimateScroll = function(params)
	{
		var lists  	= this,
			retval	= this;

		lists.each(function(){
			var plugin = $.data(this, 'tinyAnimateScroll');
			if (!plugin) {
				$.data(this, 'tinyAnimateScroll', new tinyAnimateScroll(this, params));
				$.data(this, 'tinyAnimateScroll-id', new Date().getTime());
			}
		})

		return retval || lists;
	}

})(jQuery, window, document)