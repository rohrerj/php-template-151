<?php

error_reporting(E_ALL);

require_once("../vendor/autoload.php");
$tmpl = new rohrerj\SimpleTemplateEngine(__DIR__ . "/../templates/");

switch($_SERVER["REQUEST_URI"]) {
	case "/":
		(new rohrerj\Controller\IndexController($tmpl))->homepage();
		break;
	case "/login":
		(new rohrerj\Controller\LoginController($tmpl))->showLogin();
		break;
	default:
		$matches = [];
		if(preg_match("|^/hello/(.+)$|", $_SERVER["REQUEST_URI"], $matches)) {
			(new rohrerj\Controller\IndexController($tmpl))->greet($matches[1]);
			break;
		}
		echo "Not Found";
}

