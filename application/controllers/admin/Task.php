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

    public function add(){
        $this->data['template']  = 'admin/task/add.html';
        $this->data['dataParse'] = array(
            'title_page'    => 'Add Tasks'
        );
    }

    public function save(){
        $dataForm = $this->_post('tasks');

        foreach($dataForm as $data)
        {
            $insert = $this->tasks->insert_auto($data, array('todos' => array()), 'tiny_tasks');
        }
        return $insert;
    }
}