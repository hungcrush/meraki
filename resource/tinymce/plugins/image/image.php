<?php
require_once('config.php');
require_once('functions.php');

if(!defined('LIBRARY_FOLDER_PATH')){
	define('LIBRARY_FOLDER_PATH', 'uploads/');
}

if(!defined('LIBRARY_FOLDER_PATH')){
	$pageURL = 'http';
	if(isset($_SERVER["HTTPS"]) AND $_SERVER["HTTPS"] == "on"){
		$pageURL .= "s";
	}
	$pageURL .= "://";
	if($_SERVER["SERVER_PORT"] != "80"){
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	}else{
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	if(preg_match("/(.*)\/image\.php/",$pageURL,$matches)){
		define('LIBRARY_FOLDER_URL', $matches[1] . '/uploads/');
	}
}

$max_upload = (int)(ini_get('upload_max_filesize'));
$max_post = (int)(ini_get('post_max_size'));
$memory_limit = (int)(ini_get('memory_limit'));
$upload_mb = min($max_upload, $max_post, $memory_limit);

if(isset($_GET['src'])){
	$source = clean($_GET['src']);
}else{
	$source = "";
}

if(isset($_GET['title'])){
	$title = clean($_GET['title']);
}else{
	$title = "";
}

if(isset($_GET['alt'])){
	$alt = clean($_GET['alt']);
}else{
	$alt = "";
}

if(isset($_GET['width'])){
	$width = clean($_GET['width']);
}else{
	$width = "";
}

if(isset($_GET['height'])){
	$height = clean($_GET['height']);
}else{
	$height = "";
}

if(isset($_GET['align'])){
	$align = clean($_GET['align']);
}else{
	$align = "";
}


?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>TinyMCE 4 Image Manager</title>
		<link href="<?php echo $curent_url; ?>/admin/assets/css/bootstrap.css" rel="stylesheet" media="screen">
		<link href="<?php echo $curent_url; ?>/admin/assets/css/fonts/fontawesome/css/font-awesome.min.css" rel="stylesheet" media="screen">
		<link href="bootstrap/crop/cropper.css?v=5" rel="stylesheet">
		<script src="bootstrap/js/jquery.js"></script>
		<script src="<?php echo $curent_url; ?>/admin/assets/js/bootstrap.min.js"></script>
		
		<link href="bootstrap/blueimp/css/style.css" rel="stylesheet" />
		<script src="bootstrap/blueimp/js/jquery.ui.widget.js"></script>
		<script src="bootstrap/blueimp/js/jquery.iframe-transport.js"></script>
		<script src="bootstrap/blueimp/js/jquery.fileupload.js"></script>
		<script src="bootstrap/crop/cropper.js"></script>
		
<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
<script src="bootstrap/js/html5shiv.js"></script>
<![endif]-->
<style>
.library-item div.item{
	display: block;
	float: left;
	width: 130px;
	height: 130px;
	margin-bottom: 12px;
	margin-right: 27px;
}

.library-item div.item .name_file{
	width: 90px;
	display: block;
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
}

.transparent {
	zoom: 1;
	filter: alpha(opacity=50);
	opacity: 0.5;
}

.transparent:hover {
	zoom: 1;
	filter: alpha(opacity=90);
	opacity: 0.9;
}
			
.img-polaroid:hover{
	border-color: #0088cc;
	-webkit-box-shadow: 0 1px 4px rgba(0, 105, 214, 0.25);
	-moz-box-shadow: 0 1px 4px rgba(0, 105, 214, 0.25);
	box-shadow: 0 1px 4px rgba(0, 105, 214, 0.25);
}
			
#ajax-loader-div {
    height: 400px;
    position: relative;
}
.ajax-loader {
    position: absolute;
    left: 50%;
    top: 50%;
    margin-left: -16px; /* -1 * image width / 2 */
    margin-top: -16px;  /* -1 * image height / 2 */
    display: block;     
}
<?php
if(!CanDeleteFiles()){
?>
.delete-file{
	display: none; 
}
<?php
}
?>
<?php
if(!CanDeleteFolder()){
?>
.delete-folder{
	display: none; 
}
<?php
}
?>

<?php
if(!CanRenameFiles()){
?>
.change-file{
	display: none; 
}
<?php
}
?>
<?php
if(!CanRenameFolder()){
?>
.change-folder{
	display: none; 
}
<?php
}
?>


 .eg-container {
      padding-top: 15px;
      padding-bottom: 15px;
    }

    .eg-main {
      max-height: 480px;
      margin-bottom: 30px;
    }

    .eg-wrapper {
      background-color: #f7f7f7;
      border: 1px solid #eee;
      box-shadow: inset 0 0 3px #f7f7f7;
      height: 374px;
      width: 100%;
      overflow: hidden;
    }

    .eg-wrapper img {
      width: 100%;
    }

    .eg-preview {
      margin-bottom: 15px;
    }

    .preview {
      float: left;
      margin-right: 15px;
      margin-bottom: 15px;
      overflow: hidden;
      background: #f7f7f7;
    }

    .preview-lg {
      width: 290px;
      height: 160px;
    }

    .preview-md {
      width: 145px;
      height: 90px;
    }

    .preview-sm {
      width: 72.5px;
      height: 45px;
    }

    .preview-xs {
      width: 36.25px;
      height: 22.5px;
    }

    .eg-data {
      padding-right: 15px;
    }
	
	.eg-data button.t{
		margin-right: 2%;
		margin-bottom: 15px;
		width: 48%;
	}

    .eg-data .input-group {
      width: 100%;
      margin-bottom: 15px;
    }

    .eg-data .input-group-addon {
      min-width: 65px;
    }

    .eg-button > .btn {
      margin-right: 15px;
      margin-bottom: 15px;
    }

    .eg-input .input-group {
      margin-bottom: 15px;
    }

    .eg-output .btn {
      margin-right: 15px;
      margin-bottom: 15px;
    }

    .eg-output img {
      max-height: 214px;
    }
	.modal-dialog {
  width: 100%;
  height: 100%;
  border-radius: 0 !important;
  top: 0;
  margin: 0;
  padding: 0;
}

