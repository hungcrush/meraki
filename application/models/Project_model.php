<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Project_model extends TINY_Model
{   
    public function __construct(){
        parent::__construct();
    }

    public function loadList()
    {
    	$dataOut = array();
    	$projects = $this->get_many_by('status <> 3');
    	foreach($projects as $project)
    	{
    		$project['created_at'] = $this->formatTime($project['created_at']);
    		$dataOut[] = $project;
    	}

    	return $dataOut;
    }
}