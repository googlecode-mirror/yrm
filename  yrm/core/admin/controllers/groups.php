<?php
/**
 * @version	$Id: resources.php $
 * @package	YRM
 * @subpackage	Component
 * @copyright	Copyright (C) 2010 YOS.,JSC. All rights reserved.
 * @license	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );
// Set the table directory
JTable::addIncludePath( JPATH_COMPONENT.DS.'tables' );
/**
 * @package		Joomla
 * @subpackage	Banners
 */

class YRMControllergroups extends JController
{
	/**
	 * Constructor
	 */
	
	function __construct( $config = array() )
	{
		parent::__construct( $config );
		// Register Extra tasks
		$this->registerTask( 'add',			'edit' );
		$this->registerTask( 'add_user',	'edit_user' );
		$this->registerTask( 'add_package',	'edit_package' );
		$this->registerTask( 'apply',		'save' );
		$this->registerTask( 'apply_user',	'save_user' );
		$this->registerTask( 'apply_package','save_package' );
		$this->registerTask( 'users','users' );
		// Register Extra tasks
		$this->registerTask( 'apply_resources',	'save_resources' );
	}

	/**
	 * Display the list of banners
	 */
	function display()
	{
		parent::display();
	}
	
	function cancel()
	{
		global $option;
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$this->setRedirect( 'index.php?option='. $option .'&view=groups' );
	}
	
	function cancel_multi_users()
	{
		global $option;
		$cid = JRequest::getVar('cid_group');
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$link 		=  'index.php?option=com_yos_resources_manager&view=groups&controller=groups&task=users&cid_group[]='. intval($cid[0]);

		$this->setRedirect($link );
	}
	
	function edit()
	{
		global $option;
		$db		=& JFactory::getDBO();
		$view	= $this->getView('groups', 'form');
		if ($model=& $this->getModel('groups')) {
			$view->setModel($model);
			$view->setLayout('form');
		}
		$view->display();
	}
	
	function add_multi_users(){
		global $option;
		$db		=& JFactory::getDBO();
		$view=$this->getView('groups', 'multi_users');
		if ($model=& $this->getModel('groups')) {
			$view->setModel($model);
			$view->setLayout('multi_users');
		}
		$view->display();
	}
	
	function edit_user()
	{
		global $option;
		$db		=& JFactory::getDBO();
		$view=$this->getView('groups', 'form_user');
		if ($model=& $this->getModel('groups')) {
			$view->setModel($model);
			$view->setLayout('form_user');
		}
		$view->display();
	}
	
	function edit_package()
	{
		global $option;
		$db		=& JFactory::getDBO();
		$view=$this->getView('groups', 'form_package');
		if ($model=& $this->getModel('groups')) {
			$view->setModel($model);
			$view->setLayout('form_package');
		}
		$view->display();
	}
	
	function save(){
		global $option;
		$row			=& JTable::getInstance('groups', 'Table');
		$post	= JRequest::get( 'post' );
		if (!$row->bind( $post ))
		{
			JError::raiseError(500, $row->getError() );
		}
		$isNew = ($row->id == 0);

		if (!$row->check())
		{
			JError::raiseError(500, $row->getError() );
		}
		if (!$row->store())
		{
			JError::raiseError(500, $row->getError() );
		}
		
		$arr_gid = JRequest::getVar('gid');
		
		if(count($arr_gid)){
			$model = $this->getModel('groups');
			$model->store_mapping($arr_gid, $row->id, $time);
		}
		switch ($task = JRequest::getVar('task'))
		{
			case 'apply':
				$msg = JText::_( 'CHANGES TO GROUP SAVED' );
				$link = 'index.php?option=com_yos_resources_manager&view=groups&controller=groups&task=edit&cid_group[]='. $row->id .'';
				break;

			case 'save':
			default:
				$msg = JText::_( 'GROUP SAVED' );
				$link = 'index.php?option=com_yos_resources_manager&view=groups';
				break;
		}

		$this->setRedirect($link, $msg);
	}
	
