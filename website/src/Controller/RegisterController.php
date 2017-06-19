<?php
namespace rohrerj\Controller;

use rohrerj\SimpleTemplateEngine;
use rohrerj\service\register\RegisterService;
use rohrerj\service\security\CSRFService;

class RegisterController {
	
	private $template;
	private $registerService;
	private $mailer;
	private $csrfService;
	
	public function __construct(SimpleTemplateEngine $template, RegisterService $registerService,\Swift_Mailer $mailer, CSRFService $csrfService)
	{
		$this->template = $template;
		$this->registerService = $registerService;
		$this->mailer = $mailer;
		$this->csrfService = $csrfService;
	}
	public function showRegister() {
		echo $this->template->render("register.html.php",["csrf" => $this->csrfService->getHtmlCode("csrfRegister")]);
	}
	public function register(array $data) {
		if(array_key_exists("csrf", $data)) {
			if(!$this->csrfService->validateToken("csrfRegister", $data["csrf"])) {
				$this->showRegister();
				return;
			}
		}
		else {
			$this->showRegister();
			return;
		}
		if(!array_key_exists("email", $data) OR !array_key_exists("vorname", $data) OR
				!array_key_exists("nachname", $data) OR $data["email"]=="" OR $data["vorname"]=="" OR $data["nachname"] == "") {
			echo "Error 1";
			
			$this->showRegister();
		}
		else {
				
			$url = $this->registerService->register($data["email"], $data["vorname"], $data["nachname"]);
			if($url != null) {
				$this->mailer->send(
					\Swift_Message::newInstance("Registrierung")
					->setContentType("text/html")
					->setFrom(["gibz.module.151@gmail.com" => "WebProject"])
					->setTo($data["email"])
					->setBody("Registrierungsformular<br><a href=https://".$_SERVER['HTTP_HOST']."/activate?url=".$url.">Link</a>")
				);
				header("Location: /");
			}
			else {
				echo "Error";
				$this->showRegister();
			}
			
		}
	}
	public function showSetPassword(array $data) {
		if(!array_key_exists("url", $data) OR $data['url'] == "") {
			header("Location: /");
			return;
		}
		echo $this->template->render("setPassword.html.php",["csrf" => $this->csrfService->getHtmlCode("csrfSetPassword"),"url" => $data['url']]);
	}
	public function setPassword(array $data) {
		if(array_key_exists("csrf", $data)) {
			if(!$this->csrfService->validateToken("csrfSetPassword", $data["csrf"])) {
				die();
			}
		}
		else {
			die();
		}
		if(!array_key_exists("url", $data) OR $data['url'] == "") {
			header("Location: /");
			return;
		}
		if(!array_key_exists("password1", $data) OR $data['password1'] == "" OR !array_key_exists("password2", $data) OR $data['password2'] == "" OR $data['password1']!= $data['password2']) {
			$this->showSetPassword(['url' => $data['url']]);
			return;
		}
		if($this->registerService->setPassword($data['url'], $data['password1'])) {
			header("Location: /login");
		}
	}
}