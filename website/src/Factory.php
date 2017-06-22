<?php
namespace rohrerj;

class Factory {
	private $config;
	public function __construct(array $config) {
		$this->config = $config;
	}
	
	public function GetTemplateEngine() {
		return new SimpleTemplateEngine(__DIR__ . "/../templates/");
	}
	public function GetIndexController() {
		return new Controller\IndexController($this->GetTemplateEngine());
	}
	public function GetPDO() {
		return new \PDO(
				"mysql:host=mariadb;dbname=app;charset=utf8",
				$this->config["database"]["user"],
				"my-secret-pw",
				[\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
	}
	public function GetLoginService() {
		return new service\login\LoginPDOService($this->GetPDO(),$this->getPasswordService());
	}
	public function GetLoginController() {
		return new Controller\LoginController($this->GetTemplateEngine(),$this->GetLoginService(),$this->getCSRFService(),$this->getMailer());
	}
	public function GetRegisterController() {
		return new Controller\RegisterController($this->GetTemplateEngine(), $this->GetRegisterService(),$this->getMailer(),$this->getCSRFService(),$this->getFileService());
	}
	public function GetRegisterService() {
		return new service\register\RegisterPDOService($this->GetPDO(),$this->getPasswordService());
	}
	public function getMailer()
	{
		return \Swift_Mailer::newInstance(
				\Swift_SmtpTransport::newInstance("smtp.gmail.com", 465, "ssl")
				->setUsername("gibz.module.151@gmail.com")
				->setPassword("Pe$6A+aprunu")
				);
	}
	public function getCSRFService() {
		return new service\security\CSRFService();
	}
	public function getPasswordService() {
		return new service\security\PasswordService();
	}
	public function getFileController() {
		return new Controller\FileController($this->GetTemplateEngine(),$this->getFileService(),$this->getCSRFService());
	}
	public function getFileService() {
		return new service\file\FilePDOService($this->GetPDO());
	}
	public function getAdminController() {
		return new Controller\AdminController($this->GetTemplateEngine(), $this->getCSRFService(),$this->getAdminService());
	}
	public function getAdminService() {
		return new service\admin\AdminPDOService($this->GetPDO());
	}
}
