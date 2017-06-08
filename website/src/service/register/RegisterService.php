<?php
namespace rohrerj\service\register;

interface RegisterService {
	public function register($email,$firstname,$lastname,$password);
}