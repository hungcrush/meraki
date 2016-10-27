<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Content extends TINY_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->load->model('Content_model', 'content');
    }
    
    public function index(){
        $this->data['template']  = 'admin/content/content_manager.htm';
        $this->data['dataParse'] = array(
            'title_page'    => 'Content Manager'
        );
    }
    
    public function About(){
        $this->data['template']  = 'admin/content/content_about.htm';
        $this->data['dataParse'] = array(
            'title_page'    => 'About Content',
            'description'   => 'Setup content for Our Story...',
            'href'          => 'aboutus'
        );
        $this->content->Load('about_content', $this->data['dataParse']);
    }
    
    public function Contact(){
        $this->data['template']  = 'admin/content/content_about.htm';
        $this->data['dataParse'] = array(
            'title_page'    => 'Contact Content',
            'description'   => 'Setup content for contact page',
            'href'          => 'contact'
        );
        $this->content->Load('contact_content', $this->data['dataParse']);
    }
    
    /**
     * Home Content Setup
     */
    public function Home($action = ''){
        if($action == 'save'){
            return $this->Save('homeIndex');
        }else if($action == 'load'){
            return $this->content->loadContentIndex();
        }
        $this->data['template']  = 'admin/content/home_setup.htm';
        $this->data['dataParse'] = array(
            'title_page'    => 'Home Setup',
            'description'   => 'Setup content for Index page',
            'href'          => ''
        );
    }
    
    public function Save($type = ''){
        return $this->content->Save($type);
    }
    
}