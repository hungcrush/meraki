<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tiny {
	var $isJson     = false,
        $isTemp     = false,
        $isLoad     = false;
    var $template   = '';
    var $dataParse  = array();
    
    var $noLogin    = 0; 
    
    var $mainpage   = 'mainpage.htm';

    var $URL___     = ''; 
    
    //-- Pagination default
    var $items_per_page = 15;

    public $userData = array();

    const DIR_TEMP  = 'tinyfw/';
    
    protected $CI;
    public function __construct(){
        $this->CI = & get_instance();
        $this->CI->load->library('parser');
        
        $this->isJson = isset($_REQUEST['json']);
        $this->isTemp = isset($_REQUEST['isTemp']);
        $this->isLoad = isset($_REQUEST['load_data']);
        $this->URL___ = $this->CI->config->item('base_url');
        //-- get path of Page
        $path = isset($_SERVER['SCRIPT_NAME']) ? str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']) : '';
        
        $this->dataParse = array(
            'title_page'    => TITLE_PAGE,
            'description'   => 'FW of Tran Vinh Hung',
            'tinyfw'        => $this->URL___,
            'r'             => $this->URL___.'resource',
            'path'          => $path,
            'dirTemp'       => $this->URL___.'application/views/tinyfw/',
            'hash'          => base64_encode(json_encode(array( $this->CI->security->get_csrf_token_name() => $this->CI->security->get_csrf_hash() ))),
            'item_per_page' => $this->items_per_page
         );

        $this->userData = $this->CI->session->userdata('logged_in');
    }
    
    public function YU($params){
        $data = array();
        if(is_array($params))
            $this->initialize($params);
        
        if($this->template != ''){
            $data['content'] = $this->__parsers(self::DIR_TEMP.$this->template, $this->dataParse, $this->isJson);
        }else{
            if(is_array($params))
                $data = $params;
            else
                $data['content'] = $params;
        }
        
        if($this->isJson)
            $this->ReturnYU($data);
            
    }
    
    protected function ReturnYU($data = array()){
        header('Content-Type: application/json');
        $data['hash']       = base64_encode(json_encode(array( $this->CI->security->get_csrf_token_name() => $this->CI->security->get_csrf_hash() )));
        $data['noLogin']    = $this->noLogin;
        echo json_encode($data);
    }
    
    private function __parsers($template = '', $data, $return = false){
        if(!$this->isTemp){
            //$data['content_tiny'] = $this->CI->parser->parse($template, $data, true);
            $template = self::DIR_TEMP.$this->mainpage;
            $this->CI->load->model('Content_model', 'content');
            $this->CI->content->Load('contact_content', $data);
        }else if($this->noLogin == 1){
            $this->ReturnYU();
            exit;
        }
        return $this->CI->parser->parse($template, $data, $return);
    }
    
    public function __error($data = array()){
        if($this->isTemp || $this->isJson){
            if(count($data) > 0)
                $this->YU($data);
            else
            return $this->__parsers(self::DIR_TEMP.'app/tpls/extra/page-404.html', array(), FALSE);
        }else{
            show_error('Permission Denied!');
        }
        die();
    }
    
    private function __process(){
        if($this->CI->input->post() || $this->CI->input->get()){
            if(!isset($_REQUEST['hash']) || $_REQUEST['hash'] != $this->CI->security->get_csrf_hash()) exit('OK');
        }
    }

    public function loadUserInfo($user_id)
    {
        $this->CI->load->model('User_model', 'usersssss');
        $user_info      = $this->CI->usersssss->LoadSingle($user_id, TRUE);
        return $user_info;
    }

    public function isAdmin()
    {
        return (!empty($this->userData) && (int)$this->CI->input->get_request_header('is_frontpage') != 1);
    }


    // --------------------------------------------------------------------
    
	/**
	 * Initialize Preferences
	 *
	 * @access	public
	 * @param	array	initialization parameters
	 * @return	void
	 */
    function initialize($params = array()){
        if (count($params) > 0)
		{
			foreach ($params as $key => $val)
			{
				if (isset($this->$key))
				{
				    if(is_array($val)){
				        $this->$key = array_replace_recursive($this->$key, $val);
                    }else
					$this->$key = $val;
				}
			}
		}
    }
}