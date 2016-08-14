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
    	$projects = $this->get_many_by('is_template', 0);
    	foreach($projects as $project)
    	{
    		$project['created_at'] = $this->formatTime($project['created_at']);
    		$dataOut[] = $project;
    	}

    	return $dataOut;
    }

    public function loadProjectView($project_id)
    {
        $data = $this->get($project_id);

        if(!empty($data['participants']))
        {
            $participants = json_decode($data['participants'], true);
            $data['participants'] = array();
            foreach($participants as $p)
            {
                $data['participants'][] = $this->tiny->loadUserInfo($p);
            }
        }

        $data['response_person'] = $this->tiny->loadUserInfo($data['response_person']);

        //-- options
        $options = json_decode($data['options'], true);

        return $data;
    }
}