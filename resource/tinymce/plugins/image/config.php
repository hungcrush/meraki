<?php

if(!isset($_SESSION)) 
{ 
	session_start(); 
}
header("Access-Control-Allow-Origin: *");
/** Full path to the folder that images will be used as library and upload. Include trailing slash */
define('LIBRARY_FOLDER_PATH', 'uploads/');

$base_url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
$base_url .= "://".(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
$base_url .= isset($_SERVER['SCRIPT_NAME']) ? str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']) : '';

$curent_url = str_replace('/tinymce/plugins/image/', '', $base_url);
/** Full URL to the folder that images will be used as library and upload. Include trailing slash and protocol (i.e. http://) */
define('LIBRARY_FOLDER_URL', $base_url.'uploads/');

/** The extensions for to use in validation */
define('ALLOWED_IMG_EXTENSIONS', 'gif,jpg,jpeg,png,jpe');

/**  Use these 3 functions to check cookies and sessions for permission. 
Simply write your code and return true or false */


function CanAcessLibrary(){
	return true;
}

function CanAcessUploadForm(){
	return true;
}

function CanAcessAllRecent(){
	return true;
}

function CanCreateFolders(){
	return true;
}

function CanDeleteFiles(){
	return true;
}

function CanDeleteFolder(){
	return true;
}

function CanRenameFiles(){
	return true;
}

function CanRenameFolder(){
	return true;
}
