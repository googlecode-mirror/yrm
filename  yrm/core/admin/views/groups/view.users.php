<?php
/**
* @version		$Id: view.html.php 10381 2008-06-01 03:35:53Z pasamio $
* @package		Joomla
* @subpackage	Config
* @copyright	Copyright (C) 2010 YOS.,JSC. All rights reserved.
* @license	GNU/GPL http://www.gnu.org/copyleft/gpl.html
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Poll component
 *
 * @static
 * @package		Joomla
 * @subpackage	Poll
 * @since 1.0
 */
class YRMViewgroups extends JView
{
	function display( $tpl = null )
	{
		$db = &JFactory::getDBO();
		$cid = JRequest::getVar('cid_group');
		$model = $this->getModel('groups');
		$m_users = $model->getUsers($cid);
		
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
		parent::display($tpl);
	}
	
	function _setToolBar(){
		JToolBarHelper::title( JText::_( 'MANAGE_GROUP_USERS').' <small>['.$this->group->name.']</small>' , 'user.png' );
		
		JToolBarHelper::addNewX('add_multi_users', 'GROUP_ADD_MULTIPLE_USERS');
		JToolBarHelper::addNewX('add_user', 'Add');
		JToolBarHelper::editListX('edit_user');
		JToolBarHelper::deleteList(JText::_('GROUP_USER_SURE_REMOVE_USERS_FROM_GROUP'),'remove_user','Remove');
		JToolBarHelper::cancel();
	}
}