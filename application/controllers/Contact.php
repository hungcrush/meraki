<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Contact extends TINY_Controller {
    
    public function __construct(){
        parent::__construct();

        $this->load->model('Content_model', 'content');
    }
    
    public function index(){
        $this->data['template']  = 'templates/contact.htm';
        $this->data['dataParse'] = array(
            'title_page'    => 'Contact Us',
            'description'   => 'Create your own photo books, prints and gifts with our ease-to-use online designer.'
        );

        $this->content->Load('contact_content', $this->data['dataParse']);
    }
}