<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Peoples extends TINY_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->load->model('User_model', 'users');
    }
    
    public function index(){
        $this->data['template'] = 'admin/users/list_users.htm';
        $this->data['dataParse'] = array(
            'title_page'    => 'Members List',
            'description'   => 'Members management page separated with tabs.'
        );
    }
    
    public function Load(){
        return $this->users->Load();
    }
    
    public function Edit($id = 0, $save = ''){
        if(isset($_REQUEST['load_data']))
            return $this->users->LoadSingle($id);
            
        if($save == 'save')
            return $this->users->addUser();
        
        $this->data['template'] = 'admin/users/edit_users.htm';
        $this->data['dataParse'] = array(
            'title_page'    => 'Edit Member',
            'description'   => 'Members management page separated with tabs.'
        );
    }
    
    public function Add($save = ''){
        if(isset($_REQUEST['load_data']))
            return $this->users->LoadSingle();
        
        if($save == 'save')
            return $this->users->addUser();
            
        $this->data['template'] = 'admin/users/edit_users.htm';
        $this->data['dataParse'] = array(
            'title_page'    => 'Add Member',
            'description'   => 'Members management page separated with tabs.'
        );
    }
    
    public function Remove(){
        return $this->users->Remove();
    }

    public function getUserAjax(){
        $fakeData = array(
            array('value' => 'Hung Tran', 'user_id' => 1),
            array('value' => 'Vi Lan', 'user_id' => 2)
        );

        exit(json_encode($fakeData));
    }

}