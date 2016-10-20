<?php
require 'MyHistory.php';
session_start();

const TMP_PATH = './tmp/';
const TMP_FILE_NAME = 'tmpfile.';


if (!isset($_SESSION['history'])) {
   $_SESSION['history'] = new MyHistory();
}
$ip_addr = $_SERVER['SERVER_ADDR'];
$_SESSION['history']->visit("http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
$ip = $_SERVER['SERVER_ADDR'];

echo <<<endof
<!DOCTYPE html>
<html>
<head>
    <title>Media Player</title>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="http://{$ip}/js/jquery.min.js"></script>
    <link rel="stylesheet" href="http://{$ip}/css/bootstrap.min.css">
	<link rel="stylesheet" href="http://{$ip}/css/bootstrap.min.css">
	<link rel="stylesheet" href="http://{$ip}/css/font-awesome.min.css">
    <script src="http://{$ip}/js/bootstrap.min.js"></script>
</head>
<body>
	<div style="margin:50px">
		<a href="javascript:goback()">
			<h1>
				<i class="icon-arrow-left"></i>&nbsp;&nbsp;Go Back
			</h1>
		</a>
	</div>
    <script type="text/javascript">
        function goback() {
            history.back();
        }
    </script>
    <div id='content' align='center'>
endof;

$error = '';
if (isset($_SESSION['action']) && $_SESSION['action'] == 'leave') {
	
} else if (isset($_SESSION['self']) && isset($_REQUEST['filename']) && ($file = $_REQUEST['filename'])) {//isset($_SESSION['user']) && 
    $filename = substr($file, strrpos($file, '/'));
	$path = TMP_PATH;
	if (isset($_SESSION['dir'])) {
		$path = $_SESSION['dir'];
		if (strrpos($path, '/') != strlen($path) - 1) {
			$path += '/';
		}
	}
	//$path = iconv("GB2312", "UTF-8", $path);
	$filename = iconv("UTF-8", "GB2312", $filename);
	$file = iconv("UTF-8", "GB2312", $file);
    if (strlen($filename) == 0) {
        $error = "File: Not Found 23333";
    } else {
        $end = substr($filename, strrpos($filename, '.') + 1);
        if (strlen($end) == 0) {
            $error = 'File Type Not Support';
        } else if (!myFileExists($path, $filename)) {
			$error = 'File Not Found: ' . $path . $filename;
			$error = iconv("GB2312", "UTF-8", $error);
		}/* else if (filesize($path . $filename) > 1000000000) {
			$error = 'File Too Large';
		}*/ else {
            $end = strtolower($end);
            switch ($end) {
                case 'mp4':
					playMP4(iconv("GB2312", "UTF-8", $path . $filename));
                    break;
                case 'mp3':
					playMP3(iconv("GB2312", "UTF-8", $path . $filename));
                    break;
                case 'jpg':
                case 'bmp':
                case 'jpeg':
                case 'gif':
                case 'png':
				case 'webp':
					showPicture(iconv("GB2312", "UTF-8", $path . $filename));
                    break;
                case 'txt':
				case 'php':
				case 'html':
				case 'htm':
				case 'java':
				case 'c':
				case 'cpp':
				case 'lrc':
				case 'js':
					showText(iconv("GB2312", "UTF-8", $path . $filename));
                    break;
                default: 
                    $error = 'File Type Not Support Yet';
                    break;
            }
        }
    }
} else {
    $error = "File: Not Found";
}

echo <<<endof
        </div>
        <div align="center">
            <h1 id='errorinfo'>{$error}</h1>
        </div>
		<script type='text/javascript'>
			window.onbeforeunload = function (event) {
				$.get("http://{$ip}/play.php?action=leave");
			}
		</script>
    </body>
</html>
endof;

function myFileExists($path, $filename) {
	if (file_exists($path . $filename)) {
		return true;
	}
	$convName = iconv("GB2312", "UTF-8", $path . $filename);
	if (isset($_SESSION['convFailFiles'][$convName]) && file_exists($_SESSION['convFailFiles'][$convName])) {
		return true;
	}
	return false;
}

function playMP4($filename) {
    echo <<<endof
	<div class="panel panel-default">
    <video controls="controls" style="border:1px;max-height:70%;max-width:80%;">
        <source src="media.php?filename={$filename}" type="video/mp4" />
        <object data="media.php?filename={$filename}">
            <embed src="media.php?filename={$filename}" />
        </object>
    </video>
	</div>
endof;
}
    
function playMP3($filename) {
    echo <<<endof
	<div class="panel panel-default">
    <audio controls="controls" style="border:1px;min-width:500px;max-width:800px;max-height:100px;">
      <source src="media.php?filename={$filename}" type="audio/mp3" />
      <embed height="100px" width="500px" src="media.php?filename={$filename}" />
    </audio>
	</div>
endof;
}

function showPicture($filename) {
    echo <<<endof
	<div class="center panel panel-default">
		<img src="media.php?filename={$filename}" alt="{$filename}"/>
	</div>
endof;
}

function showText($filename) {
    echo <<<endof
    <div id="text" class="panel panel-default">
	<iframe src="media.php?filename={$filename}" name="iframe1" width="100%" onload="this.height=iframe1.document.body.scrollHeight" frameborder="0"></iframe>
	</div>
endof;
}

function copyFile($src, $dst) {
	if (isset($_SESSION['lasttmpfile'])) {
		if ($_SESSION['lasttmpfile'] == ($src) && file_exists($dst)) {
			return true;
		}
		if (unlink($_SESSION['lasttmpfile']) == false) {
			return false;
		}
	}
	$_SESSION['lasttmpfile'] = $src;
	return copy($src, $dst);
}

function delFile($filename) {
	return unlink($filename);
}

session_write_close()
?>