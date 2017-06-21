<?php
namespace rohrerj\Controller;

use rohrerj\SimpleTemplateEngine;
use rohrerj\service\file\FileService;
use rohrerj\service\security\CSRFService;

class FileController {
	private $template;
	private $fileService;
	private $csrfService;
	public function __construct(SimpleTemplateEngine $template, FileService $fileService, CSRFService $csrfService) {
		$this->template = $template;
		$this->fileService = $fileService;
		$this->csrfService = $csrfService;
	}
	
	public function showFiles(array $data) {
		if($_SESSION["email"] != null) {
			$dir = '/';
			if(array_key_exists("dir",$data) && $data["dir"]!="") {
				$dir = $data['dir'];
			}
			$files = $this->fileService->showFiles($_SESSION["email"],$dir);
			echo $this->template->render("files.html.php",["files" => $files, "dir" => $dir,"csrf" => $this->csrfService->getHtmlCode("csrfShowFiles")]);
		}
	}
	//ajax method
	//$data = csrf,file
	public function delete(array $data) {
		if(array_key_exists("csrf", $data)) {
			if(!$this->csrfService->validateToken("csrfShowFiles", $data["csrf"])) {
				echo "invalid csrf token";
				return;
			}
		}
		else {
			echo "csrf token not set";
			return;
		}
		if($_SESSION["email"] != null) {
			if(array_key_exists("file",$data) && $data["file"]!="") {
				if($this->fileService->deleteFile($_SESSION["email"],$data["file"])) {
					header("HTTP/1.1 200 OK");
					return;
				}
			}
		}
		echo "command not processed";
		
		
		
	}
	//$data = dir
	public function upload(array $data, array $files) {
		if(array_key_exists("csrf", $data)) {
			if(!$this->csrfService->validateToken("csrfShowFiles", $data["csrf"])) {
				echo "invalid csrf token";
				return;
			}
		}
		else {
			echo "csrf token not set";
			return;
		}
		if($_SESSION["email"] != null) {
			if(array_key_exists("dir",$data) && $data["dir"]!="" && array_key_exists("file",$files)&&$files["file"]["name"] != "") {
				$name = $files["file"]["name"];
				if(preg_match("/^[a-zA-Z0-9_.-]*$/",$name)) {
					$id = $this->fileService->uploadFile($_SESSION["email"], $name, $data["dir"]);
					if($id != 0) {
						$path = $_SERVER['DOCUMENT_ROOT']."../../files/".$id.".dat";
						$path = realpath($path);
						if (move_uploaded_file($_FILES["file"]["tmp_name"], $path)) {
							echo "HTTP/1.1 200 OK";
						}
						
						return;
					}
				}
			}
		}
		echo "command not processed";
	}
	
	
	//ajax method
	//$data = csrf,name,dir
	public function create(array $data) {
		if(array_key_exists("csrf", $data)) {
			if(!$this->csrfService->validateToken("csrfShowFiles", $data["csrf"])) {
				echo "invalid csrf token";
				return;
			}
		}
		else {
			echo "csrf token not set";
			return;
		}
		
		if($_SESSION["email"] != null) {
			if(array_key_exists("name",$data) && $data["name"]!="" && array_key_exists("dir",$data) && $data["dir"]!="") {
				if(preg_match("/^[a-zA-Z0-9_-]*$/",$data["name"])) {
					if($this->fileService->createFolder($_SESSION["email"],$data["name"],$data["dir"])) {
						echo "HTTP/1.1 200 OK";
						return;
					}
				}
			}
		}
		echo "command not processed";
	}
	public function downloadFile(array $data) {
		
		if($_SESSION["email"] != null) {
			if(array_key_exists("file",$data) && $data["file"]!="") {
				$filename = $this->fileService->getFile($_SESSION["email"],$data["file"]);
				if($filename != null) {
					$path = $_SERVER['DOCUMENT_ROOT']."../../files/".$data["file"].".dat";
					if(file_exists($path) && is_file($path)) {
						header('Content-Description: File Transfer');
						header('Content-Type: application/octet-stream');
						header('Content-Disposition: attachment; filename="'.basename($path).'"');
						header('Expires: 0');
						header('Cache-Control: must-revalidate');
						header('Pragma: public');
						header('Content-Length: ' . filesize($path));
						readfile($path);
						exit;
					}
				}
				else {
					echo "filename is null";
				}
				
			}
			else {
				echo "file not defined";
			}
		}
		else {
			echo "not logged in";
		}
	}
}