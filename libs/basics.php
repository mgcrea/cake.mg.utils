<?php

/*************************
 *** defining constans ***
 *************************/

/**
 * Use the DS to separate the directories in other defines
 */
	if (!defined('DS')) {
		define('DS', DIRECTORY_SEPARATOR);
	}

/**
 * Use the DS to separate the directories in other defines
 */
	if (!defined('IS_WIN')) {
		define('IS_WIN', DS == '\\');
	}

/**
 * The full path to the directory which holds "app", WITHOUT a trailing DS.
 *
 */
	if (!defined('ROOT')) {
		define('ROOT', LITHIUM_APP_PATH . DS . 'webroot');
	}

/**
 * Basic defines for timing functions.
 */
	if (!defined('SECOND')) define('SECOND', 1);
	if (!defined('MINUTE')) define('MINUTE', 60);
	if (!defined('HOUR')) define('HOUR', 3600);
	if (!defined('DAY')) define('DAY', 86400);
	if (!defined('WEEK')) define('WEEK', 604800);
	if (!defined('MONTH')) define('MONTH', 2592000);
	if (!defined('YEAR')) define('YEAR', 31536000);

/***********************
 *** debug functions ***
 ***********************/

/**
 * Prints out debug information about given variable.
 *
 * Only runs if debug level is greater than zero.
 *
 * @param boolean $var Variable to show debug information for.
 * @param boolean $showHtml If set to true, the method prints the debug data in a screen-friendly way.
 * @param boolean $showFrom If set to true, the method prints from where the function was called.
 * @link http://book.cakephp.org/view/1190/Basic-Debugging
 * @link http://book.cakephp.org/view/1128/debug
 */
	if (!function_exists('debug')) {
		function debug($var = false, $showHtml = false, $showFrom = true) {
			if ($showFrom) {
				$calledFrom = debug_backtrace();
				echo '<strong>' . substr(str_replace(ROOT, '', $calledFrom[0]['file']), 1) . '</strong>';
				echo ' (line <strong>' . $calledFrom[0]['line'] . '</strong>)';
			}
			echo "\n<pre class=\"debug\">\n";

			$var = print_r($var, true);
			if ($showHtml) {
				$var = str_replace('<', '&lt;', str_replace('>', '&gt;', $var));
			}
			echo $var . "\n</pre>\n";
		}
	}

/**
 * Returns backtrace information
 *
 * @param integer $i Variable to show debug trace for.
 */
	function get_caller($i = 2) {
		$traces = debug_backtrace(false);
		if (isset($traces[$i])) {
			return $traces[$i]['class'] . '::' . $traces[$i]['function'] . '()' . (!empty($traces[$i]['line']) ? ' line ' . $traces[$i]['line'] : null);
		}
		return null;
	}

/**
 * Returns backtrace information (class)
 *
 * @param integer $i Variable to show debug trace for.
 */
	function get_caller_class($i = 2) {
		$traces = debug_backtrace(false);
		if (isset($traces[$i])) {
			return $traces[$i]['class'];
		}
		return null;
	}

/**
 * Returns backtrace information (function)
 *
 * @param integer $i Variable to show debug trace for.
 */
	function get_caller_function($i = 2) {
		$traces = debug_backtrace(false);
		if (isset($traces[$i])) {
			return $traces[$i]['function'];
		}
		return null;
	}

/**
 * Flush buffers
 */

	function flush_buffers($output_callback = "ob_gzhandler") {
		ob_end_flush();
		ob_flush();
		flush();
		if(!@ob_start($output_callback)) @ob_start();
	}

/****************************
 *** filesystem functions ***
 ****************************/

/**
 * Prints out array of files and directories... optionally filtered by a string
 *
 * @param string $d Path to analyse.
 * @param string $x Variable to filter results by extension.
 */
	function ls($d = WWW_ROOT, $x = null) {
		$l = array();
		foreach (array_diff(scandir($d), array('.', '..')) as $f) if((($x)?ereg($x,$f):1)) $l[] = $f;
		return $l;
	}

