<?php
/**
 * @version	$Id: view.form.php $
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
class YRMViewPackages extends JView
{
	function display($tpl = null){
		global $mainframe;
		$task 			= JRequest::getVar('task');
		$db 			= & JFactory::getDBO();
		$cmd 			= JRequest::getVar('task');
		$cid 			= JRequest::getVar('cid');
		$open_tab_name 	= JRequest::getVar('open_tab_name'); 
		$lists 			= array();
		$cid_tmp 		= $cid[0];
		$row 			= JTable::getInstance('package', 'Table');
		$row->load($cid_tmp);
		
		$model 			= $this->getModel('packages');
		$model->load($cid[0]);
		
		$say 			= JText::sprintf('USER_RESOURCES_TITLE', $model->package->name);		
		$root_node 		= JText::_('RESOURCES_TREE_ROOT_NODE');		
		$currency		= $model->getCurrency();
		$this->assign('say',$say);
		$this->assign('root_node',$root_node);
		$this->assignRef('package_id', $cid[0]);
		$this->assignRef('option', $option);
		$this->assignRef('task', $task);
		$this->assignRef('row', $row);
		$groups 		= $model->object_yos($cid[0], 'group');
		$roles 			= $model->object_yos($cid[0], 'role');
		$payment_methods = $model->payment_method($cid[0]);
		$this->assignRef('groups', $groups);
		$this->assignRef('roles', $roles);
		$this->assignRef('payment_methods', $payment_methods);
		$this->assignRef('currency', $currency);
		$this->assignRef('open_tab_name', $open_tab_name);
		JRequest::setVar( 'hidemainmenu', 1 );

		$this->_setToolBar();
		parent::display();
	}
	
	function _setToolBar(){
		$text = ( $this->task == 'edit') ? JText::_( 'EDIT' ) : JText::_( 'NEW' );
		JToolBarHelper::title(  JText::_( 'PACKAGE' ).': <small><small>[ ' . $text.' ]</small></small>' );
		JToolBarHelper::save('save');
		JToolBarHelper::apply('apply');
		JToolBarHelper::cancel('cancel');
	}
}