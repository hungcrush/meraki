<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends TINY_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->load->model('User_model', 'users');
        $this->load->model('Feed_model', 'feeds');
    }
    
    public function index(){
        $this->data['template']  = 'admin/profile/timeline.html';
        $this->data['dataParse'] = array(
            'title_page'    => 'Profile'
        );
    }

    public function Edit(){
        $this->data['template'] = 'admin/profile/profile.html';
        $info_user = $this->users->LoadSingle($this->tiny->userData['user_id']);

        $this->data['dataParse'] = $info_user['user_info'];
        $this->data['dataParse']['title_page'] = 'Edit your profile';
    }

    public function Save(){
        $dataFromMethod = $this->_post();
        return $this->users->SaveSingle($dataFromMethod);
    }

    public function Postfeed(){
        return $this->feeds->Save($this->_post());
    }

    public function postFeedComment(){
        
    }

    public function loadTimelines()
    {
        return array('data' => $this->feeds->Load());
    }
    
}