/**
 * Prints out array of files without directories... optionally filtered by extension
 *
 * @param string $d Path to analyse.
 * @param string $x Variable to filter results by extension.
 */
	function lsfile($d = WWW_ROOT, $x = null) {
		$l = array();
		foreach (array_diff(scandir($d), array('.', '..')) as $f) if(is_file($d . DS . $f) && (($x)?ereg($x.'$',$f):1)) $l[] = $f;
		return $l;
	}

/**
 * Prints out array of directories
 *
 * @param string $d Path to analyse.
 */
	function lsdir($d = WWW_ROOT){
		$l = array();
		foreach (array_diff(scandir($d), array('.', '..')) as $f) if(is_dir($d . DS . $f)) $l[] = $f;
		return $l;
	}

/**
 * Create directory recursively
 *
 * @param string $d Path to create.
 * @param ? $m Chmod to apply.
 */
	function rmkdir($d, $m = 0755) {
		return mkdir($d, $m, true);
	}

/**
 * Delete directory recursively. optionally preserving base directory
 *
 * @param string $d Path to delete.
 * @param boolean $k Keep base directory.
 */
	function rrmdir($d, $k = false) {
		if(!file_exists($d)) return true;
		if(!is_dir($d)) return unlink($d);
		//$d = preg_replace('/' . addslashes(DS) . '$/', null, $d);
		foreach (array_diff(scandir($d), array('.', '..')) as $f) if(!rrmdir($d . DS . $f)) return false;
		if(!$k) return rmdir($d);
		return true;
	}

/**
 * List directory recursively. optionally filtered by extension
 *
 * @param string $d Path to analyse.
 * @param string $x Variable to filter results by extension.
 */

	function rlsfile($d, $x = null) {
		$l = array();
		foreach (array_diff(scandir($d), array('.', '..')) as $f) {
			if(is_dir($d . DS . $f)) $l += rlsfile($d . DS . $f, $x);
			elseif(is_file($d . DS . $f) && (($x)?ereg($x.'$',$f):1)) $l[] = $f;
		}
		return $l;
	}

/**
 * Clear directory recursively. optionally preserving base directory
 *
 * @param string $d Path to delete.
 * @param string $x Variable to filter results by extension.
 */
	function rrmfile($d, $x = null) {
		if(!file_exists($d) || (($x)?preg_match('/' . $x . '$/', $d):0)) return true;
		if(!is_dir($d)) return unlink($d);
		foreach (array_diff(scandir($d), array('.', '..')) as $f) if(!rrmfile($d . DS . $f, $x)) return false;
		return true;
	}

/**
 * Get file lines
 *
 * @param string $d Path to delete.
 * @param string $x Variable to filter out by extension.
 */
	function wcl($f) {
		exec("wc -l $f", $r);
		return (int)strstr(current($r), ' ', true);
	}

