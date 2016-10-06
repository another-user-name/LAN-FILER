<?php
require 'MyHistory.php';
session_start();
if (!isset($_SESSION['history'])) {
   $_SESSION['history'] = new MyHistory();
}
$ip_addr = $_SERVER['SERVER_ADDR'];
$_SESSION['history']->visit("http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
echo <<<endof
<!DOCTYPE html>
<html>
<head>
    <title>Media Player</title>
</head>
<body>
	<div style="margin:20px">
		<a href="javascript:goback()"><h1>Go Back</h1></a>
	</div>
    <script type="text/javascript">
        function goback() {
            history.back();
        }
    </script>
    <div align='center'>
endof;
$error = '';
if (isset($_GET['file']) && ($file = $_GET['file'])) {//isset($_SESSION['user']) && 
    $filename = substr($file, strrpos($file, '/') + 1);
    if (strlen($filename) == 0) {
        $error = "File Not Found";
    } else {
        $end = substr($filename, strrpos($filename, '.') + 1);
        if (strlen($end) == 0) {
            $error = 'File Type Not Support';
        } else {
            $end = strtolower($end);
            switch ($end) {
                case 'mp4':
                    playMP4($file);
                    break;
                case 'mp3':
                    playMP3($file);
                    break;
                case 'jpg':
                case 'bmp':
                case 'jpeg':
                case 'gif':
                case 'png':
                    showPicture($file, $ip_addr);
                    break;
                case 'txt':
                case 'bin':
                    showText($file);
                    break;
                default: 
                    $error = 'File Type Not Support';
                    break;
            }
        }
    }
} else {
    $error = "File Not Found";
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
    <video controls="controls">
        <source src="{$filename}" type="video/mp4" />
        <object data="{$filename}">
            <embed src="{$filename}" />
        </object>
    </video>
endof;
}
    
function playMP3($filename) {
    echo <<<endof
    <audio controls="controls" height="100" width="100">
      <source src="{$filename}" type="audio/mp3" />
      <embed height="100" width="100" src="{$filename}" />
    </audio>
endof;
}

function showPicture($filename, $ip) {
    echo <<<endof
    <img src="http://{$ip}/filemanage.php?action=download&filename={$filename}" alt="{$filename}"/>
endof;
}

function showText($filename) {
    echo <<<endof
    <div id="text">
	<iframe src="test.txt" name=iframe1></iframe>
	</div>
    <script type="text/javascript">
        var div = document.getElementById("text");
        //div.textContent = "{$filename}";
    </script>
endof;
}
?>