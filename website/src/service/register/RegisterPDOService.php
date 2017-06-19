<?php
namespace rohrerj\service\register;

use rohrerj\service\security\PasswordService;

class RegisterPDOService implements RegisterService {

	private $pdo;
	private $passwordService;
	public function __construct(\PDO $pdo, PasswordService $passwordService) {
		$this->pdo = $pdo;
		$this->passwordService = $passwordService;
	}

	public function register($email,$firstname,$lastname){
		if($this->userNotExist($email)) {
			$url = $this->createUser($email,$firstname,$lastname);
			if($url != null) {
				return $url;
			}
		}
		return null;
	}
	private function userNotExist($email) {
		$stmt = $this->pdo->prepare("SELECT Email FROM user WHERE email=?");
		$stmt->bindValue(1, $email);
		$stmt->execute();
		if($stmt->rowCount() == 0) {
			return true;
		}
		else {
			return false;
		}
	}
	private function createUser($email,$firstname,$lastname) {
		$url = $this->passwordService->generateRandomString(16);
		$stmt = $this->pdo->prepare("INSERT INTO user(Email,Vorname,Nachname,Password,ActivationURL) VALUES(?,?,?,'',?)");
		$stmt->bindValue(1, $email);
		$stmt->bindValue(2, $firstname);
		$stmt->bindValue(3, $lastname);
		$stmt->bindValue(4, $url);
		$stmt->execute();
		if($stmt->errorCode()==="00000") {
			return $url;
		}
		else {
			return null;
		}
	}
	
	public function setPassword($url,$password) {
		$secure = $this->passwordService->hash($password);
		$stmt = $this->pdo->prepare("UPDATE user set active=1,ActivationURL='',Password=? where ActivationURL=?");
		$stmt->bindValue(1, $secure);
		$stmt->bindValue(2, $url);
		$stmt->execute();
		if($stmt->rowCount()==1) {
			return true;
		}
		return false;
	}
}