<?php

session_start();

$set = new stdClass();
$page = new stdClass();
$page->navbar = array();

define("APP_ROOT", dirname(dirname(__FILE__)));

include "settings.php";

include APP_ROOT."/lib/mysql.class.php";
include APP_ROOT."/lib/users.class.php";
include APP_ROOT."/lib/presets.class.php";
include APP_ROOT."/lib/options.class.php";


$db = new SafeMySQL(array(
	'host' 	=> $set->db_host,
	'user'	=> $set->db_user,
	'pass'	=> $set->db_pass,
	'db'=> $set->db_name));

	if(!($db_set = $db->getRow("SELECT * FROM `".APP_PREFIX."settings` LIMIT 1"))) {
		header("Location: install.php");
		exit;
	}

	$set = (object)array_merge((array)$set,(array)$db_set);

	$presets = new presets;
	$user = new User($db);
	$options = new Options;

	if(!$user->islg() && isset($_COOKIE['user']) && isset($_COOKIE['pass'])) {
		if($usr = $db->getRow("SELECT `userid` FROM `".APP_PREFIX."users` WHERE `username` = ?s AND `password` = ?s", $_COOKIE['user'], $_COOKIE['pass'])) {
			$_SESSION['user'] = $usr->userid;
			$user = new User($db);
		}

	} else {

		$time = time();

		if(!isset($_SESSION['last_log']))
		$_SESSION['last_log'] = 0;

		if($_SESSION['last_log'] < $time - 60 * 2){
			$db->query("UPDATE `".APP_PREFIX."users` SET `lastactive` = '".$time."' WHERE `userid`='".$user->data->userid."'");
			$_SESSION['last_log'] = $time;
		}
	}
