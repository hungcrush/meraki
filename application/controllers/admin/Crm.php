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

    public function ContactAdd(){
        $this->data['template'] = 'admin/crm/contacts-add.html';
    }

    public function contactSave(){
        $data = $this->_post();
        $feilds = array(
            'childs' => array()
        );

        $insert = $this->customers->insert_auto($data['form'], $feilds);
        if($insert)
        {
            return 'OK';
        }    
    }

    public function Stream(){
        $this->data['template'] = 'admin/crm/stream.html';
    }

    
}