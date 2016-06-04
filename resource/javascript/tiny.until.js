$.fn.serializeObject = function(){
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};
//-- function GET like PHP Hungtv --//
$_GET = function(key,def){
    try{
        return RegExp('[?&;]'+key+'=([^?&#;]*)').exec(location.href)[1]
    }catch(e){
        return def || null
    }
}

/*************************/
//-- Common functions --/
/**********************/

var tn = {
    isInArray: function(value, array) {
        return array.indexOf(value) > -1;
    },
    strInArray: function(value, array){
        if(value === undefined || value === 'home') return false;
        var c = false;
        $.each(array, function(i, obj){
            if(obj.indexOf(value)){
                c = true;
                return false;
            }
        })
        return c;
    },
    calculateAspectRatioFit: function(srcWidth, srcHeight, maxWidth, maxHeight) {

		var ratio = Math.min(maxWidth / srcWidth, maxHeight / srcHeight);

		return { width: srcWidth*ratio, height: srcHeight*ratio };
	},
    parseElement: function(el){
        if(el.attr('access-tiny-id')) return el.attr('access-tiny-id');
        
        var id = this.randomUidd();
        el.attr('access-tiny-id', id);
        return id;
    },
    randomUidd: function(){
        function _p8(s) {
            var p = (Math.random().toString(16)+"000000000").substr(2,8);
            return s ? "-" + p.substr(0,4) + "-" + p.substr(4,4) : p ;
        }
        return _p8() + _p8(true) + _p8(true) + _p8();
    },
    loadJs: function(src, callback, innerHTML){
			try
			{
				var script = document.createElement('script');
				script.async = true;
				if (innerHTML)
				{
					try
					{
						script.innerHTML = innerHTML;
					}
					catch(e2) {}
				}
				var f = function()
				{
					if (callback)
					{
					   callback(); 
                       
					   callback = null;
					}
				};
				script.onload = f;
				script.onreadystatechange = function()
				{
					if (script.readyState === 'loaded')
					{
						f();
					}
				};
				script.src = src;
				document.getElementsByTagName('head')[0].appendChild(script);
			}
			catch(e) {}
    },
    showImagePreview: function( file, size ){
        var image = $( new Image() ).attr("draggable", "true").attr("tiny-drag", "true").prependTo('#image_content'), src = '';
		var preloader = new mOxie.Image();
        size = jQuery.extend({}, size, {w: 150, h: 150})
		preloader.onload = function() {
			preloader.downsize( size.w, size.h );
            src = preloader.getAsDataURL();
			image.prop( "src", src );
		};
		preloader.load( file.getSource() );
    },
    initLoading: function(size, fileid){
        size = jQuery.extend({}, size, {w: 100, h: 90})
        var src = tinyConfig.dirTemp+'/images/loading.gif',
            iid = fileid || this.randomUidd();
        return {
            img: jQuery('<img style="width: '+size.w+'px; height: '+size.h+'px;" src="'+src+'" id="'+iid+'" />'),
            iid: iid,
            src: src
        }
    },
    getURLUploaded: function(folder, filename){
        return {
            src: URL_SERVER+'uploads/'+folder+'/thumbs/'+filename,
            origin: URL_SERVER+'uploads/'+folder+'/full-size/'+filename,
            path: folder+'|'+filename
        }
    },
    delayBeforeLoaded: function(condition, callback, type, plugin){
        var time = 0, timeout, fn, c;
        
        fn = function(){
            if(time >= 3000) return false;
            timeout = setTimeout(function(){
                switch(type){
                    case 1: // fn jQuery
                        c = typeof jQuery.fn[condition] != 'undefined';
                        break;
                    case 2: // check element exist
                        c = !plugin ? $(condition).length : $(condition).length && typeof jQuery.fn[plugin] != 'undefined';
                        break;
                }
                if(c){
                    callback.call();
                    clearTimeout(timeout);
                }else{
                    fn();
                    console.log(c);
                }
            }, time);
            time += 100;
        }
        fn();
    },
    makeQueryString: function(object){
        var st = '';
        jQuery.each(object, function(index, value){
            if(index.match(/__/) && value !== undefined){
                st += '&'+index.replace(/__/, '')+'='+value;
            }
        })
        if(st != '')
            st = st.replace('&','?');
            
        return st;
    },
    makeURL: function(path, module){
        var href = module || window.location.href;
        
        if(href.slice('-1') == '/') 
            href = href+path;
        else
            href = href+'/'+path;
            
        return href;
    },
    capitalizeFirstLetter: function(string){
        return string.charAt(0).toUpperCase() + string.slice(1);
    },
    firstToUpperCase: function( str ) {
        return str.substr(0, 1).toUpperCase() + str.substr(1);
    },
    isScrolledIntoView: function(elem){
        var docViewTop = $(window).scrollTop();
        var docViewBottom = docViewTop + $(window).height();
        var elemTop = $(elem).offset().top;
        var elemBottom = elemTop + $(elem).height();
        return ((elemBottom >= docViewTop) && (elemTop <= docViewBottom) && (elemBottom <= docViewBottom) && (elemTop >= docViewTop));
    },
    escapeHtml: function(string){
        var entityMap = {
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "'",
            "'": '&#39;',
            "/": '&#x2F;'
          };
          
          return String(string).replace(/[&<>"'\/]/g, function (s) {
              return entityMap[s];
          });
    }
}