/**
 * Copy file or folder from source to destination, it can do
 * recursive copy as well and is very smart
 * It recursively creates the dest file or directory path if there weren't exists
 *
 * Situtaions :
 *
 * - Src:/home/test/file.txt ,Dst:/home/test/b ,Result:/home/test/b -> If source was file copy file.txt name with b as name to destination
 * - Src:/home/test/file.txt ,Dst:/home/test/b/ ,Result:/home/test/b/file.txt -> If source was file Creates b directory if does not exsits and copy file.txt into it
 * - Src:/home/test ,Dst:/home/ ,Result:/home/test/** -> If source was directory copy test directory and all of its content into dest
 * - Src:/home/test/ ,Dst:/home/ ,Result:/home/**-> if source was direcotry copy its content to dest
 * - Src:/home/test ,Dst:/home/test2 ,Result:/home/test2/** -> if source was directoy copy it and its content to dest with test2 as name
 * - Src:/home/test/ ,Dst:/home/test2 ,Result:->/home/test2/** if source was directoy copy it and its content to dest with test2 as name
 *
 * @todo
 *  - Should have rollback technique so it can undo the copy when it wasn't successful
 *  - Auto destination technique should be possible to turn off
 *  - Supporting callback function
 *  - May prevent some issues on shared enviroments : http://us3.php.net/umask
 * @param $source //file or folder
 * @param $dest ///file or folder
 * @param $options //folderPermission,filePermission
 * @return boolean
 */
	function smart_copy($source, $dest, $options=array('folderPermission'=>0755,'filePermission'=>0755)) {
		$result=false;
		if (is_file($source)) {
			if (is_dir($dest)) {
				$__dest = $dest.DS.basename($source);
			} else {
				$destFolder = substr($dest, 0, strripos($dest, DS));
				@mkdir($destFolder, $options['folderPermission'], true);
				$__dest=$dest;
			}
			$result=@copy($source, $__dest);
			@chmod($__dest,$options['filePermission']);

		} elseif(is_dir($source)) {
			if ($dest[strlen($dest)-1]==DS) {
				if ($source[strlen($source)-1]==DS) {
					//Copy only contents
				} else {
					//Change parent itself and its contents
					$dest=$dest.basename($source);
					@mkdir($dest);
					chmod($dest,$options['filePermission']);
				}
			} else {
				if ($source[strlen($source)-1]==DS) {
					//Copy parent directory with new name and all its content
					@mkdir($dest,$options['folderPermission']);
					chmod($dest,$options['filePermission']);
				} else {
					//Copy parent directory with new name and all its content
					@mkdir($dest,$options['folderPermission']);
					chmod($dest,$options['filePermission']);
				}
			}

			$dirHandle=opendir($source);
			$numFiles=0;
			while($file=readdir($dirHandle))
			{
				if($file!="." && $file!="..")
				{
					$numFiles++;
					 if(!is_dir($source.DS.$file)) {
						$__dest=$dest.DS.$file;
					} else {
						if($source.DS.$file == $dest) break; // same folder w/o DS
						if($source.$file.DS == $dest) break; // same folder w/ DS
						$__dest=$dest.DS.$file;
					}
					//echo "$source/$file ||| $__dest<br />";
					$result=smart_copy($source.DS.$file, $__dest, $options);
				}
			}
			if($numFiles == 0) $result = true;
			closedir($dirHandle);

		} else {
			$result=false;
		}
		return $result;
	}

	function write_tmp($c = null, $post = null, $pre = "tmp") {
		$f = tempnam(CACHE, $pre);
		$h = fopen($f, "w");
		fwrite($h, $c);
		//fseek($f, 0);
		fclose($h);
		//file_put_contents($f, $post);
		if($post) @rename($f, $f . $post);
		return $f . $post;
	}

	function read_tmp($f) {
		$c = fread($f, 1024);
		fclose($f);
		return $f;
		//return file_get_contents($f);
	}

/***********************
 *** array functions ***
 ***********************/

/**
 * Performs a search in haystack array provided
 *
 * @param string $needle
 * @param array $hastack
 * @param boolean $search_keys
 * @return string Key found
 */
	function array_find($needle, $haystack = array(), $search_keys = false) {
		if(!$haystack) return false;
		foreach($haystack as $key => $value) {
			$what = ($search_keys) ? $key : $value;
			if(is_array($what) && $key = array_find($needle, $what, $search_keys) && $key) return $key;
			elseif(is_string($what) && strpos($what, $needle) !== false) return $key;
		}
		return false;
	}

/**
 * Performs a search in haystack array provided with a regex
 *
 * @param array $hastack
 * @param string $expression
 * @return string Key found
 */
	function array_preg_search($haystack, $expression) {
		if(!is_array($haystack)) return false;
		$result = array();
		$expression = preg_replace("/([^\s]+?)(=|<|>|!)/", "\$a['$1']$2", $expression);
		foreach($haystack as $key => $value) if(eval("return $expression;")) $result[] = $value;
		return $result;
	}

