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

JTable::addIncludePath( JPATH_COMPONENT.DS.'tables' );
class YRMViewRoles extends JView
{
	function display($tpl = null){
		global $mainframe;
		$role_id	= JRequest::getVar('role_id');
		$cmd 		= JRequest::getVar('task');
		$model		=	$this->getModel('roles');
		$obj 		= $model->getFormPackage();
		JRequest::setVar( 'hidemainmenu', 1 );
		$this->assignRef('cmd',$cmd);
		$this->assignRef('row', $obj->row);
		$this->assignRef('row_ob', $obj->row_ob);
		$this->assignRef('user', $obj->user);
		$this->assignRef('lists',$obj->lists);
		$this->assignRef('role_id', $role_id);
		$this->_setToolBar();
		parent::display($tpl);
	}
	
	function _setToolBar(){
		JToolBarHelper::title(  JText::_( 'ROLES_MANAGER_TITLE' ), 'roles' );
		JToolBarHelper::save('save_package');
		JToolBarHelper::apply('apply_package');
		JToolBarHelper::cancel('cancel_package');
	}
}