<?php
session_start();
include_once 'fileOperation.php';
$ip_addr = $_SERVER['SERVER_ADDR'];
if (isset($_REQUEST['disk'])) {
	if (preg_match("/\w/", $_REQUEST['disk']) > 0) {
		$path = $_REQUEST['disk'] . ":/";
	}
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

if (isset($_SESSION['dir']) == false) {
	$_SESSION['dir'] = "/";
}

if (isset($_SESSION['self']) && $_SESSION['self'] == 332) {
	if (isset($_REQUEST['action'])) {
		if ($_REQUEST['action'] == 'ajax') {
			if (isset($_REQUEST["dir"])) {
				//$path = iconv("UTF-8", "GB2312//translit", $_REQUEST["dir"]);
				$path = $_REQUEST["dir"] .  addEndToDirName($path);
				if (preg_match("@\w:@", $path) > 0) {
					$_SESSION['dir'] = $path;
				} else if (isset($_SESSION['dir'])) {
					$_SESSION['dir'] = $_SESSION['dir'] . $path;
				} else {
					$_SESSION['dir'] = $path;
				}
				$path = $_SESSION['dir'];
				echo json_encode(get_files($path));
			} else if (isset($_REQUEST['pdir'])) {
				if (isset($_SESSION['dir'])) {
					$path = $_SESSION['dir']; 
				} else {
					$path = getcwd();
				}
				if (strrpos($path, '/') == ($len = strlen($path) - 1)) {
					$path = substr($path, 0, $len);
				}
				$path = substr($path, 0, strrpos($path, '/'));
				$path = $path . "/";
				$_SESSION['dir'] = $path;
				echo json_encode(get_files($path));
			} else {
				
			}
		} else if ($_REQUEST['action'] == 'download') {
			if (isset($_REQUEST['filename'])) {
				$filename = $_REQUEST['filename'];
				header("Content-type:text/html;cjarset=utf-8");
				if (!isset($_SESSION['dir'])) {
					$_SESSION['dir'] = getcwd();
				}
				download($_SESSION['dir'] . $filename, $filename);
			}
		} else if ($_REQUEST['action'] == 'upload') {
			if ($_FILES["file"]["error"] > 0) {
				echo "              Error :" . $_FILES["file"]["error"] . "<br />\n";
			} else {
				$ret = array();
				$ret['status'] = 'fail';
				$name = $_FILES["file"]["name"];
				$name = substr($name, strrpos($name, '/'));
				if (move_uploaded_file($_FILES["file"]["tmp_name"], "./upload-files/" . $name) == false) {
					$ret['status'] = 'fail';
				} else if (isset($_SESSION['dir'])) {
					$_SESSION['dir'] = $_SESSION['dir'] . addEndToDirName($_SESSION['dir']);
					if (rename("./upload-files/" . $name, $_SESSION['dir'] . $name) == false) {
						$ret['status'] = 'fail';
					} else {
						$ret['status'] = 'success';
					}
				} else {
					$ret['status'] = 'success';
				}
				echo json_encode($ret);
			}
		} else if ($_REQUEST['action'] == 'logout') {
			unset($_SESSION['self']);
			$ret = array('status'=>'success');
			echo json_encode($ret);
		}
	} else {
		if (!isset($_SESSION['dir'])) {
			$_SESSION['dir'] = getcwd();
		}
		formalHtml($ip_addr, $_SESSION['dir']);
	}
} else {
	if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'login') {
		$return = array();
		if (isset($_REQUEST['username']) && isset($_REQUEST['password'])) {
			if ($_REQUEST['username'] == 'admin' && $_REQUEST['password'] == '6666662333') {
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
    <script src="http://{$ip}/js/bootstrap.min.js"></script>
  </head>
  <body>
		<script>
		function login() {
			var username = document.getElementById('username').value;
			var password = document.getElementById('password').value;
			if (username.length < 1 || password.length < 1) {
				if (username.length < 1) {
					changeClass('usernamediv', "form-group has-error");
				}
				if (password.length < 1) {
					changeClass('passworddiv', "form-group has-error");
				}
			} else {
				$.post("http://{$ip}/filemanage.php", {'action':'login', 'username':username, 'password':password}, function(data1, status) {
		            	var myjson = $.parseJSON(data1);
						if (myjson['status'] == 'success') {
							window.location.href = 'filemanage.php';
						} else {
							changeText('warning','Username or password wrong try again please.');
						}
		            });
			}
		}
		function changeClass(element, className) {
			document.getElementById(element).className = className;
		}
		function changeText(element, newText) {
			$('#' + element).text(newText);
		}
		</script>
		<div class="container center bg-success" style="max-width:300px;margin-top:100px;">
		  <br />
		  <div id="usernamediv">
		  <input type="email" class="form-control btn-block" id="username" placeholder="Enter Username" onkeypress="changeClass('usernamediv', '');changeText('warning','')">
		  </div>
		  <br />
		  <div id="passworddiv">
		  <input type="password" class="form-control btn-block" id="password" placeholder="Password" onkeypress="changeClass('passworddiv', '');changeText('warning','')">
		  </div>
		  <br />
		  <button class="btn btn-default btn-block" onClick="login()" style="width:100%;">Login</button>
		  <br />
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
    <script src="http://{$ip}/js/bootstrap.min.js"></script>
  </head>
  <body>
  <table class='table'>
  <tr>
  <td>
    <div style="margin:30px;margin-bottom:0px;">
		<a href="javascript:goback()">
			<h1>
				<i class="icon-arrow-left"></i>&nbsp;&nbsp;Go Back
			</h1>
		</a>
	</div>
	</td>
	<td align="right">
	<div style="margin:30px;margin-bottom:0px;">
		<a href="javascript:logout()">
			<h1>
				Log Out&nbsp;&nbsp;
			</h1>
		</a>
	</div>
	</td>
	</tr>
	</table>
	<div style="margin:30px">
	<table class="table table-condensed">
	<tr>
	<td>
	<font size='5' color='blue'>DISK:</font>
	</td>
myendsign;
if (strpos(php_uname('s'),'Windows') != -1) {
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
}	
echo <<<myendsign
</tr>
</table>
<!--form id='uploadFileForm' action="filemanage.php?action=upload" method="POST" onsubmit="return uploadFile()"-->
<div class="row">
    <div class="col-md-2 col-md-offset-4">
		<input type="text" class="btn btn-default" id="f_file" value="no file selected">
	</div>
	<div class="col-md-1">
		<input type="button" value="Select" class="btn btn-default" onClick="file.click()">
	</div>
    <div class="col-md-1">
		<input type="button" name="submit" value="Upload" onClick="uploadFile()" class="btn btn-default"></input>
	</div>
	<input name="file" type="file" id="file" onchange="f_file.value=this.value" class="btn btn-default" style="display:none">
</div>
<!--/form-->
myendsign;

echo <<<myendsign
	</div>
    <script type="text/javascript">
		function uploadFile() {
			var formData = new FormData();
			formData.append('file', $('#file')[0].files[0]);
			
			$.ajax({
				url: 'filemanage.php?action=upload',
				type: 'POST',
				cache: false,
				data: formData,
				processData: false,
				contentType: false
			}).done(function(res){
				var myjson = $.parseJSON(res); 
				if (myjson['status'] == 'success') {
					window.location.href = 'filemanage.php';
				} 
			}).fail(function(res){
				
			});
		}
        function goback() {
            history.back();
        }
		function logout() {
			$.post("http://{$ip}/filemanage.php", {'action':'logout'}, function(data, status) {
				var myjson = $.parseJSON(data);
				if (myjson['status'] == 'success') {
					window.location.href = "http://{$ip}/filemanage.php";
				}
			});
		}
    </script>
  	<div class="container" align="center">
  	  <div class="row">
  	    <div class="container">
  	      <div class="col-md-12">
  	        <div class="container">
			  <table id="file_list" class="table table-condensed">
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

		            $.post("http://{$ip}/filemanage.php", {'action':'ajax', 'dir':path}, function(data1, status) {
		            	var myjson = $.parseJSON(data1);
						$("#file_list").html("");
		            	$.each(myjson, makeFile);
		            });
			      });
				  
				  function makeFile(key, value) {
		            		if (value == "d") {
								if (key == '.') {
								} else {
									$("<tr><td><i class='icon-folder-close-alt icon-2x'></i></td><td><a href=\"javascript:getData('" + key + "')\">" + key.substr(key.lastIndexOf('/') + 1) + "</a></td></tr>").appendTo("#file_list");
								}
			      			} else if (value == "f") {
								$("<tr><td><i class='icon-file-alt icon-2x'></i></td><td><a href='http://{$ip}/filemanage.php?action=download&filename=" + key + "'>" + key.substr(key.lastIndexOf('/') + 1) + "</a></td><td><a href='http://{$ip}/play.php?filename=" + key + "'>play</a></td></tr>").appendTo("#file_list");
			      			} else if (value == "p") {
								var newRow = "<tr><td><i class='icon-folder-close-alt icon-2x'></i></td><td><a href=\"javascript:getUpFolder('" + key + "')\">" + "<i class='icon-arrow-up icon-2x'></i>" + "</a></td></tr>";
								var tr0 = $("#file_list tr").eq(0);
								if (tr0.size() == 0) {
									$(newRow).appendTo("#file_list");
								} else {
									tr0.after(newRow);
								}
							}
		          }
				  
				  function getData(path) {
					  $.post("http://{$ip}/filemanage.php", {'action':'ajax', 'dir':path}, function(data1, stauts){
							var myjson = $.parseJSON(data1);
							$("#file_list").html("");
							$.each(myjson, makeFile);
					  });
				  }
				  function getUpFolder(path) {
					  $.post("http://{$ip}/filemanage.php", {'action':'ajax', 'pdir':path}, function(data1, stauts){
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
session_write_close();
?>