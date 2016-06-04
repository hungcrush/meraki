<?php
require_once('config.php');
class curlDownloader
{
    private $remoteFileName = NULL;
    private $ch = NULL;
    private $headers = array();
    private $response = NULL;
    private $fp = NULL;
    private $debug = FALSE;
    private $fileSize = 0;
	private $file_ext;
	private $file_name;

    const DEFAULT_FNAME = 'remote.out';

    public function __construct($url)
    {
        $this->init($url);
    }

    public function toggleDebug()
    {
        $this->debug = !$this->debug;
    }
	

    public function init($url)
    {
        if( !$url )
            throw new InvalidArgumentException("Need a URL");

		$this->getExt($url);
        $this->ch = curl_init();
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, FALSE);     
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 2); 
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_HEADERFUNCTION,
            array($this, 'headerCallback'));
        curl_setopt($this->ch, CURLOPT_WRITEFUNCTION,
            array($this, 'bodyCallback'));
    }
	
	public function getExt($url){
		$url = end(explode('/', $url));
		$url = explode('.', $url);
		$this->file_ext = end($url);
		$this->file_name = md5(time());
	}

    public function headerCallback($ch, $string)
    {
        $len = strlen($string);
        if( !strstr($string, ':') )
        {
            $this->response = trim($string);
            return $len;
        }
        list($name, $value) = explode(':', $string, 2);
        if( strcasecmp($name, 'Content-Disposition') == 0 )
        {
            $parts = explode(';', $value);
            if( count($parts) > 1 )
            {
                foreach($parts AS $crumb)
                {
                    if( strstr($crumb, '=') )
                    {
                        list($pname, $pval) = explode('=', $crumb);
                        $pname = trim($pname);
                        if( strcasecmp($pname, 'filename') == 0 )
                        {
                            // Using basename to prevent path injection
                            // in malicious headers.
                            $this->remoteFileName = basename(
                                $this->unquote(trim($pval)));
                            $this->fp = fopen($this->remoteFileName, 'wb');
                        }
                    }
                }
            }
        }

        $this->headers[$name] = trim($value);
        return $len;
    }
    public function bodyCallback($ch, $string)
    {
        if( !$this->fp )
        {
			if(!file_exists(LIBRARY_FOLDER_PATH . 'download/')){
				mkdir(LIBRARY_FOLDER_PATH . 'download/', 0777);
			}
            $this->remoteFileName = self::DEFAULT_FNAME;
			if($this->file_name != '' && $this->file_ext != '') $this->remoteFileName = LIBRARY_FOLDER_PATH . 'download/' . $this->file_name.'.'.strtok($this->file_ext,'?');
            $this->fp = fopen($this->remoteFileName, 'wb');
            if( !$this->fp )
                throw new RuntimeException("Can't open default filename");
        }
        $len = fwrite($this->fp, $string);
        $this->fileSize += $len;
        return $len;
    }

    public function download()
    {
        $retval = curl_exec($this->ch);
        if( $this->debug )
            var_dump($this->headers);
        fclose($this->fp);
        curl_close($this->ch);
        return $this->fileSize;
    }

    public function getFileName() { return str_replace(LIBRARY_FOLDER_PATH, LIBRARY_FOLDER_URL ,$this->remoteFileName); }

    private function unquote($string)
    {
        return str_replace(array("'", '"'), '', $string);
    }
}
if(isset($_POST['url'])){
	$dl = new curlDownloader(
		$_POST['url']
	);
	$size = $dl->download();
	echo $dl->getFileName();
}
?>