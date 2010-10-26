<?php
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<?php

jimport( 'joomla.application.component.model' );

class YRMModelPackage extends JModel{	
	
	var $_data = null;
	
	function getPackages(){
		$db = $this->_db;
		if ($this->_data) {
			return $this->_data;
		}
		// Lets load the data if it doesn't already exist
		
		$query = 'SELECT YRMP.* , YRMC.currency_name,  YRMC.currency_code'
			. ' FROM #__yos_resources_manager_package YRMP, #__yos_resources_manager_currency AS YRMC WHERE published =1 AND YRMC.id = YRMP.currency';
		$db->setQuery($query);
		$this->_data = $db->loadObjectList();
				
		return $this->_data; 		
	}
	function getPackages1($package){
		
		$db = $this->_db;
		$packages = implode("','",$package);
		
		if ($this->_data) {
			return $this->_data;
		}
		// Lets load the data if it doesn't already exist
		
		$query = 'SELECT YRMP.* , YRMC.currency_name,  YRMC.currency_code'
			. " FROM #__yos_resources_manager_package YRMP, #__yos_resources_manager_currency AS YRMC WHERE published =1 AND YRMC.id = YRMP.currency AND YRMP.id IN ('".$packages."') "; //$db->Quote($packages).
		$db->setQuery($query);
		$this->_data = $db->loadObjectList();
			
		return $this->_data; 		
	}
	function getPackage($packageid){
		$db = $this->_db;
				
		$query = 'SELECT YRMP.* , YRMC.currency_name,  YRMC.currency_code '
			. ' FROM #__yos_resources_manager_package YRMP, #__yos_resources_manager_currency AS YRMC WHERE published =1 AND YRMC.id = YRMP.currency AND YRMP.id ='.$packageid;
		$db->setQuery($query);
		$row = $db->loadObject();
				
		return $row; 		
	}
	function getPackageOrder($order_id){		
		$db = $this->_db;		
		//get order 		
		$query = 'SELECT * FROM #__yos_resources_manager_order WHERE `id` ='.$order_id;
		$db->setQuery($query);
		$order = $db->loadObject();		
	
		$row = null;
		if ($order) {		
	
			$query = 'SELECT YRMP.* , YRMC.currency_name,  YRMC.currency_code '
				. ' FROM #__yos_resources_manager_package YRMP, #__yos_resources_manager_currency AS YRMC WHERE published =1 AND YRMC.id = YRMP.currency AND YRMP.id ='.$order->package_id;
			$db->setQuery($query);
			$row = $db->loadObject();
			$row->return_url = $order->return_url;
		}		
		
		return $row; 		
	}
	function getPaymentMethods($packageid){
		$db = $this->_db;
		
		// Lets load the data if it doesn't already exist
		
		$query = 'SELECT YRMPM.* '
			. ' FROM #__yos_resources_manager_payment_method YRMPM, #__yos_resources_manager_package_payment_method_xref AS YRMPPM '.
			' WHERE published =1 AND YRMPPM.payment_method_id = YRMPM.id AND YRMPPM.package_id = '.$packageid ;
		$db->setQuery($query);
		
		$row = $db->loadObjectList();
		
		return $row; 		
	}
	function getPaymentMethod($packageid){
		$db = $this->_db;
		
		// Lets load the data if it doesn't already exist
		
		$query = 'SELECT * '
			. ' FROM #__yos_resources_manager_payment_method WHERE published =1 AND id = '.$packageid ;
		$db->setQuery($query);
		$row = $db->loadObject();
		
		return $row; 		
	}
	function insertOrder($packageid,$return_url){
		$user = JFactory::getUser();
		$date = JFactory::getDate();
		$date = $date->toMySQL();
		$db =& JFactory::getDBO();
		
		$query = '';		
		$query .= "INSERT INTO `#__yos_resources_manager_order` SET \n";
		$query .= "`id` = null, \n";
		$query .= "`user_id` = $user->id, \n";
		$query .= "`package_id` = $packageid, \n";
		$query .= "`return_url` = '$return_url', \n";
		$query .= "`date` = '$date', \n";
		$query .= "`status` = 'P' \n";
		$db->setQuery($query);
		$db->query();
		
		$order_id = $db->insertid();
		
		$this->sendEmail($order_id, JText::_('PENDING'));
		return $order_id;
	}
	function updateOrder($order_id,$status){
		
		//update order table
		//$user = JFactory::getUser();
		$date = JFactory::getDate();
		$date = $date->toMySQL();
		$db =& JFactory::getDBO();		
		$query = '';		
		$query .= "UPDATE `#__yos_resources_manager_order` SET \n";		
		$query .= "`date` = '$date', \n";
		$query .= "`status` = '$status' \n";
		$query .= "WHERE `id` = $order_id";
		$db->setQuery($query);
		$db->query();
		//get user_id 		
		$query = 'SELECT user_id FROM #__yos_resources_manager_order WHERE `id` ='.$order_id;
		$db->setQuery($query);
		$userid = $db->loadResult();
		
		//get packageid
		$query = 'SELECT package_id FROM #__yos_resources_manager_order WHERE `id` ='.$order_id;
		$db->setQuery($query);
		$rs = $db->loadResult();
		
		//get package object
		
		$query = 'SELECT * FROM #__yos_resources_manager_package_object_xref WHERE `package_id` ='.$rs;
		$db->setQuery($query);
		$objlist = $db->loadObjectList();
		
		//update user_group
		
		foreach ($objlist as $object){
			if ($object->type=='group') {
				$this->updateUserGroup($object, $userid);				
			}
			if ($object->type=='role') {
				$this->updateUserRole($object, $userid);				
			}
			if ($object->type=='resource') {
				$this->updateUserRes($object, $userid);				
			}				
		}		
		return '';
	}
	function updateUserGroup($object, $userid){		
		$date = JFactory::getDate();		
		$date = $date->toMySQL();
		
		$db =& JFactory::getDBO();
		$query = 'SELECT * FROM #__yos_resources_manager_user_group_xref WHERE `user_id` ='.$userid.' AND group_id = '.$object->object_id ;
		$db->setQuery($query);
		$objusergroup = $db->loadObject();
				
		if ($objusergroup) {// update
			if ($objusergroup->end >= $date) {
				//echo 'OK 1 <hr/>';
				
				$query = "UPDATE `#__yos_resources_manager_user_group_xref` SET \n";		
				$query .= "`end` = '$objusergroup->end' + INTERVAL $object->seconds SECOND \n";				
				$query .= "WHERE `user_id` =".$userid." AND group_id = ".$object->object_id ;
				$db->setQuery($query);
				$db->query();
			}elseif ($objusergroup->end != '0000-00-00 00:00:00') {
				//echo 'OK 2 <hr />';
				
				$query = "UPDATE `#__yos_resources_manager_user_group_xref` SET \n";		
				$query .= "`end` = '$date' + INTERVAL $object->seconds SECOND, \n";			
				$query .= "`start` = '$date' \n";			
				$query .= "WHERE `user_id` =".$userid." AND `group_id` = ".$object->object_id ;
				$db->setQuery($query);
				$db->query();				
			}
		}else { // insert
			$query = '';		
			$query .= "INSERT INTO `#__yos_resources_manager_user_group_xref` SET \n";
			$query .= "`user_id` = $userid, \n";
			$query .= "`group_id` = $object->object_id, \n";
			$query .= "`end` = '$date' + INTERVAL $object->seconds SECOND, \n";
			$query .= "`start` = '$date' \n";
			$db->setQuery($query);
			$db->query();
			
		}		
	}
	function updateUserRole($object, $userid){
		$date = JFactory::getDate();		
		$date = $date->toMySQL();
		
		$db =& JFactory::getDBO();
		$query = 'SELECT * FROM #__yos_resources_manager_user_role_xref WHERE `user_id` ='.$userid.' AND role_id = '.$object->object_id ;
		$db->setQuery($query);
		$objusergrole = $db->loadObject();		
				
		if ($objusergrole) {// update
			if ($objusergrole->end >= $date) {
				//echo 'OK 1 <hr/>';
				
				$query = "UPDATE `#__yos_resources_manager_user_role_xref` SET \n";		
				$query .= "`end` = '$objusergrole->end' + INTERVAL $object->seconds SECOND \n";				
				$query .= "WHERE `user_id` =".$userid." AND role_id = ".$object->object_id ;
				$db->setQuery($query);
				$db->query();
			}elseif ($objusergrole->end != '0000-00-00 00:00:00') {
				//echo 'OK 2 <hr />';
				
				$query = "UPDATE `#__yos_resources_manager_user_role_xref` SET \n";		
				$query .= "`end` = '$date' + INTERVAL $object->seconds SECOND, \n";			
				$query .= "`start` = '$date' \n";			
				$query .= "WHERE `user_id` =".$userid." AND `role_id` = ".$object->object_id ;
				$db->setQuery($query);
				$db->query();
						
			}
		}else { // insert
			//echo 'OK 3 <hr/>';
			$query = '';		
			$query .= "INSERT INTO `#__yos_resources_manager_user_role_xref` SET \n";
			$query .= "`user_id` = $userid, \n";
			$query .= "`role_id` = $object->object_id, \n";
			$query .= "`end` = '$date' + INTERVAL $object->seconds SECOND, \n";
			$query .= "`start` = '$date' \n";
			$db->setQuery($query);
			$db->query();
			
		}
	}
	function updateUserRes($object, $userid){
		
		$date = JFactory::getDate();		
		$date = $date->toMySQL();
		
		$db =& JFactory::getDBO();
		$query = 'SELECT * FROM #__yos_resources_manager_user_resource_xref WHERE `user_id` ='.$userid.' AND resource_id = '.$object->object_id ;
		$db->setQuery($query);
		$objuserRes = $db->loadObject();
		
		if ($objuserRes) {// update
			// udata time
			if ($objuserRes->end >= $date) {
				echo 'OK 1 <hr/>';				
				$query = "UPDATE `#__yos_resources_manager_user_resource_xref` SET \n";		
				$query .= "`end` = '$objuserRes->end' + INTERVAL $object->seconds SECOND \n";				
				$query .= "WHERE `user_id` =".$userid." AND resource_id = ".$object->object_id ;
				$db->setQuery($query);
				$db->query();
			}elseif ($objuserRes->end!='0000-00-00 00:00:00' ) {
				echo 'OK 2 <hr />';
				
				$query = "UPDATE `#__yos_resources_manager_user_resource_xref` SET \n";		
				$query .= "`end` = '$date' + INTERVAL $object->seconds SECOND, \n";			
				$query .= "`start` = '$date' \n";			
				$query .= "WHERE `user_id` =".$userid." AND `resource_id` = ".$object->object_id ;
				$db->setQuery($query);
				$db->query();
					
			}
			if ($objuserRes->times_access != -1) {
				//echo 'OK 2.1 <hr />';
				
				$query = "UPDATE `#__yos_resources_manager_user_resource_xref` SET \n";		
				$query .= "`times_access` = $objuserRes->times_access +  $object->times_access , \n";					
				$query .= "WHERE `user_id` =".$userid." AND `resource_id` = ".$object->object_id ;
				$db->setQuery($query);
				$db->query();		
			}
			
		}else { // insert
			//echo 'OK 3 <hr/>';
			$query = '';		
			$query .= "INSERT INTO `#__yos_resources_manager_user_resource_xref` SET \n";
			$query .= "`user_id` = $userid, \n";
			$query .= "`resource_id` = $object->object_id, \n";
			$query .= "`times_access` = $object->times_access , \n";
			if ($object->seconds >=0) {
				$query .= "`end` = '$date' + INTERVAL $object->seconds SECOND, \n";
			}else {
				$query .= "`end` = '0000-00-00 00:00:00', \n";
			}
			
			$query .= "`start` = '$date' \n";
			$db->setQuery($query);
			$db->query();
			
		}
	}
	function updateUserGroupRefunded($object, $userid){	
		$date = JFactory::getDate();		
		$date = $date->toMySQL();
		
		$db =& JFactory::getDBO();
		$query = 'SELECT * FROM #__yos_resources_manager_user_group_xref WHERE `user_id` ='.$userid.' AND group_id = '.$object->object_id ;
		$db->setQuery($query);
		$objusergroup = $db->loadObject();
				
		if ($objusergroup) {// update
			if ($objusergroup->end >= $date) {
				//echo 'OK 1 <hr/>';
				
				$query = "UPDATE `#__yos_resources_manager_user_group_xref` SET \n";		
				$query .= "`end` = '$objusergroup->end' - INTERVAL $object->seconds SECOND \n";				
				$query .= "WHERE `user_id` =".$userid." AND group_id = ".$object->object_id ;
				$db->setQuery($query);
				$db->query();
			}elseif ($objusergroup->end != '0000-00-00 00:00:00') {
				//echo 'OK 2 <hr />';
				
				$query = "UPDATE `#__yos_resources_manager_user_group_xref` SET \n";		
				$query .= "`end` = '$date' - INTERVAL $object->seconds SECOND, \n";			
				$query .= "`start` = '$date' \n";			
				$query .= "WHERE `user_id` =".$userid." AND `group_id` = ".$object->object_id ;
				$db->setQuery($query);
				$db->query();				
			}
		}	
	}
	function updateUserRoleRefunded($object, $userid){
		$date = JFactory::getDate();		
		$date = $date->toMySQL();
		
		$db =& JFactory::getDBO();
		$query = 'SELECT * FROM #__yos_resources_manager_user_role_xref WHERE `user_id` ='.$userid.' AND role_id = '.$object->object_id ;
		$db->setQuery($query);
		$objusergrole = $db->loadObject();		
				
		if ($objusergrole) {// update
			if ($objusergrole->end >= $date) {
				//echo 'OK 1 <hr/>';
				
				$query = "UPDATE `#__yos_resources_manager_user_role_xref` SET \n";		
				$query .= "`end` = '$objusergrole->end' - INTERVAL $object->seconds SECOND \n";				
				$query .= "WHERE `user_id` =".$userid." AND role_id = ".$object->object_id ;
				$db->setQuery($query);
				$db->query();
			}elseif ($objusergrole->end != '0000-00-00 00:00:00') {
				//echo 'OK 2 <hr />';
				
				$query = "UPDATE `#__yos_resources_manager_user_role_xref` SET \n";		
				$query .= "`end` = '$date' - INTERVAL $object->seconds SECOND, \n";			
				$query .= "`start` = '$date' \n";			
				$query .= "WHERE `user_id` =".$userid." AND `role_id` = ".$object->object_id ;
				$db->setQuery($query);
				$db->query();
						
			}
		}
	}
	function updateUserResRefunded($object, $userid){
		
		$date = JFactory::getDate();		
		$date = $date->toMySQL();
		
		$db =& JFactory::getDBO();
		$query = 'SELECT * FROM #__yos_resources_manager_user_resource_xref WHERE `user_id` ='.$userid.' AND resource_id = '.$object->object_id ;
		$db->setQuery($query);
		$objuserRes = $db->loadObject();
		
		if ($objuserRes) {// update
			// udata time
			if ($objuserRes->end >= $date) {
				echo 'OK 1 <hr/>';				
				$query = "UPDATE `#__yos_resources_manager_user_resource_xref` SET \n";		
				$query .= "`end` = '$objuserRes->end' - INTERVAL $object->seconds SECOND \n";				
				$query .= "WHERE `user_id` =".$userid." AND resource_id = ".$object->object_id ;
				$db->setQuery($query);
				$db->query();
			}elseif ($objuserRes->end!='0000-00-00 00:00:00' ) {
				echo 'OK 2 <hr />';
				
				$query = "UPDATE `#__yos_resources_manager_user_resource_xref` SET \n";		
				$query .= "`end` = '$date' - INTERVAL $object->seconds SECOND, \n";			
				$query .= "`start` = '$date' \n";			
				$query .= "WHERE `user_id` =".$userid." AND `resource_id` = ".$object->object_id ;
				$db->setQuery($query);
				$db->query();
					
			}
			if ($objuserRes->times_access != -1) {
				//echo 'OK 2.1 <hr />';
				
				$query = "UPDATE `#__yos_resources_manager_user_resource_xref` SET \n";		
				$query .= "`times_access` = $objuserRes->times_access -  $object->times_access , \n";					
				$query .= "WHERE `user_id` =".$userid." AND `resource_id` = ".$object->object_id ;
				$db->setQuery($query);
				$db->query();		
			}
			
		}
	}
	