	function remove()
	{
		global $mainframe;
		$db 		= &JFactory::getDBO();
		$cid 		= JRequest::getVar('cid_group');
		$temp_ids 	= implode(',',$cid);
		
		// delete from jos_yos_resources_manager_resource_role_xref
		$query_delete_role_resource = " DELETE FROM `#__yos_resources_manager_resource_role_xref` WHERE role_id IN (".$temp_ids.")";
		$db->setQuery($query_delete_role_resource);
		if (!$db->query()) {
			JError::raiseWarning( 500, $db->getError() );
		}
		// delete from jos_yos_resources_manager_user_role_xref
		$query_delete_role_user = " DELETE FROM `#__yos_resources_manager_user_role_xref` WHERE role_idIN (".$temp_ids.")";
		$db->setQuery($query_delete_role_user);
		if (!$db->query()) {
			JError::raiseWarning( 500, $db->getError() );
		}
		// delete from jos_yos_resources_manager_group_role_xref
		$query_delete_role_group = " DELETE FROM `#__yos_resources_manager_group_role_xref` WHERE role_id IN (".$temp_ids.")";
		$db->setQuery($query_delete_role_group);
		if (!$db->query()) {
			JError::raiseWarning( 500, $db->getError() );
		}
		// delete from jos_yos_resources_manager_package_object_xref
		$query_delete_role_package = " DELETE FROM `#__yos_resources_manager_package_object_xref` 
										WHERE object_id IN (".$temp_ids.") AND `type`='role'";
		$db->setQuery($query_delete_role_package);
		if (!$db->query()) {
			JError::raiseWarning( 500, $db->getError() );
		}
		// delete from jos_yos_resources_manager_role
		$query_delete_role = " DELETE FROM `#__yos_resources_manager_role` 
										WHERE id IN (".$temp_ids.")";
		$db->setQuery($query_delete_role);
		if (!$db->query()) {
			JError::raiseWarning( 500, $db->getError() );
		}
		$mainframe->redirect('index.php?option=com_yos_resources_manager&view=roles');
	}
	
	function resources(){
		$view =& $this->getView('roles','resources');	
		
		if ($model=& $this->getModel('roles')) {
			$view->setModel($model);
		}
		$view->setLayout('resources');
		$view->display();
	}
	function xmltree(){
		$view =& $this->getView('groups', 'xml');
		if ($model=& $this->getModel('groups')) {
			$view->setModel($model);	
		}
		$view->setlayout('xml');
		$view->display();
	}
	
	function save_resources(){
		global $option;
		
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$role_id = JRequest::getInt('role_id');
		$cmd = JRequest::getCmd('task');
		
		$model=& $this->getModel('roles');
		$model->load($role_id);
		$result = $model->save_resources();
		
		if ($cmd == 'apply_resources') {
			$this->setRedirect('index.php?option='.$option.'&controller=roles&task=resources&cid_role[]='.$role_id);
		}
		else {
			$this->setRedirect('index.php?option='.$option.'&view=roles');
		}
	}
	
	function users(){
		$view =& $this->getView('groups','users');	
		
		if ($model=& $this->getModel('groups')) {
			$view->setModel($model);
		}
		$view->setLayout('users');
		$view->display();
	}
	
	function cancel_user(){
		global $mainframe;
		$group_id = JRequest::getVar('group_id');
		$mainframe->redirect('index.php?option=com_yos_resources_manager&controller=groups&view=groups&task=users&cid_group[]='.$group_id);
	}
	
	function packages(){
		$view =& $this->getView('groups','packages');	
		
		if ($model=& $this->getModel('groups')) {
			$view->setModel($model);
		}
		$view->setLayout('packages');
		$view->display();
	}
	
	function cancel_package(){
		global $mainframe;
		$group_id = JRequest::getVar('group_id');
		$mainframe->redirect('index.php?option=com_yos_resources_manager&controller=groups&view=groups&task=packages&cid_group[]='.$group_id);
	}
	
	function roles(){
		$view =& $this->getView('groups','roles');	
		
		if ($model=& $this->getModel('groups')) {
			$view->setModel($model);
		}
		$view->setLayout('roles');
		$view->display();
	}
	
	function cancel_role(){
		global $mainframe;
		$mainframe->redirect('index.php?option=com_yos_resources_manager&controller=groups&view=groups');
	}
	
