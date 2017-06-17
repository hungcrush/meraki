<?php
$json_test = json_encode(array('hung' => 'lanvi'));

if(!empty($json_test = json_decode($json_test, true)))
{
	print_r($json_test);
}