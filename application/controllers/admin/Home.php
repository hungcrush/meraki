<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends TINY_Controller {
    
    public function __construct(){
        parent::__construct();
    }
    
    public function index(){
        $this->data['template']  = 'admin/adminPage.htm';
        $this->data['dataParse'] = array(
            'title_page'    => 'Admin Home',
            'yutest'        => '99.9'
        );
    }
    
    public function DashBoard(){
        $this->data['template']  = 'admin/adminPage.htm';
    }
    
    public function checkSession(){
        //die(json_encode($this->data));
        
    }
}