.modal-content {
  height: 100%;
  border-radius: 0;
}

.crop_btn{
	position: absolute;
	top: 5px;
	left: 20px;
	display: block;
}
.loading-img-preview{
	position: absolute;
	top: 50%;
	left: 40%;
	background: rgba(0, 0, 0, 0.42);
	padding: 5px 35px;
	margin: auto;
	color: #fff;
	display: none;
}
</style>		
<script>
$(document).ready(function(){
	
	var originalWidth, originalHeight, loaded = false;
	var _parent = window.parent.document;
	<?php
	if(isset($_GET['src']) AND trim($_GET['src']) != ""){
		 echo 'var newImage = false;
		 ';
	}else{
		 echo 'var newImage = true;
		 ';
	}
	
	?>
	if($('#preview').attr('src') == ''){
		$('#crop_').hide();
	}
	
	function MySerach(needle, haystack){
		var results = new Array();
		var counter = 0;
		var rgxp = new RegExp(needle, "g");
		var temp = new Array();
		for(i=0;i<haystack.length;i++){
			temp = haystack[i][1].match(rgxp)
			if(temp && temp.length > 0){
				results[counter] = haystack[i];
				counter = counter + 1;
			}
		}
		return results;
	}
	
	function getArray(object){
		var array = [];
		for(var key in object){
			var item = object[key];
			array[parseInt(key)] = (typeof(item) == "object")?getArray(item):item;
		}
		return array;
	}
	
	var search_haystack = new Array();
	
	$("#search").focus(function () {
		$("#lib-back").attr('disabled','disabled');
		$("#newfolder_name").attr('disabled','disabled');
		$("#newfolder_btn").attr('disabled','disabled');
		
		$("#refresh").attr("rel", "searching");
		
		$('#lib-title').empty();
		$('#lib-title').append('Searching... <a href="" id="clear-search">clear</a>');
		
		$.getJSON('search.php',{}, function(returned){ 
			search_haystack = getArray(returned);
		});
	});
	
	$(document).on('click', 'a#clear-search', function () {
		$('#lib-title').empty();
		$('#lib-title').append("Home");
		
		$("#newfolder_name").removeAttr("disabled", "disabled");
		$("#newfolder_btn").removeAttr("disabled", "disabled");
		
		$("#refresh").attr("rel", "<?php echo LIBRARY_FOLDER_PATH; ?>");
		
		$("#search").val("");
    			
    		$('#gallery-images').empty();
		$('#gallery-images').append('<div id="ajax-loader-div"><img src="bootstrap/img/ajax-loader.gif" alt="Loading..." class="ajax-loader"></div>');
		$.getJSON('lib.php' + '?dummy=' + new Date().getTime(),{}, function(returned){ 
			if(returned.success == 1){
				$('#gallery-images').empty();
				$('#gallery-images').append(returned.html);
			}else{
				$('#gallery-images').empty();
				$('#gallery-images').append('<center>No images in library.</center>');
			}
		});
		return false;
	});
	
	$("#search").keyup(function(event) {
    		if(this.value.length > 1){
    			
    			
    		$('#gallery-images').empty();
			$('#gallery-images').append('<div id="ajax-loader-div"><img src="bootstrap/img/ajax-loader.gif" alt="Loading..." class="ajax-loader"></div>');
			
			var results = MySerach(this.value, search_haystack);
			$('#gallery-images').empty();
			if(results.length > 0){
				for(i=0;i<results.length;i++){
					$('#gallery-images').append('<a href="" class="img-thumbs thumbnail pull-left" style="margin-left: 5px;" rel="' + results[i][0] + '"><img src="timthumb.php?src=' + results[i][0] + '&w=130&h=90" class="img-polaroid" width="130" height="90"></a>');
				}
			}else{
				$('#gallery-images').append('<center>No images match the search.</center>');
			}
    		}else if(this.value.length == 0){
    			$('#lib-title').empty();
			$('#lib-title').append("Home");
			
			$("#newfolder_name").removeAttr("disabled", "disabled");
			$("#newfolder_btn").removeAttr("disabled", "disabled");
			
			$("#refresh").attr("rel", "<?php echo LIBRARY_FOLDER_PATH; ?>");
    			
    		$('#gallery-images').empty();
			$('#gallery-images').append('<div id="ajax-loader-div"><img src="bootstrap/img/ajax-loader.gif" alt="Loading..." class="ajax-loader"></div>');
			$.getJSON('lib.php' + '?dummy=' + new Date().getTime(),{}, function(returned){ 
				if(returned.success == 1){
					$('#gallery-images').empty();
					$('#gallery-images').append(returned.html);
				}else{
					$('#gallery-images').empty();
					$('#gallery-images').append('<center>No images in library.</center>');
				}
			});
    		}
    	});
	
	$("#preview").on("load", function () {
		alert(1)
		if(newImage){
			if ($("#preview").get(0).naturalWidth) {
				$("#width").val($("#preview").get(0).naturalWidth);
				$("#height").val($("#preview").get(0).naturalHeight);
					
				originalWidth = $("#preview").get(0).naturalWidth;
				originalHeight = $("#preview").get(0).naturalHeight;
			} else if ($("#preview").attr("naturalWidth")) {
				$("#width").val($("#preview").attr("naturalWidth"));
				$("#height").val($("#preview").attr("naturalHeight"));
					
				originalWidth = $("#preview").attr("naturalWidth");
				originalHeight = $("#preview").attr("naturalHeight");
			}
		
			_parent.getElementById("width_").value= originalWidth;
			_parent.getElementById("height_").value= originalHeight;
		}else{
			newImage = true;
			if ($("#preview").get(0).naturalWidth) {
				originalWidth = $("#preview").get(0).naturalWidth;
				originalHeight = $("#preview").get(0).naturalHeight;
			} else if ($("#preview").attr("naturalWidth")) {
				originalWidth = $("#preview").attr("naturalWidth");
				originalHeight = $("#preview").attr("naturalHeight");
			}
		}
		$('#loading-img-preview').hide();
		$('#form_preview').css('opacity', '1').find(':input').not('#link_url').not('#target').prop('disabled', false);
		$('#crop_').show();
		if(_parent.getElementById("responsive").value == 1){
				$('#responsive').prop('checked', true);
				$('#width').val('').prop('disabled', true);
				$('#height').val('').prop('disabled', true);
		}
		if(_parent.getElementById("thumbnail").value == 1){
				$('#imgThumbnail').prop('checked', true);
		}
	});
	
	$(document).on('click', 'a.mi-close', function () {
		$(this).parent().hide();
		return false;
	});
	
	$(document).on('click', 'a.img-thumbs', function () {
		$("#preview").attr("src", "");
		$("#width").val();
		$("#height").val();
		$("#source").val($(this).attr("rel"));
        	$("#preview").attr("src", $(this).attr("rel") + '?dummy=' + new Date().getTime());
        	$('#myTab a[href="#tab1"]').tab('show');
        	_parent.getElementById("src").value= $(this).attr("rel");
        	$.post("update_recent.php" + "?dummy=" + new Date().getTime(), { src: $(this).attr("rel") } );
		$('#crop_').hide();
		$('#form_preview').css('opacity', '0.5').find(':input').prop('disabled', true);
		$('#loading-img-preview').show();
		return false;
	});
	
	$("#source").bind("change", function (e) {
		e.preventDefault();
		$('#crop_').hide();
		$('#loading-img-preview').show();
		$('#form_preview').css('opacity', '0.5').find(':input').prop('disabled', true);
		$.post("update_recent.php" + "?dummy=" + new Date().getTime(), { src: this.value } );
		$("#preview").attr("src", this.value + '?dummy=' + new Date().getTime());
		_parent.getElementById("src").value= this.value;
	});

	$('#source').bind('paste', function(){
		$('#loading-img-preview').show();
		$.post("update_recent.php" + "?dummy=" + new Date().getTime(), { src: this.value } );
		$("#preview").attr("src", this.value + '?dummy=' + new Date().getTime());
		setTimeout(function(){
				_parent.getElementById("src").value= $('#source').val();
		},300);
		_parent.getElementById("src").value= $('#source').val();
	})
	
	$("#alt").bind("change", function () {
		_parent.getElementById("alt").value= this.value;
	});
	
	$("#title").bind("change", function () {
		_parent.getElementById("title").value= this.value;
	});
	
	$('#responsive').bind('click', function(){
		if($(this).is(':checked')){
			$('#width').val('').prop('disabled', true);
			$('#height').val('').prop('disabled', true);
			_parent.getElementById("width_").value = '';
			_parent.getElementById("height_").value = '';
			_parent.getElementById("responsive").value = 1;
		}else{
			$('#width').val(originalWidth).prop('disabled', false);
			$('#height').val(originalHeight).prop('disabled', false);
			_parent.getElementById("width_").value = originalWidth;
			_parent.getElementById("height_").value = originalHeight;
			_parent.getElementById("responsive").value = 0;
		}
	});
	
	$('#imgThumbnail').bind('click', function(){
		if($(this).is(':checked')){
			_parent.getElementById("thumbnail").value = 1;
		}else{
			_parent.getElementById("thumbnail").value = 0;
		}
	});
	
	$("#width").keyup(function(event) {
    		_parent.getElementById("width_").value= this.value;
		if($('#constrain').is(':checked') && this.value != originalWidth){
			_parent.getElementById("height_").value= Math.round((this.value / originalWidth) * originalHeight);
			$("#height").val(Math.round((this.value / originalWidth) * originalHeight));
		}else if(this.value == originalWidth){
			_parent.getElementById("height_").value= originalHeight;
			$("#height").val(originalHeight);
		}
    	});
    	
    	$("#height").keyup(function(event) {
    		_parent.getElementById("height_").value= this.value;
		if($('#constrain').is(':checked') && this.value != originalHeight){
			_parent.getElementById("width_").value= Math.round((this.value / originalHeight) * originalWidth);
			$("#width").val(Math.round((this.value / originalHeight) * originalWidth));
		}else if(this.value == originalHeight){
			_parent.getElementById("width_").value= originalWidth;
			$("#width").val(originalWidth);
		}
    	});
    	
    	$("#width").bind("change", function () {
    		_parent.getElementById("width_").value= this.value;
		if($('#constrain').is(':checked') && this.value != originalWidth){
			_parent.getElementById("height_").value= Math.round((this.value / originalWidth) * originalHeight);
			$("#height").val(Math.round((this.value / originalWidth) * originalHeight));
		}else if(this.value == originalWidth){
			_parent.getElementById("height_").value= originalHeight;
			$("#height").val(originalHeight);
		}
    	});
    	
    	$("#height").bind("change", function () {
    		_parent.getElementById("height_").value= this.value;
		if($('#constrain').is(':checked') && this.value != originalHeight){
			_parent.getElementById("width_").value= Math.round((this.value / originalHeight) * originalWidth);
			$("#width").val(Math.round((this.value / originalHeight) * originalWidth));
		}else if(this.value == originalHeight){
			_parent.getElementById("width_").value= originalWidth;
			$("#width").val(originalWidth);
		}
    	});
    	
	$(".dimensions").keydown(function(event) {
		if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || 
			// Allow: Ctrl+A
			(event.keyCode == 65 && event.ctrlKey === true) || 
			// Allow: home, end, left, right
			(event.keyCode >= 35 && event.keyCode <= 39)) {
			// let it happen, don't do anything
			return;
		}else {
            // Ensure that it is a number and stop the keypress
			if ((event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
				event.preventDefault(); 
			} 
		}
    	});
    	
	$("#do_link").bind("change", function () {
		if($(this).is(':checked')){
			$("#link_url").removeAttr('disabled'); 
			$("#target").removeAttr('disabled'); 
		}else{
			$("#link_url").attr('disabled','disabled');
			_parent.getElementById("linkURL").value= "";
			
			$("#target").attr('disabled','disabled');
			_parent.getElementById("target").value= "";
		}
	});
	
	$("#link_url").bind("change", function () {
		_parent.getElementById("linkURL").value= this.value;
	});
	
	$("#target").bind("change", function () {
		_parent.getElementById("target").value= this.value;
	});
	
	$("#float").bind("change", function () {
		_parent.getElementById("align").value= this.value;
	});
	
	
	$("#get-recent").bind("click", function () {
		$('#recent-images').empty();
		$('#recent-images').append('<div id="ajax-loader-div"><img src="bootstrap/img/ajax-loader.gif" alt="Loading..." class="ajax-loader"></div>');
		$.getJSON('recent.php',{}, function(returned){ 
			if(returned.success == 1){
				$('#recent-images').empty();
				$('#recent-images').append(returned.html);
			}else{
				$('#recent-images').empty();
				$('#recent-images').append('<center>No recent images found.</center>');
			}
		});
	});
	
	$("#refresh").bind("click", function () {
		if($(this).attr("rel") == 'searching'){
			return false;
		}
		
		$('#gallery-images').empty();
		$('#gallery-images').append('<div id="ajax-loader-div"><img src="bootstrap/img/ajax-loader.gif" alt="Loading..." class="ajax-loader"></div>');
		$.getJSON('lib.php' + '?dummy=' + new Date().getTime(),{path: $(this).attr("rel")}, function(returned){ 
			if(returned.success == 1){
				$('#gallery-images').empty();
				$('#gallery-images').append(returned.html);
			}else{
				$('#gallery-images').empty();
				$('#gallery-images').append('<center>No images in the folder.</center>');
			}
		});
		
		
		
		return false;
	});
	
	$("#toggle-layout").bind("click", function () {
		if($(this).attr("rel") == 'searching'){
			return false;
		}
		
		$('#gallery-images').empty();
		$('#gallery-images').append('<div id="ajax-loader-div"><img src="bootstrap/img/ajax-loader.gif" alt="Loading..." class="ajax-loader"></div>');
		$.getJSON('lib.php' + '?dummy=' + new Date().getTime(),{path: $(this).attr("rel"), toggle: 1}, function(returned){ 
			if(returned.success == 1){
				$('#gallery-images').empty();
				$('#gallery-images').append(returned.html);
			}else{
				$('#gallery-images').empty();
				$('#gallery-images').append('<center>No images in the folder.</center>');
			}
		});
		
		
		
		return false;
	});
	
	$("#get-lib").bind("click", function () {
		if(loaded == false){
			$('#gallery-images').empty();
			$('#gallery-images').append('<div id="ajax-loader-div"><img src="bootstrap/img/ajax-loader.gif" alt="Loading..." class="ajax-loader"></div>');
			$.getJSON('lib.php' + '?dummy=' + new Date().getTime(),{}, function(returned){ 
				if(returned.success == 1){
					$('#gallery-images').empty();
					$('#gallery-images').append(returned.html);
				}else{
					$('#gallery-images').empty();
					$('#gallery-images').append('<center>No images in library.</center>');
				}
			});
			loaded = true;
		}
	});
	
	$(document).on('click', '#newfolder_btn', function () {
		if($('#newfolder_name').val() == ""){
			alert('Please provide a name for the new folder');
			return false;
		}
		
		$('#new-folder-msg').empty();
		$('#new-folder-msg').append('Creating...&nbsp;&nbsp;&nbsp;');
		
		$.getJSON('new_folder.php' + '?dummy=' + new Date().getTime(),{path: $("#refresh").attr("rel"), folder: $('#newfolder_name').val()}, function(returned){ 
			if(returned.success == 1){
				$('#newfolder_name').val("");
				$('#gallery-images').empty();
				$('#gallery-images').append(returned.html);
				$('#new-folder-msg').empty();
				$('#new-folder-msg').append('<span style="color: green;"><i class="fa fa-check"></i> Done...&nbsp;&nbsp;&nbsp;</span>');
				setTimeout(function(){ $('#new-folder-msg').empty() }, 3000);
			}else{
				$('#new-folder-msg').empty();
				$('#new-folder-msg').append('<span style="color: red;">Error...&nbsp;&nbsp;&nbsp;</span>');
				setTimeout(function(){ $('#new-folder-msg').empty() }, 3000);
				if(returned.msg != ""){
					alert(returned.msg);
				}
			}
		});
		
		
		
		return false;
	});
	
	$(document).on('click', 'a.delete-file', function () {
		var content = $(this).parent().parent().html();
		var thepa = $(this).parent().parent();
		var r=confirm("Are you sure you want to delete this file?");
		if(r==false){
			return false;
		}
		$(this).parent().parent().empty().append('<p>Deleting...</p>');
		$.getJSON('delete_file.php' + '?dummy=' + new Date().getTime(),{path: $("#refresh").attr("rel"),file: $(this).attr("rel")}, function(returned){ 
			if(returned.success == 1){
				$('#gallery-images').empty();
				$('#gallery-images').append(returned.html);
			}else{
				thepa.empty();
				thepa.html(content);
				if(returned.msg != ""){
					alert(returned.msg);
				}
			}
		});
		return false;
	});
	
	$(document).on('click', 'a.delete-folder', function () {
		var content = $(this).parent().parent().html();
		var thepa = $(this).parent().parent();
		var r=confirm("Are you sure you want to delete this folder and it's contents?");
		if(r==false){
			return false;
		}
		$(this).parent().parent().empty().append('<p>Deleting...</p>');
		$.getJSON('delete_folder.php' + '?dummy=' + new Date().getTime(),{path: $("#refresh").attr("rel"),folder: $(this).attr("rel")}, function(returned){ 
			if(returned.success == 1){
				$('#gallery-images').empty();
				$('#gallery-images').append(returned.html);
			}else{
				thepa.empty();
				thepa.html(content);
				if(returned.msg != ""){
					alert(returned.msg);
				}
			}
		});
		return false;
	});
	
	
	$(document).on('click', 'a.change-folder', function () {
		var current_value = $(this).attr("rel");
		var content = $(this).parent().parent().html();
		var thepa = $(this).parent().parent();
		var r=prompt("Please enter the new name",current_value);
		if(r==null || r==""){
			return false;
		}
		
		if(r==current_value){
			return false;
		}
		
		$(this).parent().parent().empty().append('<p>Saving...</p>');
		
		
		$.getJSON('rename_folder.php' + '?dummy=' + new Date().getTime(),{path: $("#refresh").attr("rel"),new_name: r,current_name: current_value}, function(returned){ 
			if(returned.success == 1){
				$('#gallery-images').empty();
				$('#gallery-images').append(returned.html);
			}else{
				thepa.empty();
				thepa.html(content);
				if(returned.msg != ""){
					alert(returned.msg);
				}
			}
		});
		return false;
	});
	
	function getExtension(filename) {
		return filename.split('.').pop().toLowerCase();
	}
	
	$(document).on('click', 'a.change-file', function () {
		var current_value = $(this).attr("rel");
		var content = $(this).parent().parent().html();
		var thepa = $(this).parent().parent();
		var extension = getExtension(current_value);
		var current_file_name = current_value.substr(0, current_value.lastIndexOf('.')) || current_value;
		
		var r=prompt("Please enter the new name",current_file_name);
		if(r==null || r==""){
			return false;
		}
		
		if((r + "." + extension) ==current_value){
			return false;
		}
		
		$(this).parent().parent().empty().append('<p>Saving...</p>');
		
		$.getJSON('rename_file.php' + '?dummy=' + new Date().getTime(),{path: $("#refresh").attr("rel"),new_name: (r + "." + extension),current_name: current_value}, function(returned){ 
			if(returned.success == 1){
				$('#gallery-images').empty();
				$('#gallery-images').append(returned.html);
			}else{
				thepa.empty();
				thepa.html(content);
				if(returned.msg != ""){
					alert(returned.msg);
				}
			}
		});
		return false;
	});
	
	$(document).on('click', '#refresh-dirs', function () {
		$('#select-dir-msg').empty();
		$('#select-dir-msg').append('Loading...&nbsp;&nbsp;&nbsp;');
		
		$.getJSON('refresh_dir_list.php' + '?dummy=' + new Date().getTime(),{}, function(returned){ 
			if(returned.success == 1){
				$('#select-dir-msg').empty();
				$('#select-dir-msg').append('<span style="color: green;"><i class="fa fa-check"></i> Done...&nbsp;&nbsp;&nbsp;</span>');
				setTimeout(function(){ $('#select-dir-msg').empty() }, 5000);
				$('#select-dir').empty();
				$('#select-dir').append(returned.html);
			}
		});
		return false;
	});
	
	$(document).on('change', '#select-dir', function () {
		loaded = true;
		$('#select-dir-msg').empty();
		$('#select-dir-msg').append('Sending...&nbsp;&nbsp;&nbsp;');
		
		var path = $(this).val();
		toDir(decodeURIComponent(path));
		$.getJSON('set_upload_directory.php' + '?dummy=' + new Date().getTime(),{path:path }, function(returned){ 
			if(returned.success == 1){
				$('#select-dir-msg').empty();
				$('#select-dir-msg').append('<span style="color: green;"><i class="fa fa-check"></i> Done...&nbsp;&nbsp;&nbsp;</span>');
				setTimeout(function(){ $('#select-dir-msg').empty() }, 5000);
			}
		});
		return false;
	});
	
	$(document).on('click', 'a.lib-folder', function () {
		var str =  decodeURIComponent($(this).attr("rel"));
			toDir(str);
		return false;
	});
	
	$(document).on('click', 'button#lib-back', function () {
		if($(this).is(":disabled")){
			return false;
		}
		
		if($(this).attr("rel") == '<?php echo LIBRARY_FOLDER_PATH; ?>'){
			$(this).attr('disabled','disabled');
		}
		
		$("#refresh").attr("rel", $(this).attr("rel"));
		
		$('#gallery-images').empty();
		$('#gallery-images').append('<div id="ajax-loader-div"><img src="bootstrap/img/ajax-loader.gif" alt="Loading..." class="ajax-loader"></div>');
		$.getJSON('lib.php' + '?dummy=' + new Date().getTime(),{path: $(this).attr("rel")}, function(returned){ 
			if(returned.success == 1){
				$('#gallery-images').empty();
				$('#gallery-images').append(returned.html);
			}else{
				$('#gallery-images').empty();
				$('#gallery-images').append('<center>No images in the folder.</center>');
			}
		});
		
		var str =  $(this).attr("rel");
		var stringArray = str.split("/");
		
		stringArray.pop();
		
		var current_folder = stringArray.pop();
		
		if((current_folder + "/") == '<?php echo LIBRARY_FOLDER_PATH; ?>'){
			current_folder = "Home";
			$(this).attr("rel", "<?php echo LIBRARY_FOLDER_PATH; ?>");
		}else{
			$(this).attr("rel", stringArray.join("/") + "/");
		}
		
		$('#lib-title').empty();
		$('#lib-title').append(current_folder);
		
		return false;
	});
	
	if($('#select-dir').find('option[selected]').length){
		$('#select-dir').trigger('change');
	}
});

	function toDir(dir){
		var stringArray = dir.split("/");
		stringArray.pop();
			
			
		var current_folder = stringArray[stringArray.length-1];
		if((current_folder + "/") == '<?php echo LIBRARY_FOLDER_PATH; ?>'){
			current_folder = "Home";
		}
		$('#lib-title').empty();
		$('#lib-title').append(current_folder);
		
		$("#refresh").attr("rel", encodeURIComponent(dir));
		
		if($("#lib-back").is(":disabled")){
			$("#lib-back").removeAttr('disabled'); 
			
		}else{
			stringArray.pop();
			
			$("#lib-back").attr('rel', stringArray.join("/") + "/");
			
			
			
		}
		$('#gallery-images').empty();
		$('#gallery-images').append('<div id="ajax-loader-div"><img src="bootstrap/img/ajax-loader.gif" alt="Loading..." class="ajax-loader"></div>');
		$.getJSON('lib.php' + '?dummy=' + new Date().getTime(),{path: encodeURIComponent(dir)}, function(returned){ 
			if(returned.success == 1){
				$('#gallery-images').empty();
				$('#gallery-images').append(returned.html);
			}else{
				$('#gallery-images').empty();
				$('#gallery-images').append('<center>No images in the folder.</center>');
			}
		});
	}
