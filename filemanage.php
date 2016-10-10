<?php
session_start();
$ip_addr = $_SERVER['SERVER_ADDR'];
$path = './';
if (isset($_GET['disk'])) {
	if (preg_match("/\w/", $_GET['disk']) > 0) {
		$path = $_GET['disk'] . ":/";
	}
}

function my_is_dir ($file) { 
	return ((fileperms("$file") & 0x4000) == 0x4000);
}

function get_files($dir) {
	$files = array();
	if (!is_dir($dir)) {
		return $files;
	}
	
	$handle = opendir($dir);
	if ($handle) {
		while (false != ($file = readdir($handle))) {
			$filename = $dir . '/' . $file;
			if (is_dir($filename)) {
				$files[iconv("GB2312", "UTF-8//IGNORE", $file)] = "d";
			} else {
				$files[iconv("GB2312", "UTF-8//IGNORE", $file)] = "f";
			}
		}
		closedir($handle);
	}
	
	return $files;
}

function download($file, $downname) {
	$suffix = substr($file, strrpos($file, '.'));

	if (!file_exists($file)) {
	  echo $file . "<br />";
		die("error: file not exists.");
	}
	$fp = fopen($file, "r");
	$filesize = filesize($file);
	header("Content-type: application/octet-stream");
	header("Accept-Ranges: bytes");
	header("Accept-Length: " . $filesize);
	header("Content-Disposition: attachment; filename=" . $downname);
	$buffer = 1024;
	$filecount = 0;
	while (!feof($fp) && $filecount < $filesize) {
		$filecon = fread($fp, $buffer);
		$filecount += $buffer;
		echo $filecon;
	}
	fclose($fp);
}

if (isset($_SESSION['self']) && $_SESSION['self'] == 332) {
	if (isset($_GET['action'])) {
		if ($_GET['action'] == 'ajax') {
			if (isset($_GET["dir"])) {
				$path = iconv("UTF-8", "GB2312//IGNORE", $_GET["dir"]);
				if (strrpos($path, "/") != strlen($path) - 1) {
					$path = $path .  "/";
				}
				if (preg_match("@\w:@", $path) > 0) {
					$_SESSION['dir'] = $path;
				} else if (isset($_SESSION['dir'])) {
					$_SESSION['dir'] = $_SESSION['dir'] . $path;
				} else {
					$_SESSION['dir'] = $path;
				}
				$path = $_SESSION['dir'];
				echo json_encode(get_files($path));
			} else if (isset($_GET['pdir'])) {
				if (isset($_SESSION['dir'])) {
					$path = $_SESSION['dir']; 
				} else {
					$path = '..';
				}
				if (strrpos($path, '/') == ($len = strlen($path) - 1)) {
					$path = substr($path, 0, $len);
				}
				$path = substr($path, 0, strrpos($path, '/'));
				$path .= "/";
				$_SESSION['dir'] = $path;
				echo json_encode(get_files($path));
			} else {
				
			}
		} else if ($_GET['action'] == 'download') {
			if (isset($_GET['filename'])) {
				$filename = iconv("UTF-8", "GB2312//IGNORE", $_GET['filename']);
				header("Content-type:text/html;cjarset=utf-8");
				if (!isset($_SESSION['dir'])) {
					$_SESSION['dir'] = './';
				}
				download($_SESSION['dir'] . $filename, $filename);
			}
		} else if ($_GET['action'] == 'upload') {
			if ($_FILES["file"]["error"] > 0) {
				echo "              Error :" . $_FILES["file"]["error"] . "<br />\n";
			} else {
				$name = iconv("UTF-8", "gb2312", $_FILES["file"]["name"]);
				echo "              Upload: " . $_FILES["file"]["name"] . "<br />\n";
				echo "              Type: " . $_FILES["file"]["type"] . "<br />\n";
				echo "              Size: " . ($_FILES["file"]["size"] / 1024) . " KiB<br />\n";
				echo "              Stored in: " . $_FILES["file"]["tmp_name"] . "<br />";
				move_uploaded_file($_FILES["file"]["tmp_name"], "./upload-files/" . $name);
				echo "              File are saved as: " . "upload-files/" . $_FILES["file"]["name"];
			}
		}
	} else {
		formalHtml($ip_addr, $path);
	}
} else {
	if (isset($_GET['action']) && $_GET['action'] == 'login') {
		$return = array();
		if (isset($_GET['username']) && isset($_GET['password'])) {
			if ($_GET['username'] == 'admin' && $_GET['password'] == '6666662333') {
				$return['status'] = 'success';
				$_SESSION['self'] = 332;
			} else {
				$return['status'] = 'fail';
			}
		} else {
			$return['status'] = 'fail';
		}
		echo json_encode($return);
	} else {
		defaulMainBody($ip_addr);
	}
}

