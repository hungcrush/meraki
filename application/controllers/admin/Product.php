<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends TINY_Controller {
    
    public function __construct(){
        parent::__construct();
        
        $this->permission += array(
            'index' => '-/manAllProduct',
            'test/hung'  => '--/fdsfds'
        );
        
        $this->load->model('product/Product_model', 'product');
        $this->load->model('product/Product_item_model', 'item');
        $this->load->model('product/Product_post_model', 'post');
    }
    
    public function test($text = ''){
        echo 'OK'.$text; exit;
    }
    
    public function index(){
        $this->data['template']  = 'admin/product/product_manage.htm';
        $this->data['dataParse'] = array(
            'title_page'    => 'Product Manager',
            'description'   => 'Management all product and item, post of product :)'
        );
    }
    
    public function Items($product_id = 0){
        if($product_id != 0 && $this->tiny->isJson){
            return $this->item->Load($product_id);
        }
    }
    
    
    public function Post($product_id = 0){
        if(isset($_POST['getContent'])){
            
        }
        if($product_id != 0 && $this->tiny->isJson){
            return $this->post->Load($product_id);
        }
    }
    
    public function Detail($product_id = 0, $item_id = 0){
        if($product_id != 0 && $this->tiny->isJson){
            return $this->item->Load($product_id, $item_id);
        }
    }
    
    public function Load(){
        return $this->product->Load();
    }
    
    public function Add($action = ''){
        //-- add product
        if($action == '')
            return $this->product->Save();
        //-- Add Item
        else if($action == 'item')
            return $this->item->Save();
        //-- Add Post
        else if($action == 'post')
            return $this->post->Save();
        //-- Add Post
        else if($action == 'detail')
            return $this->item->Update_detail();
    }
    
    public function Sort($action = ''){
        //-- add product
        if($action == '')
            return $this->product->Sort();
        //-- Add Item
        else if($action == 'item')
            return $this->item->Sort();
        //-- Add Post
        else if($action == 'post')
            return $this->post->Sort();
    }
    
    public function Remove($action = ''){
        //-- add product
        if($action == '')
            return $this->product->Remove();
        //-- Add Item
        else if($action == 'item')
            return $this->item->Remove();
        //-- Add Post
        else if($action == 'post')
            return $this->post->Remove();
    }
    
    
    public function getTemplate($type = 'product'){
        switch($type){
            case 'product':
                $this->data['template']  = 'admin/product/product.htm';
                break;
            case 'items':
                $this->data['template']  = 'admin/product/product-items.htm';
                break;
            case 'posts':
                $this->data['template']  = 'admin/product/product-posts.htm';
                break;
            case 'detail':
                $this->data['template']  = 'admin/product/item-detail.htm';
                break;
        }
        
    }
}