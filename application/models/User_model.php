<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class User_model extends TINY_Model
{
    private $cacheUsers = array();

    public function __construct(){
        parent::__construct();
        $this->_temporary_return_type = 'array';
    }
    
    public function Load(){
        $dataOut    = array();
        $allUser    = array();
        
        if(isset($_POST['group_id']))
            $this->_database->where('tiny_users.group_id', $_POST['group_id']);
        
        $d = $this->join('tiny_user_profile', 'tiny_user_profile.user_id = tiny_users.user_id')->get_all();
        
        foreach($d as $row){
            $avatar = $row['avatar_link'];
            if($avatar != ''){
                $avatar = explode('|', $avatar);
                $avatar = $this->getImageLink($avatar[0], $avatar[1])->src;
            }
            $row['avatar'] = $avatar;
            
            $allUser[$row['group_id']][] = $row;
        }
        $this->_table = 'tiny_user_groups';
        $allGroup   = $this->get_all();
        foreach($allGroup as $row){
            if(!isset($allUser[$row['user_group_id']])) continue;
            $row['maxLength'] = count($allUser[$row['user_group_id']]);
            $row['list_user'] = $this->_pagination($allUser[$row['user_group_id']]);
            
            $dataOut[] = $row;
        }
        return array(
            'lists' => $dataOut,
            'total' => count($d)
        );
    }
    
    public function LoadSingle($user_id = 0, $justInfo = FALSE){
        if($justInfo && isset($this->cacheUsers[$user_id])) return $this->cacheUsers[$user_id];

        $user_info = '';
        if($user_id != 0){
            $user_info = $this->get($user_id) + $this->load_profile($user_id);
        }

        $this->cacheUsers[$user_id] = $user_info;

        if($justInfo) return $user_info;

        $groups = $this->load_group();
            
        if(is_array($user_info))
            $user_info['role_name'] = $groups[$user_info['group_id']]['title'];
        return array(
            'user_info'     => $user_info, 
            'permission'    => $this->load_permission($user_info, $groups),
            'groups'        => $groups
        );
    }
    
    public function load_profile($user_id){
        $this->_table = 'tiny_user_profile';
        $data = $this->get($user_id);
        $data['birth_date'] = date('d-m-Y', $data['birth_date']);
        $avatar = $data['avatar_link'];
        if($avatar != ''){
            $avatar = explode('|', $avatar);
            $avatar = $this->getImageLink($avatar[0], $avatar[1])->src;
        }
        $data['avatar'] = $avatar;
        
        return $data;
    }
    
    private function load_group(){
        $dataOut = array();
        $this->load->model('administrator/User_group_model', 'group_user');
        $groups = $this->group_user->Load();
        foreach($groups['lists'] as $row){
            $dataOut[$row['user_group_id']] = $row;
        }
        return $dataOut;
    }
    
    private function load_permission($u, &$g, $is_denied = TRUE){
        $arr_user_permission = array(1);
        if(is_array($u)){
            $this->_table = 'tiny_user_permission';
            $a = $this->get_many_by('user_id', $u['user_id']);
            
            foreach($a as $row){
                $arr_user_permission[$row['permission_id']] = 'user';
            }
            
            $denied = explode(',', $u['permission_denied']);
            
            //-- get all groupid and permission_id
            $group_id = $u['group_id'];
            $g[$group_id]['parent'] = true;
            if($u['secondary_group'] != ''){
                $group_id .= ','.$u['secondary_group'];
                $group_id = explode(',', $group_id);
            }
            if(!is_array($group_id)) $group_id = array($group_id);
    
            foreach($group_id as $value){
                if(!isset($g[$value]['parent']))
                    $g[$value]['checked'] = true;
                    
                $list_permission .= $g[$value]['permission_ids'].',';
            }
            $list_permission = explode(',', rtrim($list_permission, ','));
            foreach($list_permission as $value){
                if(!in_array($value, $denied))
                    $arr_user_permission[$value] = 'group';
                else if($is_denied)
                    $arr_user_permission[$value] = 'denied';
            }
            //-- end
        }
        if($is_denied){
            $this->load->model('administrator/Permission_model', 'per');
            return $this->per->Load($arr_user_permission);
        }else
            return $arr_user_permission;
        
    }
    
    public function Check(){
        $password   = $this->__encode($this->input->post('tinypass'));
        $username   = $this->lib->unescape($this->input->post('tinyuser'));
        
        $user = $this->get_by(array(
                'username'  => $username,
                'password'  => $password
        ));
        if(is_array($user) && count($user) > 0){
            $this->_table = '';
            $user_profile = $this->load_profile($user['user_id']);
            if(is_array($user_profile) && count($user_profile) > 0)
                $user = array_replace_recursive($user, $user_profile);
            else
                $user['noProfile'] = 1;
            
            $group = $this->load_group();
            $user['role_name']   = $group[$user['group_id']]['title'];
            $user['permissions'] = $this->load_permission($user, $group, FALSE);
            
            
            $this->session->set_userdata('logged_in', $user);
            return array(
                'accessGranted' => 1
            );
        }
        return 0;
    }
    
    public function addUser($data = array()){
        if(count($data) == 0){
            if(isset($_POST['password']) && $_POST['password'] != ''){
                $data['password'] = $this->input->post('password');
            }
            
            if(isset($_POST['username'])){
                $data['username'] = $this->input->post('username');
            }
            $data['full_name'] = $this->input->post('full_name');
            if(!isset($_POST['group_parent']) && !isset($_POST['group'])){
                return $this->___e('Please select group for user');
            }
            
            $data['group_id'] = $_POST['group_parent'];
            $secondary_group = '';
            $data['secondary_group'] = '';
            if(isset($_POST['group'])){
               if(is_array($_POST['group'])){
                    foreach($_POST['group'] as $group){
                        $secondary_group .= $group.',';
                    }
                    $secondary_group = rtrim($secondary_group, ',');
               }else{
                    $secondary_group = $_POST['group'];
               }
               $data['secondary_group'] = $secondary_group;
            }
            $data['permission_denied'] = implode(',', $_POST['denied']);
            $data['email'] = $this->input->post('email');
        }
        if(isset($data['username']) && $this->__checkExist($data['username'])) return $this->___e('User is already!');
        $this->__processDataUsr($data);
        $user_id = $this->__insert($data);
            
        $this->_table = 'tiny_user_permission';
        $this->delete_by('user_id', $user_id);
        if(isset($_POST['permission_user'])){
            $data = array();
            if(is_array($_POST['permission_user'])){
                foreach($_POST['permission_user'] as $value){
                    $data[] = array(
                        'user_id'       => $user_id,
                        'permission_id' => $value
                    );
                }
            }else{
                $data[] = array(
                    'user_id'       => $user_id,
                    'permission_id' => $_POST['permission_user']
                );
            }
            
            $this->insert_many($data);
        }
        if(isset($_POST['address'])){
            $data = array();
            $data['user_id'] = $user_id;
            $data['address'] = $this->input->post('address');
            $data['birth_date'] = strtotime($this->input->post('birthdate'));
            $data['phone'] = $this->input->post('phone');
            $data['avatar_link'] = $this->input->post('avatar_link');
            $this->_table = 'tiny_user_profile';
            if(count($this->get_by('user_id', $user_id)) > 0){
                $this->update($user_id, $data);
            }else{
                $this->insert($data);
            }
        }
        return 'OK';
    }

    public function SaveSingle($arrs = array()){
        // Before get fields list of table
        $this->_get_fields_table();
        if(isset($arrs['user_id']))
        {
            $this->update($arrs['user_id'], $arrs);

            // - reset other table
            $this->_table = 'tiny_user_profile';
            $this->_get_fields_table();
            $this->update($arrs['user_id'], $arrs);
        }else
        {
            $this->insert($arrs);
            $this->_table = 'tiny_user_profile';
            $this->_get_fields_table();
            $this->insert($arrs);
        }
        return 'OK';
    }
    
    public function Remove(){
        $user_id = $this->input->post('user_id');
        if($user_id){
            $this->delete($user_id);
            $this->_table = 'tiny_user_profile';
            $this->delete($user_id);
        }
    }
    
    protected function __processDataUsr(&$data){
        if($data['password'])
            $data['password'] = $this->__encode($data['password']);
        
        if($data['username'])
            $data['username'] = $this->lib->escape($data['username']);
    }
    
    protected function __checkExist($username = ''){
        $check = $this->get_by('username', $this->lib->escape($username));
        return is_array($check);
    }
    
    protected function __insert($data){
        if(is_array($data)){
            if(!isset($_POST['user_id']))
                return $this->insert($data);
            else{
                $this->update($_POST['user_id'], $data);
                return $_POST['user_id'];
            }
        }
        return 0;
    }
}