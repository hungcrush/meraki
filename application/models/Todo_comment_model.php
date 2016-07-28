<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Todo_comment_model extends TINY_Model
{   
    public function __construct(){
        parent::__construct();
        $this->primary_key = 'id';
    }

    public function Save($data)
    {
    	$dataInsert = array(
    		'user_id' 		=> $this->tiny->userData['user_id'],
    		'todo_id' 		=> $data['todo_id'],
    		'date'	  		=> time(),
    		'attach_files'	=> '',
    		'content'		=> $this->lib->escape($data['content'])
    	);

    	$insert = $this->insert($dataInsert);

    	return $insert ? 'OK' : 'FALSE';
    }

    public function Load($todo_id)
    {
    	$data = array();
    	$this->order_by('id', 'DESC');
    	
    	if(is_array($todo_id))
    	{
    		foreach($todo_id as $k => $id)
    		{
    			$data = $data + $this->Load($id);
    		}
    		return $data;
    	}
    	else
    	{
    		$this->limit(5, 0);
    		$data = $this->get_many_by('todo_id', $todo_id);
    		$this->fileds_output_process($data);
    	
    		return $this->groupCommentOnTodo($data, $isSingle);
    	}

    	
    }

    private function groupCommentOnTodo($data, $isSingle)
    {
    	$dataOut = array();
    	foreach($data as $row)
    	{
    		$dataOut[$row['todo_id']][] = $row;
    	}
    	return $dataOut;
    }
}