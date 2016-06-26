<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project extends TINY_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->load->model('Customer_model', 'customers');
    }
    
    public function index(){
        $this->data['template']  = 'admin/project/index.html';
        $this->data['dataParse'] = array(
            'title_page'    => 'Projects'
        );
    }
}