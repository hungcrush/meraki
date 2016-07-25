<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Tasks_todo_model extends TINY_Model
{   
    public function __construct(){
        parent::__construct();
        $this->primary_key = 'todo_id';
    }
}