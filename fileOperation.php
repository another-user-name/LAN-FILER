<?php
$fileOperateError = '';
include_once 'util.php';
$fileArrName = 'convFailFiles';

function setError($error) {
	$fileOperateError = $error;
}

function resetError() {
	$fileOperateError = '';
}

function getError() {
	return $fileOperateError;
}

function get_files($dir) {
	resetError();
	$files = array();
	global $fileArrName;
	
	if (!isset($_SESSION[$fileArrName])) {
		//unset($_SESSION[$fileArrName]);
		$_SESSION[$fileArrName] = array();
	}
	
	if (!is_dir($dir)) {
		setError($dir . ' is not a folder');
		return $files;
	}
	$dirUTF = toUTF8($dir);
	if (!preg_match("/^\w:[\/(\.\/)]+$/", $dir)) {
		$files[$dirUTF] = "p";
	}
	$dir = $dir . addEndToDirName($dir);
	$handle = opendir($dir);
	if ($handle) {
		while (false != ($file = readdir($handle))) {
			$filename = $dir . $file;
			if (is_dir($filename)) {
				if ($file == '.' || $file == '..' || $file == './' || $file == '../') {
					
				} else {
					$files[$dirUTF . toUTF8($file)] = "d";
				}
			} else {
				$convName = toUTF8($file);
				$files[$dirUTF . $convName] = "f";
				if (localConv($convName) != $file) {
					$_SESSION[$fileArrName][$dirUTF . $convName] = $dir . $file;
				}
			}
		}
		closedir($handle);
	}
	return $files;
}

function get_files_by_type($dir, $types) {
	resetError();
	$files = array();
	global $fileArrName;
	
	if (!is_dir($dir)) {
		setError($dir . ' is not a folder');
		return $files;
	}
	$dirUTF = toUTF8($dir);
	$dir = $dir . addEndToDirName($dir);
	$handle = opendir($dir);
	if ($handle) {
		while (false != ($file = readdir($handle))) {
			$filename = $dir . $file;
			if (is_dir($filename)) {
			} else {
				$convName = toUTF8(get_file_name($file));
				if (isset($types[getFileType($convName)])) {
					$files[$dirUTF . $convName] = $dir . $file;
					if (localConv($convName) != $file) {
						$_SESSION[$fileArrName][$dirUTF . $convName] = $dir . $file;
					}
				}
			}
		}
		closedir($handle);
	}
	return $files;
}

function getFileType($fileName) {
    $pos = strrpos($fileName, '.') + 1;
    if ($pos == 0) {
        return "";
    }
    return substr($fileName, $pos);
}

function addEndToDirName($dirname) {
	if (strrpos($dirname, '/') != strlen($dirname) - 1) {
		return localDiretorySeprator();
	} else {
		return '';
	}
}


function file_read($filename, $length = 102400, $create = false) {
	resetError();
	global $fileArrName;
	
	$is_exists = false;
	if (($is_exists = file_exists($filename)) == false && $create == false) {
		setError($filename . ' is not exists.');
		return "";
	}
	if ($is_exists == false) {
		$handle = fopen($filename, "w+");
	} else {
		$handle = fopen($filename, "r");
	}
	$data = '';
	if ($handle) {
		$data = fread($handle, $length);
		fclose($handle);
	} else {
		fclose($handle);
		setError('Fail to open file: ' . $filename);
	}
	return $data;
}

function file_write($filename, $content, $mode = 'w+') {
	resetError();
	global $fileArrName;
	
	try {
		$handle = fopen(localConv($filename), "a+");
	} catch(Execption $e) {
		
	}
	if ($handle && $content) {
		$result = fwrite($handle, $content);
		fclose($handle);
		return $result;
	} else {
		setError('Fail to open file: ' . $filename);
		return false;
	}
}

function file_delete($filename) {
	return unlink($filename);
}

function get_local_path($filename) {
	$filename = localConv($filename);
	$tmp = $filename;
	global $fileArrName;
	
	if (file_exists($filename)) {
		return $filename;
	} else {
		if (isset($_SESSION['dir'])) {
			$filename = localConv($_SESSION['dir']) . get_file_name($filename);
			if (file_exists($filename)) {
				return $filename;
			} else {
				$utf8name = toUTF8($filename);
				if (isset($_SESSION[$fileArrName]) && isset($_SESSION[$fileArrName][$utf8name]) && file_exists($_SESSION[$fileArrName][$utf8name])) {
					return $_SESSION[$fileArrName][$utf8name];
				} else {
					return $tmp;
				}
			}
		}
	}
}

function get_file_name($file) {
	if (($pos = strrpos($file, localDiretorySeprator())) == false) {
		return $file;
	}
	return substr($file, $pos + 1);
}
?>