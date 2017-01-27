<?php
/**
* Supply a user id and an access token
* Jelled explains how to obtain a user id and access token in the link provided
* @link http://jelled.com/instagram/access-token
*/
$userid = "merakistores";
$accessToken = "4738014d756048b8b7c7d1d3fdc5f02c";


$data = file_get_contents('https://www.instagram.com/'.$userid.'/media/');
$data = json_decode($data);

echo '<pre>';
print_r($data->items);