<?php
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<?php

jimport( 'joomla.application.component.model' );

class YRMModelUser extends JModel{	
	
	var $_data = null;
	
	function getData(){
		$user = JFactory::getUser();
		//get userinfo
		$db =& JFactory::getDBO();
		$query = 'SELECT * FROM #__yos_resources_manager_user_info WHERE `user_id` ='.$user->id;
		$db->setQuery($query);
		$rs  = $db->loadObject();	
		
		return $rs; 		
	}
	
	
	function save(){		
		$id = JRequest::getInt('id');
		
		$first_name = JRequest::getString('first_name');
		$last_name = JRequest::getString('last_name');
		$middle_name = JRequest::getString('middle_name');
		$address_1 = JRequest::getString('address_1');
		$address_2 = JRequest::getString('address_2');
		$city = JRequest::getString('city');
		$zip = JRequest::getInt('zip');
		$state = JRequest::getString('state');
		$country = JRequest::getString('country');
		$phone = JRequest::getString('phone');
		
		$user = JFactory::getUser();
		$date = JFactory::getDate();
		$date = $date->toMySQL();
		
		
		$db = $this->_db;
		$query = '';
		if ($id ==0 ) {
			$query .= "INSERT INTO `#__yos_resources_manager_user_info` SET \n";
			$query .= " `id` =null,";
		}else {
			$query .= "UPDATE `#__yos_resources_manager_user_info` SET \n";
		}
		$query .= "`user_id` = $user->id, \n";
		$query .= "`last_name` = '$last_name', \n";
		$query .= "`first_name` = '$first_name', \n";
		$query .= "`middle_name` = '$middle_name', \n";
		$query .= "`address_1` = '$address_1', \n";
		$query .= "`address_2` = '$address_2', \n";
		$query .= "`phone` = '$phone', \n";
		$query .= "`city` = '$city', \n";
		$query .= "`state` = '$state', \n";
		$query .= "`country` = '$country', \n";
		$query .= "`zip` = '$zip', \n";
		if ($id==0) {
			$query .= "`cdate` = '$date' \n";
		}else {
			$query .= "`mdate` = '$date' \n";
			$query .= "WHERE `id` = $id \n";
		}
		
		$db->setQuery($query);
		$db->query();
		//var_dump($db->getErrorMsg());	
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
}