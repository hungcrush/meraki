<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends TINY_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->load->model('payment_model', 'payments');
        $this->load->model('payment_item_model', 'items');
        $this->load->helper('cookie');
    }
    
    public function index(){
        $addr_empty   = array(
            'first_name'    => '',
            'last_name'     => '',
            'company'       => '',
            'address1'      => '',
            'address2'      => '',
            'city'          => '',
            'zip'           => ''
        );

        $addr_default = get_cookie('address_default');

        $this->data['template']  = 'templates/payment/payment.htm';
        $this->data['dataParse'] = array(
            'title_page'    => 'Payment',
            'description'   => '',
            'addr_default'  => $addr_default ? json_decode($addr_default) : array($addr_empty)
        );
    }

    public function Submit(){
        $success = TRUE;
        $data = $this->_post('payment_data');

        // get items has been added to the cart before
        // if empty return false
        $item_carts = get_cookie('tiny_carts');
        if(empty($item_carts) || empty($item_carts = json_decode($item_carts, TRUE)))
        {
            $success = FALSE;
        }
        else
        {
            $dataSave = array(
                'customer_email'    => $data['customer_email'],
                'customer_phone'    => $data['customer_phone'],
                'address_obj'       => json_encode($data['address']),
                'time'              => time()
            );

            if( !($payment_id = $this->payments->insert($dataSave)) )
            {
                $success = FALSE;
            }

            if($success)
            {
                if(!empty($data['set_as_default']))
                {
                    set_cookie('address_default', json_encode($data['address']));
                }

                // insert cart items
                foreach ($item_carts as $product_id => $data) {
                    $dataInsert = array(
                        'product_id'    => $product_id,
                        'payment_id'    => $payment_id,
                        'quantity'      => $data['quantity'],
                        'others'        => json_encode(array('size' => $data['size']))
                    );

                    $this->items->insert($dataInsert);
                }
            }
        }
        

        return compact('success');
        
    }

    public function Complete(){
        $this->data['template']  = 'templates/payment/complete.htm';
        $this->data['dataParse'] = array(
            'title_page'    => 'Payment Completed',
            'description'   => ''
        );
    }
}