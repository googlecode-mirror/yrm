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

class YRMControllerPackages extends JController
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

	function display()
	{
		parent::display();
	}
	
	function cancel()
	{
		global $option;
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$this->setRedirect( 'index.php?option='. $option .'&view=packages&controller=packages&view=packages' );
	}

	function edit(){
		$view =& $this->getView('packages','form');	
		
		if ($model=& $this->getModel('packages')) {
			$view->setModel($model);
		}
		$view->setLayout('form');
		$view->display();
	}
	
	function saveContentPrep( &$row )
	{
		// Get submitted text from the request variables
		$text = JRequest::getVar( 'text', '', 'post', 'string', JREQUEST_ALLOWRAW );

		// Clean text for xhtml transitional compliance
		$text		= str_replace( '<br>', '<br />', $text );

		// Search for the {readmore} tag and split the text up accordingly.
		$pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
		$tagPos	= preg_match($pattern, $text);

		$row->text	= $text;

		// Filter settings
		jimport( 'joomla.application.component.helper' );
		$config	= JComponentHelper::getParams( 'com_content' );
		$user	= &JFactory::getUser();
		$gid	= $user->get( 'gid' );

		$filterGroups	=  $config->get( 'filter_groups' );
		
		// convert to array if one group selected
		if ( (!is_array($filterGroups) && (int) $filterGroups > 0) ) { 
			$filterGroups = array($filterGroups);
		}

		if (is_array($filterGroups) && in_array( $gid, $filterGroups ))
		{
			$filterType		= $config->get( 'filter_type' );
			$filterTags		= preg_split( '#[,\s]+#', trim( $config->get( 'filter_tags' ) ) );
			$filterAttrs	= preg_split( '#[,\s]+#', trim( $config->get( 'filter_attritbutes' ) ) );
			switch ($filterType)
			{
				case 'NH':
					$filter	= new JFilterInput();
					break;
				case 'WL':
					$filter	= new JFilterInput( $filterTags, $filterAttrs, 0, 0, 0);  // turn off xss auto clean
					break;
				case 'BL':
				default:
					$filter	= new JFilterInput( $filterTags, $filterAttrs, 1, 1 );
					break;
			}
			$row->text	= $filter->clean( $row->text );
		} elseif(empty($filterGroups) && $gid != '25') { // no default filtering for super admin (gid=25)
			$filter = new JFilterInput( array(), array(), 1, 1 );
			$row->text	= $filter->clean( $row->text );
		}
		return true;
	}
	
	function save(){
		
		global $mainframe;
		$db 					= &JFactory::getDBO();
		$task 					= JRequest::getVar('task');
		$cid_resource			= JRequest::getVar('cid');
		$cid_role				= JRequest::getVar('cid_role');
		$cid_group				= JRequest::getVar('cid_group');
		$cid_payment_methods	= JRequest::getVar('cid_payment_methods');
		$open_tab_name  		= JRequest::getVar('open_tab_name');
		
		$row	=& JTable::getInstance('package', 'Table');
		$post	= JRequest::get( 'post' );
		if (!$row->bind( $post ))
		{
			JError::raiseError(500, $row->getError() );
		}
		$this->SaveContentPrep(&$row);
		if (!$row->check())
		{
			JError::raiseError(500, $row->getError() );
		}
		
		if (!$row->store())
		{
			JError::raiseError(500, $row->getError() );
		}
		$package_id = $row->id;
		// save
		$model=& $this->getModel('packages');
		$model->save($cid_resource, $cid_role, $cid_group, $cid_payment_methods,$package_id);
		
		switch ($task) {
			case 'apply' :
				$msg = JText::_('Successfully Saved Package');
				$mainframe->redirect('index.php?option=com_yos_resources_manager&view=packages&controller=packages&cid[]='.$package_id.'&task=edit&open_tab_name='.$open_tab_name, $msg);
				break;

			case 'save' :
				$msg = JText::_('Successfully Saved Package');
				$mainframe->redirect('index.php?option=com_yos_resources_manager&view=packages&controller=packages', $msg);
				break;
		}
	}	
	
	function remove(){
		global $mainframe;
		$db = &JFactory::getDBO();
		$cid = JRequest::getVar('cid');
		$cid_del = implode(',',$cid);
		$query = 'DELETE FROM #__yos_resources_manager_package WHERE id IN('.$cid_del.')';
		$db->setQuery($query);
		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg());			
		}
		$query = 'DELETE FROM #__yos_resources_manager_package_object_xref WHERE package_id IN('.$cid_del.')';
		$db->setQuery($query);
		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg());			
		}
		$query = 'DELETE FROM #__yos_resources_manager_package_payment_method_xref WHERE package_id IN('.$cid_del.')';
		$db->setQuery($query);
		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg());			
		}
		$mainframe->redirect('index.php?option=com_yos_resources_manager&view=packages&controller=packages');
	}
	
	function publish()
	{
		global $mainframe;

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$db 	=& JFactory::getDBO();
		$cid		= JRequest::getVar( 'cid', array(), '', 'array' );
		$publish	= ( $this->getTask() == 'publish' ? 1 : 0 );
		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1)
		{
			$action = $publish ? 'publish' : 'unpublish';
			JError::raiseError(500, JText::_( 'Select an item to' .$action, true ) );
		}

		$cids = implode( ',', $cid );

		$query = 'UPDATE #__yos_resources_manager_package'
		. ' SET published = ' . (int) $publish
		. ' WHERE id IN ( '. $cids .' )'
		;
		
		$db->setQuery( $query );
		if (!$db->query())
		{
			JError::raiseError(500, $db->getErrorMsg() );
		}
		$mainframe->redirect( 'index.php?option=com_yos_resources_manager&view=packages&controller=packages' );
	}


	function xmltree(){
		$view =& $this->getView('packages', 'xml');
		if ($model=& $this->getModel('packages')) {
			$view->setModel($model);	
		}
		$view->setlayout('xml');
		$view->display();
	}
	
	function get_rs_form(){
		$view =& $this->getView('packages','rs_form');	
		
		if ($model=& $this->getModel('packages')) {
			$view->setModel($model);
			
		}
		$view->setLayout('rs_form');
		$view->display();
		
		//ajax
		die();
	}
	
	function save_rs_form(){
		$model=& $this->getModel('packages');
		if($model->save_rs_form()){
			echo JText::_('USER_RESOURCES_ITEM_UPDATED');
		}
		else {
			echo JText::_('USER_RESOURCES_ITEM_NOT_UPDATED');
		}
		die();
	}
}
