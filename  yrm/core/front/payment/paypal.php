<?php

class ipn {
	function process_ipn($order_id,$mode, $model){
		
		/*
			Simple IPN processing script
			based on code from the "PHP Toolkit" provided by PayPal
			*/
			
			//$url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
			if ($mode) {
				$paypal = 'www.sandbox.paypal.com';
			}
			else {
				$paypal = 'www.paypal.com';
			}
			$url = 'https://'.$paypal.'/cgi-bin/webscr';			
			
			$postdata = '';
			foreach($_POST as $i => $v) {
				$postdata .= $i.'='.urlencode($v).'&';
			}
			
			$payment_status = trim(stripslashes($_POST['payment_status']));
			
		
			
			
			
			$postdata .= 'cmd=_notify-validate';			
			$web = parse_url($url);
			if ($web['scheme'] == 'https') { 
				$web['port'] = 443;  
				$ssl = 'ssl://'; 
			} else { 
				$web['port'] = 80;
				$ssl = ''; 
			}
			
			$fp = @fsockopen($ssl.$web['host'], $web['port'], $errnum, $errstr, 30);
			
			if (!$fp) { 
				echo $errnum.': '.$errstr;
				
			} else {
				fputs($fp, "POST ".$web['path']." HTTP/1.1\r\n");
				fputs($fp, "Host: ".$web['host']."\r\n");
				fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
				fputs($fp, "Content-length: ".strlen($postdata)."\r\n");
				fputs($fp, "Connection: close\r\n\r\n");
				fputs($fp, $postdata . "\r\n\r\n");
			
				while(!feof($fp)) { 
					$info[] = @fgets($fp, 1024); 
				}
				fclose($fp);
				
				$info = implode(',', $info);
				
				if (eregi('VERIFIED', $info)) { 
					if (eregi ("Completed", $payment_status)) {
						// yes valid, f.e. change payment status
						$rs = $model->updateOrder($order_id);
						$model->sendEmail($order_id, JText::_('CONFIRMED'));
					}
					elseif (eregi ("Refunded", $payment_status)) {
						$rs = $model->updateOrderRefunded($order_id);
						
					}
				
					//end here
				} else {
					// invalid, log error or something
				}
			}
			
			
	}
}

?>