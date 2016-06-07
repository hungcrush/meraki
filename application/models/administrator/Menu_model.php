<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Menu_model extends TINY_Model
{
    var $user_permission = array();
    var $all_menu = array();
    
    public function __construct(){
        parent::__construct();
        $this->_temporary_return_type = 'array';
        $this->load->config('tiny');
    }
    
    public function Load(){
        $data       = array();
        $options    = array(); 
        
        foreach($this->__load() as $key => $arr)
        {
            $data[$key] = array();
            $this->__parse($arr, 0, $data[$key]);
        }
        
        //-- Get for options
        $temp = array();
        $this->__parse($this->all_menu, 0, $temp, $options);
        
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
        foreach($this->__load(true) as $key => $arr)
        {
            $data[$key] = array();
            $this->__parse($arr, 0, $data[$key]);
        }
        
        
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
        
        $of_page = $this->input->post('of_page') ?: 1;
        if(!is_array($of_page))
        {
            $data['of_page'] = $of_page;
        }else
        {
            $data['of_page'] = '';
            foreach($of_page as $page)
            {
                $data['of_page'] .= $page . ',';
            }
            $data['of_page'] = rtrim($data['of_page'], ',');
        }
        
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
            $of_page = array(1);
            if(!empty($row['of_page']))
            {
                $of_page = explode(',', $row['of_page']);
            }
            
            foreach($of_page as $page)
            {
                $data[$page][$row['parent']][] = $row;
            }
            
            $this->all_menu[$row['parent']][] = $row;
            
        }
        
        return $data;
    }

    protected function __fillParent($data, &$childData)
    {
        foreach(array_keys($childData) as $parent){
            if(isset($data[$parent]))
            {
                $find = FALSE;
                if(isset($childData[$data[$parent]['parent']]))
                {
                    foreach($childData[$data[$parent]['parent']] as $c){
                        if($c['menu_id'] == $data[$parent]['menu_id']) $find = TRUE;
                    }
                }

                if(!$find)
                {
                    $childData[$data[$parent]['parent']][] = $data[$parent];
                }
                
            }
        }
    }
    
}