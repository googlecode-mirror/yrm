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
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.archive');
jimport('joomla.filesystem.archive.zip');

/**
 * @package		Joomla
 * @subpackage	Banners
 */
class YRMControllerResources extends JController
{
	/**
	 * Constructor
	 */
	function __construct( $config = array() )
	{
		parent::__construct( $config );
		// Register Extra tasks
		$this->registerTask( 'add',			'edit' );
		$this->registerTask( 'apply',		'save' );
		$this->registerTask( 'unpublish',	'publish' );
	}

	/**
	 * Display the list of banners
	 */
	function display()
	{
		parent::display();
	}
	
	/**
	 * display xml tree
	 *
	 */
	function xmltree(){
		$view=$this->getView('resources','xml');	
		
		if ($model=& $this->getModel('resources')) {
			$view->setModel($model);
			$view->setLayout('xml');
		}
		$view->display();
	}
	

	function edit()
	{
		global $option;
		// Check for request forgeries
		JRequest::checkToken('request') or jexit( 'Invalid Token' );
		
		$db		=& JFactory::getDBO();
		$user	=& JFactory::getUser();

		$view=$this->getView('resources','form');
		
		if ($model=& $this->getModel('resources')) {
			$view->setModel($model);
			$view->setLayout('form');
		}
		
		$view->display();
	}

	function cancel()
	{
		global $option;
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$this->setRedirect( 'index.php?option='. $option );
	}
	
	function cancel_form(){
		global $option;
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$this->setRedirect( 'index.php?option='. $option.'&view=resources' );
	}
	
	function publish()
	{
		global $option;
		$cmd = JRequest::getCmd('task');
		
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$this->setRedirect( 'index.php?option='.$option.'&view=resources' );
		
		$affected = 0;
		
		$arr_cid = JRequest::getVar('cid');
		if ($model=& $this->getModel('resources')) {
			foreach ($arr_cid as $cid){
				if ($cmd == 'publish') {
					$affected += $model->publish($cid);
				}
				else {
					$affected += $model->unpublish($cid);
				}
			}
		}
		
		if ($cmd == 'publish') {
			$this->setMessage( JText::sprintf( 'RESOURCES_TREE_ITEM_PUBLISHED', $affected ) );
		}
		else {
			$this->setMessage( JText::sprintf( 'RESOURCES_TREE_ITEM_UNPUBLISHED', $affected ) );
		}
		
	}
	
	function save(){
		global $mainframe, $option;
		
		$task = JRequest::getCmd('task');
		
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
				
		$model=& $this->getModel('resources');
		$model->res_id 					= JRequest::getInt('res_id', 0);
		$model->res_parent_id 			= JRequest::getInt('res_parent_id');
		$model->res_name 				= JRequest::getVar('res_name');
		$model->res_affected			= JRequest::getVar('res_affected');
		$model->res_type 				= JRequest::getVar('res_type');
		$model->res_option 				= JRequest::getVar('res_option');
		$model->res_task			 	= JRequest::getVar('res_task');
		$model->res_view 				= JRequest::getVar('res_view');
		$model->res_params 				= JRequest::getVar('res_params');
		$model->res_plug_in 			= JRequest::getInt('res_plug_in');
		$model->res_redirect_url 		= JRequest::getVar('res_redirect_url');
		$model->res_redirect_message 	= JRequest::getVar('res_redirect_msg');
		$model->res_description 		= JRequest::getVar('res_description');
		$model->res_sticky 				= JRequest::getInt('res_sticky');
		$model->res_published 			= JRequest::getInt('res_published');
		
		$model->save();
		
		//if this is new resource (id = 0) and parrent's sticky is 'yes'
		$parentResource =& $this->getModel('resources');
		$parentResource->load($model->res_parent_id);
		if (JRequest::getInt('res_id', 0) == 0 && $parentResource->res_sticky) {
		//sticky processing BEGIN
		//add this resource to parrent's roles BEGIN
			$model->sticky_roles();
		//add this resource to parrent's roles END
		
		//add this resource to parrent's users BEGIN
			$model->sticky_users();
		//add this resource to parrent's users END
		//sticky processing END
		}
		
		if ($task == 'apply') {
			$this->setRedirect( 'index.php?option='.$option.'&view=resources&controller=resources&task=edit&cid[]='.$model->res_id.'&'.JUtility::getToken().'=1' );
		}
		else {
			$this->setRedirect('index.php?option='.$option.'&view=resources');
		}
		
		$this->setMessage( JText::_( 'RESOURCES_TREE_ITEM_UPDATED'));
	}

	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$this->setRedirect( 'index.php?option=com_yos_resources_manager&view=resources' );

		$model=& $this->getModel('resources');
		
		$cid = JRequest::getVar('cid');
		//revert selected array
		$cid = array_reverse($cid);
		
		$deleted = 0;
		for ($i = 0; $i < count($cid); $i++){
			$result = $model->remove($cid[$i]);
			if ($result == 0) {
				break;
			}
			$deleted += $result;
		}

		$this->setMessage( JText::sprintf( 'RESOURCES_TREE_ITEM_REMOVED', $deleted ) );
	}
	
	function desplg_form(){
//		$plgid = JRequest::getVar('plgid');
		$view =& $this->getView('resources','plg');	
		
		if ($model=& $this->getModel('resources')) {
			$view->setModel($model);
		}
		$view->setLayout('plg');
		$view->display();
		//ajax
		die();
	}	
	function export()
	{	
		$model=& $this->getModel('resources');
		$model->export();
		return ;
	}
	function import()
	{
		$view=$this->getView('resources','import');	
		
		if ($model=& $this->getModel('resources')) {
			$view->setModel($model);
			$view->setLayout('import');
		}
		$view->display();
	}
	function toDatabase()
	{
		global $option;
		$model=& $this->getModel('resources');
		
		$pathBase=$model->toDatabase();
		jimport('joomla.filesystem.folder');
		JFolder::delete($pathBase);
		
		if ($model->import_mess) {
			JError::raiseWarning('c',$model->import_mess);
			$this->setRedirect( 'index.php?option='.$option.'&view=resources');
		}
		$msg = JText::_( 'Import sucsefull' );
		$this->setRedirect( 'index.php?option='.$option.'&view=resources',$msg);
	}
	function cancelImport()
	{
		global $option;
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$msg = JText::_( 'operation cancelled' );
		$this->setRedirect( 'index.php?option='.$option.'&view=resources',$msg);
	}
}