/**
 * Converts an object to an array
 *
 * @param array $hastack
 * @param string $expression
 * @return string Key found
 */
	function array_from_object($object) {
		if(!is_object($object) && !is_array($object)) {
			return $object;
		}
		if(is_object($object)) {
			$object = get_object_vars( $object );
		}
		return array_map('array_from_object', $object);
	}

	// From object to array.
	function to_array($data) {
		if (is_object($data)) $data = get_object_vars($data);
		return is_array($data) ? array_map(__FUNCTION__, $data) : $data;
	}

	// From array to object.
	function to_object($data) {
		return is_array($data) ? (object) array_map(__FUNCTION__, $data) : $data;
	}

/************************
 *** string functions ***
 ************************/

/**
 * Removes head & tail lines from source
 *
 * @param string $str
 * @param string $head
 * @param string $tail
 * @return string Sliced
 */
	function str_slice_lines($str, $head = 0, $tail = 0) {
		return implode("\n", array_slice(array_slice(explode("\n", $str), $head), 0, count(explode("\n", $str)) - $head - $tail));
	}

/**
 * Removes file unsafe chars from a string
 *
 * @param string $subject
 * @param string $replace
 * @return string Cleaned $subject
 */
	function strip_unsafe($subject, $replace = '_') {
		return preg_replace('/([\\\\\/\:*?"<>|])/', $replace, $subject);
	}

/**
 * Removes accents from a string
 *
 * @param string $subject
 * @return string Cleaned $subject
 */
	function strip_accents($subject) {
		$subject = str_ireplace(array('à', 'â', 'ä'), 'a', $subject);
		$subject = str_ireplace(array('é', 'è', 'ê', 'ë'), 'e', $subject);
		$subject = str_ireplace(array('î', 'ï'), 'i', $subject);
		$subject = str_ireplace(array('ô', 'ö'), 'o', $subject);
		$subject = str_ireplace(array('ù', 'û', 'ü'), 'u', $subject);
		$subject = str_ireplace(array('ÿ'), 'y', $subject);
		return $subject;
	}

/**
 * Creates a global unique identifier
 *
 * @return string guid
 */
	function create_guid() {
		 if (function_exists('com_create_guid') === true) {
			return strtolower(trim(com_create_guid(), '{}'));
		}
		return strtolower(sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535)));
	}

/**
 * Converts dos to unix (cygwin) path
 *
 * @param string $path
 * @param boolean $checkDS
 * @return string converted $path
 */
	function cygpath($path, $checkDS = false) {
		if((!$checkDS || DS == '\\') && preg_match('/' . '([a-z]):\\\\((?:[-\\w\\.\\d\\`]+\\\\)*(?:[-\\w\\.\\d\\`]+)?)(\\s+(.*))?' . '/is', $path, $m)) {
			return strtolower('/cygdrive/' . $m[1] . '/' . str_replace('\\', '/', $m[2])) . (!empty($m[4]) ? ' ' . $m[4] : null);
		}
		return $path;
	}

/**
 * Execute a command with pipes control
 *
 * @param string $subject
 * @param boolean $explode
 * @return array $pipes
 */
	function proc_exec($cmd, $explode = false) {
		if($process = proc_open($cmd, array(array("pipe", "r"), array("pipe", "w"), array("pipe", "w")), $pipes, TMP)) {
			$output_pipes = array_map("stream_get_contents", $pipes);
			array_map('fclose', $pipes);
			proc_close($process);
			if($explode) foreach($output_pipes as &$pipe) $pipe = explode("\n", $pipe);
			return $output_pipes;
		}
		return false;
	}

