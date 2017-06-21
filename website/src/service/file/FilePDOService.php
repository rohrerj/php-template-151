<?php
namespace rohrerj\service\file;

class FilePDOService implements FileService {
	private $pdo;
	public function __construct(\PDO $pdo) {
		$this->pdo = $pdo;
	}
	public function showFiles($email,$dir = "/") {
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
	public function getFile($email,$file) {
		$stmt = $this->pdo->prepare("select dokument.name from dokument inner join Freigabe on dokument.Id = Freigabe.DokumentId inner join user on Freigabe.UserId = user.Id inner join freigabeLevel on Freigabe.FreigabeLevel = freigabeLevel.Id where freigabeLevel.Freigabe = 'Owner' and dokument.exists='1' and dokument.name not like '/%' and user.email=? and dokument.Id=?");
		$stmt->bindValue(1, $email);
		$stmt->bindValue(2, $file);
		$stmt->execute();
		if($stmt->rowCount()==1) {
			return $stmt->fetch($this->pdo::FETCH_NUM, $this->pdo::FETCH_ORI_NEXT)[0];
		}
		else {
			return null;
		}
	}
	public function deleteFile($email, $file) {
		if($this->checkAccess($email,$file)) {
			$path = $_SERVER['DOCUMENT_ROOT']."../../files/".$file.".dat";
			$path = realpath($path);
			if(is_writable($path) && file_exists($path) && is_file($path)) {
				try {
					//$this->pdo->beginTransaction();//beginn transaction
					//$stmt = $this->pdo->prepare("delete from Freigabe where DokumentId=?");
					//$stmt->bindValue(1, $file);
					//$stmt->execute();
				
					//$stmt2 = $this->pdo->prepare("delete from dokument where Id=?");
					//$stmt2->bindValue(1, $file);
					//$stmt2->execute();
				
					unlink($path);
				
					//$this->pdo->commit();//end transaction
					return true;
				}
				catch(Exception $ex) {
					//$this->pdo->rollBack();//rollback
				}
			}
			else {
				echo $path;
			}
			
		}
	}
	private function checkAccess($email,$file) {
		$stmt = $this->pdo->prepare("select dokument.name from dokument inner join Freigabe on dokument.Id = Freigabe.DokumentId inner join user on Freigabe.UserId = user.Id inner join freigabeLevel on Freigabe.FreigabeLevel = freigabeLevel.Id where (freigabeLevel.Freigabe = 'Owner' OR freigabeLevel.Freigabe = 'ReadWrite') and dokument.exists='1' and user.email=? and dokument.Id=?");
		$stmt->bindValue(1, $email);
		$stmt->bindValue(2, $file);
		$stmt->execute();
		return ($stmt->rowCount()==1);
	}
}