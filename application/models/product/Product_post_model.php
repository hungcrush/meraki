<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Product_post_model extends TINY_Model
{
    public function __construct(){
        parent::__construct();
        $this->_temporary_return_type = 'array';
    }
    
    public function Load($product_id = 0){
        $dataOut = array();
        
        $data = $this->get_many_by('product_id', $product_id);
        if(count($data) > 0){
            foreach($data as $row){
                $row['content_trucate'] = $this->lib->truncate_words($row['content']);
                $dataOut[] = $row;
            
            }
        }
        return array(
            'posts'     => $dataOut,
            'product_id'=> $product_id
        );
    }
    
    public function Save(){
        $title          = $this->input->post('title');
        $content        = $this->input->post('description');
        $product_id     = $this->input->post('product_id');
        
        $post_id        = $this->input->post('post_id');
        
        $data_insert = array(
            'product_id'    => $product_id,
            'content'       => $content,
            'name'          => $title       
        );
        if($post_id == FALSE)
            $id = $this->insert($data_insert);
        else{
            unset($data_insert['product_id']);
            $this->update($post_id, $data_insert);
        }
        return array(
            'content'   => 'OK',
            '_id'       => $id,
            'product_id'=> $product_id
        );
    }
    
    public function Sort(){
        $dataSort = $this->input->post('sortarr');
        if($dataSort){
            $dataSort = json_decode($dataSort, true);
            foreach($dataSort as $key => $value){
                $this->update(str_replace('sort_', '', $key), array('order' => $value));
            }
        }
        return 'OK';
    }
    
    public function Remove(){
        $item_id = $this->input->post('id');
        $this->delete($item_id);
        
        return 'OK';
    }
}