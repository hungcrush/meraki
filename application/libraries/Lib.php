<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Lib {
  
    function __construct() {
        $this->CI = & get_instance();
    }
    
    function truncate_words($text, $limit = 50, $ellipsis = '...') {
    	$text = strip_tags(preg_replace("/<img[^>]+\>/i", '', $this->unescape($text)));
        $words = preg_split("/[\n\r\t ]+/", $text, $limit + 1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_OFFSET_CAPTURE);
        if (count($words) > $limit) {
            end($words); //ignore last element since it contains the rest of the string
            $last_word = prev($words);
               
            $text =  substr($text, 0, $last_word[1] + strlen($last_word[0])) . $ellipsis;
        }
        return $text;
    }
    
    function truncateHtml($text, $length = 100, $ending = '...', $exact = false, $considerHtml = true) {
        $text = $this->unescape($text);
        $text = preg_replace("/<img[^>]+\>/i", '', $text); 
    	if ($considerHtml) {
    		// if the plain text is shorter than the maximum length, return the whole text
    		if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
    			return $text;
    		}
    		// splits all html-tags to scanable lines
    		preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
    		$total_length = strlen($ending);
    		$open_tags = array();
    		$truncate = '';
    		foreach ($lines as $line_matchings) {
    			// if there is any html-tag in this line, handle it and add it (uncounted) to the output
    			if (!empty($line_matchings[1])) {
    				// if it's an "empty element" with or without xhtml-conform closing slash
    				if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
    					// do nothing
    				// if tag is a closing tag
    				} else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
    					// delete tag from $open_tags list
    					$pos = array_search($tag_matchings[1], $open_tags);
    					if ($pos !== false) {
    					unset($open_tags[$pos]);
    					}
    				// if tag is an opening tag
    				} else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
    					// add tag to the beginning of $open_tags list
    					array_unshift($open_tags, strtolower($tag_matchings[1]));
    				}
    				// add html-tag to $truncate'd text
    				$truncate .= $line_matchings[1];
    			}
    			// calculate the length of the plain text part of the line; handle entities as one character
    			$content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
    			if ($total_length+$content_length> $length) {
    				// the number of characters which are left
    				$left = $length - $total_length;
    				$entities_length = 0;
    				// search for html entities
    				if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
    					// calculate the real length of all entities in the legal range
    					foreach ($entities[0] as $entity) {
    						if ($entity[1]+1-$entities_length <= $left) {
    							$left--;
    							$entities_length += strlen($entity[0]);
    						} else {
    							// no more characters left
    							break;
    						}
    					}
    				}
    				$truncate .= substr($line_matchings[2], 0, $left+$entities_length);
    				// maximum lenght is reached, so get off the loop
    				break;
    			} else {
    				$truncate .= $line_matchings[2];
    				$total_length += $content_length;
    			}
    			// if the maximum length is reached, get off the loop
    			if($total_length>= $length) {
    				break;
    			}
    		}
    	} else {
    		if (strlen($text) <= $length) {
    			return $text;
    		} else {
    			$truncate = substr($text, 0, $length - strlen($ending));
    		}
    	}
    	// if the words shouldn't be cut in the middle...
    	if (!$exact) {
    		// ...search the last occurance of a space...
    		$spacepos = strrpos($truncate, ' ');
    		if (isset($spacepos)) {
    			// ...and cut the text in this position
    			$truncate = substr($truncate, 0, $spacepos);
    		}
    	}
    	// add the defined ending to the text
    	$truncate .= $ending;
    	if($considerHtml) {
    		// close all unclosed html-tags
    		foreach ($open_tags as $tag) {
    			$truncate .= '</' . $tag . '>';
    		}
    	}
    	return $truncate;
    }

    function cleanSpecialCharsFromUrl($string) {
        if (isset($string) && !empty($string)) {
            return preg_replace('/[^A-Za-z0-9\-\_]/', '', $string);
        }
    }

    function removeAllSpecial($str){
        return str_replace(array('"', "'", '>', '<'), '', $str);

    }
    
    function catch_that_image($content = ''){
        $first_img = '';
        $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $this->unescape($content), $matches);
        $first_img = $matches[1][0];
    
        if(empty($first_img)) {
            $first_img = $this->CI->tiny->URL___.'resource/images/no-thumb.jpg?1';
        }
        return $first_img;
    }

    function escape($str) {
        $str = trim($str);
        if(empty($str)) return $str;

        $str = str_replace("\\", "", $str);

        if (is_string($str)) {
            $str = htmlentities($str, ENT_QUOTES | ENT_IGNORE, "UTF-8");
        } elseif (is_bool($str)) {
            $str = ($str === FALSE) ? 0 : 1;
        } elseif (is_null($str)) {
            $str = 'NULL';
        }
        $str = str_replace('&ldquo;','&quot;', $str);
        $str = str_replace('&ndash;', '-', $str);
        $str = str_replace('&rdquo;', '&quot;', $str);
        return $str;
    }

    function unescape($str){
        $str = trim($str);
        $str = str_replace("&amp;","&",$str);
        $str = str_replace("&apos;","'",$str);
        $str = str_replace("&lsquo;","'",$str);
        $str = str_replace("&rsquo;","'",$str);
        $str = str_replace("&acute;","'",$str);
        $str = str_replace("&ldquo;",'"',$str);
        $str = str_replace("&rdquo;",'"',$str);
        $str = str_replace("&quot;",'"',$str);
        $str = str_replace("&ndash;",'-',$str);
        $str = str_replace("&mdash;",'-',$str);
        $str = html_entity_decode($str, ENT_QUOTES, 'utf-8');
        return $str;
    }

    function mysql_escape_mimic($inp) { 
        if(is_array($inp)) return array_map(__METHOD__, $inp);     
        if(!empty($inp) && is_string($inp)) { 
            return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a","_"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z','\\_'), $inp); 
        } 
            return $inp; 
    } 
    function ConvertToHtml($str) {
        $array = array("\r\n", "\n\r", "\n", "\r");
        $str = str_replace($array, "<br>", $str);
        $str = str_replace('\"', '&quot;', $str);
        $str = str_replace('"', '&quot;', $str);
        $str = str_replace("\'", '&acute;', $str);
        $str = str_replace("'", '&acute;', $str);
        $str = str_replace("\\\\", "\\", $str);
        return $str;
    }
    function ConvertToNormalText($str) {
        $replace = "";
        $search = "";
        $str = html_entity_decode($str, ENT_QUOTES, 'utf-8');
        return str_replace($replace, $search, $str);
    }
    function ConvertToTest($str) {
        $replace = array("\\0", "\n", "\r", "\Z", "\'", '\"');
        $search = array("\0", "\\n", "\\r", "\x1a", "\'", '\"');
        $str = html_entity_decode($str, ENT_QUOTES, 'utf-8');
        return str_replace($replace, $search, $str);
    }

    function ConvertToSQL($str) {
        $str = trim($str);
        $str = str_replace("\\", "\\\\", $str);
        $str = str_replace("\n", "\\n", $str);
        $str = str_replace("\r", "\\r", $str);
        $str = str_replace("\x1a", "\\Z", $str);
        $str = str_replace("'", "\'", $str);
        $str = str_replace('"', '\"', $str);
        return $str;
    }

    function FCKToSQL($str) {
        $str = trim($str);
        $str = str_replace("\\", "\\\\", $str);
        $str = str_replace("\n", "\\n", $str);
        $str = str_replace("\r", "\\r", $str);
        $str = str_replace("\x1a", "\\Z", $str);
        $str = str_replace("'", "\'", $str);
        $str = str_replace('"', '\"', $str);
        return $str;
    }

     function htmlentitiesOutsideHTMLTags($htmlText, $ent = ENT_QUOTES){
        $matches = Array();
        $sep = '###HTMLTAG###';

        preg_match_all(":</{0,1}[a-z]+[^>]*>:i", $htmlText, $matches);

        $tmp = preg_replace(":</{0,1}[a-z]+[^>]*>:i", $sep, $htmlText);
        $tmp = explode($sep, $tmp);

        for ($i=0; $i<count($tmp); $i++){
            $tmp[$i] = htmlentities($tmp[$i], $ent, 'UTF-8', false);
            $tmp[$i] = str_replace("&","&amp;",$tmp[$i]);
        }

        $tmp = join($sep, $tmp);

        for ($i=0; $i<count($matches[0]); $i++)
            $tmp = preg_replace(":$sep:", $matches[0][$i], $tmp, 1);

        return $tmp;
    }
   
    function SQLToFCK($str, $ckEditor = false) {
        $str = trim($str);
        $str = str_replace("\\\\", "\\", $str);
        $str = str_replace("\\n", "\n", $str);
        $str = str_replace("\\r", "\r", $str);
        $str = str_replace("\'", "'", $str);
        $str = str_replace('\"', '"', $str);
        
        $isProduct = $this->CI->uri->segment(1) == 'shop' || $this->CI->uri->segment(1) == 'store';
        //-- Is Product
        if($isProduct){
            $this->ProcessTableHTML($str);
            if(strpos($this->unescape($str), '<br') === FALSE && strpos($this->unescape($str), '<p>') === FALSE && strpos($this->unescape($str), '<li>') === FALSE){
                $str = preg_replace( "/\r|\n/", "<br />", $str );
            }
        }
        if($ckEditor)
            $str = $this->htmlentitiesOutsideHTMLTags($str);

        return $str;
    }
	
    function GeneralRandomKey($size) {
        $keyset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $randkey = "";
        for ($i = 0; $i < $size; $i++)
            $randkey .= substr($keyset, rand(0, strlen($keyset) - 1), 1);
        return $randkey;
    }

    function GeneralRandomReferralCode($size) {
        $keyset = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $randkey = "";
        for ($i = 0; $i < $size; $i++)
            $randkey .= substr($keyset, rand(0, strlen($keyset) - 1), 1);
            if (preg_match('/[0-9]/', "$randkey") && !preg_match('/[A-Z]/', "$randkey") ) {
                    $randkey ='A'.substr($randkey, 1);  
            }else if(!preg_match('/[0-9]/', "$randkey") && preg_match('/[A-Z]/', "$randkey")) {
                    $randkey ='0'.substr($randkey, 1);
            }
        return $randkey;
    }

    function GeneralRandomNumberKey($size) {
        $keyset = "0123456789";
        $randkey = "";
        for ($i = 0; $i < $size; $i++)
            $randkey .= substr($keyset, rand(0, strlen($keyset) - 1), 1);
        return $randkey;
    }

    function destroy_dir($dir) { 
        if (!is_dir($dir) || is_link($dir)) return unlink($dir); 
        foreach (scandir($dir) as $file) { 
            if ($file == '.' || $file == '..') continue; 
            if (!$this->destroy_dir($dir . DIRECTORY_SEPARATOR . $file)) { 
                chmod($dir . DIRECTORY_SEPARATOR . $file, 0777); 
                if (!$this->destroy_dir($dir . DIRECTORY_SEPARATOR . $file)) return false; 
            }; 
        } 
        return rmdir($dir);
    }
}