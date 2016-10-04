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
		},
		renderOptions: function()
		{
			var ul 		= $('<ul />'),
				_self	= this;;
			this.el.find('option').each(function(){
				var opt = $(this);
				ul.append('<li class="'+ ((opt.text() == _self.$selected.text()) ? "active" : "")+ '">' + opt.text() + '</li>');
			})

			ul.find('li').on('click', function(e){
				var t = $(this);
				ul.find('.active').removeClass('active');
				t.addClass('active');
				_self.$tpl.find('.selected').text(t.text());

				_self.onToggle(true);
			})

			this.$div = $('<div />').append(ul);

			return this.$div;
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
	}
})(jQuery, window, document)