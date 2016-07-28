<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Tasks_todo_model extends TINY_Model
{   
    public function __construct(){
        parent::__construct();
        $this->primary_key = 'todo_id';
    }

    public function Check_complete($task_id)
    {
    	$check = 1;
    	$data = $this->get_many_by('task_id', $task_id);
    	foreach($data as $todo)
    	{
    		if($todo['is_complete'] == 0) {
    			$check = 0;
    			break;
    		}
    	}

    	return $check;
    }
}