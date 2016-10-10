<?php
require 'MyHistory.php';
session_start();
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
    <!--script src="http://{$ip}/js/jquery.min.js"></script-->
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
    <div align='center'>
endof;
$error = '';
if (isset($_SESSION['self']) && isset($_GET['filename']) && ($file = $_GET['filename'])) {//isset($_SESSION['user']) && 
    $filename = substr($file, strrpos($file, '/') + 1);
	if (isset($_SESSION['dir'])) {
		$filename = "./" . $_SESSION['dir'] . $filename;
	}
    if (strlen($filename) == 0) {
        $error = "File: " . $filename . " Not Found23333";
    } else {
        $end = substr($filename, strrpos($filename, '.') + 1);
        if (strlen($end) == 0) {
            $error = 'File Type Not Support';
        } else {
            $end = strtolower($end);
            switch ($end) {
                case 'mp4':
                    playMP4($filename);
                    break;
                case 'mp3':
                    playMP3($filename);
                    break;
                case 'jpg':
                case 'bmp':
                case 'jpeg':
                case 'gif':
                case 'png':
				case 'webp':
                    showPicture($file, $ip);
                    break;
                case 'txt':
                case 'bin':
				case 'php':
				case 'html':
				case 'java':
				case 'c':
				case 'cpp':
				case 'cmd':
				case 'lrc':
				case 'js':
				case 'rar':
                    showText($file, $ip);
                    break;
                default: 
                    $error = 'File Type Not Support';
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
            <h1>{$error}</h1>
        </div>
    </body>
</html>
endof;

function playMP4($filename) {
    echo <<<endof
	<div class="panel panel-default">
    <video controls="controls" style="border:1px;">
        <source src="{$filename}" type="video/mp4" />
        <object data="{$filename}">
            <embed src="{$filename}" />
        </object>
    </video>
	</div>
endof;
}
    
function playMP3($filename) {
    echo <<<endof
	<div class="panel panel-default">
    <audio controls="controls" height="100" width="100" style="border:1px;">
      <source src="{$filename}" type="audio/mp3" />
      <embed height="100" width="100" src="{$filename}" />
    </audio>
	</div>
endof;
}

function showPicture($filename, $ip) {
    echo <<<endof
	<div class="center panel panel-default">
		<img src="{$filename}" alt="{$filename}"/>
	</div>
endof;
}
//http://{$ip}/filemanage.php?action=download&filename=
function showText($filename, $ip) {
    echo <<<endof
    <div id="text" class="panel panel-default">
	<iframe src="{$filename}" name="iframe1" width="100%" onload="this.height=iframe1.document.body.scrollHeight" frameborder="0"></iframe>
	</div>
endof;
}
//http://{$ip}/filemanage.php?action=download&filename=
session_write_close()
?>