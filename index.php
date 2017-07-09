<?php
session_start();
echo <<<border
<!DOCTYPE html>
<html>
<head>
	<title>
		my web page
	</title>
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
border;

$mydb = new MyDB();

if (isset($_SESSION['uid']) && !isset($_GET['logout'])) {
	echo "<h1>welcome {$_SESSION['username']}</h1>
	<h1>your id is: {$_SESSION['uid']}</h1>
	<h1>your register on: {$_SESSION['rgdate']}</h1>
	";
	echo "<a href='index.php?logout=233'><button>logout</button></a>";
} else if (isset($_GET['register'])) {
	if (!isset($_GET['username']) || !isset($_GET['password'])) {
		defaulMainBody();
	} else {
		$username = $_GET['username'];
		$password = $_GET['password'];
		if (registerUser($username, $password) == 0) {
			jumpTo('index.php');
		} else {
			jumpTo('index.php');
		}
	}
} else {
	if (isset($_GET['username']) && isset($_GET['password'])) {
		$username = $_GET['username'];
		$password = $_GET['password'];
		$dbms = 'mysql';
		$host = 'localhost';
		$dbName = 'web';
		$user = 'root';
		$pass = '';
		$dsn = "$dbms:host=$host;dbname=$dbName";

		try {
			$dbh = new PDO($dsn, $user, $pass);
			$sql = "SELECT * FROM `userinfo` WHERE username=\"$username\"";
			$sth = $dbh->query($sql);
			if ($sth->rowCount() == 0) {
				echo "<h1>username or password error, try again please. <a href='index.php'>again</a></h1>
				<script typr='text/javascript'>
				setTimeout('window.location.href=\"index.php\"', 5);
				</script>
				";
			} else {
				$sth = $dbh->query($sql);
				$res = $sth->fetch(PDO::FETCH_ASSOC);
				$pwdmd5 = md5($password);
				if ($pwdmd5 == $res['pwdmd5']) {
					$_SESSION['uid'] = $res['id'];
					$_SESSION['username'] = $res['username'];
					$_SESSION['rgdate'] = $res['rgdate'];

					echo "<h1>login success. click <a href='index.php'>here</a> to return index or just wait for several seconds.</h1>
					<script typr='text/javascript'>
						setTimeout('window.location.href=\"index.php\"', 3);
					</script>
					";
				} else {
					echo "<h1>username or password error, try again please. <a href='index.php'>again</a></h1>
					<script typr='text/javascript'>
						setTimeout('window.location.href=\"index.php\"', 5);
					</script>
					";
				}
			}
		} catch (PDOException $e) {
			die ("Error: ". $e->getMessage() . "<br />");
		}
	} else {
		if (isset($_GET['logout'])) {
			if (isset($_SESSION['uid'])) {
				unset($_SESSION['uid']);
			}
			if (isset($_SESSION['rgdate'])) {
				unset($_SESSION['rgdate']);
			}
			if (isset($_SESSION['username'])) {
				unset($_SESSION['username']);
			}
		}
		defaulMainBody();
	}
}

if (isset($_POST['action'])) {
	switch ($_POST['action']) {
		case 'login':
		login();
		break;
		case 'logout':
		logout();
		break;
		case 'register':
		$username = $_GET['username'];
		$password = $_GET['password'];
		if (registerUser($username, $password) == 0) {
			jumpTo('index.php');
		} else {
			jumpTo('index.php');
		}
		break;
		case 'ajax':
		ajax();
		break;
		case 'close':
		userClose();
		break;
		default:
		
		break;
	}
} else if (isset($_SESSION['user'])) {
	$_SESSION['user']->$printUserInfo();
}

function userClose() {
	logout(false);
}

function register() {
	if (!isset($_GET['username']) || !isset($_GET['password'])) {
		defaulMainBody();
	} else {
		$username = $_GET['username'];
		$password = $_GET['password'];
		if (registerUser($username, $password) == 0) {
			jumpTo('index.php');
		} else {
			jumpTo('index.php');
		}
	}
}

function jumpTo($URL) {
	echo "<script type='text/javascript'>
	window.location.href='$URL';
</script>";
}

