<?php
namespace rohrerj\service\login;

interface LoginService {
	public function authenticate($username,$password);
}


