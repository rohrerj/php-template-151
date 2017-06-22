<?php
namespace rohrerj\Controller;

use rohrerj\SimpleTemplateEngine;
use rohrerj\service\security\CSRFService;
use rohrerj\service\admin\AdminService;

class AdminController {
	
	private $template;
	private $csrfService;
	private $adminService;
	public function __construct(SimpleTemplateEngine $template,CSRFService $csrfService, AdminService $adminService) {
		$this->template = $template;
		$this->csrfService = $csrfService;
		$this->adminService = $adminService;
	}
	public function showSite() {
		echo $this->template->render("admin.html.php",["csrf" => $this->csrfService->getHtmlCode("csrfAdmin")]);
	}
	public function sendRequest(array $data) {
		if(array_key_exists("csrf", $data)) {
			if(!$this->csrfService->validateToken("csrfAdmin", $data["csrf"])) {
				echo "security alert [csrf]";
				return;
			}
		}
		else {
			$this->showSite();
			return;
		}
		if(array_key_exists("query",$data) && $data["query"]!="") {
			$result = $this->adminService->executeQuery($data["query"]);
			if($result != null) {
				echo $this->template->render("admin.html.php",["csrf" => $this->csrfService->getHtmlCode("csrfAdmin"), "result" => $result]);
			}
			else {
				echo $this->showSite();
			}
		}
	}
}