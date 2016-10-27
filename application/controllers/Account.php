<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends TINY_Controller {
    
    public function __construct(){
        parent::__construct();
    }
    
    public function index(){
        $this->data['template']  = 'templates/account.htm';
        $this->data['dataParse'] = array(
            'title_page'    => 'Account',
            'description'   => ''
        );
    }
}