<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Product_item_model extends TINY_Model
{
    public function __construct(){
        parent::__construct();
        $this->_temporary_return_type = 'array';
    }
    
    public function Load($product_id = 0, $item_id = 0){
        $dataOut = array();
        $this->_ctrl->load->model('product/product_items_cate', 'cates');

        $where = $product_id != 0 ? array('product_id' => $product_id) : array();
        //-- get other categories
        if($product_id != 0)
        {
            $otherCates = $this->_ctrl->cates->Load($product_id);

            if(!empty($otherCates))
            {
                
                //$where['product_item_id'] = NULL;
                $where[1] = 'OR product_item_id IN ('.implode(',', $otherCates).')';
            }
        }
        
        if($item_id != 0){
            $where['product_item_id'] = $item_id;
        }
        $this->order_by('order');
        $data = $this->get_many_by($where);
        //debug($this->_database->last_query());
        if(count($data) > 0){
            foreach($data as $row){
                $image = explode('|', $row['item_image']);
                $row['origin_img'] = $row['item_image'];
                $row['name']  = $row['item_name'];  
                $row['image'] = $this->getImageLink($image[0], $image[1])->origin;
                $row['thumb'] = $this->getImageLink($image[0], $image[1])->src;

                $row['item_detail'] = $this->lib->unescape($row['item_detail']);

                $row['url']   = $this->lib->makeURLSEO($row['product_item_id'], $row['item_name']);

                $row['price'] = number_format($row['price']);

                if($this->tiny->isAdmin())
                {
                    $row['category_list'] = $this->cates->Load_by_item($row['product_item_id']);
                }
                
                if($item_id > 0){
                    $row['detail'] = $row['item_detail'];
                    $row['images'] = $this->getDetailImage($item_id);
                }
                $dataOut[] = $row;
            }
        }
        return array(
            'product'   => $item_id == 0 ? $dataOut : $dataOut[0],
            'product_id'=> $product_id
        );
    }
    
    private function getDetailImage($item_id = 0){
        $Image = array();
        $this->_table = 'tiny_item_images';
        $data = $this->get_many_by('item_id', $item_id);
        
        if(count($data) > 0){
            foreach($data as $img){
                $path = $this->getImageLink($img['path']);
                $Image[] = array(
                     'origin'   => $path->origin,
                     'src'      => $path->src,
                     'path'     => $img['path'],
                     'id'       => $this->lib->GeneralRandomNumberKey(5)
                );
            }
        }
        return $Image;
    }
    
    public function Save($params){
        // $name           = $this->input->post('name');
        // $description    = $this->input->post('description');
        // $image          = $this->input->post('image');
        // $product_id     = $this->input->post('product_id');
        
        // $item_id        = $this->input->post('item_id');
        
        // $data_insert = array(
        //     'product_id'         => $product_id,
        //     'item_name'          => $this->lib->escape($name),
        //     'item_description'   => $this->lib->escape($description),
        //     'item_image'         => $image
        // );
        // if($item_id == FALSE)
        //     $id = $this->insert($data_insert);
        // else{
        //     $this->update($item_id, $data_insert);
            
        //     if(isset($_POST['image_list'])){
        //         //-- save image detail
                
        //     }
        // }
        $product_id = $params['product_id'];

        $item_id = $this->insert_auto($params);

        if(isset($params['categories']) && is_numeric($item_id))
        {
            $this->_table = 'tiny_product_items_cates';
            $this->delete_by('product_item_id', $item_id);
            foreach($params['categories'] as $product_id)
            {
                $this->insert(array('product_id' => $product_id, 'product_item_id' => $item_id));
            }
        }

        return array(
            'content'   => 'OK',
            '_id'       => $item_id,
            'product_id'=> $product_id
        );
    }
    
    public function Update_detail(){
        $item_id = $this->input->post('item_id');
        $title   = $this->input->post('title');
        $content = $this->input->post('content');
        
        $this->update($item_id, array(
            'item_name'     => $title,
            'item_detail'   => $this->lib->escape($content)
        ));
        
        $imgs    = $this->input->post('images');
        $images  = array();
        foreach($imgs as $image){
            $images[] = array(
                'path'      => $image['path'],
                'item_id'   => $item_id
            );
        }
        $this->_table = 'tiny_item_images';
        $this->delete_by('item_id', $item_id);
        $this->insert_many($images);
        
        return 'OK';
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
        
        $this->_table = 'tiny_item_images';
        $this->delete_by('item_id', $item_id);
        
        return 'OK';
    }
}