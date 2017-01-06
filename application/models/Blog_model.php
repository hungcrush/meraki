<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Blog_model extends TINY_Model
{   
    var $arr_categoires = array();
    
    public function __construct(){
        parent::__construct();
    }
    
    public function Load($post_id = 0, &$dataParse = FALSE){
        $Count   = 1;
        $dataOut = array();

        if($post_id > 0){
            $this->_database->where('blog_id', $post_id);
        }else{
            $this->_initSearch(array(
                'keyword'       => array('content', 'title'),
                'search_other'  => array('category_id')
            ));
            $Count = $this->count_by();
        }
        
        $this->_pagination();
        $data = $this->get_all();
        
        foreach($data as $row){
            $row['created_at'] = $this->formatTime($row['created_at']);
            $row['image'] = $this->lib->catch_that_image($row['content']);
            $row['description'] = $this->lib->truncate_words($row['content']);
            $row['blog_url'] = '/blog/' . $this->lib->makeURLSEO($row['blog_id'], $row['title']);
            $row['content'] = $this->lib->unescape($row['content']);
            $dataOut[] = $row;
        }

        if($dataParse)
        {
            $dataParse['blogs'] = $dataOut;
        }

        return array(
            'lists' => $dataOut,
            'count' => $Count
        );
    }
    
    public function Save($type = 'blog'){
        
        $title = $this->input->post('title');
        $content = $this->input->post('content');
        $category_id = $this->input->post('category_id');
        $tags = $this->input->post('tags');
        
        if(empty($content) || trim($title) == '')
            return $this->___e('Please complete all information!');
        
        $blog_id = $this->input->post('blog_id');
        
        $data = array(
            'title'         => $this->lib->escape($title),
            'content'       => $this->lib->escape($content),
            'category_id'   => $category_id,
            'tags'          => $tags
        );
        
        if(!$blog_id){
            $data['created_at'] = time();
            $this->insert($data);
            
            $this->updateCountBlog($category_id);
        }else
            $this->update($blog_id, $data);
            
        return 'OK';
    }
    
    public function RemoveBlog(){
        $blog_id = $this->input->post('blog_id');
        $category_id = $this->input->post('category_id');
        $this->delete($blog_id);
        $this->updateCountBlog($category_id, false);
        
        return 'OK';
    }
    
    public function updateCountBlog($category_id = 0, $add = TRUE){
        if($category_id != 0){
            $this->__isCategory();
            $data = $this->get($category_id);
            if(!empty($data)){
                if($add)
                    $this->update($category_id, array('blog_count'  => (int)$data['blog_count']+1));
                else
                    $this->update($category_id, array('blog_count'  => (int)$data['blog_count']-1));
            }
        }
    }
    
    public function getAllCategories(){
        $dataOut = array();
        $this->_table = 'tiny_blog_categories';
        $data = $this->get_all();
        
        foreach($data as $row){
            $this->arr_categoires[$row['category_id']] = $row;
            $dataOut[] = $row;
        }
        return array(
            'cate_list'  => $dataOut,
            'cate'       => $this->arr_categoires
        );
    }
    
    public function addCategory(){
        $this->__isCategory();
        
        $title = $this->input->post('title');
        $category_id = $this->input->post('category_id');
        
        if(!$title || trim($title) == '')
            return $this->___e('Please enter category name');
        
        $data = array(
            'title' => $title
        );
        if($category_id)
            $this->update($category_id, $data);
        else
            $this->insert($data);
            
        return 'OK';
    }
    
    public function removeCategory(){
        $this->__isCategory();
        
        $category_id = $this->input->post('category_id');
        $this->delete($category_id);
    }
    
    private function __isCategory(){
        $this->_table = 'tiny_blog_categories';
        $this->primary_key = 'category_id';
    }
}