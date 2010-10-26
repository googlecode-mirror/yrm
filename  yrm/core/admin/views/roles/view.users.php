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
class YRMViewRoles extends JView
{
	function display( $tpl = null )
	{
		global $mainframe, $option;
		$cid = JRequest::getVar('cid_role');
		$model	=	$this->getModel('roles');
		$obj_re = $model->getDataUsers();
		
		$model->load(intval($cid[0]));
		$role = $model->role;
		$this->assignRef('role', $role);
		$task = JRequest::getVar('task');
		$this->assignRef('task',$task);
		$this->assignRef('user',		JFactory::getUser());
		$this->assignRef('lists',		$obj_re->lists);
		$this->assignRef('items',		$obj_re->rows);
		$this->assignRef('pagination',	$obj_re->pagination);
		$this->assignRef('role_id', $cid[0]);
		$this->_setToolBar();
		parent::display($tpl);
	}
	function _setToolBar()
	{
		JToolBarHelper::title( JText::_( 'MANAGE_ROLE_USERS').' <small>['.$this->role->name.']</small>', 'roles' );
		JToolBarHelper::addNewX('add_multi_users', 'ROLE_ADD_MULTIPLE_USERS');
		JToolBarHelper::addNewX('add_user', 'Add');
		JToolBarHelper::editListX('edit_user');
		JToolBarHelper::deleteList(JText::_('ROLE_USER_SURE_REMOVE_USERS_FROM_ROLE'),'remove_user','Remove');
		JToolBarHelper::cancel();
	}
}