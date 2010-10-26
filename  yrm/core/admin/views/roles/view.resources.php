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
class YRMViewRoles extends JView
{
	function display($tpl = null){
		global $mainframe, $option;
		
		$cid = JRequest::getVar('cid_role');
		
		if (!is_array($cid)) {
			$mainframe->redirect( 'index.php?option='.$option );
			return ;
		}
		
		$role_id = $cid[0];
		
		$model	=	$this->getModel('roles');
		$model->load($role_id);
		
		$this->_setToolBar();
		$say 		= JText::sprintf('USER_RESOURCES_TITLE', $model->role->name);		
		$root_node 		= JText::_('RESOURCES_TREE_ROOT_NODE');		
		
		$this->assign('say',$say);
		$this->assign('root_node',$root_node);
		$this->assignRef('role_id', $role_id);
		$this->assignRef('option', $option);
		//hide the menu
		JRequest::setVar( 'hidemainmenu', 1 );

		parent::display();
	}
	
	function _setToolBar()
	{
		JToolBarHelper::title( JText::_( 'USER_RESOURCES_MANAGER' ), 'roles' );
		JToolBarHelper::save('save_resources');
		JToolBarHelper::apply('apply_resources');
		JToolBarHelper::cancel();
	}
}