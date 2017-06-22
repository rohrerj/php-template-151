<?php
namespace rohrerj\service\file;

class FilePDOService implements FileService {
	private $pdo;
	public function __construct(\PDO $pdo) {
		$this->pdo = $pdo;
	}
	public function showFiles($email,$dir = "/") {
		if($dir!="/" && !$this->directoryExists($dir)) {
			return null;
		}
		$stmt = $this->pdo->prepare("select dokument.id, dokument.name, freigabeLevel.Freigabe from dokument inner join Freigabe on dokument.Id = Freigabe.DokumentId inner join user on Freigabe.UserId = user.Id inner join freigabeLevel on Freigabe.FreigabeLevel = freigabeLevel.Id where dokument.exists='1' and user.email=? and dokument.Directory=?");
		$stmt->bindValue(1, $email);
		$stmt->bindValue(2, $dir);
		$stmt->execute();
		if($stmt->rowCount()>0) {
			return $stmt->fetchAll($this->pdo::FETCH_NUM);
		}
		else {
			return null;
		}
	}
	public function getFile($email,$id) {
		$stmt = $this->pdo->prepare("select dokument.name from dokument inner join Freigabe on dokument.Id = Freigabe.DokumentId inner join user on Freigabe.UserId = user.Id inner join freigabeLevel on Freigabe.FreigabeLevel = freigabeLevel.Id where dokument.exists='1' and dokument.name not like '/%' and user.email=? and dokument.Id=?");
		$stmt->bindValue(1, $email);
		$stmt->bindValue(2, $id);
		$stmt->execute();
		if($stmt->rowCount()==1) {
			return $stmt->fetch($this->pdo::FETCH_NUM, $this->pdo::FETCH_ORI_NEXT)[0];
		}
		else {
			return null;
		}
	}
	public function uploadFile($email,$name,$dir) {
		if($this->directoryExists($dir)) {
			if($this->checkDirAccess($email, $dir, "Owner") || $this->checkDirAccess($email, $dir, "Owner") == "ReadWrite") {
				if(!$this->fileExists($name,$dir)) {
					try {
						$this->pdo->beginTransaction();
						$stmt = $this->pdo->prepare("insert into dokument(Name,Directory) values(?,?)");
						$stmt->bindValue(1, $name);
						$stmt->bindValue(2, $dir);
						$stmt->execute();
						
						$documentId = $this->pdo->lastInsertId();
						$userId = $this->getUserIdForEmail($email);
						$freigabeLevelId = $this->getFreigabeIdForName("Owner");
						
						$stmt2 = $this->pdo->prepare("insert into Freigabe(UserId, DokumentId,FreigabeLevel) values(?,?,?)");
						$stmt2->bindValue(1, $userId);
						$stmt2->bindValue(2, $documentId);
						$stmt2->bindValue(3, $freigabeLevelId);
						$stmt2->execute();
						
						$this->pdo->commit();
						
						
						return $documentId;
					}
					catch(Exception $ex) {
						$this->pdo->rollBack();
					}
				}
			}
		}
		return 0;
	}
	public function createFolder($email,$name,$dir) {
		$fullPath = $dir."/".$name;
		$perm = "";
		if($dir == '/'.$email) {
			$perm = "Owner";
		}
		else {
			$stmt = $this->pdo->prepare("select freigabeLevel.Freigabe from dokument inner join Freigabe on Freigabe.DokumentId = dokument.Id inner join freigabeLevel on freigabeLevel.Id = Freigabe.FreigabeLevel inner join user on user.Id = Freigabe.UserId where user.Email=? and dokument.Name=?");
			$stmt->bindValue(1, $email);
			$stmt->bindValue(2, $dir);
			$stmt->execute();//get user permissions for directory
				
			if($stmt->rowCount()==1) {
				$perm = $stmt->fetch($this->pdo::FETCH_NUM, $this->pdo::FETCH_ORI_NEXT)[0];
			}
		}
		if($this->directoryExists($dir)&&!$this->directoryExists($fullPath)) {
			if($perm == "ReadWrite" OR $perm == "Owner") {
				try {
					$this->pdo->beginTransaction();
					$stmt = $this->pdo->prepare("insert into dokument(Name,Directory) values(?,?)");
					$stmt->bindValue(1, $dir."/".$name);
					$stmt->bindValue(2, $dir);
					$stmt->execute();
			
					$documentId = $this->pdo->lastInsertId();
					$userId = $this->getUserIdForEmail($email);
					$freigabeId = $this->getFreigabeIdForName("Owner");
			
					$stmt2 = $this->pdo->prepare("insert into Freigabe(UserId,DokumentId,FreigabeLevel) values(?,?,?)");
					$stmt2->bindValue(1, $userId);
					$stmt2->bindValue(2, $documentId);
					$stmt2->bindValue(3, $freigabeId);
					$stmt2->execute();
			
					$this->pdo->commit();
					return true;
				}
				catch(Exception $ex) {
					$this->pdo->rollBack();
				}
					
			}
		}
		else {
			echo "directory does not exist / already exists<br>";
			
		}
		
	}
	public function createRootFolder($email) {
		$stmt = $this->pdo->prepare("select Name from dokument where name = ?");
		$stmt->bindValue(1, "/".$email);
		$stmt->execute();
		if($stmt->rowCount() == 0) {
			try {
				$this->pdo->beginTransaction();
	
				$stmt2 = $this->pdo->prepare("insert into dokument(Name,Directory) values(?,'/')");
				$stmt2->bindValue(1, "/".$email);
				$stmt2->execute();
				
				$dokumentId = $this->pdo->lastInsertId();
				$userId = $this->getUserIdForEmail($email);
				$freigabeId = $this->getFreigabeIdForName("Owner");
				
				$stmt3 = $this->pdo->prepare("insert into Freigabe (DokumentId,UserId,FreigabeLevel) values(?,?,?)");
				$stmt3->bindValue(1, $dokumentId);
				$stmt3->bindValue(2, $userId);
				$stmt3->bindValue(3, $freigabeId);
				$stmt3->execute();
	
				$this->pdo->commit();
			}
			catch(Exception $ex) {
				$this->pdo->rollBack();
			}
		}
	}
	public function share($email,$sharedFileId,$sharedUserEmail,$sharedType) {
		if($this->checkFileAccess($email, $sharedFileId, "Owner")) {
			$userId = $this->getUserIdForEmail($sharedUserEmail);
			if($userId != 0) {
				if($sharedType == "Non Share") {
					$stmt = $this->pdo->prepare("delete from Freigabe where UserId=? and DokumentId=?");
					$stmt->bindValue(1, $userId);
					$stmt->bindValue(2, $sharedFileId);
					$stmt->execute();
				}
				else {
					$freigabeId = $this->getFreigabeIdForName($sharedType);
					$stmt = $this->pdo->prepare("select Id from Freigabe where UserId=? and DokumentId=?");
					$stmt->bindValue(1, $userId);
					$stmt->bindValue(2, $sharedFileId);
					$stmt->execute();
					if($stmt->rowCount()==1) {
						$stmt2 = $this->pdo->prepare("update Freigabe set FreigabeLevel=? where UserId=? and DokumentId=?");
						$stmt2->bindValue(1, $freigabeId);
						$stmt2->bindValue(2, $userId);
						$stmt2->bindValue(3, $sharedFileId);
						$stmt2->execute();
						if($stmt2->rowCount() == 1) {
							return true;
						}
					}
					else {
						$stmt2 = $this->pdo->prepare("insert into Freigabe(UserId,DokumentId,FreigabeLevel) values(?,?,?)");
						$stmt2->bindValue(1, $userId);
						$stmt2->bindValue(2, $sharedFileId);
						$stmt2->bindValue(3, $freigabeId);
						$stmt2->execute();
						if($stmt2->rowCount() == 1) {
							return true;
						}
					}
					
				}
			}
			
		}
	}
	private function checkFileAccess($email,$docId,$access) {
		$stmt = $this->pdo->prepare("select user.Id from freigabeLevel inner join Freigabe on Freigabe.freigabeLevel = freigabeLevel.Id inner join dokument on dokument.Id = Freigabe.DokumentId inner join user on user.Id = Freigabe.UserId where user.Email = ? and freigabeLevel.Freigabe=? and dokument.Id = ?");
		$stmt->bindValue(1, $email);
		$stmt->bindValue(2, $access);
		$stmt->bindValue(3, $docId);
		$stmt->execute();
		return $stmt->rowCount()==1;
	}
	//use this method only to check access for directorys
	private function checkDirAccess($email,$path,$access) {
		$stmt = $this->pdo->prepare("select user.Id from freigabeLevel inner join Freigabe on Freigabe.freigabeLevel = freigabeLevel.Id inner join dokument on dokument.Id = Freigabe.DokumentId inner join user on user.Id = Freigabe.UserId where user.Email = ? and freigabeLevel.Freigabe=? and dokument.Name = ?");
		$stmt->bindValue(1, $email);
		$stmt->bindValue(2, $access);
		$stmt->bindValue(3, $path);
		$stmt->execute();
		return $stmt->rowCount()==1;
	}
	private function directoryExists($dir) {
		$stmt = $this->pdo->prepare("select id from dokument where Name=?");
		$stmt->bindValue(1, $dir);
		$stmt->execute();
		return $stmt->rowCount()==1;
	}
	private function fileExists($file,$dir) {
		$stmt = $this->pdo->prepare("select id from dokument where name=? && directory=?");
		$stmt->bindValue(1, $file);
		$stmt->bindValue(2, $dir);
		$stmt->execute();
		return $stmt->rowCount()==1;
	}
	private function getFreigabeIdForName($name) {
		$stmt = $this->pdo->prepare("select id from freigabeLevel where Freigabe=?");
		$stmt->bindValue(1, $name);
		$stmt->execute();
		if($stmt->rowCount()==1) {
			return $stmt->fetch($this->pdo::FETCH_NUM, $this->pdo::FETCH_ORI_NEXT)[0];
		}
	}
	private function getUserIdForEmail($email) {
		$stmt = $this->pdo->prepare("select id from user where email=?");
		$stmt->bindValue(1, $email);
		$stmt->execute();
		if($stmt->rowCount()==1) {
			return $stmt->fetch($this->pdo::FETCH_NUM, $this->pdo::FETCH_ORI_NEXT)[0];
		}
		return 0;
	}
	public function deleteFile($email, $file) {
		$result = $this->checkType($email,$file);
		if($result == 1) {
			
			try {
				$this->pdo->beginTransaction();
				$this->deleteSpecificFile($file);
				$this->pdo->commit();
				return true;
			}
			catch(Exception $ex) {
				$this->pdo->rollBack();
			}
			
		}
		else if($result == 2) {
			//get all files
			$name = $this->documentNameForId($file);
			$stmt = $this->pdo->prepare("select id from dokument where Directory like ? OR name=?");
			$stmt->bindValue(1, $name."%");
			$stmt->bindValue(2, $name);
			$stmt->execute();
			if($stmt->rowCount()>0) {
				$res = $stmt->fetchAll($this->pdo::FETCH_NUM);
				try {
					$this->pdo->beginTransaction();
					for($i = 0; $i < count($res);$i++) {
						$this->deleteSpecificFile($res[$i][0]);
					}
					$this->pdo->commit();
					return true;
				}
				catch(Exception $ex) {
					$this->pdo->rollBack();
				}
				
			}
		}
	}
	private function deleteSpecificFile($id) {
		$path = $_SERVER['DOCUMENT_ROOT']."../../files/".$id.".dat";
		$path = realpath($path);
		
		$stmt = $this->pdo->prepare("delete from Freigabe where DokumentId=?");
		$stmt->bindValue(1, $id);
		$stmt->execute();
			
		$stmt2 = $this->pdo->prepare("delete from dokument where Id=?");
		$stmt2->bindValue(1, $id);
		$stmt2->execute();
		if(is_writable($path) && file_exists($path) && is_file($path)) {
			unlink($path);
		}
	}
	private function documentNameForId($id) {
		$stmt = $this->pdo->prepare("select name from dokument where id=?");
		$stmt->bindValue(1, $id);
		$stmt->execute();
		if($stmt->rowCount()==1) {
			return $stmt->fetch($this->pdo::FETCH_NUM, $this->pdo::FETCH_ORI_NEXT)[0];
		}
		
	}
	//returns 0=nothing 1=file 2=folder
	private function checkType($email,$file) {
		$stmt = $this->pdo->prepare("select dokument.name from dokument inner join Freigabe on dokument.Id = Freigabe.DokumentId inner join user on Freigabe.UserId = user.Id inner join freigabeLevel on Freigabe.FreigabeLevel = freigabeLevel.Id where (freigabeLevel.Freigabe = 'Owner' OR freigabeLevel.Freigabe = 'ReadWrite') and dokument.exists='1' and user.email=? and dokument.Id=?");
		$stmt->bindValue(1, $email);
		$stmt->bindValue(2, $file);
		$stmt->execute();
		if(($stmt->rowCount()==1)) {
			$result = $stmt->fetch($this->pdo::FETCH_NUM, $this->pdo::FETCH_ORI_NEXT)[0];
			if($pos = strrpos($result, "/")!==false) {
				return 2;
			}
			else {
				return 1;
			}
		}
		return 0;
	}
}