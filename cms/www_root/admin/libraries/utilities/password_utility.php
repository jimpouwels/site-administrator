<?php

	// No direct access
	defined('_ACCESS') or die;
	
	class PasswordUtility {
		
		/*
			Private constructor.
		*/
		private function __construct() {
		}
	
		/*
			Generates and returns a random password.
		*/
		public static function generatePassword() {
			$length=9;
			$strength=0;
			$vowels = 'aeuy';
			$consonants = 'bdghjmnpqrstvz';
			if ($strength & 1) {
				$consonants .= 'BDGHJLMNPQRSTVWXZ';
			}
			if ($strength & 2) {
				$vowels .= "AEUY";
			}
			if ($strength & 4) {
				$consonants .= '23456789';
			}
			if ($strength & 8) {
				$consonants .= '@#$%';
			}
		 
			$password = '';
			$alt = time() % 2;
			for ($i = 0; $i < $length; $i++) {
				if ($alt == 1) {
					$password .= $consonants[(rand() % strlen($consonants))];
					$alt = 0;
				} else {
					$password .= $vowels[(rand() % strlen($vowels))];
					$alt = 1;
				}
			}
			return $password;
		}
		
	}
	
?>