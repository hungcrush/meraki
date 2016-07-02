<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Task extends TINY_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->load->model('Task_model', 'tasks');
    }
    
    public function index(){
        $this->data['template']  = 'admin/task/index.html';
        $this->data['dataParse'] = array(
            'title_page'    => 'Tasks'
        );
    }
}