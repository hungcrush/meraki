<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Permission_model extends TINY_Model
{   
    public function __construct(){
        parent::__construct();
    }
    
    public function Load($arr = array()){
        
        $dataOut = $this->__load();
		$list_group = $this->Load_Group_Permission();
        return $this->__parse($dataOut, $list_group, $arr) + array(
            'total' 	=> count($sql),
			'list_group'=> $list_group,
            'user_group'=> $this->load_user_group()
        );
    }
	
	public function Load_Group_Permission(){
		$this->_table = 'tiny_permission_groups';
		return $this->get_all();
	}
    
    private function load_user_group(){
        $dataOut = array();
        $this->load->model('administrator/User_group_model', 'group_user');
        $groups = $this->group_user->Load();
        
        foreach($groups['lists'] as $row){
            $dataOut[] = array(
                'label' => $row['title'],
                'value' => $row['user_group_id']
            );
        }
        return $dataOut;
    }
	
	protected function __parse($data, $dataGroup, $arr){
		$dataOut = array();
		foreach($dataGroup as $row){
			$d = array();
			if(isset($data[$row['permission_group_id']])){
				foreach($data[$row['permission_group_id']] as $value){
					$d[] = array(
						'permission_id'	=> $value['permission_id'],
						'title'			=> $value['title'],
						'description'	=> $value['description'],
                        'checked'       => isset($arr[$value['permission_id']]) ? $arr[$value['permission_id']] : ''
					);
				}
				
			}
            if(!$arr){
                $n = 'list_'.$row['type'];
            }else{
                $n = 'list';
            }
			$dataOut[$n][] = array(
				'title'				=> $row['title'],
				'id'				=> $row['permission_group_id'],
				'type'				=> $row['type'],
				'list_permission'	=> $d
			);
		}
		return $dataOut;
	}
    
    public function Save(){
        $data = array(
            'permission_id' => $this->input->post('permission_id'),
            'title'         => $this->input->post('title'),
            'description'   => $this->input->post('description'),
			'group_id'		=> $this->input->post('group_permission')
        );
        if(!isset($_POST['isedit']))
            $this->insert($data);
        else
            $this->update($_POST['isedit'], $data);
            
        if(isset($_POST['group'])){
            $data = array();
            $groups = $_POST['group'];
            if(!is_array($groups)){
                $groups = array($groups);
            }
            $this->_table = 'tiny_user_groups';
            $this->primary_key = 'user_group_id';
            $permission_id = $this->input->post('permission_id');
            
            foreach($groups as $value){
                $d = $this->get($value);
                if(trim($d['permission_ids']) != ''){
                    $data['permission_ids'] = $d['permission_ids'].','.$permission_id;
                }else{
                    $data['permission_ids'] = $permission_id;
                }
                $this->update($value, $data);
            }
        }
		
		return 'OK';
    }
	
	public function addGroup(){
		$this->_table = 'tiny_permission_groups';
		$data = array(
			'title'	=> $this->input->post('title'),
			'type'	=> $this->input->post('type')
		);
		$this->insert($data);
		
		return 'OK';
	}
	
	public function Remove(){
		$id = $this->input->post('id');
		$this->delete($id);
	}
    
    private function __load(){
        $dataOut = array();
		$sql = $this->get_all();
        if(count($sql) > 0){
            foreach($sql as $row){
				$dataOut[$row['group_id']][] = $row;
			}
        }
        return $dataOut;
    }
}