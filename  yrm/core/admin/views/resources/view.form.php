<?php
/**
 * @version	$Id: view.html.php $
 * @package	YRM
 * @subpackage	Component
 * @copyright	Copyright (C) 2010 YOS.,JSC. All rights reserved.
 * @license	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

// Set the table directory
JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');

/**
 * HTML View class for the YRM component
 *
 * @static
 * @package	YRM
 * @subpackage	Component
 * @since 1.0
 */
class YRMViewResources extends JView
{
	function display($tpl = null){
		global $mainframe;
		
		$model	=	$this->getModel('resources');
		
		$cmd = JRequest::getVar('task');
		$cid = JRequest::getVar('cid', array(0));
		
		if ($cmd == 'add') {
			$model->load(0);
			$model->res_parent_id = $cid[0];
		}
		else {
			$model->load($cid[0]);
		}
		
		//get lists
		$list = $model->getList();
		
		//$this->assignRef('selected_id', $cid[0]);
		
		$this->assignRef('res', $model);
		$this->assignRef('list', $list);
					
		$this->_setToolBar();
		//hide the menu
		JRequest::setVar( 'hidemainmenu', 1 );
		
		parent::display($tpl);
	}
	
	function _setToolBar()
	{
		$cmd = JRequest::getVar('task');
		if ($cmd == 'add') {
			JToolBarHelper::title( JText::_( 'RESOURCES_FORM_TITLE_NEW' ), 'resources' );
		}
		else {
			JToolBarHelper::title( JText::_( 'RESOURCES_FORM_TITLE_EDIT' ), 'resources' );
		}
		JToolBarHelper::cancel('cancel_form');
		JToolBarHelper::apply();
		JToolBarHelper::save();
	}
}