<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Permission extends TINY_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->load->model('administrator/Permission_model', 'per');
        $this->permission += array(
            'index' => '-/adminPermission'
        );
    }
    
    public function index(){
        $this->data['template']  = 'admin/permission/manager.htm';
        $this->data['dataParse'] = array(
            'title_page'    => 'Permissions Manage',
            'description'   => 'Manage all permission'
        );
    }
    
    public function Load(){
        return $this->per->Load();
    }
    
    public function Add(){
        if (!$this->tiny->isJson) {
           exit('No direct script access allowed');
        }
        return $this->per->Save();
    }
	
	public function AddGroup(){
		if (!$this->tiny->isJson) {
           exit('No direct script access allowed');
        }
        return $this->per->addGroup();
	}
	
	public function Remove(){
		if (!$this->tiny->isJson) {
           exit('No direct script access allowed');
        }
        return $this->per->Remove();
	}
}