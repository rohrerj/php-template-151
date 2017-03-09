<?php

use ihrname\Controller;

error_reporting(E_ALL);

require_once("../vendor/autoload.php");
$tmpl = new ihrname\SimpleTemplateEngine(__DIR__ . "/../templates/");

switch($_SERVER["REQUEST_URI"]) {
	case "/":
		(new Controller\IndexController($tmpl))->homepage();
		break;
	case "/login":
		(new Controller\LoginController($tmpl))->showLogin();
		break;
	default:
		$matches = [];
		if(preg_match("|^/hello/(.+)$|", $_SERVER["REQUEST_URI"], $matches)) {
			(new ihrname\Controller\IndexController($tmpl))->greet($matches[1]);
			break;
		}
		echo "Not Found";
}

