<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Timeline_model extends TINY_Model
{   
    public function __construct(){
        parent::__construct();
    }
    
    public function Save($content_id = ''){
        $type           = $content_id;
        $title          = $this->__request('title');
        $description    = $this->lib->escape($this->__request('description'));
        $content        = $this->lib->escape($this->input->post('content'));
        
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