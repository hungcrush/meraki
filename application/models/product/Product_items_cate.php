<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Product_items_cate extends TINY_Model
{
    public function __construct(){
        parent::__construct();
        $this->_temporary_return_type = 'array';
    }

    public function Load($product_id)
    {
        $dataOut = array();
        $data = $this->get_many_by('product_id', $product_id);

        foreach($data as $row)
        {
            $dataOut[] = $row['product_item_id'];
        }

        return $dataOut;
    }

    public function Load_by_item($item_id)
    {
        $dataOut = array();
        $data = $this->get_many_by('product_item_id', $item_id);

        foreach($data as $row)
        {
            $dataOut[] = $row['product_id'];
        }

        return $dataOut;
    }
}