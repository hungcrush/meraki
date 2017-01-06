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

        $content = array(
            array(
                'label' => 'Cover',
                'name'  => 'cover',
                'type'  => 'image',
                'value' => ''
            ),
            array(
                'label' => 'Content 1',
                'name'  => 'content1',
                'type'  => 'text',
                'value' => ''
            ),
            array(
                'label' => 'Image 1',
                'name'  => 'image1',
                'type'  => 'image',
                'value' => ''
            ),
            array(
                'label' => 'Content 2',
                'name'  => 'content2',
                'type'  => 'text',
                'value' => ''
            ),
            array(
                'label' => 'Image 2',
                'name'  => 'image2',
                'type'  => 'image',
                'value' => ''
            ),
            array(
                'label' => 'Footer Content',
                'name'  => 'footer',
                'type'  => 'text',
                'value' => ''
            ),
        );

        $this->content->Load('about_content', $this->data['dataParse'], $content);
    }
    
    public function Contact(){
        $this->data['template']  = 'admin/content/content_about.htm';
        $this->data['dataParse'] = array(
            'title_page'    => 'Contact Content',
            'description'   => 'Setup content for contact page',
            'href'          => 'contact'
        );

        $content = array(
            array(
                'label' => 'Address',
                'name'  => 'address',
                'type'  => 'text',
                'value' => ''
            ),
            array(
                'label' => 'Phone',
                'name'  => 'phone',
                'type'  => 'stext',
                'value' => ''
            ),
            array(
                'label' => 'Email',
                'name'  => 'email',
                'type'  => 'stext',
                'value' => ''
            ),
            array(
                'label' => 'Working Hours',
                'name'  => 'working',
                'type'  => 'stext',
                'value' => ''
            )
        );

        $this->content->Load('contact_content', $this->data['dataParse'], $content);
    }

    public function Pages($page = ''){
        $this->data['template']  = 'admin/content/content_about.htm';

        $page = explode('-', $page);

        $this->data['dataParse'] = array(
            'title_page'    => '',
            'description'   => 'Setup content for Shipping page',
            'href'          => 'pages/' . $page[0]
        );

        

        $content = array(
            array(
                'label' => 'Content',
                'name'  => 'page_content',
                'type'  => 'text',
                'value' => ''
            )
        );

        $this->content->Load($page[0].'_content', $this->data['dataParse'], $content);

        //debug($this->data['dataParse']);
    }
    
    /**
     * Home Content Setup
     */
    public function Home($action = ''){
        if($action == 'save'){
            return $this->content->Save('homeIndex');
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
        $params = $this->_post();
        return $this->content->Savev2($params);
    }
    
}