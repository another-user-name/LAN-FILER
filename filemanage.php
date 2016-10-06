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

	//$files[iconv("GB2312", "UTF-8//IGNORE", $dir . "--dir")] = "d";
	//$files[iconv("GB2312", "UTF-8//IGNORE", $_SESSION['dir'])] = "{$_SESSION['dir']}";
	
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

if (isset($_GET['u']) && isset($_GET['p'])) {
	$username = $_GET['u'];
	$password = $_GET['p'];
	if ($username == 'pfh' &&  $password == '233') {
		$_SESSION['self'] = 332;
	}
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
		}
	} else {
		formalHtml($ip_addr, $path);
	}
} else {
	echo "<h1>You don't have the permission to accessing this page.</h1>";
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
									//$("<tr><td><i class='icon-folder-close-alt icon-2x'></i></td><td><a href=\"javascript:getUpFolder('" + key + "')\">" + "<i class='icon-arrow-up icon-2x'></i>" + "</a></td></tr>").appendTo("#file_list");
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
			      				$("<tr><td><i class='icon-file-alt icon-2x'></i></td><td><a href='http://{$ip}/filemanage.php?action=download&filename=" + key + "'>" + key + "</a></td><td><a href='http://{$ip}/play.php?filename=" + key + "'>play</a></td></tr>").appendTo("#file_list");
			      			}
		          }
				  
				  function getData(path) {
					  $.get("http://{$ip}/filemanage.php?action=ajax&dir=" + path, function(data1, stauts){
							var myjson = $.parseJSON(data1);
							$("#file_list").html("");
							$.each(myjson, makeFile);
							/*
							function(key, value) {
								if (value == "d") {
									if (key == "..") {
										var newRow = "<tr><td><i class='icon-folder-close-alt icon-2x'></i></td><td><a href=\"javascript:getUpFolder('" + key + "')\">" + "<i class='icon-arrow-up icon-2x'></i>" + "</a></td></tr>";
										//$("#file_list tr:eq(0)").after(newRow);
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
									$("<tr><td><i class='icon-file-alt icon-2x'></i></td><td><a href='http://{$ip}/filemanage.php?action=download&filename=" + key + "'>" + key + "</a></td></tr>").appendTo("#file_list");
								}
							}
							*/
					  });
				  }
				  function getUpFolder() {
					  $.get("http://{$ip}/filemanage.php?action=ajax&pdir=" + path, function(data1, stauts){
							var myjson = $.parseJSON(data1);
							$("#file_list").html("");
							$.each(myjson, makeFile);
							/*
							function(key, value) {
								if (value == "d") {
									//$("<p><li class='icon-folder-close-alt icon-2x'><a href=\"javascript:getData('" + path + "/" + key + "')\">" + key + "</a></li></p>").appendTo("#file_list");
									if (key == "..") {
										//$("<tr><td><i class='icon-folder-close-alt icon-2x'></i></td><td><a href=\"javascript:getUpFolder('" + key + "')\">" + "<i class='icon-arrow-up icon-2x'></i>" + "</a></td></tr>").appendTo("#file_list");
										var newRow = "<tr><td><i class='icon-folder-close-alt icon-2x'></i></td><td><a href=\"javascript:getUpFolder('" + key + "')\">" + "<i class='icon-arrow-up icon-2x'></i>" + "</a></td></tr>";
										//$("#file_list tr:eq(0)").after(newRow);
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
									$("<tr><td><i class='icon-file-alt icon-2x'></i></td><td><a href='http://{$ip}/filemanage.php?action=download&filename=" + key + "'>" + key + "</a></tdi></tr>").appendTo("#file_list");
								}
							}
							*/
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

?>