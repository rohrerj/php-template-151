<?php
namespace rohrerj\service\security;

class PasswordService {
	
	public function hash($password) {
		$options = [
			'cost' => 10
		];
		return password_hash($password, PASSWORD_BCRYPT, $options);
	}
	public function verify($password, $hash) {
		return password_verify($password, $hash);
	}
	
	public function generateRandomString($length = 16) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
}