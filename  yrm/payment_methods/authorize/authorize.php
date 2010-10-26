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
class authorize {

    var $classname = "authorize";
    var $payment_code = "AUTHORIZE";
    
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
        <td width="20%"><strong><?php echo JText::_('Test mode ?') ?></strong></td>
            <td>
                <select name="AUTHORIZE_DEBUG" class="inputbox" >
                <option <?php if (@AUTHORIZE_DEBUG == '1') echo "selected=\"selected\""; ?> value="1"><?php echo ('Yes') ?></option>
                <option <?php if (@AUTHORIZE_DEBUG != '1') echo "selected=\"selected\""; ?> value="0"><?php echo ('No') ?></option>
                </select>
            </td>
            <td>
            <?php        
            printf( JText::_('Des'), '<pre>'.' ' ."notify.php</pre>" );
			?>            
            </td>
        </tr>
        <tr class="row1">
        <td><strong><?php echo JText::_('Authorize.net API Login ID:') ?></strong></td>
            <td>
                <input type="text" name="API_ID" class="inputbox" value="<?php  echo API_ID ?>" />
            </td>
            <td><?php echo JText::_('des') ?>
            </td>
        </tr>
        <tr class="row0">
            <td><strong><?php echo JText::_('Transaction Key') ?></strong></td>
            <td>
            	<?php
            		$cid=JRequest::getVar('cid','','','array');
            		$href='index.php?option=com_yos_resources_manager&task=payment_method.executes&payment_action=_editKey&cid='.$cid[0];
            	?>
            	<a href="<?php echo $href; ?>"><?php echo JText::_('Show / Change the Transaction Key') ?></a>
                <!--<input type="text" name="TRANSACTION_KEY" class="inputbox" value="<?php  echo TRANSACTION_KEY ?>" />-->
            </td>
            <td><?php echo JText::_('des') ?>
            </td>
            </td>
        </tr>        
      </table>
    <?php
    }
    //****************************************************************************************************************** 
    ////////////   ACTION WITH KEY: BEGIN
    //******************************************************************************************************************
   		    
    function _editKey($base_url)
    {
    	$path = dirname(__FILE__);
    	 include_once($path.DS.$this->classname.".cfg.php");
    	$str= $transaction_key=TRANSACTION_KEY;
    	 if (strlen($transaction_key)>=4) {
    	 	$st1=substr($transaction_key,strlen($transaction_key)-4);
    	 	$st2=substr($transaction_key,0,strlen($transaction_key)-4);    	 	
    	 	$st2=preg_replace('/./','*',$st2);
    	 	$str=$st2.$st1;  	 	
    	 }    	
    	?>
    	<div align="center">
	    	<form  action="<?php echo $base_url; ?>" method="post">
	    	<h3><?php echo JText::_('Show/Change the Password/Transaction Key') ?>	</h3>
	    	<table>
	    		<tbody>
	    			<tr>
	    				<td><?php echo JText::_('Current Transaction Key:') ?></td>
	    				<td><?php echo $str; ?></td>
	    			</tr>
	    			<tr>
	    				<td><?php echo JText::_('Please type in your User Password (Your Joomla Password):') ?></td>
	    				<td><input style="width:150px;" name="joomla_password" type="password" value="" /></td>
	    			</tr>
	    		</tbody>
	    	</table>
	    	<br />
	    	<input type="hidden" name="payment_action" value="_checkPass" />
	    	<input style="margin:0 0 0 200px;" type="submit" value="<?php echo JText::_('submit')?>" />
	    	<input onclick="this.form.payment_action.value=''; this.form.submit();"  type="button" value="<?php echo JText::_('Cancel')?>" />	    	
	    	</form>	    	
    	</div>
    	<?php
    }
    function _checkPass($base_url)
    {
    	$joomla_password=JRequest::getVar('joomla_password');
    	$user=JFactory::getUser();
    	$user_pass=$user->password;
    	jimport('joomla.user.helper');
    	$salt_old=explode(':',$user_pass);
    	$salt_new="";
    	if (isset($salt_old[1])) {
    		$salt_new=$salt_old[1];
    	}
    	$crypt = JUserHelper::getCryptedPassword($joomla_password, $salt_new);
    	$pass=$crypt.':'.$salt_new;
    	if ($pass==$user_pass) {
    		$path = dirname(__FILE__);
    		include_once($path.DS.$this->classname.".cfg.php");
    		?>
    	<div align="center">
	    	<form  action="<?php echo $base_url; ?>" method="post">
	    	<h3><?php echo JText::_('Show/Change the Password/Transaction Key') ?>	</h3>
	    	<table>
	    		<tbody>
	    			<tr>
	    				<td><?php echo JText::_('Old Transaction Key:') ?></td>
	    				<td><input style="width:150px;" type="text" value="<?php echo TRANSACTION_KEY; ?>" /></td>
	    			</tr>
	    			<tr>
	    				<td><?php echo JText::_('Please type New Transaction Key:') ?></td>
	    				<td><input style="width:150px;" name="TRANSACTION_KEY" type="password" value="" /></td>
	    			</tr>
	    		</tbody>
	    	</table>
	    	<br />	    	
	    	<input type="hidden" name="payment_action" value="_saveKey" />
	    	<input  style="margin:0 0 0 120px;" type="submit" value="<?php echo JText::_('submit')?>" />
	    	<input onclick="this.form.payment_action.value=''; this.form.submit();"  type="button" value="<?php echo JText::_('Cancel')?>" />
	    	</form>	    	
    	</div>
    	<?php
    	}
    	else {
    		$this->_editKey($base_url);
    	}
    }
    
    function _saveKey($base_url)
    {     	
    	$path = dirname(__FILE__);
    	 include_once($path.DS.$this->classname.".cfg.php");
    	$my_config_array = array(
                              "AUTHORIZE_DEBUG" => AUTHORIZE_DEBUG,
                              "API_ID" => API_ID,
                              "TRANSACTION_KEY" => JRequest::getVar('TRANSACTION_KEY')                              
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
     }
     global $mainframe;     
     $mainframe->redirect($base_url);
    }
    function has_configuration() {
      // return false if there's no configuration
      return true;
   }
   
   
    //****************************************************************************************************************** 
    ////////////   ACTION WITH KEY: END
    //******************************************************************************************************************
    
    
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
                              "AUTHORIZE_DEBUG" => JRequest::getVar('AUTHORIZE_DEBUG'),
                              "API_ID" => JRequest::getVar('API_ID'),
                              "TRANSACTION_KEY" => JRequest::getVar('TRANSACTION_KEY')                              
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
   		$url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
		if (AUTHORIZE_DEBUG) {
				$host = 'test.authorize.net';
		}
		else {
				$host = 'secure.authorize.net';
		}
		$url= "https://$host:443/gateway/transact.dll";
		
		$session=JFactory::getSession();
		
		$creditcard_code=$session->get('creditcard_code');
		$order_payment_name=$session->get('order_payment_name');
		$order_payment_number=$session->get('order_payment_number');
		$credit_card_code=$session->get('credit_card_code');
		$order_payment_expire_month=$session->get('order_payment_expire_month');
		$order_payment_expire_year=$session->get('order_payment_expire_year');	
			
		$order_number=$order_number;
		$order_total=$order_total;
		
		//Authnet vars to send
		$formdata = array (
		'x_version' => '3.1',
		'x_login' => API_ID,
		'x_tran_key' => TRANSACTION_KEY,
		'x_test_request' => strtoupper( 'true' ),		

		// Gateway Response Configuration
		'x_delim_data' => 'TRUE',
		'x_delim_char' => '|',
		'x_relay_response' => 'FALSE',

		// Customer Name and Billing Address
		'x_first_name' => $user->first_name,
		'x_last_name' =>$user->middle_name.$user->last_name,
		'x_company' =>"",
		'x_address' =>$user->address_1,
		'x_city' =>$user->city,
		'x_state' => $user->state,
		'x_zip' =>$user->zip,
		'x_country' =>$user->country,
		'x_phone' =>"",
		'x_fax' =>"",

		// Customer Shipping Address
		'x_ship_to_first_name' =>"",
		'x_ship_to_last_name' =>"",
		'x_ship_to_company' => "",
		'x_ship_to_address' =>"",
		'x_ship_to_city' => "",
		'x_ship_to_state' =>"",
		'x_ship_to_zip' => "",
		'x_ship_to_country' => "",

		// Additional Customer Data
		'x_cust_id' => $user->user_id,
		'x_customer_ip' => $_SERVER["REMOTE_ADDR"],
		'x_customer_tax_id' => "",

		// Email Settings
		'x_email' => "",
		'x_email_customer' => "TRUE",
		'x_merchant_email' => "",

		// Invoice Information
		'x_invoice_num' => $order_number,
		'x_description' => "Purchase Order",

		// Transaction Data
		'x_amount' => $order_total,
		'x_currency_code' =>$currency_code,
		'x_method' => 'CC',
		'x_type' => "AUTH_CAPTURE",
		'x_recurring_billing' => "No",

		'x_card_num' => $order_payment_number,
		'x_card_code' => $credit_card_code,
		'x_exp_date' =>$order_payment_expire_month.$order_payment_expire_year ,

		// Level 2 data
		'x_po_num' => $order_number,
		'x_tax' => "",
		'x_tax_exempt' => "FALSE",
		'x_freight' =>"",
		'x_duty' => 0

		);

		//build the post string
		$poststring = '';
		foreach($formdata AS $key => $val){
			$poststring .= urlencode($key) . "=" . urlencode($val) . "&";
		}
		// strip off trailing ampersand
		$poststring = substr($poststring, 0, -1);
		
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
				fputs($fp, "Content-length: ".strlen($poststring)."\r\n");
				fputs($fp, "Connection: close\r\n\r\n");
				fputs($fp, $poststring . "\r\n\r\n");	
	
				while(!feof($fp)) { 
					$info[] = @fgets($fp, 1024); 
				}
				fclose($fp);
			}
			
			
		if (AUTHORIZE_DEBUG) {
			$response 	=	explode("|", $info[13]);
		}
		else {
			$response 	=	explode("|", $info[7]);
		}		
		$status				=	$response[0];		
		$order_status		=	'';
		$payment_status		=	'';		
		$path 		=	dirname(__FILE__);      
        include_once($path.DS.'..'.DS."pcl_payment.cfg.php");       
		switch ($status)	
		{
			case 1:
			{
				$order_status	=	COMPLETED;
				$payment_status	=	'Completed';
				break;
			}
			case 2:
			{
				$order_status	=	REFUNDED;
				$payment_status	=	'Refunded';
				break;				
			}
			case 3:
			{
				$order_status	=	CANCELLED;
				$payment_status	=	'Cancelled';
				break;				
			}
			case 4:
			{
				$order_status	=	PENDING;
				$payment_status	=	'Pending';
				break;				
			}
			default:
			{
				$order_status	=	PENDING;
				$payment_status	=	'pending';
				break;				
			}			
		}
		$session=JFactory::getSession();		
		$session->set('creditcard_code',null);
		$session->set('order_payment_name',null);
		$session->set('order_payment_number',null);
		$session->set('credit_card_code',null);
		$session->set('order_payment_expire_month',null);
		$session->set('order_payment_expire_year',null);
		$this->updateOrder($order_status,$payment_status,$order_number,$model,$return_url);
		
    }
    
    function updateOrder($order_status,$payment_status,$order_id,$model,$return_url)
    {
    	
    	$mess='Transaction is '.$payment_status;		
		$rs = $model->updateOrder($order_id,$order_status);
		$model->sendEmail($order_id, JText::_($payment_status));
			
		global $mainframe;	
		$mainframe->redirect($return_url,$mess);	
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
			
		//, l,,=null,=null,=null
		$session=JFactory::getSession();		
		$payment=$session->get('payment');		
		$creditcard_code=$session->get('creditcard_code');
		$order_payment_name=$session->get('order_payment_name');
		$order_payment_number=$session->get('order_payment_number');
		$credit_card_code=$session->get('credit_card_code');
		$order_payment_expire_month=$session->get('order_payment_expire_month');
		$order_payment_expire_year=$session->get('order_payment_expire_year');
		
		$document =& JFactory::getDocument();
		JHTML::_('behavior.mootools');
		$document->addStyleSheet(JURI::root().'components/com_yos_resources_manager/paymentclass/pcl_authorize/assets/css/authorize.css');
		$document->addScript(JURI::root().'components/com_yos_resources_manager/paymentclass/pcl_authorize/assets/js/authorize.js');
		$today = getdate();						
		$year=$today["year"];
		?>
		<div id="authorize">
		<div class="title"> <h1 class="componentheading"> <?php echo JText::_('Credit Card Payment: '); ?> </h1> </div>	
			<form id="payment" action="<?php echo $base_url; ?>" method="POST">
				<div class="left">Credit Card Type:</div> 
				<div class="right">
					<select size="1" id="creditcard_code" name="creditcard_code" class="inputbox">
						<option value="VISA">Visa</option>
						<option value="MC">MasterCard</option>
						<option value="jcb">JCB</option>
						<option value="australian_bc">Australian Bankcard</option>
					</select>
				</div>
				<div class="clr"></div>
				<div class="left">Name On Card:</div>
				<div class="right"><input type="text" id="order_payment_name" name="order_payment_name" value="<?php echo $order_payment_name; ?>" /> </div>
				<div class="clr"></div>
				<div class="left">Credit Card Number:</div>
				<div class="right"><input type="text" id="order_payment_number" name="order_payment_number" value="<?php echo $order_payment_number; ?>" /> </div>
				<div class="clr"></div>
				<div class="left"> Credit Card Security Code:</div>
				<div class="right"><input type="text" id="credit_card_code" name="credit_card_code" value="<?php echo $credit_card_code; ?>" /> </div>
				<div class="clr"></div>
				<div class="left special"> Expiration Date:</div>
				<div class="right">
					<select size="1" name="order_payment_expire_month" class="inputbox">
						<option value="0">Month</option>
						<option value="01">January</option>
						<option value="02">February</option>
						<option value="03">March</option>
						<option value="04">April</option>
						<option value="05">May</option>
						<option value="06">June</option>
						<option value="07">July</option>
						<option value="08">August</option>
						<option value="09">September</option>
						<option value="10">October</option>
						<option value="11">November</option>
						<option selected="selected" value="12">December</option>
					</select> / 
					<div class="clr"></div>					
					<select size="1" name="order_payment_expire_year" class="inputbox">
					<?php
						for ($i=0;$i<10;$i++)
						{
							$data=$year+$i;
							?>
								<option value="<?php echo $data;?>"><?php echo $data;?></option>
							<?php							
						}
					?>						
					</select>				
				</div>
				<div class="clr"></div>
				<input type="hidden" name="payment_action" value="transferData" />
			</form>
			<div class="bottom">
				<div class="bot_left">				
					<div class="button_blue">
						<a href="javascript:history.go(-1);"><span><?php echo JText::_('Back'); ?></span></a>
					</div>
				</div>
				<div class="bot_right">				
					<div class="button_blue">
						<a  id="authorize_next" href="javascript:void();"><span><?php echo JText::_('Next'); ?></span></a>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
	
	/**
	 * Enter description here...
	 *
	 * @param array $data. contain: 
	 */
	function transferData($data)
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
		
		$session=JFactory::getSession();
		$creditcard_code=JRequest::getVar('creditcard_code');		
		$order_payment_name=JRequest::getVar('order_payment_name');		
		$order_payment_number=JRequest::getVar('order_payment_number');		
		$credit_card_code=JRequest::getVar('credit_card_code');		
		$order_payment_expire_month=JRequest::getVar('order_payment_expire_month');		
		$order_payment_expire_year=JRequest::getVar('order_payment_expire_year');		
		
		$session->set('creditcard_code',$creditcard_code);
		$session->set('order_payment_name',$order_payment_name);
		$session->set('order_payment_number',$order_payment_number);
		$session->set('credit_card_code',$credit_card_code);
		$session->set('order_payment_expire_month',$order_payment_expire_month);
		$session->set('order_payment_expire_year',$order_payment_expire_year);
		
		$credit_card_code=$session->get('credit_card_code');

		global $mainframe;
//		$url=$address.'&authorize_method=""';
		$mainframe->redirect($base_url);	
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
		$document->addStyleSheet(JURI::root().'components/com_yos_resources_manager/paymentclass/authorize/assets/css/authorize.css');
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