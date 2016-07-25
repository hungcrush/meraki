<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Task_model extends TINY_Model
{   
    public function __construct(){
        parent::__construct();
    }
    
    public function loadTaskProject($project_id)
    {
    	$tasks = $this->get_many_by('project_id', $project_id);
    	$procesing_task = array();

    	foreach($tasks as $task)
    	{
    		if($task['status'] == 1)
    		{
    			$procesing_task = $task;
    			$this->_table = 'tiny_tasks_todos';
    			$todos = $this->get_many_by('task_id', $task['task_id']);


    			$procesing_task['todo_list'] = $this->_checkCompleteTodos($todos);
    			break;
    		}
    	}

    	return array(
    		'data' 			=> $tasks,
    		'processing' 	=> $procesing_task
    	);
    }

    private function _checkCompleteTodos(&$todos)
    {
        foreach($todos as &$todo)
        {
            if($todo['is_complete'] == 1)
            {
                $todo['completed'] = true;
            }
        }

        return $todos;
    }
}