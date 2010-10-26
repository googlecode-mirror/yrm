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

class YRMControllerRoles extends JController
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

		$this->setRedirect( 'index.php?option='. $option .'&view=roles' );
	}
	
	function edit()
	{
		global $option;
		$db		=& JFactory::getDBO();
		$view 	= $this->getView('roles', 'form');
		if ($model=& $this->getModel('roles')) {
			$view->setModel($model);
			$view->setLayout('form');
		}
		$view->display();
	}
	
	function edit_user()
	{
		global $option;
		$db		=& JFactory::getDBO();
		$view 	= $this->getView('roles', 'form_user');
		if ($model = & $this->getModel('roles')) {
			$view->setModel($model);
			$view->setLayout('form_user');
		}
		$view->display();
	}
	
	function edit_package()
	{
		global $option;
		$db		=& JFactory::getDBO();
		$view	= $this->getView('roles', 'form_package');
		if ($model = & $this->getModel('roles')) {
			$view->setModel($model);
			$view->setLayout('form_package');
		}
		$view->display();
	}
	
	function save(){
		global $option;
		$row	=& JTable::getInstance('roles', 'Table');
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
		
		switch ($task = JRequest::getVar('task'))
		{
			case 'apply':
				$msg = JText::_( 'Changes to Role saved' );
				$link = 'index.php?option=com_yos_resources_manager&view=roles&controller=roles&task=edit&cid_role[]='. $row->id .'';
				break;

			case 'save':
			default:
				$msg = JText::_( 'Role saved' );
				$link = 'index.php?option=com_yos_resources_manager&view=roles';
				break;
		}

		$this->setRedirect($link);
	}
	
	function remove()
	{
		global $mainframe;
		$db 		= &JFactory::getDBO();
		$cid 		= JRequest::getVar('cid_role');
		$temp_ids 	= implode(',',$cid);
		
		// delete from jos_yos_resources_manager_resource_role_xref
		$query_delete_role_resource = " DELETE FROM `#__yos_resources_manager_resource_role_xref` WHERE role_id IN (".$temp_ids.")";
		$db->setQuery($query_delete_role_resource);
		if (!$db->query()) {
			JError::raiseWarning( 500, $db->getError() );
		}
		// delete from jos_yos_resources_manager_user_role_xref
		$query_delete_role_user 	= " DELETE FROM `#__yos_resources_manager_user_role_xref` WHERE role_idIN (".$temp_ids.")";
		$db->setQuery($query_delete_role_user);
		if (!$db->query()) {
			JError::raiseWarning( 500, $db->getError() );
		}
		// delete from jos_yos_resources_manager_group_role_xref
		$query_delete_role_group 	= " DELETE FROM `#__yos_resources_manager_group_role_xref` WHERE role_id IN (".$temp_ids.")";
		$db->setQuery($query_delete_role_group);
		if (!$db->query()) {
			JError::raiseWarning( 500, $db->getError() );
		}
		// delete from jos_yos_resources_manager_package_object_xref
		$query_delete_role_package 	= " DELETE FROM `#__yos_resources_manager_package_object_xref` 
										WHERE object_id IN (".$temp_ids.") AND `type`='role'";
		$db->setQuery($query_delete_role_package);
		if (!$db->query()) {
			JError::raiseWarning( 500, $db->getError() );
		}
		// delete from jos_yos_resources_manager_role
		$query_delete_role 			= " DELETE FROM `#__yos_resources_manager_role` 
										WHERE id IN (".$temp_ids.")";
		$db->setQuery($query_delete_role);
		if (!$db->query()) {
			JError::raiseWarning( 500, $db->getError() );
		}
		$mainframe->redirect('index.php?option=com_yos_resources_manager&view=roles');
	}
	
	function resources(){
		$view =& $this->getView('roles','resources');	
		
		if ($model	= & $this->getModel('roles')) {
			$view->setModel($model);
		}
		$view->setLayout('resources');
		$view->display();
	}
	function xmltree(){
		$view = & $this->getView('roles', 'xml');
		if ($model	= & $this->getModel('roles')) {
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
		$view =& $this->getView('roles','users');	
		
		if ($model=& $this->getModel('roles')) {
			$view->setModel($model);
		}
		$view->setLayout('users');
		$view->display();
	}
	
	function cancel_user(){
		global $mainframe;
		$role_id = JRequest::getVar('role_id');
		$mainframe->redirect('index.php?option=com_yos_resources_manager&controller=roles&view=roles&task=users&cid_role[]='.$role_id);
	}
	
	function packages(){
		$view =& $this->getView('roles','packages');	
		
		if ($model=& $this->getModel('roles')) {
			$view->setModel($model);
		}
		$view->setLayout('packages');
		$view->display();
	}
	
	function cancel_package(){
		global $mainframe;
		$role_id = JRequest::getVar('role_id');
		$mainframe->redirect('index.php?option=com_yos_resources_manager&controller=roles&view=roles&task=packages&cid_role[]='.$role_id);
	}
	
	function groups(){
		$view =& $this->getView('roles','groups');	
		
		if ($model=& $this->getModel('roles')) {
			$view->setModel($model);
		}
		$view->setLayout('groups');
		$view->display();
	}
	
	function cancel_group(){
		global $mainframe;
		$mainframe->redirect('index.php?option=com_yos_resources_manager&controller=roles&view=roles');
	}
	
	function save_groups(){
		global $mainframe;
		$db 		= &JFactory::getDBO();
		$role_id 	= JRequest::getVar('role_id');
		$cids		= JRequest::getVar('cid_group');
		
		$query 		= 'DELETE FROM `#__yos_resources_manager_group_role_xref` WHERE role_id='.$role_id;
		$db->setQuery($query);
		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg() );
		}
		if (count($cids)) {
			$value 	= array();
			foreach ($cids as $cid) {
				$value[] =' ('.$cid.', '.$role_id.')';
			}
			$value = ' VALUES '. implode( ', ', $value );
			
			$query_insert = 'INSERT INTO `#__yos_resources_manager_group_role_xref`(`group_id`, `role_id`) ';
			$query_insert.=$value;
			$db->setQuery($query_insert);
			if (!$db->query()) {
				JError::raiseError(500, $db->getErrorMsg());
			}
		}
		
		$msg = JText::_('Successfully Saved Groups');
		$mainframe->redirect('index.php?option=com_yos_resources_manager&view=roles&controller=roles&cid_role[]='.$role_id.'&task=groups', $msg);
			
	}
	
	function save_user(){
		global $mainframe;
		$db 		= &JFactory::getDBO();
		$user_id 	= intval(JRequest::getVar('user_id'));
		$role_id 	= intval(JRequest::getVar('role_id'));
		$cid		= JRequest::getVar('cid_user');
		$user_id_old = intval($cid[0]);
		$task 		= JRequest::getVar('task');
		$cmd 		= JRequest::getVar('cmd');
		$details 	= JRequest::getVar('details');
		
		$start 		= $details['start'];
		$end 		= $details['end'];
		$date 		=& JFactory::getDate($start);
		$date_start = $date->toMySQL();
		
		$date 		=& JFactory::getDate($end);
		$date_end 	= $date->toMySQL();

		// Store the content to the database
		if ($cmd == 'add_user') {
			$query_check = 'SELECT COUNT(*) FROM #__yos_resources_manager_user_role_xref WHERE user_id='.$user_id.' AND role_id='.$role_id;
			$db->setQuery($query_check);
			$count 	= $db->loadResult();
			if ($count > 0) {
				$msg = JText::_('The selected user has assigned to this role before. Please select an other user');
				$mainframe->enqueueMessage($msg,'notice');
				$mainframe->redirect('index.php?option=com_yos_resources_manager&view=roles&controller=roles&role_id='.$role_id.'&task=add_user');
			}
			$query = 'INSERT INTO #__yos_resources_manager_user_role_xref(`user_id`, `role_id`, `start`, `end`) '
					.' VALUES( '.$user_id.', '.$role_id.', "'.$date_start.'","'.$date_end.'")';
		}else {
			$query_check = 'SELECT COUNT(*) FROM #__yos_resources_manager_user_role_xref WHERE user_id='.$user_id.' AND role_id='.$role_id.' AND user_id !='.$user_id_old;
			$db->setQuery($query_check);
			$count = $db->loadResult();
			if ($count > 0) {
				$msg = JText::_('The selected user has assigned to this role before. Please select an other user');
				$mainframe->enqueueMessage($msg,'notice');
				$mainframe->redirect('index.php?option=com_yos_resources_manager&view=roles&controller=roles&role_id='.$role_id.'&task=add_user');
			}
			$query = 'UPDATE #__yos_resources_manager_user_role_xref 
						SET	`user_id`='.$user_id.', 
					 		`start`="'.$date_start.'", 
					 		`end`="'.$date_end.'"
					 	WHERE user_id='.$user_id_old.'
					 		 AND role_id ='.$role_id;
		}
		$db->setQuery($query);
		if (!$db->query())
		{
			JError::raiseError(500, $db->getErrorMsg() );
		}
		switch ($task) {
			case 'apply_user' :
				$msg = JText::_('Successfully Saved User');
				$mainframe->redirect('index.php?option=com_yos_resources_manager&view=roles&controller=roles&cid_user[]='.$user_id.'&role_id='.$role_id.'&task=edit_user', $msg);
				break;

			case 'save_user' :
				$msg = JText::_('Successfully Saved User');
				$mainframe->redirect('index.php?option=com_yos_resources_manager&view=roles&controller=roles&cid_role[]='.$role_id.'&task=users', $msg);
				break;
		}
	}
	
	function remove_user(){
		global $mainframe;
		$db 		= &JFactory::getDBO();
		
		$cid 		= JRequest::getVar('cid_user');
		$user_id 	= implode(',',$cid);
		$role_id 	= JRequest::getVar('role_id');
		$query 		= "DELETE FROM `#__yos_resources_manager_user_role_xref` WHERE role_id=".$role_id." AND user_id IN(".$user_id.")";
		$db->setQuery($query);
		if (!$db->query())
		{
			JError::raiseError(500, $db->getErrorMsg() );
		}
		
		$msg = JText::_('Successfully Removed User');
		$mainframe->redirect('index.php?option=com_yos_resources_manager&view=roles&controller=roles&cid_role[]='.$role_id.'&task=users', $msg);
	}
	function save_package(){
		
		global $mainframe;
		$db 			= &JFactory::getDBO();
		$package_id 	= intval(JRequest::getVar('package_id'));
		$role_id 		= intval(JRequest::getVar('role_id'));
		$cid			= JRequest::getVar('cid_package');
		$package_id_old = intval($cid[0]);
		$task 			= JRequest::getVar('task');
		$cmd 			= JRequest::getVar('cmd');
		$times_access	= JRequest::getVar('times_access');
		$seconds 		= JRequest::getVar('seconds');
		// Store the content to the database
		if ($cmd == 'add_package') {
			$query_check = 'SELECT COUNT(*) FROM #__yos_resources_manager_package_object_xref WHERE package_id='.$package_id.' AND object_id='.$role_id.' AND type="role"';
			$db->setQuery($query_check);
			$count = $db->loadResult();
			
			if ($count > 0) {
				$msg = JText::_('The selected package has assigned to this role before. Please select an other package');
				$mainframe->enqueueMessage($msg,'notice');
				$mainframe->redirect('index.php?option=com_yos_resources_manager&view=roles&controller=roles&role_id='.$role_id.'&task=add_package');
			}
			$query = 'INSERT INTO #__yos_resources_manager_package_object_xref(`package_id`, `object_id`, `type`, `times_access`,`seconds`) '
					.' VALUES( '.$package_id.', '.$role_id.', "role","'.$times_access.'", "'.$seconds.'")';
		}else {
			if ($package_id !=$package_id_old) {
				$query_check = 'SELECT COUNT(*) FROM #__yos_resources_manager_package_object_xref WHERE package_id='.$package_id.' AND object_id='.$role_id.' AND type="role"';
				$db->setQuery($query_check);
				$count = $db->loadResult();
				
				if ($count > 0) {
					$msg = JText::_('The selected package has assigned to this role before. Please select an other package');
					$mainframe->enqueueMessage($msg,'notice');
					$mainframe->redirect('index.php?option=com_yos_resources_manager&view=roles&controller=roles&cid_package[]='.$package_id_old.'&role_id='.$role_id.'&task=edit_package');
				}
			}
			
			$query = 'UPDATE #__yos_resources_manager_package_object_xref 
						SET	`package_id`='.$package_id.', 
					 		`times_access`="'.$times_access.'", 
					 		`seconds`="'.$seconds.'"
					 	WHERE package_id='.$package_id_old.'
					 		 AND object_id ='.$role_id.'
					 		 AND type = "role"';
		}
		$db->setQuery($query);
		if (!$db->query())
		{
			JError::raiseError(500, $db->getErrorMsg() );
		}
		switch ($task) {
			case 'apply_package' :
				$msg = JText::_('Successfully Saved Package');
				$mainframe->redirect('index.php?option=com_yos_resources_manager&view=roles&controller=roles&cid_package[]='.$package_id.'&role_id='.$role_id.'&task=edit_package', $msg);
				break;

			case 'save_package' :
				$msg = JText::_('Successfully Saved Package');
				$mainframe->redirect('index.php?option=com_yos_resources_manager&view=roles&controller=roles&cid_role[]='.$role_id.'&task=packages', $msg);
				break;
		}
	}
	
	function remove_package(){
		global $mainframe;
		$db 		= &JFactory::getDBO();
		
		$cid 		= JRequest::getVar('cid_package');
		$package_id = implode(',',$cid);
		$role_id 	= JRequest::getVar('role_id');
		$query = "DELETE FROM `#__yos_resources_manager_package_object_xref` 
					WHERE object_id=".$role_id." 
						AND package_id IN(".$package_id.") 
						AND type='role'";
		$db->setQuery($query);
		if (!$db->query())
		{
			JError::raiseError(500, $db->getErrorMsg() );
		}
		
		$msg = JText::_('Successfully Removed Package');
		$mainframe->redirect('index.php?option=com_yos_resources_manager&view=roles&controller=roles&cid_role[]='.$role_id.'&task=packages', $msg);
	}
	
	function cancel_multi_users()
	{
		global $option;
		$cid = JRequest::getVar('cid_role');
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$link 		=  'index.php?option=com_yos_resources_manager&view=roles&controller=roles&task=users&cid_role[]='. intval($cid[0]);

		$this->setRedirect($link );
	}
	
	function add_multi_users(){
		global $option;
		$db		=& JFactory::getDBO();
		$view=$this->getView('roles', 'multi_users');
		if ($model=& $this->getModel('roles')) {
			$view->setModel($model);
			$view->setLayout('multi_users');
		}
		$view->display();
	}
	
	
	function save_multi_users(){ 
		global $mainframe;
		$db = &JFactory::getDBO();
		
		$cid_role 	= JRequest::getVar('cid_role');
		$gr_u_start = JRequest::getVar('ru_start');
		$gr_u_end 	= JRequest::getVar('ru_end');
		$cid_user 	= JRequest::getVar('cid_user');
		if (count($cid_user)) {
			$query_insert = 'INSERT INTO #__yos_resources_manager_user_role_xref(`user_id`, `role_id`, `start`, `end`)  VALUES ';
			$values = array();
			foreach ($cid_user as $cid){
				$values[] = '('.$cid.', '.intval($cid_role[0]).', "'.$gr_u_start.'","'.$gr_u_end.'")';
			}
			$str_values = implode(',', $values);
			$query_insert .= $str_values;
			$db->setQuery($query_insert);
			if(!$db->query()){
				JError::raiseError(500, $db->getErrorMsg());
			}
		}
		
		$msg = JText::_('Successfully Added Users');
		$mainframe->redirect('index.php?option=com_yos_resources_manager&view=roles&controller=roles&cid_role[]='.intval($cid_role[0]).'&task=users', $msg);
				
	}
	
}