function defaulMainBody($ip) {
	echo <<<myendsign
<!DOCTYPE html>
<html>
  <head>
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
		<script>
		function login() {
			var username = document.getElementById('username').value;
			var password = document.getElementById('password').value;
			if (username.length < 1 || password.length < 1) {
				if (username.length < 1) {
					document.getElementById('usernamediv').className  = "form-group has-error";
				}
				if (password.length < 1) {
					document.getElementById('passworddiv').className  = "form-group has-error";
				}
			} else {
				$.get("http://{$ip}/filemanage.php?action=login&username=" + username + '&password=' + password, function(data1, status) {
		            	var myjson = $.parseJSON(data1);
						if (myjson['status'] == 'success') {
							window.location.href = 'filemanage.php';
						} else {
							document.getElementById('warning').value = 'Username or password wrong try again please.';
						}
		            });
			}
		}
		function changeClass(element, className) {
			document.getElementById(element).className = className;
		}
		</script>
		<div class="container center" style="max-width:300px;">
		  <br />
		  <div id="usernamediv">
		  <input type="email" class="form-control" id="username" placeholder="Enter Username" onkeypress="changeClass('usernamediv', '')">
		  </div>
		  <br />
		  <div id="passworddiv">
		  <input type="password" class="form-control" id="password" placeholder="Password" onkeypress="changeClass('passworddiv', '')">
		  </div>
		  <br />
		  <button class="btn btn-default" onClick="login()" style="width:100%;">Login</button>
		  <p id='warning'></p>
		</div>
  </body>
  </html>
myendsign;
}

function formalHtml($ip, $path)
{
	echo <<<myendsign
<!DOCTYPE html>
<html>
  <head>
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
	<div style="margin:50px">
	<font size='5' color='blue'>DISK:</font>
	<br /><br />
	<table class="table">
	<tr>
myendsign;
	$fso = new COM('Scripting.FileSystemObject');
	foreach($fso->Drives as $driver) {
		$d0 = $fso->GetDrive($driver);
		$driver = $d0->DriveLetter;
		try {
			if (file_exists($driver . ":/")) {
				echo "<td><a href='javascript:getData(\"{$driver}:\")' class='button' style='margin:20px'><font size='5'>{$driver}</font></a></td>";
			}
		} catch (Exception $e) {
			
		}
	}
	
echo <<<myendsign
</tr>
</table>
<form action="filemanage.php?action=upload&" method="POST" enctype="multipart/form-data">
<div class="row">
    <div class="col-md-2 col-md-offset-4">
		<input type="text" class="btn btn-default" id="f_file" value="no file selected">
	</div>
	<div class="col-md-1">
		<input type="button" value="Select" class="btn btn-default" onClick="file.click()">
	</div>
    <div class="col-md-1">
		<input type="submit" name="submit" value="Upload" class="btn btn-default"></input>
	</div>
	<input name="file" type="file" id="file" onchange="f_file.value=this.value" class="btn btn-default" style="display:none">
</div>
</form>
myendsign;

echo <<<myendsign
	</div>
    <script type="text/javascript">
        function goback() {
            history.back();
        }
    </script>
  	<div class="container" align="center">
  	  <div class="row">
  	    <div class="container">
  	      <div class="col-md-12">
  	        <div class="container">
			  <table id="file_list" class="table">
			    <script type="text/javascript">
			      $(document).ready(function(){
			      	
			      	(function ($) {
		                $.getUrlParam = function (name) {
		                    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
		                    var r = window.location.search.substr(1).match(reg);
		                    if (r != null) return unescape(r[2]); return null;
		                }
		            })(jQuery);
		            path = $.getUrlParam("dir");

		            if (path == null || path == "") {
		            	path = "{$path}";
		            }

		            $.get("http://{$ip}/filemanage.php?action=ajax&dir=" + path, function(data1, status) {
		            	var myjson = $.parseJSON(data1);
						$("#file_list").html("");
		            	$.each(myjson, makeFile);
		            });
			      });
				  
				  function makeFile(key, value) {
		            		if (value == "d") {
								if (key == "..") {
									var newRow = "<tr><td><i class='icon-folder-close-alt icon-2x'></i></td><td><a href=\"javascript:getUpFolder('" + key + "')\">" + "<i class='icon-arrow-up icon-2x'></i>" + "</a></td></tr>";
									var tr0 = $("#file_list tr").eq(0);
									if (tr0.size() == 0) {
										$(newRow).appendTo("#file_list");
									} else {
										tr0.after(newRow);
									}
								} else if (key == '.') {
								} else {
									$("<tr><td><i class='icon-folder-close-alt icon-2x'></i></td><td><a href=\"javascript:getData('" + key + "')\">" + key + "</a></td></tr>").appendTo("#file_list");
								}
			      			} else {
			      				//$("<tr><td><i class='icon-file-alt icon-2x'></i></td><td><a href='http://{$ip}/filemanage.php?action=download&filename=" + key + "'>" + key + "</a></td><td><a href='http://{$ip}/play.php?filename=" + key + "'>play</a></td></tr>").appendTo("#file_list");
								$("<tr><td><i class='icon-file-alt icon-2x'></i></td><td><a href='http://{$ip}/filemanage.php?action=download&filename=" + key + "'>" + key + "</a></td><td><a href='#'>play</a></td></tr>").appendTo("#file_list");
			      			}
		          }
				  
				  function getData(path) {
					  $.get("http://{$ip}/filemanage.php?action=ajax&dir=" + path, function(data1, stauts){
							var myjson = $.parseJSON(data1);
							$("#file_list").html("");
							$.each(myjson, makeFile);
					  });
				  }
				  function getUpFolder() {
					  $.get("http://{$ip}/filemanage.php?action=ajax&pdir=" + path, function(data1, stauts){
							var myjson = $.parseJSON(data1);
							$("#file_list").html("");
							$.each(myjson, makeFile);
					  });
				  }
			    </script>
  	          </table>
  	        </div>
  	      </div>
  	    </div>
  	  </div>
  	</div>
  </body>
</html>
myendsign;
}
session_write_close()
?>