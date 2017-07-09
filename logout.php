<?php
include_once 'util.php';
session_start();
if (isset($_SESSION['user'])) {
	unset($_SESSION['user']);
}
if (isset($_SESSION['palylist'])) {
    unset($_SESSION['playlist']);
}
loginPage();
session_write_close();
?>