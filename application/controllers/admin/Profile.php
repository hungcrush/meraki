<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends TINY_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->load->model('User_model', 'users');
    }
    
    public function index(){
        $this->data['template']  = 'admin/profile/timeline.html';
        $this->data['dataParse'] = array(
            'title_page'    => 'Profile'
        );
    }

    
}