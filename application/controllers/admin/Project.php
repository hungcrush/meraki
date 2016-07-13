<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project extends TINY_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->load->model('Project_model', 'projects');
    }
    
    public function index(){
        $this->data['template']  = 'admin/project/index.html';
        $this->data['dataParse'] = array(
            'title_page'    => 'Projects'
        );
    }

    public function add(){
        $this->data['template']  = 'admin/project/add.html';
        $this->data['dataParse'] = array(
            'title_page'    => 'Add Project'
        );
    }

    public function saveProject()
    {
        $params = $this->_post('project_info');

        if(!empty($params))
        {   
            $project_id = $this->projects->insert_auto($params);
       
            return array(
                'project_id'    => $project_id
            );
        }
    }

    public function loadProjectInfo($project_id = 0)
    {
        return array('data' => $this->projects->get($project_id));
    }

    public function projectList()
    {
        $data = $this->_post();
        return $this->projects->getDataTable($data);
    }
}