<?php
namespace rohrerj\service\file;

interface FileService {
	public function showFiles($email,$dir = "/");
	public function getFile($email,$file);
	public function deleteFile($email,$file);
}