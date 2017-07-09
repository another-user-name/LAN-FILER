<?php
include_once 'util.php';
session_start();

if (isset($_SESSION['user']) == false) {
	loginPage();
} else {
	if (isset($_REQUEST['file'])) {
		$file = $_REQUEST['file'];
		if (file_exists($file) == false) {
			$filel = localConv($file);
			if (file_exists($filel)) {
				$file = $filel;
			} else {
				if (isset($_SESSION['dir')) {
					$path = localConv($_SESSION['dir']);
					$filel = $path . basename($filel);
					if (file_exists($filel)) {
						$file = $filel;
					} else {
						$filel = basename($filel);
						if (isset($_SESSION[''])) {
							
						}
					}
				}
			}
		}
	}
}

class FileDownload {
	private $speed = 1024;
	
	public function download($file, $name='', $reload=true) {
		if (file_exists($file)) {
			if ($name == '') {
				$name = basename($file);
			}
			
			$fp = fopen($file, 'rb');
			$file_size = filesize($file);
			$ranges = $this->getRange($file_size);
			
			header("cache-control:public");
			header("content-type:application/octet-stream");
			header("content-disposition:attachment; filename=" . $name);
			
			if ($reload && $ranges != null) {
				header('HTTP/1.1 206 Partial Content');
				header("Accept-Ranges:bytes");
				
				header(sprintf('content-length:%u', $ranges['end'] - $ranges['start']));
				header(sprintf('content-range:bytes %s-%s/%s', $ranges['start'], $ranges['end'], $file_size));
				
				fseek($fp, sprintf('%u', $ranges['start']));
			} else {
				header('HTTP/1.1 200 OK');
				header('content-length:' . $file_size);
			}
			
			while (!feof($fp)) {
				echo fread($fp, round($this->speed * 1024, 0));
				ob_flush();
			}
			
			if ($fp != null) {
				fclose($fp);
			}
		}
	}
	
	public function setSpeed($_speed) {
		if (is_numeric($_speed) && $_speed > 16 && $_speed < 10240) {
			$this->speed = $_speed;
		}
	}
	
	private function getRange($file_size) {
		if (isset($_SERVER['HTTP_RANGE'] && !empty($_SERVER['HTTP_RANGE'])) {
			$range = $_SERVER['HTTP_RANGE'];
			$range = preg_replace('/[\s|,].*/', '', $range);
			$range = explode('-', substr($range, 6));
			if (count($range) < 2) {
				$range[1]=  $file_size;
			}
			$range = array_combine(array('start', 'end'), $range);
			if (empty($range['start'])) {
				$range['start'] = 0;
			}
			if (empty($range['end'])) {
				$range['end'] = $file_size;
			}
			return $range;
		}
		return null;
	}
}

session_write_close();
?>