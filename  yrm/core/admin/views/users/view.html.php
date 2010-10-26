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
class YRMViewUsers extends JView
{
	function display($tpl = null){
		global $mainframe, $option;
		
		$model	=	$this->getModel('users');
		
		$lists = $model->getList();
		$rows = $model->getData();
		$pagination = $model->getPagination();
		
		$this->assignRef('lists',		$lists);
		$this->assignRef('items',		$rows);
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('option',	$option);
		
		$this->_setToolBar();

		parent::display($tpl);
	}
	
	function _setToolBar()
	{
		JToolBarHelper::title( JText::_( 'USERS_MANAGER' ), 'users' );
		JToolBarHelper::customX('groups', 'groups', '', JText::_('USERS_MANAGER_GROUPS'));
		JToolBarHelper::customX('roles', 'roles', '', JText::_('USERS_MANAGER_ROLES'));
		JToolBarHelper::customX('resources', 'resources', '', JText::_('USERS_MANAGER_RESOURCES'));
		JToolBarHelper::customX('resources_banned', 'resources_banned', '', JText::_('USERS_MANAGER_RESOURCES_BANNED'));
	}
}