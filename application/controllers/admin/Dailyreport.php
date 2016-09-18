<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dailyreport extends TINY_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->load->model('Daily_model', 'dailys');
    }
    
    public function index(){
        $this->data['template']  = 'admin/dailyreport/index.html';
        $this->data['dataParse'] = array(
            'title_page'    => 'Daily Report'
        );
    }

    public function reportWriter()
    {
        $this->data['template'] = 'admin/dailyreport/report-add.html';
    }

    public function Save()
    {
        $data = $this->_post();
        $this->dailys->insert_auto($data);

        return 'OK';
    }

    public function dailiesList()
    {
        $data = $this->_post();
        return $this->dailys->getDataTable($data);
    }
}