	function save_roles(){
		global $mainframe;
		$db = &JFactory::getDBO();
		$group_id = JRequest::getVar('group_id');
		$cids = JRequest::getVar('cid_role');
		
		$query = 'DELETE FROM `#__yos_resources_manager_group_role_xref` WHERE group_id='.$group_id;
		$db->setQuery($query);
		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg() );
		}
		
		$value = array();
		if (count($cids)) {
			foreach ($cids as $cid) {
				$value[] =' ('.$group_id.', '.$cid.')';
			}
			$value = ' VALUES '. implode( ', ', $value );
			$query_insert = 'INSERT INTO `#__yos_resources_manager_group_role_xref`(`group_id`, `role_id`) ';
			$query_insert.=$value;
			$db->setQuery($query_insert);
			if (!$db->query()) {
				JError::raiseError(500, $db->getErrorMsg());
			}
		}
		$msg = JText::_('Successfully Saved Roles');
		$mainframe->redirect('index.php?option=com_yos_resources_manager&view=groups&controller=groups&cid_group[]='.$group_id.'&task=roles', $msg);
	}
	
	function save_user(){
		global $mainframe;
		$db 			= &JFactory::getDBO();
		$user_id 		= intval(JRequest::getVar('user_id'));
		$group_id 		= intval(JRequest::getVar('group_id'));
		$cid			= JRequest::getVar('cid_user');
		$user_id_old	= intval($cid[0]);
		$task 			= JRequest::getVar('task');
		$cmd			= JRequest::getVar('cmd');
		$details 		= JRequest::getVar('details');
		
		$start 			= $details['start'];
		$end 			= $details['end'];
		$date 			=& JFactory::getDate($start);
		$date_start 	= $date->toMySQL();
		
		$date 			=& JFactory::getDate($end);
		$date_end 		= $date->toMySQL();

		// Store the content to the database
		if ($cmd == 'add_user') {
			$query_check = 'SELECT COUNT(*) FROM #__yos_resources_manager_user_group_xref WHERE user_id='.$user_id.' AND group_id='.$group_id;
			$db->setQuery($query_check);
			$count = $db->loadResult();
			if ($count > 0) {
				$msg = JText::_('The selected user has assigned to this group before. Please select an other user');
				$mainframe->enqueueMessage($msg,'notice');
				$mainframe->redirect('index.php?option=com_yos_resources_manager&view=groups&controller=groups&group_id='.$group_id.'&task=add_user');
			}
			$query = 'INSERT INTO #__yos_resources_manager_user_group_xref(`user_id`, `group_id`, `start`, `end`) '
					.' VALUES( '.$user_id.', '.$group_id.', "'.$date_start.'","'.$date_end.'")';
		}else {
			$query_check = 'SELECT COUNT(*) FROM #__yos_resources_manager_user_group_xref WHERE user_id='.$user_id.' AND group_id='.$group_id.' AND user_id!='.$user_id_old;
			$db->setQuery($query_check);
			$count = $db->loadResult();
			if ($count > 0) {
				$msg = JText::_('The selected user has assigned to this group before. Please select an other user');
				$mainframe->enqueueMessage($msg,'notice');
				$mainframe->redirect('index.php?option=com_yos_resources_manager&view=groups&controller=groups&group_id='.$group_id.'&task=add_user');
			}
			$query = 'UPDATE #__yos_resources_manager_user_group_xref 
						SET	`user_id`	= '.$user_id.', 
					 		`start`		= "'.$date_start.'", 
					 		`end`		= "'.$date_end.'"
					 	WHERE user_id	='.$user_id_old.'
					 	AND group_id 	='.$group_id;
		}
		$db->setQuery($query);
		if (!$db->query())
		{
			JError::raiseError(500, $db->getErrorMsg() );
		}
		switch ($task) {
			case 'apply_user' :
				$msg = JText::_('Successfully Saved User');
				$mainframe->redirect('index.php?option=com_yos_resources_manager&view=groups&controller=groups&cid_user[]='.$user_id.'&group_id='.$group_id.'&task=edit_user', $msg);
				break;

			case 'save_user' :
				$msg = JText::_('Successfully Saved User');
				$mainframe->redirect('index.php?option=com_yos_resources_manager&view=groups&controller=groups&cid_group[]='.$group_id.'&task=users', $msg);
				break;
		}
	}
	
	function save_multi_users(){ 
		global $mainframe;
		$db = &JFactory::getDBO();
		
		$cid_group 	= JRequest::getVar('cid_group');
		$gr_u_start = JRequest::getVar('gu_start');
		$gr_u_end 	= JRequest::getVar('gu_end');
		$cid_user 	= JRequest::getVar('cid_user');
		if (count($cid_user)) {
			$query_insert = 'INSERT INTO #__yos_resources_manager_user_group_xref(`user_id`, `group_id`, `start`, `end`)  VALUES ';
			$values = array();
			foreach ($cid_user as $cid){
				$values[] = '('.$cid.', '.intval($cid_group[0]).', "'.$gr_u_start.'","'.$gr_u_end.'")';
			}
			$str_values = implode(',', $values);
			$query_insert .= $str_values;
			$db->setQuery($query_insert);
			if(!$db->query()){
				JError::raiseError(500, $db->getErrorMsg());
			}
		}
		
		$msg = JText::_('Successfully Added Users');
		$mainframe->redirect('index.php?option=com_yos_resources_manager&view=groups&controller=groups&cid_group[]='.intval($cid_group[0]).'&task=users', $msg);
				
	}
	
	function remove_user(){
		global $mainframe;
		$db 		= &JFactory::getDBO();
		
		$cid 		= JRequest::getVar('cid_user');
		$user_id 	= implode(',',$cid);
		$group_id 	= JRequest::getVar('group_id');
		$query 		= "DELETE FROM `#__yos_resources_manager_user_group_xref` WHERE group_id=".$group_id." AND user_id IN(".$user_id.")";
		$db->setQuery($query);
		if (!$db->query())
		{
			JError::raiseError(500, $db->getErrorMsg() );
		}
		
		$msg = JText::_('Successfully Removed User');
		$mainframe->redirect('index.php?option=com_yos_resources_manager&view=groups&controller=groups&cid_group[]='.$group_id.'&task=users', $msg);
	}
	function save_package(){
		
		global $mainframe;
		$db 			= &JFactory::getDBO();
		$package_id 	= intval(JRequest::getVar('package_id'));
		$group_id 		= intval(JRequest::getVar('group_id'));
		$cid			= JRequest::getVar('cid_package');
		$package_id_old = intval($cid[0]);
		$task 			= JRequest::getVar('task');
		$cmd 			= JRequest::getVar('cmd');
		$times_access	= JRequest::getVar('times_access');
		$seconds 		= JRequest::getVar('seconds');
		// Store the content to the database
		if ($cmd == 'add_package') {
			$query_check = 'SELECT COUNT(*) FROM #__yos_resources_manager_package_object_xref WHERE package_id='.$package_id.' AND object_id='.$group_id.' AND type="group"';
			$db->setQuery($query_check);
			$count = $db->loadResult();
			
			if ($count > 0) {
				$msg = JText::_('The selected package has assigned to this group before. Please select an other package');
				$mainframe->enqueueMessage($msg,'notice');
				$mainframe->redirect('index.php?option=com_yos_resources_manager&view=groups&controller=groups&group_id='.$group_id.'&task=add_package');
			}
			$query = 'INSERT INTO #__yos_resources_manager_package_object_xref(`package_id`, `object_id`, `type`, `times_access`,`seconds`) '
					.' VALUES( '.$package_id.', '.$group_id.', "group","'.$times_access.'", "'.$seconds.'")';
		}else {
			if ($package_id !=$package_id_old) {
				$query_check = 'SELECT COUNT(*) FROM #__yos_resources_manager_package_object_xref WHERE package_id='.$package_id.' AND object_id='.$group_id.' AND type="group"';
				$db->setQuery($query_check);
				$count = $db->loadResult();
				
				if ($count > 0) {
					$msg = JText::_('The selected package has assigned to this group before. Please select an other package');
					$mainframe->enqueueMessage($msg,'notice');
					$mainframe->redirect('index.php?option=com_yos_resources_manager&view=groups&controller=groups&cid_package[]='.$package_id_old.'&group_id='.$group_id.'&task=edit_package');
				}
			}
			
			$query = 'UPDATE #__yos_resources_manager_package_object_xref 
						SET	`package_id`	='.$package_id.', 
					 		`times_access`	="'.$times_access.'", 
					 		`seconds`		="'.$seconds.'"
					 	WHERE package_id	='.$package_id_old.'
					 		 AND object_id 	='.$group_id.'
					 		 AND type 		= "group"';
		}
		$db->setQuery($query);
		if (!$db->query())
		{
			JError::raiseError(500, $db->getErrorMsg() );
		}
		switch ($task) {
			case 'apply_package' :
				$msg = JText::_('Successfully Saved Package');
				$mainframe->redirect('index.php?option=com_yos_resources_manager&view=groups&controller=groups&cid_package[]='.$package_id.'&group_id='.$group_id.'&task=edit_package', $msg);
				break;

			case 'save_package' :
				$msg = JText::_('Successfully Saved Package');
				$mainframe->redirect('index.php?option=com_yos_resources_manager&view=groups&controller=groups&cid_group[]='.$group_id.'&task=packages', $msg);
				break;
		}
	}
	
	function remove_package(){
		global $mainframe;
		$db 		= &JFactory::getDBO();
			
		$cid 		= JRequest::getVar('cid_package');
		$package_id = implode(',',$cid);
		$group_id 	= JRequest::getVar('group_id');
		$query 		= "DELETE FROM `#__yos_resources_manager_package_object_xref` 
					WHERE object_id=".$group_id." 
						AND package_id IN(".$package_id.") 
						AND type='group'";
		$db->setQuery($query);
		if (!$db->query())
		{
			JError::raiseError(500, $db->getErrorMsg() );
		}
		
		$msg = JText::_('Successfully Removed Package');
		$mainframe->redirect('index.php?option=com_yos_resources_manager&view=groups&controller=groups&cid_group[]='.$group_id.'&task=packages', $msg);
	}
	
	function mapping_groups(){
		die();
	}
}
