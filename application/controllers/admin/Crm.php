<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crm extends TINY_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->load->model('Customer_model', 'customers');
    }
    
    public function index(){
        $this->data['template']  = 'admin/crm/index.html';
        $this->data['dataParse'] = array(
            'title_page'    => 'CRM'
        );
    }

    public function Contact(){
        $this->data['template'] = 'admin/crm/contacts.html';
    }

    public function Stream(){
        $this->data['template'] = 'admin/crm/stream.html';
    }

    
}