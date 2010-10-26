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
class YRMViewMapping extends JView
{
	function display($tpl = null){
		$db = &JFactory::getDBO();
		$cid = JRequest::getVar('cid_group');
		$model = $this->getModel('mapping');
		$m_users = $model->getMapping();
		
		$query = "SELECT * FROM #__yos_resources_manager_group WHERE id=".intval($cid[0]);
		$db->setQuery($query);
		$group = $db->loadObject();
		$this->assignRef('group', $group);
		
		$task = JRequest::getVar('task');
		$this->assignRef('task',$task);
		$this->assignRef('user',		JFactory::getUser());
		$this->assignRef('lists',		$m_users->lists);
		$this->assignRef('items',		$m_users->rows);
		$this->assignRef('pagination',	$m_users->pagination);
		$this->assignRef('group_id', $cid[0]);
		
		$this->_setToolBar();
		
		parent::display();
	}
	
	function _setToolBar()
	{
		JToolBarHelper::title( JText::_( 'MAPPING GROUPS' ), 'mapping');
		JToolBarHelper::addNewX();
		JToolBarHelper::editListX();
		JToolBarHelper::deleteListX();
	}
}