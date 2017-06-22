<?php
namespace rohrerj\service\admin;

class AdminPDOService implements AdminService {
	private $pdo;
	public function __construct(\PDO $pdo) {
		$this->pdo = $pdo;
	}
	public function executeQuery($query) {
		$cmd = substr(strtolower($query), 0,6);
		if($cmd != "select" && $cmd != "update") {
			return false;
		}
		if (strpos($query, ';') !== false) {
			return false;
		}
		$hasResults = false;
		if($cmd == "select") {
			$hasResults = true;
		}
		$stmt = $this->pdo->prepare($query);
		try {
			$stmt->execute();
			if($hasResults && $stmt->rowCount()>0) {
				return $stmt->fetchAll($this->pdo::FETCH_NUM);
			}
		}
		catch(Exception $ex) {
			return $ex;
		}
		
	}
}