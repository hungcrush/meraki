<?php
class Extend_Controller extends CI_Controller
{
    /**
	 * The arguments for the GET request method
	 *
	 * @var array
	 */
	protected $_get_args = array();

	/**
	 * The arguments for the POST request method
	 *
	 * @var array
	 */
	protected $_post_args = array();
    
    /**
	 * The arguments for the PUT request method
	 *
	 * @var array
	 */
	protected $_put_args = array();
    
    /**
     * The arguments for the PATCH request method
     *
     * @var array
     */
    protected $_patch_args = array();
    
    /**
	 * The arguments for the DELETE request method
	 *
	 * @var array
	 */
     
	protected $_delete_args = array();

	/**
	 * The arguments from GET, POST, PUT, DELETE request methods combined.
	 *
	 * @var array
	 */
	protected $_args = array();
    
    public function __construct()
    {
        parent::__construct();
        
        $this->_post_args = $_POST;
        // Set up our GET variables
		$this->_get_args = array_merge($this->_get_args, $this->uri->ruri_to_assoc());
        // Merge both for one mega-args variable
		$this->_args = array_merge($_REQUEST, $this->_get_args, $this->_put_args, $this->_post_args, $this->_delete_args, $this->{'_'.$this->request->method.'_args'});
    }
    
    /**
	 * Retrieve a value from the All request arguments.
	 *
	 * @param string $key The key for the All request argument to retrieve
	 * @param boolean $xss_clean Whether the value should be XSS cleaned or not.
	 * @return string The All argument value.
	 */
	public function get_param($key = NULL, $xss_clean = TRUE)
	{
		if ($key === NULL)
		{
			return $this->_xss_clean($this->_args, $xss_clean);
		}

		return array_key_exists($key, $this->_args) ? $this->_xss_clean($this->_args[$key], $xss_clean) : FALSE;
	}
    
    public function get_params_from($from)
    {
        
    }
    
    /**
	 * Process to protect from XSS attacks.
	 *
	 * @param string $val The input.
	 * @param boolean $process Do clean or note the input.
	 * @return string
	 */
	protected function _xss_clean($val, $process)
	{
		if (CI_VERSION < 2)
		{
			return $process ? $this->input->xss_clean($val) : $val;
		}

		return $process ? $this->security->xss_clean($val) : $val;
	}
}