</script>
	</head>
	<body>
		<div class="container-fluid">
			<div class="row-fluid">
			
				<div class="span12" style="margin-top: 20px;">
					
					
					<div class="tabbable tabs-left">
						<ul class="nav nav-tabs" id="myTab">
							<li><a href="#tab1" data-toggle="tab"><i class="icon-globe"></i> Insert from URL</a></li>
							<?php if(CanAcessLibrary()){?>
							<li><a href="#tab2" data-toggle="tab" id="get-lib"><i class="icon-folder-open"></i> Get from Library</a></li>
							<?php }?>
							<?php if(CanAcessUploadForm()){?>
							<li><a href="#tab3" data-toggle="tab"><i class="icon-upload"></i> Upload Now</a></li>
							<?php }?>
							<li><a href="#tab4" data-toggle="tab" id="get-recent"><i class="icon-time"></i> Recent</a></li>
						</ul>
						<div class="tab-content">
							<div class="tab-pane" id="tab1">
								
<div class="row" style="padding-top: 25px;">
			
				<div class="col-sm-6">								
							<form id="form_preview" class="form-horizontal" action="" method="">
<p>
<input class="form-control" type="text" id="source" name="source" value="<?php echo $source;?>" placeholder="URL" title="URL">
</p>

<p>
<input class="form-control" type="text" id="title" name="title" value="<?php echo $title;?>" placeholder="Title" title="Title">
</p>

