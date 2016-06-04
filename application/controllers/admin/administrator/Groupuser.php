<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Groupuser extends TINY_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->load->model('administrator/User_group_model', 'group');
        $this->permission += array(
            'index' => '-/userGroup'
        );
    }
    
    public function index(){
        $this->data['template'] = 'admin/users/user-group.htm';
        $this->data['dataParse'] = array(
            'title_page'    => 'User Groups',
            'description'   => 'Manage all User Groups'
        );
    }
    
    public function Load(){
        return $this->group->Load();
    }
    
    public function add(){
        return $this->group->Save();
    }
    
    public function Remove(){
        return $this->group->Remove();
    }

}