<?php
	// requires php5
	require_once('config.php');
	
	function getExt($url){
		$url = end(explode('/', $url));
		$url = explode('.', $url);
		return array(
			'file_ext' 	=> strtok(end($url),'?'),
			'file_name'	=> str_replace('-'.end($url), '', implode('-', $url))
		);
	}
	$img = $_POST['img'];
	$path = $_POST['src'];
	$folder = isset($_POST['folder']) && $_POST['folder'] != 'Home' ? $_POST['folder'].'/' : '';
	$FileName = getExt($path);
	
	$img = str_replace('data:image/png;base64,', '', $img);
	$img = str_replace(' ', '+', $img);
	$data = base64_decode($img);
	$file = LIBRARY_FOLDER_PATH . $folder . $FileName['file_name'] .'.'. $FileName['file_ext'];
	$success = file_put_contents($file, $data);
	$file = $base_url.$file;
	print $success ? $file : 'Unable to save the file.';
?>