/**********************
 *** date functions ***
 **********************/

	# function : parse a date
	function date_parse_format($format, $date, $newformat = 'c') {

		$m = 1; $d = 1; $y = 1970; $h = 0; $i = 0; $s = 0;
		$format = strtolower($format);

		// Masque des séparateurs possible.
		$sep = '([\\\/:_;\s-]{1})';
		$date   = preg_split($sep, $date);
		$format = preg_split($sep, $format);

		// On fait correspondre au format de la date.
		foreach($format as $key => $formatDate) {
			//  on vérifie si le format est numérique
			if(!preg_match('`^([0-9]{1,4})$`', $date[$key])) return false;
			$$formatDate = $date[$key];
		}

		$timestamp = mktime($h, $i, $s, $m, $d, $y);
		// Si on spécifie un nouveau format, on retourne la date formatée.
		if($newformat) return date($newformat, $timestamp);
		// Sinon on retourne la date au format timestamp donc integer.
		return (integer)$timestamp;
	}

	function date_parse_time($format, $date) {
		return date_parse_format($format, $date, 'U');
	}

	function date_string_format($format, $date, $locale = false) {
		if($locale) $oldLocale = setlocale(LC_TIME, $locale);
		$date = utf8_encode(strftime($format, strtotime(str_replace('/', '-', $date))));
		if($locale) setlocale(LC_TIME, $oldLocale);
		return $date;
	}

/*********************
 *** dom functions ***
 *********************/
//debug(simplexml_import_dom($parent)->asXML(), true);

function dom_insert($elements = array(), DOMDocument $dom = null, DOMElement $parent = null) {

	if(!$dom) {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
	}

	$parent = $parent?$parent:$dom;

	foreach($elements as $key => $val) {

		if($key === '@attributes') {
			foreach($val as $attrKey => $attrVal) {
				$parent->setAttribute($attrKey, $attrVal);
			}
		} else {

			if(is_numeric($key)) {
				$key = '_' . $key;
			}

			if(is_array($val)) {

				if(array_key_exists(0, $val)) {
					foreach($val as $v) {
						$key_element = $dom->createElement($key);
						$parent->appendChild($key_element);
						dom_insert($v, $dom, $key_element);
					}
				} else {
					$key_element = $dom->createElement($key);
					$parent->appendChild($key_element);
					dom_insert($val, $dom, $key_element);
				}

			} else {
				$key_element = $dom->createElement($key);
				$element = $parent->appendChild($key_element);

				if(preg_match('/[&<>]/i', $val)) {
					$element->appendChild($dom->createCDATASection($val));
				} else {
					$key_element->nodeValue = $val;
				}
			}

		}

	}

	return $dom;
}

function tail($file, $numLines = 1000)
{
    $fp = fopen($file, "r");
    $chunk = 4096;
    $fs = sprintf("%u", filesize($file));
    $max = (intval($fs) == PHP_INT_MAX) ? PHP_INT_MAX : filesize($file);
	$data = null;

    for ($len = 0; $len < $max; $len += $chunk) {
        $seekSize = ($max - $len > $chunk) ? $chunk : $max - $len;

        fseek($fp, ($len + $seekSize) * -1, SEEK_END);
        $data = fread($fp, $seekSize) . $data;

        if (substr_count($data, "\n") >= $numLines + 1) {
            preg_match("!(.*?\n){".($numLines)."}$!", $data, $match);
            fclose($fp);
            return $match[0];
        }
    }
    fclose($fp);
    return $data;
}

function tail2($file, $num_to_get=1000)
{
  $fp = fopen($file, 'r');
  $position = filesize($file);
  fseek($fp, $position-1);
  $chunklen = 4096;
  $data = null;
  while($position >= 0)
  {
    $position = $position - $chunklen;
    if ($position < 0) { $chunklen = abs($position); $position=0;}
    fseek($fp, $position);
    $data = fread($fp, $chunklen) . $data;
    if (substr_count($data, "\n") >= $num_to_get + 1)
    {
       preg_match("!(.*?\n){".($num_to_get-1)."}$!", $data, $match);
       return $match[0];
    }
  }
  fclose($fp);
  return $data;
}

?>
