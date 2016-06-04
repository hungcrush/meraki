<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Blog extends TINY_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->load->model('Blog_model', 'blog');
        
        $this->permission += array(
            'index' => '-/manageBlog',
            'Add'   => '-/addBlog'
        );
    }
    
    public function index(){
        $this->data['template']  = 'admin/blog/blog_manager.htm';
        $this->data['dataParse'] = array(
            'title_page'    => 'Blog Manager',
            'description'   => 'Manager All Blog Post'
        );
    }
    
    public function Add(){
        if($this->tiny->isJson){
            return $this->blog->Save();
        }
        $this->data['template']  = 'admin/blog/blog_edit.htm';
        $this->data['dataParse'] = array(
            'title_page'    => 'Add New Post',
            'description'   => 'Manager All Blog Post'
        );
    }
    
    public function Edit($id = 0){
        $this->data['template']  = 'admin/blog/blog_edit.htm';
        $this->data['dataParse'] = array(
            'title_page'    => 'Add New Post',
            'description'   => 'Manager All Blog Post'
        );
    }
    
    public function Remove(){
        return $this->blog->RemoveBlog();
    }
    
    public function Categories($action = ''){
        if($this->tiny->isLoad){
            return $this->blog->getAllCategories();
        }
        
        if($action == 'add'){
            return $this->blog->addCategory();
        }else if($action == 'remove'){
            return $this->blog->removeCategory();
        }
        $this->data['template']  = 'admin/blog/categories_list.htm';
        $this->data['dataParse'] = array(
            'title_page'    => 'Categories',
            'description'   => 'Manager Categories Blog List'
        );
    }
    
    public function Load(){
        $post_id = $this->blog->__request('post_id') != '' ? $this->blog->__request('post_id') : 0;
        return $this->blog->Load($post_id);
    }
}