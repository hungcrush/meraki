<?php
	// requires php5
	require_once('config.php');
	
    class CropAvatar {
        private $src;
        private $data;
        private $file;
        private $dst;
        private $type;
        private $extension;
		private $file_name;
		private $folder;
		
        private $srcDir = 'img/upload';
        private $dstDir = LIBRARY_FOLDER_PATH;
        private $msg;

        function __construct($src, $data, $folder = '') {
			$this -> folder = $folder != '' && $folder != 'Home' ? $folder.'/' : '';
            $this -> setSrc(strtok($src,'?'));
            $this -> setData($data);
			//-- start to crop image
            $this -> crop($this -> src, $this -> dst, $this -> data);
        }

        private function setSrc($src) {
            if (!empty($src)) {
                $type = $this->exif_imagetypes($src);
                
				$file = @pathinfo($src);
                if ($type && $file) {
                    $this -> src = $src;
                    $this -> type = $type;
                    $this -> extension = '.'.$file['extension'];
					$this -> file_name = $file['filename'];
                    $this -> setDst();
                }
            }
        }
        
        private function exif_imagetypes ( $filename ) {
            if ( ( list($width, $height, $type, $attr) = getimagesize( $filename ) ) !== false ) {
                return $type;
            }
        return false;
        }

        private function setData($data) {
            if (!empty($data)) {
                $this -> data = json_decode(stripslashes($data));
            }
        }

        private function setFile($file) {
            $errorCode = $file['error'];

            if ($errorCode === UPLOAD_ERR_OK) {
                $type = $this->exif_imagetypes($file['tmp_name']);
                
                if ($type) {
                    $dir = $this -> srcDir;

                    if (!file_exists($dir)) {
                        mkdir($dir, 0777);
                    }

                    $extension = image_type_to_extension($type);
                    $src = $dir . '/' . date('YmdHis') . $extension;

                    if ($type == IMAGETYPE_GIF || $type == IMAGETYPE_JPEG || $type == IMAGETYPE_PNG) {

                        if (file_exists($src)) {
                            unlink($src);
                        }

                        $result = move_uploaded_file($file['tmp_name'], $src);

                        if ($result) {
                            $this -> src = $src;
                            $this -> type = $type;
                            $this -> extension = $extension;
                            $this -> setDst();
                        } else {
                             $this -> msg = 'Failed to save file';
                        }
                    } else {
                        $this -> msg = 'Please upload image with the following types: JPG, PNG, GIF';
                    }
                } else {
                    $this -> msg = 'Please upload image file';
                }
            } else {
                $this -> msg = $this -> codeToMessage($errorCode);
            }
        }

        private function setDst() {
            $dir = $this -> dstDir;

            if (!file_exists($dir)) {
                mkdir($dir, 0777);
            }

            $this -> dst = $dir . $this->folder . '/' . $this->file_name . $this -> extension;
        }

        private function crop($src, $dst, $data) {
            if (!empty($src) && !empty($dst) && !empty($data)) {
                switch ($this -> type) {
                    case IMAGETYPE_GIF:
                        $src_img = imagecreatefromgif($src);
                        break;

                    case IMAGETYPE_JPEG:
                        $src_img = imagecreatefromjpeg($src);
                        break;

                    case IMAGETYPE_PNG:
                        $src_img = imagecreatefrompng($src);
                        break;
                }

                if (!$src_img) {
                    $this -> msg = "Failed to read the image file";
                    return;
                }

                $dst_img = imagecreatetruecolor($data -> width, $data -> height);
                $result = imagecopyresampled($dst_img, $src_img, 0, 0, $data -> x, $data -> y, $data -> width, $data -> height, $data -> width, $data -> height);

                if ($result) {
                    switch ($this -> type) {
                        case IMAGETYPE_GIF:
                            $result = imagegif($dst_img, $dst);
                            break;

                        case IMAGETYPE_JPEG:
                            $result = imagejpeg($dst_img, $dst);
                            break;

                        case IMAGETYPE_PNG:
                            $result = imagepng($dst_img, $dst);
                            break;
                    }

                    if (!$result) {
                        $this -> msg = "Failed to save the cropped image file";
                    }
                } else {
                    $this -> msg = "Failed to crop the image file";
                }

                imagedestroy($src_img);
                imagedestroy($dst_img);
            }
        }

        private function codeToMessage($code) {
            switch ($code) {
                case UPLOAD_ERR_INI_SIZE:
                    $message = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
                    break;

                case UPLOAD_ERR_FORM_SIZE:
                    $message = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
                    break;

                case UPLOAD_ERR_PARTIAL:
                    $message = 'The uploaded file was only partially uploaded';
                    break;

                case UPLOAD_ERR_NO_FILE:
                    $message = 'No file was uploaded';
                    break;

                case UPLOAD_ERR_NO_TMP_DIR:
                    $message = 'Missing a temporary folder';
                    break;

                case UPLOAD_ERR_CANT_WRITE:
                    $message = 'Failed to write file to disk';
                    break;

                case UPLOAD_ERR_EXTENSION:
                    $message = 'File upload stopped by extension';
                    break;

                default:
                    $message = 'Unknown upload error';
            }

            return $message;
        }

        public function getResult() {
            return !empty($this -> data) ? $this -> dst : $this -> src;
        }

        public function getMsg() {
            return $this -> msg;
        }
    }

    $crop = new CropAvatar($_POST['img'], $_POST['img_data'], $_POST['folder']);
    $response = array(
        'state'  => 200,
        'message' => $crop -> getMsg(),
        'result' => $crop -> getResult()
    );

    echo json_encode($response);
?>