<p>
<input class="form-control" type="text" id="alt" name="alt" value="<?php echo $alt;?>" placeholder="Description" title="Description">
</p>
<br/>
<div class="clearfix">
<input style="width: 46%" type="text" id="width" name="width" class="input-small dimensions form-control pull-left" placeholder="Width" title="Width" value="<?php echo $width;?>"> <span style="font-size: 24px;width: 8%;text-align: center;float: left;">&times;</span> <input style="width: 46%" type="text" id="height" name="height" class="input-small dimensions form-control pull-right" placeholder="Height" title="Height" value="<?php echo $height;?>"> 
<div style="clear: both;padding-top: 5px;display: block;"></div><input type="checkbox" id="constrain" name="constrain" checked="checked"> Force original aspect ratio
<div style="clear: both;padding-top: 5px;display: block;"></div><input type="checkbox" id="responsive" name="responsive"> Image Responsive
<div style="clear: both;padding-top: 5px;display: block;"></div><input type="checkbox" id="imgThumbnail" name="imgThumbnail"> Image Thumbnail
</div>

<br/>
<p>
<select class="form-control" id="float" name="float">
<option value="">Alignment: None</option>
<option value="left" <?php echo ($align == 'left' ? 'selected="selected"' : '');?>>Left</option>
<option value="right" <?php echo ($align == 'right' ? 'selected="selected"' : '');?>>Right</option>
</select>
</p>

