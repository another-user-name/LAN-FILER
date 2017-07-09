<?php 
session_start();

$indexPage = 'filemanage.php';

if (isset($_SESSION['user']) == false && isset($_REQUEST['username']) == false) {?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="/js/jquery.min.js"></script>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
	<link rel="stylesheet" href="/css/bootstrap.min.css">
	<link rel="stylesheet" href="/css/font-awesome.min.css">
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/mylib.js"></script>
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
				$.post("login.php", {'action':'login', 'username':username, 'password':password}, function(data1, status) {
		            	var myjson = $.parseJSON(data1);
						if (myjson['status'] == 'success') {
							window.location.href = myjson['link'];
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
		function keypress(element) {
			changeClass(element, '');
			if (event.keyCode == 13) {
				if (element == 'usernamediv') {
					$("#password").focus();
				} else {
					login();
				}
			}
		}
		</script>
		<div class="container center bg-success" style="max-width:300px;margin-top:100px;">
		  <br />
		  <div id="usernamediv">
		  <input type="email" class="form-control btn-block" id="username" placeholder="Enter Username" onkeypress="keypress('usernamediv');changeText('warning','')">
		  </div>
		  <br />
		  <div id="passworddiv">
		  <input type="password" class="form-control btn-block" id="password" placeholder="Password" onkeypress="keypress('passworddiv');changeText('warning','')">
		  </div>
		  <br />
		  <button class="btn btn-default btn-block" onclick="login()" style="width:100%;">Login</button>
		  <br />
		  <p id='warning'></p>
		</div>
  </body>
</html>
<?php
} else if (isset($_REQUEST['username']) && isset($_REQUEST['password'])) {
	$user = $_REQUEST['username'];
	$pwd = $_REQUEST['password'];
	if ($user == 'admn' && $pwd='6666662333') {
		$_SESSION['user'] = $user;
		$ret = array('status' => 'success');
		if (isset($_SESSION['lastPage'])) {
			$ret['link'] = $_SESSION['lastPage'];
		} else {
			$ret['link'] = $indexPage;
		}
		echo json_encode($ret);
	}  else {
		$ret = array('status' => 'fail');
		echo json_encode($ret);
	}
} else if (isset($_SESSION['user'])) {
?>
<script type="text/javascript">
	window.location.href=<?php
	if (isset($_SESSION['lastPage'])) {
		echo "\"{$_SESSION['lastPage']}\"";
	} else {
		echo "\"{$indexPage}\"";
	}
	?>
</script>
<?php
} else {
	$ret = array('status' => 'fail');
	echo json_encode($ret);
}
session_write_close();
?>