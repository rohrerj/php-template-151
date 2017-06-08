<?php
namespace rohrerj\service\register;

class RegisterPDOService implements RegisterService {

	private $pdo;
	public function __construct(\PDO $pdo) {
		$this->pdo = $pdo;
	}

	public function register($email,$firstname,$lastname, $password){
		if($this->userNotExist($email)) {
			if($this->createUser($email,$firstname,$lastname,$password)) {
				$_SESSION["email"] = $email;
				return true;
			}
		}
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
	private function createUser($email,$firstname,$lastname,$password) {
		$pw = md5($password);
		$stmt = $this->pdo->prepare("INSERT INTO user(Email,Vorname,Nachname,Password) VALUES(?,?,?,?)");
		$stmt->bindValue(1, $email);
		$stmt->bindValue(2, $firstname);
		$stmt->bindValue(3, $lastname);
		$stmt->bindValue(4, $pw);
		$stmt->execute();
		if($stmt->errorCode()==="00000") {
			return true;
		}
		else {
			return false;
		}
	}
}