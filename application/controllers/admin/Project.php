<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project extends TINY_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->load->model('Project_model', 'projects');
        $this->load->model('Task_model', 'tasks');
        $this->load->model('Tasks_todo_model', 'todos');
        $this->load->model('Todo_comment_model', 'comments');
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
        return array('data' => $this->projects->loadProjectView($project_id));
    }

    public function loadTasks($project_id = 0)
    {
        $taskData = $this->tasks->loadTaskProject($project_id);
        return array_merge($taskData, array('is_complete' => $this->todos->Check_complete($taskData['processing']['task_id'])));
    }

    public function loadComments()
    {
        $todo_id = $this->_post('todo_id');
        return array('comments' => $this->comments->Load($todo_id));
    }

    public function projectList()
    {
        $data = $this->_post();
        return $this->projects->getDataTable($data);
    }

    public function projectView()
    {
        $this->data['template']  = 'admin/project/view.html';
        $this->data['dataParse'] = array(
            'title_page'    => 'View Project'
        );
    }

    public function todoComplete()
    {
        $data = $this->_post();
        $this->todos->update($data['todo_id'], array('is_complete' => $data['is_complete']));

        return array('is_complete' => $this->todos->Check_complete($data['task_id']));
    }

    public function postComment()
    {
        return $this->comments->Save($this->_post());
    }

    public function changeToTask()
    {
        return array('content' => $this->tasks->loadTaskById($this->_post('task_id')));
    }

    public function nextTask()
    {
        return $this->tasks->nextTask($this->_post());
    }
}