	function isUpdateUserInfo(){
		$user = JFactory::getUser();
		//get userinfo
		$db =& JFactory::getDBO();
		$query = 'SELECT * FROM #__yos_resources_manager_user_info WHERE `user_id` ='.$user->id;
		$db->setQuery($query);
		$rs = $db->loadObject();
		
		return $rs;		
	}
	function isUpdateUserInfo1($user_id){
		
		//get userinfo
		$db =& JFactory::getDBO();
		$query = 'SELECT * FROM #__yos_resources_manager_user_info WHERE `user_id` ='.$user_id;
		$db->setQuery($query);
		$rs = $db->loadObject();
		
		return $rs;		
	}
	function sendEmail($order_id,$order_status){
		global $mainframe;
		$db =& JFactory::getDBO();
//		$db->query();
		//get order 		
		$query = 'SELECT * FROM #__yos_resources_manager_order WHERE `id` ='.$order_id;
		$db->setQuery($query);
		$order = $db->loadObject();	
		
		$mail = JFactory::getMailer();
		$MailFrom 	= $mainframe->getCfg('mailfrom');
		$FromName 	= $mainframe->getCfg('fromname');
		$subject 	= JText::_('PURCHASE_ORDER');
		$subject 	= str_replace('{ORDER_NUMBER}',$order_id,$subject);
		$user_info = $this->isUpdateUserInfo1($order->user_id);
		$user = JFactory::getUser($order->user_id);
			
		$package = $this->getPackage($order->package_id);
		
		$body = 'Dear '.$user_info->first_name. ', <br />';
		$body .= JText::_('ORDER_INFORMATION').' <br />';
		$body .= JText::_('ORDER_NUMBER').': '.$order_id.'<br />';
		$body .= JText::_('ORDER_DATE').': '.JHTML::_('date',JFactory::getDate()->toMySQL()).'<br />';
		$body .= JText::_('ORDER_STATUS').': '.$order_status.'<br />';
		$body .= JText::_('CUSTOMER_INFORMATION').'<br />';
		$body .= JText::_('EMAIL').': '.$user->email.'<br />';
		$body .= JText::_('FIRST_NAME').': '.$user_info->first_name.'<br />';
		$body .= JText::_('LAST_NAME').': '.$user_info->last_name.'<br />';
		$body .= JText::_('ADDRESS').': '.$user_info->address_1.' '.$user_info->address_2.'<br />';
		$body .= JText::_('CITY').': '.$user_info->city.'<br />';
		$body .= JText::_('STATE').': '.$user_info->state.'<br />';
		$body .= JText::_('COUNTRY').': '.$user_info->country.'<br />';
		$body .= JText::_('PHONE').': '.$user_info->phone.'<br />';
		$body .= JText::_('ORDER_ITEMS').'<br />';
		$body .= JText::_('NAME').': '.$package->name.'<br />';
		$body .= JText::_('PRICE').': '.$package->value.' '.$package->currency_code.'<br /><br />';
		$body .= JText::_('THANK_YOU_YOUR_PURCHASE');
		
		$mail->addRecipient($user->email);
		$mail->addBCC($MailFrom);		
		$mail->setSender( array( $MailFrom, $FromName ) );
		$mail->setSubject( $FromName.': '.$subject );
		$mail->setBody( $body );
		$mail->IsHTML(1);
		$sent = $mail->Send();		
		
	}
}