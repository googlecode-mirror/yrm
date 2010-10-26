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
		
		$this->_setToolBar();
			
		$say 		= JText::_('RESOURCES_TREE_DESCRIPTION');		
		$root_node 		= JText::_('RESOURCES_TREE_ROOT_NODE');		
		
		$this->assign('say',$say);
		$this->assign('root_node',$root_node);
		
		//hide the menu
		JRequest::setVar( 'hidemainmenu', 1 );
		
		parent::display($tpl);
	}
	
	function _setToolBar()
	{		
		JToolBarHelper::title( JText::_( 'RESOURCES_MANAGER' ), 'resources' );
		JToolBarHelper::cancel();
		JToolBarHelper::addNew();
		JToolBarHelper::editListX();
		JToolBarHelper::customX('import','import','','Import',false);
		JToolBarHelper::customX('export','export','','Export');		
		JToolBarHelper::unpublishList();
		JToolBarHelper::publishList();
		JToolBarHelper::deleteListX(JText::_('RESOURCES_TREE_REMOVE_CONFIRM_MESSAGE'));
	}
}