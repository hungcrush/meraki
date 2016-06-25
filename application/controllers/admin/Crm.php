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

    public function ContactList(){
        $data = $this->_post();
        return $this->customers->getDataTable($data);
    }

    public function ContactAdd(){
        $this->data['template'] = 'admin/crm/contacts-add.html';
    }

    public function contactSave(){
        $data = $this->_post();
        $fields = array(
            'childs' => array()
        );
        $insert = $this->customers->insert_auto($data['form'], $fields);
        if($insert)
        {
            return 'OK';
        }    
    }

    public function contactDelete(){
        $customer_id = $this->_post('customer_id');

        if(!is_array($customer_id)){
            $this->customers->delete($customer_id);
        }else{
            $this->customers->delete_many($customer_id);
        }

        //-- delete childs of customers
        $this->customers->_table = 'tiny_customers_childs';
        $this->customers->delete_by('customer_id', $customer_id);

        return 'OK';
    }

    public function Stream(){
        $this->data['template'] = 'admin/crm/stream.html';
    }

    
}