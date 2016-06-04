<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends TINY_Controller {
    
    public function __construct(){
        parent::__construct();
    }
    
    public function index(){
        $this->data['template']  = 'templates/product/all-product.htm';
        $this->data['dataParse'] = array(
            'title_page'    => 'All Products',
            'description'   => 'Create your own photo books, prints and gifts with our ease-to-use online designer.'
        );
    }
    
    public function Product($id = 0){
        $this->data['template']  = 'templates/product/all-photobook.htm';
        $this->data['dataParse'] = array(
            'title_page'    => 'All Products',
            'description'   => 'Create your own photo books, prints and gifts with our ease-to-use online designer.'
        );
    }
    
    public function Photobook($product_id = 0, $photobook_id = 0){
        $this->data['template']  = 'templates/product/photobook.htm';
        $this->data['dataParse'] = array(
            'title_page'    => 'PhotoBook',
            'description'   => 'Move your photos off your device and into your life with our line of premium quality photo books. Featuring interior pages printed on 100% recycled paper. Our collection of photo books includes fabric-bound hardcover photo books and softcover photo books renowned for their textured paper cover.'
        );
    }
    
    public function Detail($product_id = 0, $photobook_id = 0, $detail_id = 0){
        $this->data['template']  = 'templates/product/detail.htm';
        $this->data['dataParse'] = array(
            'title_page'    => 'Detail',
            'description'   => 'Move your photos off your device and into your life with our line of premium quality photo books. Featuring interior pages printed on 100% recycled paper. Our collection of photo books includes fabric-bound hardcover photo books and softcover photo books renowned for their textured paper cover.'
        );
    }
}