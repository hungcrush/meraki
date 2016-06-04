<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu extends TINY_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->load->model('administrator/Menu_model', 'menus');
        
        $this->permission += array(
            'Add'   => '-/addAdminMenu'
        );
    }
    
    public function index(){
        $this->data['template'] = 'admin/menu/menu_manager.htm';
        $this->data['dataParse'] = array(
            'title_page'    => 'Admin Navigation',
            'description'   => 'Menu for administrator management page.'
        );
    }
    
    public function Load(){
        return $this->menus->Load();
    }
    
    public function Navigation(){
        return $this->menus->Navigation();
    }
    
    public function Add(){
        return $this->menus->Save();
    }
    
    public function Sort(){
        return $this->menus->Sort();
    }
    
    public function Remove(){
        return $this->menus->Remove();
    }
    
}