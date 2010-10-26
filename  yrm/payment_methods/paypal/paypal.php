<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/**
*
* @copyright	Copyright (C) 2010 YOS.,JSC. All rights reserved.
* @license	GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

/**
* This class implements the configuration panel for paypal
* If you want to change something "internal", you must modify the 'payment extra info'
* in the payment method form of the PayPal payment method
*/

class paypal {

    var $classname = "paypal";
    var $payment_code = "PAYPAL";
    
    /**
    * Show all configuration parameters for this payment method
    * @returns boolean False when the Payment method has no configration
    */
    function show_configuration() {
               
        /** Read current Configuration ***/
        $path = dirname(__FILE__);
       
        include_once($path.DS.$this->classname.".cfg.php");
        
    ?>
    <table class="adminform">
        <tr class="row0">
        <td width="20%"><strong><?php echo JText::_('Test mode ?'); ?></strong></td>
        <td width="30%">
            <select name="PAYPAL_DEBUG" class="inputbox" >
            <option <?php if (@PAYPAL_DEBUG == '1') echo "selected=\"selected\""; ?> value="1"><?php echo ('Yes') ?></option>
            <option <?php if (@PAYPAL_DEBUG != '1') echo "selected=\"selected\""; ?> value="0"><?php echo ('No') ?></option>
            </select>
        </td>
        <td>
            <?php
            printf( JText::_('Des'), '<pre>'.' ' ."notify.php</pre>" );
			?>            
            </td>
        </tr>
        <tr class="row1">
        <td><strong><?php echo JText::_('PayPal payment email:') ?></strong></td>
            <td>
                <input style="width:300px;" type="text" name="PAYPAL_EMAIL" class="inputbox" value="<?php  echo PAYPAL_EMAIL ?>" />
            </td>
            <td><?php echo JText::_('Your business email address for PayPal payments. Also used as receiver_email.') ?>
            </td>
        </tr>    
      </table>
    <?php
    }
    
    function has_configuration() {
      // return false if there's no configuration
      return true;
   }
   
  /**
	* Returns the "is_writeable" status of the configuration file
	* @param void
	* @returns boolean True when the configuration file is writeable, false when not
	*/
   function configfile_writeable() {
      return is_writeable( $path.DS.$this->classname.".cfg.php" );
   }
   
  /**
	* Returns the "is_readable" status of the configuration file
	* @param void
	* @returns boolean True when the configuration file is writeable, false when not
	*/
   function configfile_readable() {
      return is_readable( $path.DS.$this->classname.".cfg.php" );
   }
   
  /**
	* Writes the configuration file for this payment method
	* @param array An array of objects
	* @returns boolean True when writing was successful
	*/
   function write_configuration() {     
      $my_config_array = array(
                              "PAYPAL_DEBUG" => JRequest::getVar('PAYPAL_DEBUG'),
                              "PAYPAL_EMAIL" => JRequest::getVar('PAYPAL_EMAIL')                              
                            );
      $config = "<?php\n";
      $config .= "if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); \n\n";
      foreach( $my_config_array as $key => $value ) {
        $config .= "define ('$key', '$value');\n";
      }
     
      $config .= "?>";  
      //var_dump(htmlspecialchars($config));   die();  
      if ($fp = fopen(dirname(__FILE__).DS.$this->classname.".cfg.php", "w")) {      	 
          fputs($fp, $config, strlen($config));
         
          fclose ($fp);     
          return true;
     }
     else
        return false;
   }
   
  /**************************************************************************
  ** name: process_payment()
  ** returns: 
  ***************************************************************************/
  
