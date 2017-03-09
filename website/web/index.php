<?php

error_reporting(E_ALL);

require_once("../vendor/autoload.php");
$tmpl = new mineichen\SimpleTemplateEngine(__DIR__ . "/../templates/");

switch($_SERVER["REQUEST_URI"]) {
	case "/":
		(new mineichen\Controller\IndexController($tmpl))->homepage();
		break;
	case "/testroute":
		echo "Test";
		break;
	case "/login":
		(new mineichen\Controller\LoginController($tmpl))->showLogin();
		break;
	default:
		$matches = [];
		if(preg_match("|^/hello/(.+)$|", $_SERVER["REQUEST_URI"], $matches)) {
			(new mineichen\Controller\IndexController($tmpl))->greet($matches[1]);
			break;
		}
		echo "Not Found";
}

