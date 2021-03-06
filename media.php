<?php
session_start();
	
if (isset($_SESSION['self']) && isset($_GET['filename'])) {
	$filename = iconv("UTF-8", "GB2312", $_GET['filename']);
	if (!file_exists($filename)) {
		if (isset($_SESSION['convFailFiles'][$_GET['filename']]) && file_exists($_SESSION['convFailFiles'][$_GET['filename']])) {
			$filename = $_SESSION['convFailFiles'][$_GET['filename']];
		} else {
			die("File not found in this server." . $_SESSION['convFailFiles'][$_GET['filename']] . "<br />" . $_GET['filename']);
		}
	}
	
	if (isset($_SESSION['videoStream'])) {
		if ($_SESSION['videoStream']->path == $filename) {
		} else {
			$_SESSION['videoStream'] = new VideoStream($filename);
		}
	} else {
		$_SESSION['videoStream'] = new VideoStream($filename);
	}
	$_SESSION['videoStream']->start();
} else {
	echo "";
}



/**
 * Description of VideoStream
 *
 * @author Rana
 * @link http://codesamplez.com/programming/php-html5-video-streaming-tutorial
 * 
 * @changed-by PFH
 * @change-date 2016.10.20
 */
class VideoStream
{
    public $path = "";
    private $stream = "";
    private $buffer = 102400;
    private $start  = -1;
    private $end    = -1;
    private $size   = 0;
 
	public static $headerExts = array(
		'webm'=> 'video/webm',
		'mp4'=>'video/mp4',
		'mpeg'=>'video/mp4',
		'ogv'=>'video/ogv',
		
		'mp3'=>'audio/mpeg',
		
		'jpg'=>'image/jpeg',
		'jpeg'=>'image/jpeg',
		'jpe'=>'image/jpeg',
		'png'=>'image/jpeg',
		'webp'=>'image/jpeg',
		'bmp'=>'image/bmp',
		'gif'=>'image/gif',
		'ico'=>'image/x-icon',

		'txt'=>'text/plain',
		'html'=>'text/html',
		'htm'=>'text/html',
		'stm'=>'text/html',
		'php'=>'text/html',
		'c'=>'text/plain',
		'cpp'=>'text/plain',
		'h'=>'text/plain',
		'rtx'=>'text/richtext',
		'js'=>'application/x-javascript',
		'css'=>'text/css',
		'lrc'=>'text/plain',
		'java'=>'text/plain'
	);
 
    function __construct($filePath) 
    {
        $this->path = $filePath;
    }
     
    /**
     * Open stream
     */
    private function open()
    {
        if (!($this->stream = fopen($this->path, 'rb'))) {
            die('Could not open stream for reading');
        }
    }
     
    /**
     * Set proper header to serve the video content
     */
    private function setHeader()
    {
        ob_get_clean();
		$exts = strtolower(substr($this->path, strrpos($this->path, '.') + 1));
		if (isset(self::$headerExts[$exts]) == false) {
			die("File type not supporte." . $exts. self::$headerExts[$exts]);
		}
        header("Content-Type: " . self::$headerExts[$exts]);
        header("Cache-Control: max-age=2592000, public");
        header("Expires: ".gmdate('D, d M Y H:i:s', time()+2592000) . ' GMT');
        header("Last-Modified: ".gmdate('D, d M Y H:i:s', @filemtime($this->path)) . ' GMT' );
        $this->start = 0;
        $this->size  = filesize($this->path);
        $this->end   = $this->size - 1;
        header("Accept-Ranges: 0-".$this->end);
         
        if (isset($_SERVER['HTTP_RANGE'])) {
  
            $c_start = $this->start;
            $c_end = $this->end;
 
            list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
            if (strpos($range, ',') !== false) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $this->start-$this->end/$this->size");
                exit;
            }
            if ($range == '-') {
                $c_start = $this->size - substr($range, 1);
            }else{
                $range = explode('-', $range);
                $c_start = $range[0];
                 
                $c_end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $c_end;
            }
            $c_end = ($c_end > $this->end) ? $this->end : $c_end;
            if ($c_start > $c_end || $c_start > $this->size - 1 || $c_end >= $this->size) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $this->start-$this->end/$this->size");
                exit;
            }
            $this->start = $c_start;
            $this->end = $c_end;
            $length = $this->end - $this->start + 1;
            fseek($this->stream, $this->start);
            header('HTTP/1.1 206 Partial Content');
            header("Content-Length: ".$length);
            header("Content-Range: bytes $this->start-$this->end/".$this->size);
        }
        else
        {
            header("Content-Length: ".$this->size);
        }  
         
    }
    
    /**
     * close curretly opened stream
     */
    private function end()
    {
        fclose($this->stream);
        exit;
    }
     
    /**
     * perform the streaming of calculated range
     */
    private function stream()
    {
        $i = $this->start;
        set_time_limit(0);
        while(!feof($this->stream) && $i <= $this->end) {
            $bytesToRead = $this->buffer;
            if(($i+$bytesToRead) > $this->end) {
                $bytesToRead = $this->end - $i + 1;
            }
            $data = fread($this->stream, $bytesToRead);
            echo $data;
            flush();
            $i += $bytesToRead;
        }
    }
     
    /**
     * Start streaming video content
     */
    function start()
    {
        $this->open();
        $this->setHeader();
        $this->stream();
        $this->end();
    }
}

session_write_close();
?>