   function process_payment($data) {
   	   	
   		$base_url		=	$data['base_url'];
		$model			=	$data['model'];
		$user			=	$data['user'];
		$order_number	=	$data['order_id'];
		$order_total	=	$data['amount'];
		$currency_code	=	$data['currency_code'];
		$return_url		=	$data['return_url'];
		$cancel_url		=	$data['cancel_url'];
		$description	=	$data['description'];
		$name			=	$data['name'];
		
		$notify_url		=	JRoute::_($base_url.'&payment_action=ipn&order_id='.$order_number);
		
   		global $mainframe;
   		 /** Read current Configuration ***/
        $path = dirname(__FILE__);
       
        include_once($path.DS.$this->classname.".cfg.php");
   		$url	= "https://www.sandbox.paypal.com/cgi-bin/webscr";
		if (PAYPAL_DEBUG) {
				$url 	= "https://www.sandbox.paypal.com/cgi-bin/webscr";
		}
		else {
				$url 	= "https://www.paypal.com/cgi-bin/webscr";
		}
	
		$return_url						=	$return_url;		
		$order_number					=	$order_number;
		$prices							=	$order_total;
		$cmd							=	"_ext-enter";
		$redirect_cmd					=	"_xclick";
		$return_method					=	2;//1=GET 2=POST
		$currency_code					=	$currency_code;
		$lc="US";
		$comment_header					=	"Comments";
		$continue_button_text			=	"";
		$background_color				=	null; //""=white 1=black
		$display_shipping_address		=	null; //""=yes 1=no
		$display_comment				=	null; //""=yes 1=no
		$edit_quantity					=	"";  //1=yes ""=no
		
		?>
		<body onLoad="document.paypal_form.submit();">
		<form method="post" name="paypal_form" action="<?=$url; ?>">
			<!-- PayPal Configuration -->
			<input type="hidden" name="business" value="<?=PAYPAL_EMAIL; ?>">
			<input type="hidden" name="cmd" value="<?=$cmd; ?>">
			<input type="hidden" name="redirect_cmd" value="<?=$redirect_cmd; ?>">
			<input type="hidden" name="image_url" value="">
			<input type="hidden" name="return" value="<? echo "$return_url"; ?>">
			<input type="hidden" name="cancel_return" value="<? echo "$cancel_url"; ?>">
			<input type="hidden" name="notify_url" value="<? echo "$notify_url"; ?>">
			<input type="hidden" name="rm" value="<?="$return_method"?>">
			<input type="hidden" name="currency_code" value="<?="$currency_code"?>">
			<input type="hidden" name="lc" value="<?="$lc"?>">			
			<input type="hidden" name="cbt" value="<?="$continue_button_text"?>">
			
			<!-- Payment Page Information -->
			<input type="hidden" name="no_shipping" value="<?="$display_shipping_address"; ?>">
			<input type="hidden" name="no_note" value="<?="$display_comment"?>">
			<input type="hidden" name="cn" value="<?="$comment_header"?>">
			<input type="hidden" name="cs" value="<?="$background_color"?>">
			
			<!-- Product Information -->
			<input type="hidden" name="order_id" value="<?echo $order_number; ?>">
			<input type="hidden" name="item_name" value="<?echo $name; ?>">
			<input type="hidden" name="amount" value="<? echo $order_total; ?>">
			<input type="hidden" name="quantity" value="">
			<input type="hidden" name="item_number" value="<? echo $order_number; ?>">
			<input type="hidden" name="undefined_quantity" value="<? echo $edit_quantity; ?>">
			<input type="hidden" name="on0" value="">
			<input type="hidden" name="os0" value="">
			<input type="hidden" name="on1" value="">
			<input type="hidden" name="os1" value="">
			
			<!-- Shipping and Misc Information -->
			<input type="hidden" name="shipping" value="">
			<input type="hidden" name="shipping2" value="">
			<input type="hidden" name="handling" value="">
			<input type="hidden" name="tax" value="">
			<input type="hidden" name="custom" value="">
			<input type="hidden" name="invoice" value="<? echo $order_number; ?>">
			
			<!-- Customer Information -->
			<input type="hidden" name="first_name" value="<? echo $user->first_name; ?>">
			<input type="hidden" name="last_name" value="<? echo $user->middle_name.$user->last_name; ?>">
			<input type="hidden" name="address1" value="<? echo $user->address_1; ?>">
			<input type="hidden" name="address2" value="<? echo $user->address_2; ?>">
			<input type="hidden" name="city" value="<? echo $user->address_2; ?>">
			
			<input type="hidden" name="address_name" value="">			
			<input type="hidden" name="address_street" value="">
			<input type="hidden" name="address_country" value="<? echo $user->country; ?>">
			<input type="hidden" name="address_city" value="<? echo $user->city; ?>">
			<input type="hidden" name="address_state" value="<? echo $user->state; ?>">			
			
			
			<input type="hidden" name="state" value="<? echo $user->state; ?>">
			<input type="hidden" name="zip" value="<? echo $user->zip; ?>">
			<input type="hidden" name="email" value="<? echo $user->address_2; ?>">
			<input type="hidden" name="night_phone_a" value="">
			<input type="hidden" name="night_phone_b" value="">
			<input type="hidden" name="night_phone_c" value="">			
			<center>
				<font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="333333">
					<h3>
					<?php echo JText::_('Please wait while you are being redirected to PayPal to complete your payment...') ?>
					</h3>
				</font>
			</center>		
		</form>
		</body>
		<br />
		<br />
		<br />
<?php 		
		return true;
    }
    
