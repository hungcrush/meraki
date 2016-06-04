<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Install extends CI_Controller {
    
	protected $permission_default = array(
        array(
            'permission_id'     => 'adminPermission',
            'title'             => 'Admin Permission Manage',
            'description'       => 'Admin manage all permission',
            'group_id'          => 1
        ),
        array(
            'permission_id'     => 'adminLogin',
            'title'             => 'Admin Login',
            'description'       => 'Permission allow admin login to Administrator Page',
            'group_id'          => 1
        )
    );
    
    protected $permission_group = array(
        array(
            'title'                 => 'Administrator Manage',
            'type'                  => 1,
            'permission_group_id'   => 1
        )
    );
	
	protected $user_group_default = array(
		array(
			'user_group_id'	=> 2,
			'title'			=> 'Administrator'
		),
		array(
			'user_group_id'	=> 1,
			'title'			=> 'Unregistered / Unconfirmed'
		)
	);
	
	protected $user_default = array(
		'user_id'	=> 1,
		'username'	=> 'yuyunguyen',
		'password'	=> '0862549688',
		'email'		=> 'hungtranqt93@gmail.com',
		'full_name'	=> 'Trần Vĩnh Hưng',
		'group_id'	=> 2
	);
	
    public function __construct(){
        parent::__construct();
		
		//if(!file_exists('install.rst')) exit('FALSE');
		
		$this->load->model('administrator/User_group_model', 'group');
        $this->load->model('User_model', 'user');
		$this->load->model('administrator/Permission_model', 'per');
    }
    
    public function index(){
        //-- Install permission default
		$this->per->truncate();
        foreach($this->permission_default as $arr){
            $this->per->insert($arr);
        }
        
        $this->per->_table = 'tiny_permission_groups';
        
        $this->per->truncate();
        
        foreach($this->permission_group as $arr){
            $this->per->insert($arr);
        }
		
		//-- Install User Group
		foreach($this->user_group_default as $arr){
			$this->group->insert($arr);
		}
		
		//-- Install user default
		$this->user->truncate();
		$this->user->addUser($this->user_default);
		
		@unlink('install.rst');
		echo 'OK';
    }
}