<?php if(!isset($_GET['src']) OR trim($_GET['src']) == ""){?>
<br/>
<p>
<input type="checkbox" id="do_link" name="do_link"> Wrap image in a link
</p>

<p>
<input class="form-control" type="text" id="link_url" name="link_url" disabled placeholder="Link URL" title="Link URL">
</p>

<p>
<select class="form-control" id="target" name="target" disabled>
<option value="_self">Target: None</option>
<option value="_blank">New window</option>
</select>
</p>

<?php }?>

</form>	
</div>
		<div class="col-sm-6" style="height: 70%; position: relative;">
			<button id="crop_" title="Crop Image" data-toggle="tooltip" data-placement="top" class="btn btn-success crop_btn"><i class="fa fa-crop"></i></button>
			<div id="loading-img-preview" class="loading-img-preview">Loading...</div>
			<img id="preview" class="img-responsive" src="<?php echo $source;?>" alt="Preview" />
		</div>
						<div style="clear: both;"></div>
						</div>
							</div>
							<div class="tab-pane" id="tab2">
								<div class="row" style="margin-top: 15px;">
									<div class="col-sm-2">
										<button class="btn" disabled id="lib-back" rel="<?php echo LIBRARY_FOLDER_PATH; ?>"><i class="icon-hand-left"></i> Back</button>&nbsp;&nbsp;&nbsp;<a href="" title="refresh" rel="<?php echo LIBRARY_FOLDER_PATH; ?>" id="refresh"><i class="icon-refresh"></i></a>
									</div>
									
									<div class="col-sm-3">
										
										<div class="input-group pull-right">
											<input class="input-medium form-control" type="text" class="input-medium" id="search" placeholder="Search">
											<span class="input-group-btn">
												<button class="btn" type="button"><i class="fa fa-search"></i></button>
											</span>
										</div>
									</div>
									<?php if(CanCreateFolders()){?>
									
									<div class="pull-right col-sm-4">
										
										<div class="input-group pull-right">
											<input class="input-medium form-control" id="newfolder_name" type="text" placeholder="Create folder here">
											<span class="input-group-btn">
												<button id="newfolder_btn" class="btn" type="button"><i class="icon-plus"></i></button>
											</span>
										</div>
									</div>
									<div style="padding: 0; margin-top: 5px;" class="col-sm-1 pull-right"><span id="new-folder-msg" class="pull-right"></span></div>
									<?php }?>
									<div style="clear: both;"></div>
								</div>
								<div class="row" style="margin-top: 15px;">
									<div class="col-sm-6"><p class="pull-left muted" id="lib-title">Home</p></div>
									
									<div class="col-sm-6">
										<p class="pull-right transparent"><a id="toggle-layout" href="" title="Toggle List/Grid Views"><i class="icon-th-list"></i></a></p>
									</div>
								</div>
								<div class="library-item" id="gallery-images"></div>
							</div>
							<div class="tab-pane" id="tab3">
