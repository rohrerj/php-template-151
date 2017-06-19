<?php
namespace rohrerj\service\login;

use rohrerj\service\security\PasswordService;

class LoginPDOService implements LoginService {
	
	private $pdo;
	private $passwordService;
	public function __construct(\PDO $pdo,PasswordService $passwordService) {
		$this->pdo = $pdo;
		$this->passwordService=$passwordService;
	}
	
	public function authenticate($username, $password){
		$hash = $this->getPasswordInDb($username);
		if($hash == null) {
			return false;
		}
		if(!$this->passwordService->verify($password, $hash)) {
			return false;
		}
		$stmt = $this->pdo->prepare("SELECT Email FROM user WHERE email=? AND Active='1'");
		$stmt->bindValue(1, $username);
		$stmt->execute();
		if($stmt->rowCount() == 1) {
			return true;
		}
		else {
			return false;
		}
	}
	
	private function getPasswordInDb($email) {
		$stmt = $this->pdo->prepare("SELECT Password FROM user WHERE email=?");
		$stmt->bindValue(1, $email);
		$stmt->execute();
		if($stmt->rowCount()==1) {
			return $stmt->fetch($this->pdo::FETCH_NUM, $this->pdo::FETCH_ORI_NEXT)[0];
		}
		return null;
	}
	public function prepareSession($email) {
		session_regenerate_id();
		
		$stmt = $this->pdo->prepare("SELECT user.Email, user.Vorname, user.Nachname, userLevel.Level FROM user INNER JOIN userLevel ON userLevel.Id = user.UserLevel WHERE email=?");
		$stmt->bindValue(1, $email);
		$stmt->execute();
		
		if($stmt->rowCount()==1) {
			
			$result = $stmt->fetch($this->pdo::FETCH_NUM, $this->pdo::FETCH_ORI_NEXT);
			
			$_SESSION["email"] = $result[0];
			$_SESSION["firstname"] = $result[1];
			$_SESSION["lastname"] = $result[2];
			$_SESSION["accessLevel"] = $result[3];
			return true;
		}
		else {
			return false;
		}
	}
	public function forgotPassword($email) {
		$stmt = $this->pdo->prepare("SELECT Email FROM user WHERE email=?");
		$stmt->bindValue(1, $email);
		$stmt->execute();
		
		if($stmt->rowCount()==1) {
			$url = $this->passwordService->generateRandomString();
			$stmt2 = $this->pdo->prepare("UPDATE user SET ActivationUrl=? WHERE Email=?");
			$stmt2->bindValue(1, $url);
			$stmt2->bindValue(2, $email);
			$stmt2->execute();
			if($stmt2->errorCode()==="00000") {
				return $url;
			}
		}
	}
}




