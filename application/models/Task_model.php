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
    			$procesing_task['todo_list'] = $this->_checkCompleteTodos($this->_loadTodo($task['task_id']));
    			break;
    		}
    	}

    	return array(
    		'data' 			=> $tasks,
    		'processing' 	=> $procesing_task
    	);
    }

    public function loadTaskById($task_id)
    {
        $data = $this->get($task_id);
        $data['todo_list'] = $this->_checkCompleteTodos($this->_loadTodo($task_id));

        return $data;

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

    private function _loadTodo($task_id)
    {
        $this->_table = 'tiny_tasks_todos';
        $todos = $this->get_many_by('task_id', $task_id);

        //- back to current Table
        $this->_table = 'tiny_tasks';
        return $todos;
    }

    public function nextTask($dataPost)
    {   
        if(empty($dataPost)) return;

        //-- set this task is completed
        $this->update($dataPost['task_id'], array('status' => 3));
        //-- set next task is Processing
        $this->update($dataPost['next_task_id'], array('status' => 1));

        return array_merge($this->loadTaskProject($dataPost['project_id']), array(
            'status'    => 'OK'
        ));
    }

    public function _calcTaskPercent($task_id = 0)
    {
        $todos = $this->_loadTodo($task_id);

        if($todos)
        {
            $total      = count($todos);
            $completed  = 0;
            foreach($todos as $todo)
            {
                if($todo['is_complete'] == 1)
                {
                    $completed++;
                }
            }

            $percent = ($completed / $total) * 100;
            $this->update($task_id, array('percent' => $percent));
        }
    }
}