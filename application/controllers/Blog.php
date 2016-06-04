<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Blog extends TINY_Controller {
    
    public function __construct(){
        parent::__construct();
    }
    
    public function index(){
        $this->data['template']  = 'templates/blog/all-post.htm';
        $this->data['dataParse'] = array(
            'title_page'    => 'Blog',
            'description'   => 'Create your own photo books, prints and gifts with our ease-to-use online designer.'
        );
    }
    
    public function Detail($id = 0){
        $this->data['template']  = 'templates/blog/post.htm';
        $this->data['dataParse'] = array(
            'title_page'    => 'Blog',
            'description'   => 'Create your own photo books, prints and gifts with our ease-to-use online designer.'
        );
    }
}