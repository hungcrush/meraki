<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Content_model extends TINY_Model
{   
    public function __construct(){
        parent::__construct();
    }
    
    public function Load($content_id, &$dataParse = array()){
        $dataOut = array();
        if( !is_array($content_id) && !strpos($content_id, ',') ){
            $data = $this->get($content_id);
            
            $dataParse['content_title']     = '';
            $dataParse['content_des']       = '';
            $dataParse['content_html']      = '';
            
            if(count($data) > 0){
                $dataParse['content_title'] = $data['title'];
                $dataParse['content_des']   = $data['description'];
                $dataParse['content_html']  = $data['content'];
            }
        }else{
            if(!is_array($content_id))
                $content_id = explode(',', $content_id);
                
            $data = $this->get_many($content_id);
            if(count($data) > 0){
                foreach($data as $row){
                    $dataOut[] = array(
                        'content_title' => $row['title'],
                        'description'   => $row['description'],
                        'content'       => $row['content']
                    );
                }
                $dataParse['list_content'] = $dataOut;
            }
        }
        
        return $dataParse;
    }
    
    public function loadContentIndex(){
        $data = $this->Load('homeIndex');
        $data['content_html'] = $this->lib->unescape($data['content_html']);
        $content = json_decode($data['content_html'], true);
        
        $path = $content['path'];
        $path = explode('|', $path);
        $data['background'] = $this->getImageLink($path[0], $path[1])->origin;
        
        if($data['content_title'] == 'video'){
            $video = explode('?v=', $content['content']);
            $data['src_cur'] = 'http://www.youtube.com/embed/'.end($video);
            $data['src'] = $data['src_cur'].'?'.http_build_query(array(
                'rel'       => isset($content['show_suggest']) ? 1 : 0,
                'controls'  => isset($content['show_control']) ? 1 : 0,
                'showinfo'  => isset($content['show_title'])   ? 1 : 0
            ));
        }
        
        return array(
            'home'  => $data,
            'data'  => array_replace_recursive($content, $data)
        );
    }
    
    public function Save($content_id = ''){
        $type           = $content_id;
        $title          = $this->__request('title');
        $description    = ($this->__request('description'));
        $content        = ($this->input->post('content'));
        
        if(empty($type)) return 'FALSE';
        $data = array(
            'content_id'    => $type,
            'title'         => $title,
            'description'   => $description,
            'content'       => $content
        );
        
        if(count($this->get($type)) > 0){
            $this->update($type, $data);
        }
        else
        $this->insert($data);
        
        return 'OK';
    }
    
}