<script>
$(function(){
	
    var ul = $('#upload ul');

    $('#drop a').click(function(){
        // Simulate a click on the file input button
        // to show the file browser dialog
        $(this).parent().find('input').click();
    });

    // Initialize the jQuery File Upload plugin
    $('#upload').fileupload({
	dataType: 'json',
	acceptFileTypes: /(\.|\/)(<?php echo implode("|", explode(",", ALLOWED_IMG_EXTENSIONS));?>)$/i,
        maxFileSize: <?php echo MBToBytes($upload_mb);?>,
	
        // This element will accept file drag/drop uploading
        dropZone: $('#drop'),

        // This function is called when a file is added to the queue;
        // either via the browse button, or via drag/drop:
        add: function (e, data) {

            var tpl = $('<li><div class="alert alert-info"><img class="loader" src="bootstrap/blueimp/img/ajax-loader.gif"> <a class="close" data-dismiss="alert">×</a></div></li>');

            // Append the file name and file size
           // Append the file name and file size
            tpl.find('div').append(data.files[0].name + ' <small>[<i>' + formatFileSize(data.files[0].size) + '</i>]</small>');

            // Add the HTML to the UL element
            data.context = tpl.appendTo(ul);

            // Automatically upload the file once it is added to the queue
            var jqXHR = data.submit();
        },
        
        done: function (e, data) {
            if(data.result.success == true){
        		data.context.remove();
        		$("#uploaded-images").append('<a style="margin: 9px; margin-right: 27px;" href="" class="img-thumbs" rel="' + data.result.file + '"><img src="timthumb.php?src=' + encodeURIComponent(data.result.file) + '&w=130&h=90" class="img-polaroid" width="130" height="90"></a>');
        	}else{
        		data.context.empty();
            		var tpl = $('<li><div class="alert alert-error"><a class="close" data-dismiss="alert">×</a></div></li>');
			tpl.find('div').append('<b>Error:</b> ' + data.files[0].name + ' <small>[<i>' + formatFileSize(data.files[0].size) + '</i>]</small> ' + data.result.reason);
			data.context.append(tpl);
        	}
        },
         fail: function (e, data) {
            data.context.empty();
            		var tpl = $('<li><div class="alert alert-error"><a class="close" data-dismiss="alert">×</a></div></li>');
			tpl.find('div').append('<b>Error:</b> ' + data.files[0].name + ' <small>[<i>' + formatFileSize(data.files[0].size) + '</i>]</small> ' + data.errorThrown);
			data.context.append(tpl);
        }
    });


    // Prevent the default action when a file is dropped on the window
    $(document).on('drop dragover', function (e) {
        e.preventDefault();
    });

    // Helper function that formats the file sizes
    function formatFileSize(bytes) {
        if (typeof bytes !== 'number') {
            return '';
        }

        if (bytes >= 1000000000) {
            return (bytes / 1000000000).toFixed(2) + ' GB';
        }

        if (bytes >= 1000000) {
            return (bytes / 1000000).toFixed(2) + ' MB';
        }

        return (bytes / 1000).toFixed(2) + ' KB';
    }

});
</script>
<div style="margin-top: 15px;" class="row">
<div class="col-sm-6">
	<p class="muted pull-left">Maximum upload file size: <?php echo $upload_mb;?> MB</p>
