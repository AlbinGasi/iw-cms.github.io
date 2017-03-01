<?php
if(Users::is_loggedin()){
	unset($_SESSION['user_ag']);
	session_unset();
	session_destroy();
	Alerts::get_alert("info","Successfuly logged out");
}
?>