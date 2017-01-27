<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends TINY_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->load->model('content_model', 'content');
        $this->load->model('product/Product_model', 'product');
    }
    
    public function index(){
        $data = $this->content->loadContentIndex();

        unset($data['data']['path']);
        $this->data = array(
            'template'  => 'templates/indexPage.htm',
            'dataParse' => $data['data']
        );
    }

    public function getNavbar()
    {
        return $this->product->Load();
    }
    
    public function testDuLieu(){
        $str = 'priority';
        if(!preg_match("/(created|date)/", $str))
        {
            echo 'OK';
            exit;
        }
        exit('AAAA');
    }
    
    //-- function load Template for angular
    public function Load(){
        if(isset($_GET['template'])){
            $this->data = array('template' => $_GET['template']);
        }else{
            show_error('Found not file.');
        }
    }
    
    public function Delete(){
        if(!isset($_GET['path'])) return false;
        switch($_GET['path']){
            case 'uploads':
                if(isset($_POST['folder']) && isset($_POST['filename'])){
                    @unlink('../uploads/'.$_POST['folder'].'/full-size/'.$_POST['filename']);
                    @unlink('../uploads/'.$_POST['folder'].'/thumbs/'.$_POST['filename']);
                }
                break;
        }
    }
         
    public function Dataupload()
    {
        $s = $this->params('uploads', 'URL');
        if($s)
        {
            $filePath = end(explode('/', $s));
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $contentType = finfo_file($finfo, $filePath);
            finfo_close($finfo);
            
            $url = dirname(__FILE__).'/../../../uploads/' . $s;
            if(file_exists($url))
            {
                header('Cache-control: max-age='.(60*60*24*365));
                header('Expires: '.gmdate(DATE_RFC1123,time()+60*60*24*365));
                header('Last-Modified: '.gmdate(DATE_RFC1123,filemtime($url)));
                header('Content-Type: image/png');
                if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
                   header('HTTP/1.1 304 Not Modified');
                   die();
                }
                header('Content-Type: ' . $contentType);
                readfile($url);
                exit;
            }
        }
        show_404();
    }

    public function loadTime()
    {
        // - year - mon - mday - hours - minutes - seconds
        return getdate();
    }

    public function getRecentInstagram($instagram_user = 'merakistores')
    {
        $limit = 8;
        $medias = array();

        $data = file_get_contents('https://www.instagram.com/'.$instagram_user.'/media/');
        $data = json_decode($data);

        $instagramMedia = $data->items;

        foreach($instagramMedia as $key => $item)
        {
            if($key + 1 > $limit) break;
            $medias[] = array(
                'link'  => $item->link,
                'src'   => $item->images->low_resolution->url,
                'hash'  => '#'
            );
        }

        return array('medias' => $medias);

    }
}
