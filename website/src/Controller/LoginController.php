<?php

namespace rohrerj\Controller;

use rohrerj\SimpleTemplateEngine;
use rohrerj\service\login\LoginService;
use rohrerj\service\security\CSRFService;

class LoginController 
{
  /**
   * @var ihrname\SimpleTemplateEngine Template engines to render output
   */
  private $template;
  private $loginService;
  private $csrfService;
  private $mailer;
  
  /**
   * @param ihrname\SimpleTemplateEngine
   */
  public function __construct(SimpleTemplateEngine $template, LoginService $loginService, CSRFService $csrfService,\Swift_Mailer $mailer)
  {
     $this->template = $template;
     $this->loginService = $loginService;
     $this->csrfService = $csrfService;
     $this->mailer = $mailer;
  }

  public function showLogin() {
  	echo $this->template->render("login.html.php",["csrf" => $this->csrfService->getHtmlCode("csrfLogin")]);
  }
  public function login(array $data) {
  	
  	if(array_key_exists("csrf", $data)) {
  		if(!$this->csrfService->validateToken("csrfLogin", $data["csrf"])) {
  			//$this->showLogin();
  			echo "<br>csrf";
  			return;
  		}
  	}
  	else {
  		$this->showLogin();
  		return;
  	}
  	if(!array_key_exists("email", $data) OR !array_key_exists("password", $data)) {
  		$this->showLogin();
  		return;
  	}
  	
  	if($this->loginService->authenticate($data["email"],$data["password"])) {
  		if($this->loginService->prepareSession($data["email"])) {
  			header("Location: /");
  			return;
  		}
  		else {
  			$this->showLogin();
  			return;
  		}
  	}
  	else {
  		//echo $this->template->render("login.html.php",["email" => $data["email"],"csrf" => $this->csrfService->getHtmlCode("csrfLogin")]);
  		$this->showLogin();
  		echo "wrong username / password";
  	}
  }
  public function showForgotPassword() {
  	echo $this->template->render("forgotPassword.html.php",["csrf" => $this->csrfService->getHtmlCode("csrfForgotPassword")]);
  }
  public function forgotPassword(array $data) {
  	if(array_key_exists("csrf", $data)) {
  		if(!$this->csrfService->validateToken("csrfForgotPassword", $data["csrf"])) {
  			return;
  		}
  	} else {
  		$this->showForgotPassword();
  		return;
  	}
  	if(!array_key_exists("email", $data)) {
  		$this->showForgotPassword();
  	}
  	$url = $this->loginService->forgotPassword($data["email"]);
  	if($url != null) {
  		$this->mailer->send(
  				\Swift_Message::newInstance("Password vergessen")
  				->setContentType("text/html")
  				->setFrom(["gibz.module.151@gmail.com" => "WebProject"])
  				->setTo($data["email"])
  				->setBody("Password zurücksetzen<br><a href=https://".$_SERVER['HTTP_HOST']."/activate?url=".$url.">Link</a>")
  		);
  		header("Location: /");
  	}
  }
  public function logout() {
  	session_destroy();
  	header("Location: /");
  }
}
