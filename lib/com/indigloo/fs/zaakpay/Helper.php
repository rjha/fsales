<?php

namespace com\indigloo\fs\zaakpay  {

	Class Helper {

		const SECRET_KEY = "d409bd01a7114a5186bf82b9d466c741" ;
		const IDENTIFIER = "90efd5c603c3427f93f7b9de57f60195" ;

		static function calculateChecksum($data) {

			$all = '';
			foreach($data as $key => $value){
				if($key != 'checksum') {
					$all .= "'";
					if ($key == 'returnUrl') {
						$all .= self::sanitizedURL($value);
					} else {
						$all .= self::sanitizedParam($value);
					}
					$all .= "'";
				}
			}

			$hash = hash_hmac('sha256', $all , self::SECRET_KEY);
			$checksum = $hash;
			return $checksum;
		}

		static function outputForm($data,$checksum) {

			foreach($data as $key => $value) {
				if ($key == 'returnUrl') {
					echo '<input type="hidden" name="'.$key.'" value="'.self::sanitizedURL($value).'" />'."\n";
				} else {
					echo '<input type="hidden" name="'.$key.'" value="'.self::sanitizedParam($value).'" />'."\n";
				}
			}

			echo '<input type="hidden" name="checksum" value="'.$checksum.'" />'."\n";
		}

		static function sanitizedParam($param) {
			$pattern[0] = "%,%";
		        $pattern[1] = "%#%";
		        $pattern[2] = "%\(%";
	       		$pattern[3] = "%\)%";
		        $pattern[4] = "%\{%";
		        $pattern[5] = "%\}%";
		        $pattern[6] = "%<%";
		        $pattern[7] = "%>%";
		        $pattern[8] = "%`%";
		        $pattern[9] = "%!%";
		        $pattern[10] = "%\\$%";
		        $pattern[11] = "%\%%";
		        $pattern[12] = "%\^%";
		        $pattern[13] = "%=%";
		        $pattern[14] = "%\+%";
		        $pattern[15] = "%\|%";
		        $pattern[16] = "%\\\%";
		        $pattern[17] = "%:%";
		        $pattern[18] = "%'%";
		        $pattern[19] = "%\"%";
		        $pattern[20] = "%;%";
		        $pattern[21] = "%~%";
		        $pattern[22] = "%\[%";
		        $pattern[23] = "%\]%";
		        $pattern[24] = "%\*%";
		        $pattern[25] = "%&%";
	        	$sanitizedParam = preg_replace($pattern, "", $param);
			return $sanitizedParam;
		}

		static function sanitizedURL($param) {
			$pattern[0] = "%,%";
		        $pattern[1] = "%\(%";
	       		$pattern[2] = "%\)%";
		        $pattern[3] = "%\{%";
		        $pattern[4] = "%\}%";
		        $pattern[5] = "%<%";
		        $pattern[6] = "%>%";
		        $pattern[7] = "%`%";
		        $pattern[8] = "%!%";
		        $pattern[9] = "%\\$%";
		        $pattern[10] = "%\%%";
		        $pattern[11] = "%\^%";
		        $pattern[12] = "%\+%";
		        $pattern[13] = "%\|%";
		        $pattern[14] = "%\\\%";
		        $pattern[15] = "%'%";
		        $pattern[16] = "%\"%";
		        $pattern[17] = "%;%";
		        $pattern[18] = "%~%";
		        $pattern[19] = "%\[%";
		        $pattern[20] = "%\]%";
		        $pattern[21] = "%\*%";
	        	$sanitizedParam = preg_replace($pattern, "", $param);
			return $sanitizedParam;
		}
	}

}

?>