</div>
<div class="col-sm-6">
	<p style="margin-right: 8px;margin-left: 15px;margin-top: 7px;" class="pull-right"><a href="" title="refresh folders list" id="refresh-dirs"><i class="icon-refresh"></i></a></p>
	<p class="pull-right">
	<select id="select-dir" class="input-medium form-control">
		<?php echo Dirtree(LIBRARY_FOLDER_PATH);?>
	</select>
	</p>
	<span style="margin-top: 7px;" id="select-dir-msg" class="pull-right"></span>
</div>
<div class="clearfix"></div>
</div>
<form id="upload" method="post" action="upload.php" enctype="multipart/form-data">
			
			<div id="drop">
				

				<a class="btn">Click Or Drop</a>
				<input type="file" name="upl" multiple />
			</div>
			<br/>
			<ul id="upload-msg">
				<!-- The file uploads will be shown here -->
			</ul>

		</form>
<br/>
<div class="library-item" id="uploaded-images"></div>

							
							</div>
							
							<div class="tab-pane" id="tab4">
								<div style="margin-top: 15px;" class="library-item" id="recent-images"></div>
							</div>
						</div>
					</div> <!-- /tabbable -->
<script>
  var urlServer = '<?php echo $_SERVER['HTTP_HOST']; ?>';
	  urlServer = new RegExp(urlServer, 'gi');	
  $(function () {
	$('[data-toggle="tooltip"]').tooltip();
    $('#myTab a[href="#tab1"]').tab('show');
		var $image = $(".cropper"),
          //$dataX = $("#dataX"),
          //$dataY = $("#dataY"),
          //$dataHeight = $("#dataHeight"),
          //$dataWidth = $("#dataWidth"),
          console = window.console || {log:$.noop},
          cropper;

      $image.cropper({
        // autoCropArea: 1,
        data: {
          x: 420,
          y: 50,
          width: 640,
          height: 360
        },
        done: function(data) {
          //$dataX.val(data.x);
          //$dataY.val(data.y);
          //$dataHeight.val(data.height);
          //$dataWidth.val(data.width);
        },
      });

      cropper = $image.data("cropper");

      $("#reset").click(function() {
        $image.cropper("reset");
      });

      $("#clear").click(function() {
        $image.cropper("clear");
      });


      $("#zoom").click(function() {
        $image.cropper("zoom", $("#zoomWith").val());
      });

      $("#zoomIn").click(function() {
        $image.cropper("zoom", 0.1);
      });

      $("#zoomOut").click(function() {
        $image.cropper("zoom", -0.1);
      });

      $("#rotate").click(function() {
        $image.cropper("rotate", $("#rotateWith").val());
      });

      $("#rotateLeft").click(function() {
        $image.cropper("rotate", -90);
      });

      $("#rotateRight").click(function() {
        $image.cropper("rotate", 90);
      });

      $("#setAspectRatio").click(function() {
			$image.cropper("setAspectRatio", $("#aspectRatio").val());
      });

      $("#replace").click(function() {
        $image.cropper("replace", $("#replaceWith").val());
      });
	  
	  $('#crop_').click(function(){
		var _this = $(this);
		var src = $("#preview").attr('src');
		if(!src.match(urlServer)){
			$.ajax({
				url: 'curl.php',
				type: 'POST',
				data: {url: src},
				beforeSend: function(){
					_this.html('<i class="fa fa-spinner fa-spin"></i>');
				},
				success: function(data){
					$image.cropper("replace", data);
					var img = new Image();
					img.onload = function(){
						_this.html('<i class="fa fa-crop"></i>');
						toDir('<?php echo LIBRARY_FOLDER_PATH . 'download/'; ?>');
						loaded = true;
						$('#modal_crop').modal('show');
						$('#source').val(data).trigger('change');
					};
					img.src = data;
				}
			});
		}else{
			$image.cropper("replace", $("#preview").attr('src'));
			$('#modal_crop').modal('show');
		}
	  });

      $("#getDataURL").click(function() {
		var _this = $(this);
		_this.text('Saving...').prop('disabled', true);
		
		var dataIMG = JSON.stringify($image.cropper("getData"));
        $.ajax({
			url: 'crop-image.php',
			data:{
				img: $("#preview").attr('src'),
				img_data: dataIMG,
				folder: $('#lib-title').text()
			},
			type: 'POST',
			dataType: 'json',
			success: function(data){
				if(data.message != null){
					alert(data.message);
				}else{
					$('#source').val($("#preview").attr('src')).trigger('change');
				}
				$('#modal_crop').modal('hide');
				_this.text('Save').prop('disabled', false);
			}
		});
        //$("#showDataURL").html('<img src="' + dataURL + '">');
      });
  });
