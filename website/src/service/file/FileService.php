<?php
namespace rohrerj\service\file;

interface FileService {
	public function showFiles($email,$dir = "/");
	public function getFile($email,$id);
	public function deleteFile($email,$file);
	public function uploadFile($email,$name,$dir);
	public function createFolder($email,$name,$dir);
}