<?php
namespace rohrerj\service\login;

interface LoginService {
	public function authenticate($username,$password);
	public function prepareSession($email);
	public function forgotPassword($email);
}
