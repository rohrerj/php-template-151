<?php
namespace rohrerj\service\login;

class LoginPDOService implements LoginService {
	
	private $pdo;
	public function __construct(\PDO $pdo) {
		$this->pdo = $pdo;
	}
	
	public function authenticate($username, $password){
		$pw = md5($password);
		$stmt = $this->pdo->prepare("SELECT Email FROM user WHERE email=? AND password =?");
		$stmt->bindValue(1, $username);
		$stmt->bindValue(2, $pw);
		$stmt->execute();
		if($stmt->rowCount() == 1) {
			session_regenerate_id();
			$_SESSION["email"] = $username;
			return true;
		}
		else {
			return false;
		}
	}
}