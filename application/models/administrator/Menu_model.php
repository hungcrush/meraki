<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Menu_model extends TINY_Model
{
    var $user_permission = array();
    
    public function __construct(){
        parent::__construct();
        $this->_temporary_return_type = 'array';
        $this->load->config('tiny');
    }
    
    public function Load(){
        $data       = array();
        $options    = array(); 
        $this->__parse($this->__load(), 0, $data, $options);
        
        return array(
            'menus'     => $data,
            'options'   => $options,
            'pages'     => $this->config->item('tiny_pages')
        );
    }
    
    public function Navigation(){
        $p = $this->session->userdata('logged_in');
        $this->user_permission = $p['permissions'];
        
        $data = array();
        $this->__parse($this->__load(true), 0, $data);
        
        return array(
            'menu'  => $data
        );
    }
    
    public function Save(){
        $data = array(
            'title'         => $this->input->post('title'),
            'parent'        => $this->input->post('parent'),
            'permission_id' => $this->input->post('permission_id'),
            'link'          => $this->input->post('link'),
            'icon'          => $this->input->post('icon'),
            'status'        => 1
        );
        if(isset($_POST['menu_id']))
            $this->update($_POST['menu_id'], $data);
        else
            $this->insert($data);
        
        return 'OK';
    }
    
    public function Sort(){
        $data = json_decode($this->input->post('data'), true);
        $this->primary_key = 'menu_id';
        foreach($data as $value){
            $this->update($value['id'], array('weight' => $value['order'], 'parent' => $value['parent']));
        }
        return 'OK';        
    }
    
    public function Remove(){
        $id = $this->input->post('menu_id');
        $this->delete($id);
        return 'OK';
    }
    
    private function __parse($data, $parent = 0, &$arr, &$options = array(), $attr = ''){
        if($parent != 0)
            $attr .= '&nbsp; &nbsp; ';
        //else
            //$str .= '<option value="0">--- Root ---</option>';
        if(isset($data[$parent])){
            foreach($data[$parent] as $row){
                $options[] = array(
                    'title' => $attr.$row['title'],
                    'id'    => $row['menu_id']
                );
                $id = count($arr);
                $arr[$id] = $row;
                $str .= $this->__parse($data, $row['menu_id'], $arr[$id]['child'], $options, $attr);
            }
        }
        return $str;
    }
    
    private function __load($check = false){
        $data = array();
        
        $this->order_by('weight');
        $sql = $this->get_many_by('status', 1);
        
        foreach($sql as $row)
        {
            if($check && trim($row['permission_id']) != ''){
                if(!isset($this->user_permission[$row['permission_id']])) continue;
            }
            $data[$row['parent']][] = $row;
        }
        
        return $data;
    }
    
}