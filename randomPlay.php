<?php
function searchFile($dir, $filename, $searchSubDir = false) {
	$arr = array();
	if (is_dir($dir)) {
		return $arr;
	}
	$handle = opendir($dir);
	while (($file = readdir($handle)) !== false) {
		if ($file != "." && $file != "..") {
			if (is_file($file)) {
				if (strpos($file, $filename) >= 0) {
					$arr[$dir . '/' . $file] = $file;
				}
			} else if ($searchSubDir == true && is_dir($file)) {
				$arr = $arr + searchFile($dir . '/' . $file, $filename, true);
			}
		}
	}
	return $arr;
}
?>