function defaulMainBody() {
	echo <<<border
	<script type='text/javascript'>
		function login() {
			if ($("#username").is(":hidden")) {
				$("#username").show();
				$("#password").show();
				$("#warning").hide();
				$("#warn").hide();
				$("#register").hide();
			} else {
				var username = document.getElementById('username').value;
				var password = document.getElementById('password').value;
				if (username.length < 1 || password.length < 1) {
					document.getElementById('warning').innerHTML = 'You have to type all the input area';
					showHidden('warning');
				} else {
					window.location.href = 'index.php?username=' + username + '&password=' + password;
				}
			}
		}
		function showHidden(element) {
			document.getElementById(element).style='display:block';
		}
		function hideObject(element) {
			document.getElementById(element).style='display:none';
		}
	</script>
	<div class='container center bg-success' style="max-width:500px;margin-top:100px;">
		<input id='username' type="email"  class="form-control btn-block" placeholder="Enter Username"></input>
		<input id='password' type="password" class="form-control btn-block" placeholder="Password"></input>
		<p id='warning' style='display:none, background=blue' background='#00ffff'></p>
		<button class="btn btn-default btn-block" onclick="login()" style="width:100%;">login</button>
		<section id='register' style='display:none'>
			<input id='usrname' class="form-control btn-block" placeholder="Enter Username"></input>
			<input id='pwd' type='password' class="form-control btn-block" placeholder="Password"></input>
			<input id='confirm' type='password' class="form-control btn-block" placeholder="Confirm Password"></input>
			<p id='warn' style='display:none, background=blue' background='#00ffff'></p>
		</section>
		<button class="btn btn-default btn-block" onClick="register()" style="width:100%;">register</button>
		<script type='text/javascript'>
			function register() {
				if ($("#register").is(":hidden")) {
					$("#register").show();
					$("#username").hide();
					$("#password").hide();
					$("#warning").hide();
					$("#warn").hide();
				} else {
					var username = document.getElementById('usrname').value;
					var password = document.getElementById('pwd').value;
					var confirm = document.getElementById('confirm').value;
					if (username == '' || password == '' || confirm == '') {
						document.getElementById('warn').innerHTML = 'You have to type all the input area';
						showHidden('warn');
					} else if (password != confirm) {
						document.getElementById('warn').innerHTML = 'You password is different from confirm.';
						showHidden('warn');
					} else {
						window.location.href = 'index.php?register=1&username=' + username + '&password=' + password;
					}
				}
			}
		</script>
	</div>
border;
}

function logout($jump = true) {
	if (isset($_SESSION['user'])) {
		unset($_SESSION['user']);
	}
	
	if (isset($_SESSION['right'])) {
		unset($_SESSION['right']);
	}
	
	if ($jump) {
		jumpTo('index.php');
	}
}

function registerUser($username, $password) {
	$dbms = 'mysql';
	$host = 'localhost';
	$dbName = 'web';
	$user = 'root';
	$pass = '';
	$dsn = "$dbms:host=$host;dbname=$dbName";
	
	try {
		$dbh = new PDO($dsn, $user, $pass);
		$sql = "SELECT * FROM `userinfo` WHERE username=\"$username\"";
		$sth = $dbh->query($sql);
		if ($sth->rowCount() == 0) {
			$pwdmd5 = md5($password);
			$sql = "insert into `userinfo`(username, pwdmd5, rgdate) values(\"$username\", \"$pwdmd5\", now())";
			if ($dbh->exec($sql) == 0) {
				echo "<h1>insert failure</h1>";
				$error = $dbh->errorInfo();
				foreach($error as $key=>$value) {
					echo "<h1>$key: $value</h1>";
				}
				return -1;
			} else {
				return 0;
			}
		} else {
			$res = $sth->fetch(PDO::FETCH_ASSOC);
			print_r($res);
			print("<br />");
			return -1;
		}
	} catch (PDOException $e) {
		die ("Error: ". $e->getMessage() . "<br />");
		return -1;
	}
}

function login() {
	$loginError = false;
	if (!isset($_POST['username']) || !isset($_POST['password'])) {
		$loginError = true;
	} else {
		$user = new User($_POST['username']);
		if ($user.$nouser) {
			$loginError = true;
		} else {
			$pwdmd5 = md5($_POST['password']);
			if ($pwdmd5 != $user->$pwdmd5) {
				$loginError = true;
			} else {
				$loginError = false;
			}
		}
	}
	
	if ($loginError) {
		//jumpTo('index.php');
		defaulMainBody();;
	} else {
		$_SESSION['user'] = $user;
		$userRighta = new UserRights($user->$uid);
		$_SESSION['right'] = $userRighta;
		
		$user->printUserInfo();
		
		if ($userRighta->canRead()) {
			printRead();
		}
		if ($userRighta->canWrite()) {
			printWrite();
		}
		if ($userRighta->canManage()) {
			printManage();
		}
		
		echo '<button class="btn btn-default btn-block" >logout</button>';
	}
}

