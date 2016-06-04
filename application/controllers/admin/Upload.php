<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Upload extends TINY_Controller {
    
    public function __construct(){
        parent::__construct();
    }
    
    public function index(){
        $this->data['template']  = 'admin/uploadFile.htm';
    }
}