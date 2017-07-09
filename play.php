<?php
include_once 'MyHistory.php';
include_once 'playlist.php';
include_once 'util.php';
include_once 'commonElement.php';
session_start();

const TMP_PATH = './tmp/';

if (isset($_SESSION['user']) == false) {
    $_SESSION['lastPage'] = $_SERVER['REQUEST_URI'];
    loginPage();
} else {
    if (!isset($_SESSION['history'])) {
        $_SESSION['history'] = new MyHistory();
    }
    $ip_addr = $_SERVER['SERVER_ADDR'];
    $_SESSION['history']->visit("http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    $ip = $_SERVER['SERVER_ADDR'];

    if (isset($_SESSION['playlist']) == false) {
        //$_SESSION['playlist'] = new PlayList('test.lst');
        $_SESSION['playlist'] = 'test.lst';
    }
    $playlist = new PlayList($_SESSION['playlist']);

    if (isset($_REQUEST['action'])) {

        switch ($_REQUEST['action']) {

            case 'content':
                echo echoContent($playlist);
                break;

            case 'list':
                $ret = array();
                if (isset($_REQUEST['filename'])) {
                    $filename = toUTF8($_REQUEST['filename']);
                    if ($playlist->containsFile($filename)) {
                        $playlist->cutFile($filename);
                        $ret['content'] = 'AddToList';
                    } else {
                        $playlist->addFile($filename);
                        $ret['content'] = 'removeFromList';
                    }
                    $playlist->writeback();
                    $ret['list'] = $playlist->getList();
                }
                echo json_encode($ret);
                break;

            case 'ajax':
                # code...
                break;

            case 'getlist':
                echo json_encode($playlist->getList());
                break;

            default:
                # code...
                break;
        }


    } else {
        $error = '';
        echo <<<endof
	<!DOCTYPE html>
	<html>
	<head>
		<title>Media Player</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<script type="text/javascript" src="/js/jquery.min.js"></script>
		<link rel="stylesheet" href="/css/bootstrap.min.css">
		<link rel="stylesheet" href="/css/bootstrap.min.css">
		<link rel="stylesheet" href="/css/font-awesome.min.css">
		<script src="/js/bootstrap.min.js"></script>
	</head>
	<body>
endof;
        myHeader();
        echo <<<endof
		<div id='content' align='center'>
endof;

        $error = echoContent($playlist);
        echo "</div>";

        if (isset($_REQUEST['filename'])) {
            $filename = substr($_REQUEST['filename'], strrpos($_REQUEST['filename'], '/'));
            $path = TMP_PATH;
            if (isset($_SESSION['dir'])) {
                $path = $_SESSION['dir'];
                if (strrpos($path, '/') != strlen($path) - 1) {
                    $path += '/';
                }
            }
            showPlayList(($path . $filename), $playlist);
        }

        echo <<<endof
			<div align="center">
				<h1 id='errorinfo'>{$error}</h1>
			</div>
			<script type='text/javascript'>
				window.onbeforeunload = function (event) {
					$.get("play.php?action=leave");
				}
			</script>
endof;
        myFooter();
        echo <<<endof
		</body>
	</html>
endof;
    }
}
function echoContent($playlist)
{
    $error = '';
    if (isset($_SESSION['user']) && isset($_REQUEST['filename']) && ($file = $_REQUEST['filename'])) {//isset($_SESSION['user']) &&
        $filename = substr($file, strrpos($file, '/'));
        $path = '';
        $filename = localConv($filename);
        $file = localConv($file);
        if (myFileExists('', $file) == false) {
            $path = TMP_PATH;
            if (isset($_SESSION['dir'])) {
                $path = $_SESSION['dir'];
                if (strrpos($path, '/') != strlen($path) - 1) {
                    $path += '/';
                }
            }
            if (myFileExists($path, $filename) == false) {
                $error = 'File Not Found: ' . $path . $filename;
                $error = toUTF8($error);
                $filename = '';
            }
        } else {
            $filename = $file;
        }

        if (strlen($filename) == 0) {
        } else {
            $end = substr($filename, strrpos($filename, '.') + 1);
            if (strlen($end) == 0) {
                $error = 'File Type Not Support';
            } else {
                $end = strtolower($end);
                switch ($end) {
                    case 'mp4':
                        playMP4(toUTF8($path . $filename));
                        break;
                    case 'mp3':
                        //$playlist = new PlayList('test.lst');
                        playMP3(toUTF8($path . $filename), $playlist);
                        break;
                    case 'jpg':
                    case 'bmp':
                    case 'jpeg':
                    case 'gif':
                    case 'png':
                    case 'webp':
                        showPicture(toUTF8($path . $filename));
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
                        showText(toUTF8($path . $filename));
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
    return $error;
}

function showPlayList($filename, $playlist)
{
    $buttonText = 'AddToList';
    if ($playlist->containsFile(localConv($filename))) {
        $buttonText = 'RemoveFromList';
    }
    echo <<<endof
	<div class="container center">
	    <button id="loop_button" class="btn btn-default" onClick="loopPlay()">
	        Loop
	    </button>
		<button id="add_cut_button" class="btn btn-default" onclick="addOrCut()">
	    	{$buttonText}
	    </button>
	    <button id="" class="btn btn-default" onclick="playlist(0)">
	    	PlayList
	    </button>
	    <button id="" class="btn btn-default" onclick="playlist(1)">
	    	Random
	    </button>
	    <button id="" class="btn btn-default" onclick="playnext()">
	    	Next
	    </button>
	</div>
	<br />
	<div class="container">
    	<table id='playlisttbl' class='table table-condensed' align='center'>
endof;

    $list = $playlist->getList();

    foreach ($list as $key => $value) {
        $key = get_file_name($key);
        echo "<tr><td><a href='play.php?filename={$value}'>{$key}</a></td><td><a href='javascript:addOrCut(\"{$value}\")'>RemoveFromList</a><td></td></tr>";
    }
    echo <<<endof

    	</table>
    </div>
    <script type="text/javascript">
    var playing_mode = -1;
    var num_of_playing = 0;
    var mediaes = [];
    var playings = [];
    var isLoop = false;

    $(document).ready(function(){
    	$.get("play.php?action=getlist", function(data1, status){
    		mediaes = $.parseJSON(data1);
    	});
    });
    function loopPlay() {
        if ($("#media").attr('loop') == 'loop') {
            $("#media").prop('loop', false);
            $("#loop_button").html('Loop');
        } else {
            $("#media").attr('loop', 'loop');
            $("#loop_button").html('Unloop');
        }
        console.log($("#media").attr('loop'));
    }
    function playlist(mode = 0) {
    	if (mode >= 1) {
    		playing_mode == mode;
    	}
    	if (playing_mode == -1) {
    		num_of_playing = 0;
    		playing_mode = mode;
    	} 
    	if(playing_mode == 0) {
    		var count = 0;
    		playings = playings.splice(0, playings.length);
    		for (key in mediaes) {
    			playings[count] = key;
    			count++;
    		}
    		playnext();
    	} else if (playing_mode == 1) {
    		randomPlay();
    		playnext();
    	}
    }

    function playnext() {
    	while (num_of_playing < playings.length && typeof mediaes[playings[num_of_playing]] == 'undefined') {
    		playings.splice(num_of_undefined, 1);
    	}
    	if (num_of_playing ==  playings.length) {
    		 num_of_playing = 0;
    	}
    	playmedia(mediaes[playings[num_of_playing]]);
    	num_of_playing = (num_of_playing + 1) % playings.length;
    }

    function randomPlay() {
    	//playings = playings.splice(0, playings.length);
    	var idx = 0;
    	for(key in mediaes) {
    		playings[idx] = '';
    		idx++;
    	}

    	length = idx;

    	for(key in mediaes) {
    		idx = Math.floor(Math.random() * length) % length;
    		while (playings[idx] != '') {
    			idx = (idx + 1) % length;
    		}
    		playings[idx] = key;
    	}
    }

    function playmedia(filename) {    	
    	volume = 0.2;
    	$('#media').trigger('pause');
    	$("#content").html('');
    	$.post("play.php", {'action':'content', 'filename':filename}, function(data1, status){
    		$("#content").html(data1);
    		$('#media').bind('ended', playnext);
    		document.getElementById('media').volume = 0.2;
    	});
    }

    function addOrCut(filename = '') {
    	if (filename == '') {
    		filename = '{$filename}';
    	}
    	$.post("play.php", {'action':'list', 'filename':filename, 'playlist:':''}, function(data1, status) {
        	var myjson = $.parseJSON(data1);
			$("#add_cut_button").html(myjson['content']);
			var list = myjson['list'];
			mediaes = list;
			$('#playlisttbl').html('');
			$.each(list, function(key, value){
				///////////////////////////////////////////////////////////////////////
				///////////////////////////////////////////////////////////////////////
				///////////////////////////////////////////////////////////////////////
				key = key.substring(key.lastIndexOf("/") + 1);
				key = key.substring(key.lastIndexOf("\\\\") + 1);
				$("<tr><td><a href='play.php?filename=" + value + "'>" + key + "</a></td><td><a href='javascript:addOrCut(\"" + value + "\")'>RemoveFromList</a></td></tr>").appendTo('#playlisttbl');
			});
        });
    }
    </script>
endof;
}

function extraFilename($filename)
{
    $ret = substr($filename, strrpos($filename, '/') + 1);
    $ret = substr($ret, 0, strrpos($ret, '.'));
    return $ret;
}

function myFileExists($path, $filename)
{
    if (file_exists($path . $filename)) {
        return true;
    }
    $convName = toUTF8($path . $filename);
    if (isset($_SESSION['convFailFiles'][$convName]) && file_exists($_SESSION['convFailFiles'][$convName])) {
        return true;
    }
    $convName = substr($convName, strrpos($convName, '/') + 1);
    if (isset($_SESSION['convFailFiles'][$convName]) && file_exists($_SESSION['convFailFiles'][$convName])) {
        return true;
    }
    return false;
}

function playMP4($filename)
{
    $file = extraFilename($filename);
    echo <<<endof
	<div class="panel panel-default">
	<h2>Playing: {$file}</h2>
    <video id='media' controls="controls" style="border:1px;max-height:70%;max-width:80%;" autoplay="autoplay">
        <source src="media.php?filename={$filename}" type="video/mp4" />
        <!--object data="media.php?filename={$filename}">
            <embed src="media.php?filename={$filename}" />
        </object-->
    </video>
    </div>
endof;
}

function playMP3($filename, $playlist)
{
    $file = extraFilename($filename);
    echo <<<endof
	<div class="panel panel-default">
		<h2>Playing: {$file}</h2>
	    <audio id='media' controls="controls" style="border:1px;min-width:500px;max-width:800px;max-height:100px;" autoplay="autoplay">
	      <source src="media.php?filename={$filename}" type="audio/mp3" />
	      <embed height="100px" width="500px" src="media.php?filename={$filename}" />
	    </audio>
    </div>
endof;
}

function showPicture($filename)
{
    echo <<<endof
	<div class="center panel panel-default">
		<img src="media.php?filename={$filename}" alt="{$filename}"/>
	</div>
endof;
}

function showText($filename)
{
    echo <<<endof
    <div id="text" class="panel panel-default">
	<iframe src="media.php?filename={$filename}" name="iframe1" width="100%" onload="this.height=iframe1.document.body.scrollHeight" frameborder="0"></iframe>
	</div>
endof;
}

function copyFile($src, $dst)
{
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

function delFile($filename)
{
    return unlink($filename);
}

session_write_close();
?>