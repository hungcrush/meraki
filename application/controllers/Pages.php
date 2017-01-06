<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pages extends TINY_Controller {
    
    public function __construct(){
        parent::__construct();

        $this->load->model('Content_model', 'content');
    }
    
    public function index($page){
        $this->data['template']  = 'templates/pages.htm';
        $this->data['dataParse'] = array(
            'title_page'    => 'Pages',
            'description'   => ''
        );

        $this->content->Load($page.'_content', $this->data['dataParse']);
    }
}