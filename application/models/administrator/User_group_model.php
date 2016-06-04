<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class User_group_model extends TINY_Model
{   
    public function __construct(){
        parent::__construct();
		$this->primary_key = 'user_group_id';
    }
    
    public function Save(){
        $data = array(
            'title'         => $this->input->post('title'),
			'permission_ids'=> $this->input->post('permissions')
        );
        if(!isset($_POST['isedit']))
            $this->insert($data);
        else
            $this->update($_POST['isedit'], $data);
		
		return 'OK';
    }
    
    public function Load(){
        return array(
            'lists' => $this->get_all()
        );
    }
    
    public function Remove(){
		$id = $this->input->post('id');
		$this->delete($id);
	}
}