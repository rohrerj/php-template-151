<?php

error_reporting(E_ALL);
session_start();
require_once("../vendor/autoload.php");
$config = parse_ini_file(__DIR__ . "/../config.ini",true);

$factory = new rohrerj\Factory($config);
$uri = $_SERVER["REQUEST_URI"];
if (strpos($uri, '?') !== false) {
	$uri = substr($uri, 0,strpos($uri, '?'));
}
if($_SERVER["REQUEST_METHOD"] == "PUT") {
	die();
}
switch($uri) {
	case "/":
		$factory->GetIndexController()->homepage();
		break;
	case "/login":
		$cnt = $factory->GetLoginController();
		if($_SERVER["REQUEST_METHOD"] == "GET") {
			$cnt->showLogin();
		}
		else {
			$cnt->login($_POST);
		}
		break;
	case "/logout": {
		$cnt = $factory->GetLoginController();
		$cnt->logout();
		break;
	}
	case "/register": {
		$cnt = $factory->GetRegisterController();
		if($_SERVER["REQUEST_METHOD"] == "GET") {
			$cnt->showRegister();
		}
		else {
			$cnt->register($_POST);
		}
		break;
	}
	case "/activate": {
		$cnt = $factory->GetRegisterController();
		if($_SERVER["REQUEST_METHOD"] == "GET") {
			$cnt->showSetPassword($_GET);
		}
		else {
			$cnt->setPassword($_POST);
		}
		break;
	}
	case "/forgotPassword": {
		$cnt = $factory->GetLoginController();
		if($_SERVER["REQUEST_METHOD"] == "GET") {
			$cnt->showForgotPassword();
		}
		else {
			$cnt->forgotPassword($_POST);
		}
		break;
	}
	default:
		$matches = [];
		if(preg_match("|^/hello/(.+)$|", $_SERVER["REQUEST_URI"], $matches)) {
			$factory->GetLoginController()->greet($matches[1]);
			break;
		}
		echo "Not Found";
		echo $uri;
}
