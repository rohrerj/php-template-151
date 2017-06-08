<?php
namespace rohrerj\Controller;

use rohrerj\SimpleTemplateEngine;
use rohrerj\service\register\RegisterService;

class RegisterController {
	
	private $template;
	private $registerService;
	private $mailer;
	
	public function __construct(SimpleTemplateEngine $template, RegisterService $registerService)
	{
		$this->template = $template;
		$this->registerService = $registerService;
	}
	public function showRegister() {
		echo $this->template->render("register.html.php");
	}
	public function register(array $data) {
		if(!array_key_exists("email", $data) OR !array_key_exists("password1", $data) OR !array_key_exists("vorname", $data) OR
				!array_key_exists("nachname", $data) OR !array_key_exists("password2", $data) OR $data["email"]=="" OR $data["password1"]=="" OR $data["vorname"]=="" OR $data["nachname"] == "") {
			echo "Error 1";
			
			$this->showRegister();
		}
		else {
			if($data["password1"] != $data["password2"]) {
				$this->showRegister();
				echo "Error 2";
				$this->showRegister();
			}
			else {
				if($this->registerService->register($data["email"], $data["vorname"], $data["nachname"], $data["password1"])) {
					header("Location: /");
				}
				else {
					echo "Error";
					$this->showRegister();
				}
			}
			
		}
	}
}