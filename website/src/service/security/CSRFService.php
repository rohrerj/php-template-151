<?php
namespace rohrerj\service\security;

class CSRFService {
	
	private function store_in_session($key,$value)
	{
		if (isset($_SESSION))
		{
			$_SESSION[$key]=$value;
		}
	}
	private function unset_session($key)
	{
		$_SESSION[$key]=' ';
		unset($_SESSION[$key]);
	}
	private function get_from_session($key)
	{
		if (isset($_SESSION[$key]))
		{
			return $_SESSION[$key];
		}
		else { return false; }
	}
	
	public function validateToken($uid, $sentToken) {
		$token = $this->get_from_session($uid);

		if (!is_string($sentToken) OR !is_string($token)) {
			return false;
		}
		$result = hash_equals($token, $sentToken);

		$this->unset_session($uid);
		return $result;
	}
	
	public function generateToken($uid) {
		$token = bin2hex(random_bytes(64));
		$this->store_in_session($uid,$token);
		return $token;
	}
	public function getHtmlCode($uid) {
		return "<input type='hidden' name='csrf' value='".$this->generateToken($uid)."'/>";
	}
}