class Right{
	private $right = 0;
	const READ = 0x01;
	const POST = 0x02;
	const MANAGE = 0x04;
	
	public function __construct($right) {
		if (is_int($right)) {
			$this->$right = $right;
		}
	}
	
	public function canRead($query = null) {
		if ($query == null) {
			$query = $this;
		}
		$right = $query->$right;
		if (!is_int($right)) {
			return false;
		}
		return ($right & READ != 0);
	}
	
	public function canWrite($query = null) {
		if ($query == null) {
			$query = $this;
		}
		$right = $query->$right;
		if (!is_int($right)) {
			return false;
		}
		return ($right & POST != 0);
	}
	
	public function canManage($query = null) {
		if ($query == null) {
			$query = $this;
		}
		$right = $query->$right;
		if (!is_int($right)) {
			return false;
		}
		return ($right & MANAGE != 0);
	}
	
	public function setRight($right) {
		$this->$right = $right;
	}
}

class UserRights {
	public $uid;
	public $right;
	public $info = '';
	public $class = 'user';
	public static $tableName = 'authority';
	
	public function __construct($uid) {
		$this->$uid = $uid;
		$this->$right = new Right(0);
		$data = $mydb.query($tableName, 'uid', $uid);
		if ($data != null) {
			foreach ($data as $column => $value) {
				if ($column == 'class' && $value != null) {
					$this->$class = $value;
				}
				else if ($column == 'right' && $value != null) {
					$this->$right.setRight($value);
				}
				else if ($column == 'info' && $value != null) {
					$this->$info = $value;
				}
			}
		}
	}
	
}

class User {
	public $uid;
	public $username;
	public $pwdmd5;
	public $rgdate;
	public $others;
	public $nouser = false;
	
	public function __construct($username) {
		$this->$username = $username;
		$this->$others = array();
		if ($this->$username != null && $this->$username != '') {
			$data = $mydb->query('userinfo', 'username', $this->$username);
			if ($data != null) {
				foreach ($data as $key => $value) {
					if ($key == 'id' && $value != null) {
						$this->$uid = $value;
					} else if ($key == 'username' && $value != null) {
						//$this->$uid = $value;
					} else if ($key == 'pwdmd5' && $value != null) {
						$this->$pwdmd5 = $value;
					} else if ($key == 'rgdate' && $value != null) {
						$this->$rgdate = $value;
					} else if ($value != null) {
						$this->$others[$key] = $value;
					}
				}
			} else {
				$this->$nouser = true;
			}
		} else {
			$this->$nouser = true;
		}
	}
	
	public function printUserInfo() {
		echo "<h1>welcome {$username}</h1>
		<h1>your id is: {$uid}</h1>
		<h1>your register on: {$rgdate}</h1>
		<h1>your user rank is: {$class}</h1>
		";
	}
}

class MyDB {
	public $dbms = 'mysql';
	public $host = 'localhost';
	public $dbName = 'web';
	public $user = 'root';
	public $pass = '';
	public static $dsn; //= "$dbms:host=$host;dbname=$dbName";
	public static $dbh;// = new PDO($dsn, $user, $pass);
	
	function __construct() {
		$dbms = 'mysql';
		$host = 'localhost';
		$dbName = 'web';
		$user = 'root';
		$pass = '';
		$dsn = "$dbms:host=$host;dbname=$dbName";
		try {
			$dbh = new PDO($dsn, $user, $pass);	
		} catch (PDOException $e) {
			die("ERROR: " . $e.getMessage() . "<br />");
		}
	}
	
	function query($tableName, $columnName, $queryWord) {	
		if ($dbh == null) {
			return null;
		}
		try {
			//$dbh = new PDO($dsn, $user, $pass);
			$sql = "SELECT * FROM `$tableName` WHERE $columnName=\"$queryWord\"";
			$sth = $dbh->query($sql);
			if ($sth->rowCount() == 0) {
				return null;
			} else {
				$res = $sth->fetch(PDO::FETCH_ASSOC);
				return $res;
			}
		} catch (PDOException $e) {
			//die ("Error: ". $e->getMessage() . "<br />");
			
			return null;
		}
	}
}

echo "</body>
</html>";
?>