<?php
class TINY_Parser extends CI_Parser{
    
	public function __construct()
	{
		$this->CI =& get_instance();
	}
    
    protected function _parse($template, $data, $return = FALSE)
	{
        //-- Replace
	    $template = str_replace('#!', $this->CI->tiny->URL___, $template);
           
        parent::_parse($template, $data, $return);
	}
}