</script>					
				</div>
			</div>
		</div> <!-- /container -->	
		<div id="modal_crop" class="modal fade">
		  <div class="modal-dialog">
			<div class="modal-content">
			  <div class="modal-body">
				<div class="container-fluid eg-container" id="basic-example">
					<div class="row eg-main">
					  <div class="col-xs-12 col-sm-7">
						<div class="eg-wrapper">
						  <img style="width: 100%;" class="cropper" src="" alt="Picture">
						</div>
					  </div>
					  <div class="col-xs-12 col-sm-5">
						<div class="eg-data">
						  <button class="t btn btn-warning pull-left" id="reset" type="button"><i class="fa fa-refresh"></i> Reset</button>
							<button class="t btn btn-primary pull-left" id="clear" type="button"><i class="fa fa-times"></i> Clear</button>
							<div class="clearfix"></div>
							<button class="t btn btn-info pull-left" id="zoomIn" type="button"><i class="fa fa-search-plus"></i> Zoom In</button>
							<button class="t btn btn-info pull-left" id="zoomOut" type="button"><i class="fa fa-search-minus"></i> Zoom Out</button>

							  <div class="input-group">
								<span class="input-group-btn">
								  <button class="btn btn-primary" id="setAspectRatio" type="button">Set Aspect Ratio</button>
								</span>
								<input class="form-control" id="aspectRatio" type="text" value="auto">
							  </div>
							 <hr /> 
							 <div class="clearfix">
							 <button id="getDataURL" class="btn btn-danger">Save</button>
							 <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
							 </div>
						</div>
					  </div>
					</div>
			  </div>
			</div><!-- /.modal-content -->
		  </div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
	</body>
</html>
