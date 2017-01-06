<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Product_model extends TINY_Model
{
    public function __construct(){
        parent::__construct();
        $this->_temporary_return_type = 'array';
    }
    
    public function Load($product_id = 0){
        $dataOut = array();
        $listProductName = array();

        if($product_id != 0)
        {
            $this->_database->where('product_id', $product_id);
        }

        if($this->tiny->isAdmin())
        {   
            //-- All category
            $dataOut[] = array(
                'product_id'    => 0,
                'name'          => 'ALL PRODUCTS',
                'thumb'         => 'http://merakistores.com/resource/images/logo-f.png',
                'parent'        => 0
            );
        }
         
        $this->order_by('order');
        $data = $this->get_all();
        if(count($data) > 0){
            foreach($data as $row){
                $image = explode('|', $row['image']);
                $row['origin_img'] = $row['image'];
                $row['image'] = $this->getImageLink($image[0], $image[1])->origin;
                $row['thumb'] = $this->getImageLink($image[0], $image[1])->src;

                $row['parent_obj'] = $this->_getParentObj($data, $row['parent']);

                //-- url
                $row['url'] = '/product/' . $this->lib->makeURLSEO($row['product_id'], $row['name']);
                $dataOut[] = $row;
                $listProductName[$row['product_id']] = $row['name'];
            }
        }
        return array(
            'product'   => $product_id != 0 ? $dataOut[0] : $dataOut,
            'productn'  => $listProductName //-- list product name
        );
    }

    private function _getParentObj($data, $selected)
    {
        $selectedData = array();
        foreach($data as $item)
        {
            if($item['product_id'] == $selected)
            {
                $selectedData = $item;
                break;
            }
        }

        return $selectedData;
    }
    
    public function Save($params){
        // $name           = $this->input->post('name');
        // $description    = $this->input->post('description');
        // $image          = $this->input->post('image');
        
        // $product_id     = $this->input->post('product_id');
        
        // $data_insert = array(
        //     'name'          => $this->lib->escape($name),
        //     'description'   => $this->lib->escape($description),
        //     'image'         => $image
        // );
        // if($product_id == FALSE)
        //     $id = $this->insert($data_insert);
        // else{
        //     $this->update($product_id, $data_insert);
        //     $id = $product_id;
        // }

        $id = $this->insert_auto($params);
            
        return array(
            'content'   => 'OK',
            '_id'       => $id
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
        $product_id = $this->input->post('id');
        $this->delete($product_id);
        $this->_table = 'tiny_product_items';
        $this->delete_many($product_id);
        
        $this->_table = 'tiny_product_posts';
        $this->delete_many($product_id);
        
        return 'OK';
    }
}