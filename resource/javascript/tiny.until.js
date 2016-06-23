$.fn.serializeObject = function(){

    var self = this,
        json = {},
        push_counters = {},
        patterns = {
            "validate": /^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
            "key":      /[a-zA-Z0-9_]+|(?=\[\])/g,
            "push":     /^$/,
            "fixed":    /^\d+$/,
            "named":    /^[a-zA-Z0-9_]+$/
        };


    this.build = function(base, key, value){
        base[key] = value;
        return base;
    };

    this.push_counter = function(key){
        if(push_counters[key] === undefined){
            push_counters[key] = 0;
        }
        return push_counters[key]++;
    };

    $.each($(this).serializeArray(), function(){

        // skip invalid keys
        if(!patterns.validate.test(this.name)){
            return;
        }

        var k,
            keys = this.name.match(patterns.key),
            merge = this.value,
            reverse_key = this.name;

        while((k = keys.pop()) !== undefined){

            // adjust reverse_key
            reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');

            // push
            if(k.match(patterns.push)){
                merge = self.build([], self.push_counter(reverse_key), merge);
            }

            // fixed
            else if(k.match(patterns.fixed)){
                merge = self.build([], k, merge);
            }

            // named
            else if(k.match(patterns.named)){
                merge = self.build({}, k, merge);
            }
        }

        var _k = Object.keys(merge)[0],
            _r;
        var _bk;
        if(typeof merge[_k] == 'object' && typeof json[_k] === 'object')
        {
            _r = Object.keys(merge[_k])[0];
            
            if(typeof json[_k][_r] !== 'undefined')
            {
                console.log(json[_k]);
                _bk = json[_k][_r];
                json[_k] = [];
                json[_k].push(_bk);
                json[_k].push([]);
                merge = {};
            }
        }
        json = $.extend(true, json, merge);
    });

    return json;
};

//-- function GET like PHP Hungtv --//
$_GET = function(key,def){
    try{
        return RegExp('[?&;]'+key+'=([^?&#;]*)').exec(location.href)[1]
    }catch(e){
        return def || null
    }
}

/*
    COOKIE METHODS
*/

var tinyCookies = {  
  getItem: function (sKey) {  
    if (!sKey || !this.hasItem(sKey)) { return null; }  
    return unescape(document.cookie.replace(new RegExp("(?:^|.*;\\s*)" + escape(sKey).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=\\s*((?:[^;](?!;))*[^;]?).*"), "$1"));  
  },  
  /** 
  * docCookies.setItem(sKey, sValue, vEnd, sPath, sDomain, bSecure) 
  * 
  * @argument sKey (String): the name of the cookie; 
  * @argument sValue (String): the value of the cookie; 
  * @optional argument vEnd (Number, String, Date Object or null): the max-age in seconds (e.g., 31536e3 for a year) or the 
  *  expires date in GMTString format or in Date Object format; if not specified it will expire at the end of session;  
  * @optional argument sPath (String or null): e.g., "/", "/mydir"; if not specified, defaults to the current path of the current document location; 
  * @optional argument sDomain (String or null): e.g., "example.com", ".example.com" (includes all subdomains) or "subdomain.example.com"; if not 
  * specified, defaults to the host portion of the current document location; 
  * @optional argument bSecure (Boolean or null): cookie will be transmitted only over secure protocol as https; 
  * @return undefined; 
  **/  
  setItem: function (sKey, sValue, vEnd, sPath, sDomain, bSecure) {  
    if (!sKey || /^(?:expires|max\-age|path|domain|secure)$/.test(sKey)) { return; }  
    var sExpires = "";  
    if (vEnd) {  
      switch (typeof vEnd) {  
        case "number": sExpires = "; max-age=" + vEnd; break;  
        case "string": sExpires = "; expires=" + vEnd; break;  
        case "object": if (vEnd.hasOwnProperty("toGMTString")) { sExpires = "; expires=" + vEnd.toGMTString(); } break;  
      }  
    }  
    document.cookie = escape(sKey) + "=" + escape(sValue) + sExpires + (sDomain ? "; domain=" + sDomain : "") + (sPath ? "; path=" + sPath : "") + (bSecure ? "; secure" : "");  
  },  
  removeItem: function (sKey) {  
    if (!sKey || !this.hasItem(sKey)) { return; }  
    var oExpDate = new Date();  
    oExpDate.setDate(oExpDate.getDate() - 1);  
    document.cookie = escape(sKey) + "=; expires=" + oExpDate.toGMTString() + "; path=/";  
  },  
  hasItem: function (sKey) { return (new RegExp("(?:^|;\\s*)" + escape(sKey).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=")).test(document.cookie); }  
}; 

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