<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends TINY_Controller {
    
    public function __construct(){
        parent::__construct();
    }
    
    public function index(){
        $this->data['template']  = 'templates/payment.htm';
        $this->data['dataParse'] = array(
            'title_page'    => 'Payment',
            'description'   => ''
        );
    }

    public function Submit(){
        $data = $this->_post('payment_data');

        debug($data);
    }
}