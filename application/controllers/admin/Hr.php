<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hr extends TINY_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->load->model('Hr_model', 'hrs');
        date_default_timezone_set('Asia/Bangkok');
    }
    
    public function index(){
        $this->data['template']  = 'admin/adminPage.htm';
        $this->data['dataParse'] = array(
            'title_page'    => 'Admin Home',
            'yutest'        => '99.9'
        );
    }

    public function checkStatusPunch()
    {
        $ck = $this->hrs->checkPunchIn();
        if(empty($ck))
        {
            return array('check' => 0);
        }
        else
        {
            if(empty($ck['out_time']))
                return array('check' => 1);
            else
                return array('check' => 2);
        }
    }
    
    public function punchIn()
    {
        $punch = $this->hrs->Punch('in');
    }

    public function punchOut()
    {
        $punch = $this->hrs->Punch('out');
    }
}