   function ipn($data)
    {		
    	$path = dirname(__FILE__);
       
        include_once($path.DS.$this->classname.".cfg.php");
    	$base_url		=	$data['base_url'];
		$model			=	$data['model'];
		$user			=	$data['user'];
		$order_number	=	$data['order_id'];
		$order_total	=	$data['amount'];
		$currency_code	=	$data['currency_code'];
		$return_url		=	$data['return_url'];
		$cancel_url		=	$data['cancel_url'];
		$description	=	$data['description'];
		$name			=	$data['name'];
		
		$notify_url		=	JRoute::_($base_url.'&payment_action=ipn&order_id='.$order_number);		
    	$mode=PAYPAL_DEBUG;
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
			
			$order_status	=	'';
			$path 			=	dirname(__FILE__);
        	 include_once($path.DS.'..'.DS."pcl_payment.cfg.php");
        	
				if (eregi('VERIFIED', $info)) { 
					if (eregi ("Completed", $payment_status)) {
						// yes valid, f.e. change payment status
						$order_status=COMPLETED;
					}
					elseif (eregi ("Refunded", $payment_status)) {
						$order_status=REFUNDED;
					}
					else {
						$order_status=CANCELLED;
					}				
					//end here
				} 
				else {
					$order_status=PENDING;
				}
				
				$this->_updateOrder($order_status,$payment_status,$order_number,$model,$return_url);
			}
    }
    
    function _updateOrder($order_status,$payment_status,$order_id,$model,$return_url)
    {
		$rs = $model->updateOrder($order_id,$order_status);
		$model->sendEmail($order_id, JText::_($payment_status));
    }
    
    /**
     * **************************************************************************
     */
    function showPayment($data)
	{
		$base_url		=	$data['base_url'];
		$model			=	$data['model'];
		$user			=	$data['user'];
		$order_number	=	$data['order_id'];
		$order_total	=	$data['amount'];
		$currency_code	=	$data['currency_code'];
		$return_url		=	$data['return_url'];
		$cancel_url		=	$data['cancel_url'];
		$description	=	$data['description'];
		$name			=	$data['name'];
		
		$notify_url		=	JRoute::_($base_url.'&payment_action=ipn&order_id='.$order_number);
		
		global $mainframe;
		$mainframe->redirect(JRoute::_($base_url));
	}

	/**
	 * ctring to show info
	 *
	 * @return unknown
	 */
	function getCodePaymentInfo()
	{			
		$document =& JFactory::getDocument();
		JHTML::_('behavior.mootools');
		$document->addStyleSheet(JURI::root().'components/com_yos_checkout/classes/assets/css/authorize.css');
		ob_start();
		?>
		<div class="pay_center">
			<div class="pay_left">Payment mathod:</div>
			<div class="pay_right">Paypal</div>
			<div class="clr"></div>
		</div>
		<?php
		$str=ob_get_contents();
		ob_clean();
		return $str;
	}
}
