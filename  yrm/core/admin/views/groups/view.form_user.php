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
class YRMViewgroups extends JView
{
	function display($tpl = null){
		global $mainframe;
		
		$model		=	$this->getModel('groups');
		$db 		= & JFactory::getDBO();
		$cmd 		= JRequest::getVar('task');
		$cid 		= JRequest::getVar('cid_user');
		$lists 		= array();
		$group_id	= JRequest::getVar('group_id');
		$user_id 	= intval(JRequest::getVar('user_id'));
		$cid_tmp 	= $cid[0];
		if ($user_id > 0) {
			$cid_tmp = $user_id;
		}

		$user = JTable::getInstance('users', 'Table');
		$user->load(intval($cid[0]));
		$query = ' SELECT a.*, a.id as user_id, g.name as groupname '
				.' FROM `#__users` AS a'
				. ' INNER JOIN #__core_acl_aro AS aro ON aro.value = a.id'
				. ' INNER JOIN #__core_acl_groups_aro_map AS gm ON gm.aro_id = aro.id'
				. ' INNER JOIN #__core_acl_aro_groups AS g ON g.id = gm.group_id'
				.' WHERE a.id='.intval($cid_tmp);
		$db->setQuery($query);
		$row 	= $db->loadObject();

		$javascript = 'onchange="document.adminForm.submit();"';
		// get list of Authors for dropdown filter
		$query = 'SELECT u.id as value, u.username as text'
				.' FROM #__users AS u' 
				;
		$db->setQuery($query);
		$users = $db->loadObjectList();
		$lists['users'] = JHTML::_('select.genericlist',  $users, 'user_id', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', $cid_tmp);
		
		$form = new JParameter('', JPATH_COMPONENT.DS.'views'.DS.'groups'.DS.'tmpl'.DS.'user.xml');
		$set_start = '';
		$set_end = '';
		if ($cmd == 'edit_user') {
			$query = 'SELECT * FROM #__yos_resources_manager_user_group_xref WHERE user_id='.intval($cid[0]).' AND group_id='.$group_id;
			$db->setQuery($query);
			$row1 = $db->loadObject();
			$set_start = $row1->start;
			$set_end = $row1->end;
		}
		$form->set('start', JHTML::_('date', $set_start, '%Y-%m-%d %H:%M:%S'));
		$form->set('end', JHTML::_('date', $set_end, '%Y-%m-%d %H:%M:%S'));
		$this->assignRef('form',$form);
		JRequest::setVar( 'hidemainmenu', 1 );
		$this->assignRef('cmd',$cmd);
		$this->assignRef('row', $row);
		$this->assignRef('user', $user);
		$this->assignRef('lists',$lists);
		$this->assignRef('group_id', $group_id);
		parent::display($tpl);
	}
}