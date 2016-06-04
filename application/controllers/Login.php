<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends TINY_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->load->model('User_model', 'user');
        $this->permission += array(
            'getUserdata'  => '-/getUserData'
        );
    }
    
    public function index(){
        if($this->input->post('do_login')){
            return $this->user->Check();
        }
        $this->data['template']  = 'templates/login.htm';
        $this->data['dataParse'] = array(
            'title_page'    => 'Login',
            'description'   => 'Login Page'
        );
    }
    
    public function logout(){
        $this->session->unset_userdata('logged_in');
        header('Location: '.$this->tiny->URL___.'login');
    }
    
    public function getUserdata(){
        return array('userdata' => $this->session->userdata('logged_in'));
    }
}