<?php
/**
 * @version		$Id: yos_resources_manager.php $
 * @package		YRM
 * @subpackage	plugin system
 * @copyright	Copyright (C) 2010 YOS.,JSC. All rights reserved.
 * @license	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.plugin.plugin');

/**
 * Example User Plugin
 *
 * @package		Joomla
 * @subpackage	JFramework
 * @since 		1.5
 */
class plgUserYos_Resources_manager extends JPlugin {

	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @param 	array  $config  An array that holds the plugin configuration
	 * @since 1.5
	 */
	function plgUserExample(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	/**
	 * Example store user method
	 *
	 * Method is called before user data is stored in the database
	 *
	 * @param 	array		holds the old user data
	 * @param 	boolean		true if a new user is stored
	 */
	function onBeforeStoreUser($user, $isnew)
	{
		global $mainframe;
	}

	/**
	 * Example store user method
	 *
	 * Method is called after user data is stored in the database
	 *
	 * @param 	array		holds the new user data
	 * @param 	boolean		true if a new user is stored
	 * @param	boolean		true if user was succesfully stored in the database
	 * @param	string		message
	 */
	function onAfterStoreUser($user, $isnew, $success, $msg)
	{
		if ($success) {
			if ($isnew)
			{
				// Call a function in the external app to create the user
				$this->createUser($user);
			}
		}
	}

	function createUser($user){
		$db = &JFactory::getDBO();
		
		$query = 'SELECT `yrm_group_id` FROM `#__yos_resources_manager_mapping` WHERE `joomla_group_id`='.$user['gid'];
		$db->setQuery($query);
		$arr_gid = $db->loadResultArray();
		if (count($arr_gid)) {
			$query_insert = ' INSERT INTO `#__yos_resources_manager_user_group_xref`(`user_id`, `group_id`, `start`, `end`) VALUES';
			$values = array();
			foreach ($arr_gid as $gid ){
				// get fields from group table
				$query = ' SELECT * FROM `#__yos_resources_manager_group` WHERE `id`='.$gid;
				$db->setQuery($query);
				$row = $db->loadObject();
				
				// --> start, end for user.
				$start_tmp	 = JFactory::getDate();
				$start 		 = $start_tmp->toMySQL();
				if ($row->time_mapping > 0) {
					$end_tmp = JFactory::getDate((JFactory::getDate()->_date)+($row->time_mapping));
					$end 	 = $end_tmp->toMySQL();
				} else {
					$end 	 = '0000-00-00 00:00:00';
				}
				// assign for $values variable
				$values[] = '('.$user['id'].','.$gid.', "'.$start.'", "'.$end.'")';
			}
			$str_values = implode(',', $values);
			// insert selected data to database.
			$query_insert .= $str_values;
			$db->setQuery($query_insert);
			if (!$db->query()) {
				JError::raiseError(500, $db->getErrorMsg());				
			}
		}
	}
	
	/**
	 *
	 * Method is called before user data is deleted from the database
	 *
	 * @param 	array		holds the user data
	 */
	function onBeforeDeleteUser($user)
	{
		global $mainframe;
	}

	/**
	 *
	 * Method is called after user data is deleted from the database
	 *
	 * @param 	array		holds the user data
	 * @param	boolean		true if user was succesfully stored in the database
	 * @param	string		message
	 */
	function onAfterDeleteUser($user, $succes, $msg)
	{
		global $mainframe;
	 	// only the $user['id'] exists and carries valid information

		// Call a function in the external app to delete the user
		if ($succes) {
			$this->deleteUser($user['id']);	
		}
	}

	function deleteUser($user_id){
		$db = &JFactory::getDBO();
		
		$arr_table_name = array();
		$arr_table_name[] = '#__yos_resources_manager_user_group_xref';
		$arr_table_name[] = '#__yos_resources_manager_user_info';
		$arr_table_name[] = '#__yos_resources_manager_user_resource_banned';
		$arr_table_name[] = '#__yos_resources_manager_user_resource_xref';
		$arr_table_name[] = '#__yos_resources_manager_user_role_xref';
		$arr_table_name[] = '#__yos_resources_manager_order';
		foreach ($arr_table_name as $table_name){
			$query_del = ' DELETE IGNORE FROM '.$table_name.' WHERE `user_id`='.$user_id;
			$db->setQuery($query_del);
			if (!$db->query()) {
				JError::raiseError(500, $db->getErrorMsg());
			}
		}
	}
	
	/**
	 * This method should handle any login logic and report back to the subject
	 *
	 * @access	public
	 * @param 	array 	holds the user data
	 * @param 	array    extra options
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function onLoginUser($user, $options)
	{
		// Initialize variables
		$success = true;

		// Here you would do whatever you need for a login routine with the credentials
		//
		// Remember, this is not the authentication routine as that is done separately.
		// The most common use of this routine would be logging the user into a third party
		// application.
		//
		// In this example the boolean variable $success would be set to true
		// if the login routine succeeds

		// ThirdPartyApp::loginUser($user['username'], $user['password']);

		return $success;
	}

	/**
	 * This method should handle any logout logic and report back to the subject
	 *
	 * @access public
	 * @param array holds the user data
	 * @return boolean True on success
	 * @since 1.5
	 */
	function onLogoutUser($user)
	{
		// Initialize variables
		$success = true;

		// Here you would do whatever you need for a logout routine with the credentials
		//
		// In this example the boolean variable $success would be set to true
		// if the logout routine succeeds

		// ThirdPartyApp::loginUser($user['username'], $user['password']);

		return $success;
	}
}

