<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Post extends TINY_Controller {
    
    public function __construct(){
        parent::__construct();
    }
    
    public function index(){
        $this->data['template']  = 'admin/AddPost.htm';
    }
}