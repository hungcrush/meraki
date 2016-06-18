<?php

function debug($data, $exit = TRUE){
	echo '<pre>';
	print_r($data);
	echo '</pre>';
	if($exit) die();
}