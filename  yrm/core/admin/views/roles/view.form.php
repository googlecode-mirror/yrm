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
		global $mainframe;
		
		$model	=	$this->getModel('roles');
		$db 	= & JFactory::getDBO();
		$cmd = JRequest::getVar('task');
		$cid = JRequest::getVar('cid_role');
		if ($cmd == 'edit') {
			$query = "SELECT * FROM `#__yos_resources_manager_role` WHERE `id`=".$cid[0];
			$db->setQuery($query);
			$row = $db->loadObject();
			$this->assignRef('role', $row);
		}
		JRequest::setVar( 'hidemainmenu', 1 );
		$this->assignRef('cmd',$cmd);
		$this->_setToolBar();
		parent::display($tpl);
	}
	function _setToolBar(){
		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		JArrayHelper::toInteger($cid, array(0));
		$text = ( $this->cmd == 'edit') ? JText::_( 'EDIT' ) : JText::_( 'NEW' );
	
		JToolBarHelper::title(  JText::_( 'ROLE' ).': <small><small>[ ' . $text.' ]</small></small>', 'roles' );
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel('cancel','Close');
	}
}