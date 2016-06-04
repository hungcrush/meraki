<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class TINY_Controller extends Extend_Controller
{
    protected $class_name;
    protected $function_name;
    
    protected $data = array();
    
    protected $permission = array();

    public function __construct()
    {
        $this->permission = array(
            'Load'  => isset($_REQUEST['load_data'])
        );
    }


    public function _remap($method, $params = array())
    {
        $this->getClassAndMethod($method);
        if (method_exists($this, $method))
        {
            $result = '';
            if(isset($_REQUEST['load_data']) && $method == 'index')
                $method = 'Load';
                
            if($this->authorization($method, $params))
            $result = @call_user_func_array(array($this, $method), $params);
            else
            $result = array('error' => "You don't have permission to use this function!");
                
            if(count($this->data) == 0 || !is_array($this->data)){
                if(is_array($result)) $this->data = $result;
                else if(!empty($result)){
                     $this->data['content'] = $result;
                }else
                    $this->data['template']  = 'admin/indexPage.htm';
            }
            
            if(isset($_GET['debug'])) $this->debug($this->data);
                
            $this->tiny->YU($this->data);
        }
        else{
            if($this->uri->segment(1) == 'admin'){
                $this->tiny->__error('admin');
            }else
                $this->tiny->__error();
        }
    }
    
    protected function debug($data){
        echo '<pre>';
        print_r($data);
        exit;
    }
    //-- Check permission [Important]
    protected function authorization($method, $params = array()){
        //-- if METHOD index not have permission
        if($method != 'index' && isset($this->permission['index']) && !$this->authorization('index')) return FALSE;
        
        if(!isset($params[0])) $params[0] = 1;
        
        if(isset($this->permission[$method]) || isset($this->permission[$method.'/'.$params[0]])){
            if (isset($this->permission[$method.'/'.$params[0]]) && substr($this->permission[$method.'/'.$params[0]], 0, 2) == '--'){

                if($this->_p($method.'/'.$params[0], 2))
                return TRUE;
            }else if(is_string($this->permission[$method]) && strpos('-/', $this->permission[$method]) !== -1){
                if($this->_p($method)) 
                return TRUE;
            }else if($this->permission[$method]){
                return TRUE;
            }
            if(!$this->tiny->isJson && !$this->tiny->isTemp)
                $this->tiny->__error();
            return FALSE;
        }
        return TRUE;
    }
    
    protected function _p($method, $type = 1){
        $p = $this->session->userdata('logged_in');
        if($type != 1){
            $permission_id = str_replace('--/', '', $this->permission[$method]);
        }else
        $permission_id = str_replace('-/', '', $this->permission[$method]);
        
        if(isset($p['permissions'][$permission_id]))
            return TRUE;
        return FALSE;
    }
    
    protected function _rebuildURI(&$method){
        $method = preg_replace_callback ("/-([a-z])/", function($match){
        	return strtoupper($match[1]);
        }, $method);
        
        return $this;
    }

    protected function getClassAndMethod(&$method)
    {
        $this->_rebuildURI($method);
        $isPage = $this->uri->segment(1);
        if($isPage == 'admin' || $isPage == 'login'){
            if($isPage == 'admin'){
                if ($this->session->userdata('logged_in') == null || $this->session->userdata('logged_in') < 1) {
                    if(!$this->tiny->isTemp && !$this->tiny->isJson)
                        header('Location: '.$this->tiny->URL___.'login', true, 301);
                    else
                        $this->data['noLogin'] = 1;
                }
            }
            $this->tiny->mainpage = 'admin_mainpage.htm';
        }
        $this->class_name = strtolower(get_class($this));
        $this->function_name = strtolower($method);
    }
}