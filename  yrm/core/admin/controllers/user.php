<?php
/**
 * @version	$Id: users.php $
 * @package	YRM
 * @subpackage	Component
 * @copyright	Copyright (C) 2010 YOS.,JSC. All rights reserved.
 * @license	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

/**
 * @package		Joomla
 * @subpackage	Banners
 */
class YRMControllerUser extends JController
{
	/**
	 * Constructor
	 */
	function __construct( $config = array() )
	{
		parent::__construct( $config );
		// Register Extra tasks
		$this->registerTask( 'apply_resources',		'save_resources' );
		$this->registerTask( 'apply_resources_banned',	'save_resources_banned' );
		$this->registerTask( 'unpublish',	'publish' );
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

		$this->setRedirect( 'index.php?option='. $option .'&view=users' );
	}
	
	function resources(){
		$view =& $this->getView('user','resources');	
		
		if ($model=& $this->getModel('user')) {
			$view->setModel($model);
			
		}
		$view->setLayout('resources');
		$view->display();
	}
	
	function xmltree(){
		$view =& $this->getView('user', 'xml');
		if ($model=& $this->getModel('user')) {
			$view->setModel($model);	
		}
		$view->setlayout('xml');
		$view->display();
	}
	
	function xmlrsbtree(){
		$view =& $this->getView('user', 'xml');
		if ($model=& $this->getModel('user')) {
			$view->setModel($model);	
		}
		$view->setlayout('xml');
		$view->display();
	}
	
	
	function save_resources(){
		global $option;
		
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$uid = JRequest::getInt('uid');
		$cmd = JRequest::getCmd('task');
		
		$model=& $this->getModel('user');
		$model->load($uid);
		$result = $model->save_resources();
		
		if ($cmd == 'apply_resources') {
			$this->setRedirect('index.php?option='.$option.'&controller=user&task=resources&cid_user[]='.$uid);
		}
		else {
			$this->setRedirect('index.php?option='.$option.'&view=users');
		}
	}
	
	function get_rs_form(){
		$view =& $this->getView('user','rs_form');	
		
		if ($model=& $this->getModel('user')) {
			$view->setModel($model);
			
		}
		$view->setLayout('rs_form');
		$view->display();
		
		//ajax
		die();
	}
	
	function save_rs_form(){
		$model=& $this->getModel('user');
		if($model->save_rs_form()){
			echo JText::_('USER_RESOURCES_ITEM_UPDATED');
		}
		else {
			echo JText::_('USER_RESOURCES_ITEM_NOT_UPDATED');
		}
		die();
	}
	
	function get_rsb_form(){
		$view =& $this->getView('user','rsb_form');	
		
		if ($model=& $this->getModel('user')) {
			$view->setModel($model);
			
		}
		$view->setLayout('rsb_form');
		$view->display();
		
		//ajax
		die();
	}
	
	function save_rsb_form(){
		$model=& $this->getModel('user');
		if($model->save_rsb_form()){
			echo JText::_('USER_RESOURCES_ITEM_UPDATED');
		}
		else {
			echo JText::_('USER_RESOURCES_ITEM_NOT_UPDATED');
		}
		die();
	}
	
	function groups(){
		$view =& $this->getView('user','groups');	
		
		if ($model=& $this->getModel('user')) {
			$view->setModel($model);
			
		}
		$view->setLayout('groups');
		$view->display();
	}
	
	function cancel_group(){
		global $mainframe;
		$mainframe->redirect('index.php?option=com_yos_resources_manager&controller=user&view=users');
	}
	
	function save_groups(){
		global $mainframe;
		var_dump($_POST);
		$user_id = JRequest::getVar('user_id');
		$model = $this->getModel('user');
		$model->save_groups();
		$msg = JText::_('Successfully Saved Groups');
		$mainframe->redirect('index.php?option=com_yos_resources_manager&view=user&controller=user&cid_user[]='.$user_id.'&task=groups', $msg);
			
	}
	
	function roles(){
		$view =& $this->getView('user','roles');	
		
		if ($model=& $this->getModel('user')) {
			$view->setModel($model);
			
		}
		$view->setLayout('roles');
		$view->display();
	}
	
	function cancel_role(){
		global $mainframe;
		$mainframe->redirect('index.php?option=com_yos_resources_manager&controller=user&view=users');
	}
	
	function save_roles(){
		global $mainframe;
		$user_id = JRequest::getVar('user_id');
		$model = $this->getModel('user');
		$model->save_roles();
		$msg = JText::_('Successfully Saved Roles');
		$mainframe->redirect('index.php?option=com_yos_resources_manager&view=user&controller=user&cid_user[]='.$user_id.'&task=roles', $msg);	
	}
	
	function resources_banned(){
		$view =& $this->getView('user','resources_banned');	
		
		if ($model=& $this->getModel('user')) {
			$view->setModel($model);
			
		}
		$view->setLayout('resources_banned');
		$view->display();
	}
	
	function cancel_resources_banned(){
		global $mainframe;
		$mainframe->redirect('index.php?option=com_yos_resources_manager&controller=user&view=users');
	}
	
	function save_resources_banned(){
		global $option;
		
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$uid = JRequest::getInt('uid');
		$cmd = JRequest::getCmd('task');
		
		$model=& $this->getModel('user');
		$model->load($uid);
		$result = $model->save_resources_banned();
		
		if ($cmd == 'apply_resources_banned') {
			$this->setRedirect('index.php?option='.$option.'&controller=user&task=resources_banned&cid_user[]='.$uid);
		}
		else {
			$this->setRedirect('index.php?option='.$option.'&view=users');
		}
	}
}
