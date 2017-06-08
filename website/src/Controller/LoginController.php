<?php

namespace rohrerj\Controller;

use rohrerj\SimpleTemplateEngine;
use rohrerj\service\login\LoginService;

class LoginController 
{
  /**
   * @var ihrname\SimpleTemplateEngine Template engines to render output
   */
  private $template;
  private $loginService;
  
  /**
   * @param ihrname\SimpleTemplateEngine
   */
  public function __construct(SimpleTemplateEngine $template, LoginService $loginService)
  {
     $this->template = $template;
     $this->loginService = $loginService;
  }

  public function showLogin() {
  	echo $this->template->render("login.html.php");
  }
  public function login(array $data) {
  	if(!array_key_exists("email", $data) OR !array_key_exists("password", $data)) {
  		$this->showLogin();
  		return;
  	}
  	
  	if($this->loginService->authenticate($data["email"],$data["password"])) {
  		header("Location: /");
  	}
  	else {
  		echo $this->template->render("login.html.php",["email" => $data["email"]]);
  		echo "wrong username / password";
  	}
  }
  public function logout() {
  	if(isset($_SESSION["email"]) && $_SESSION["email"] != "") {
  		$_SESSION["email"] = "";
  		header("Location: /");
  	}
  }
}
