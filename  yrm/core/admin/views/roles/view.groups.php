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

		$cid 				= JRequest::getVar('cid_role');
		$model	=	$this->getModel('roles');
		$model->load(intval($cid[0]));
		$role = $model->role;
		$task = JRequest::getVar('task');
		$this->assignRef('task',$task);
		$obj_re = $model->getDataGroups();
		$this->assignRef('role', $role);
		$this->assignRef('lists',		$obj_re->lists);
		$this->assignRef('items',		$obj_re->rows);
		$this->assignRef('check_rows',	$obj_re->check_rows);
		$this->assignRef('pagination',	$obj_re->pagination);
		$this->_setToolBar();
		parent::display($tpl);
	}
	function _setToolBar(){
		JToolBarHelper::title(  JText::_( 'ROLES_MANAGER_TITLE' ), 'roles' );
		JToolBarHelper::save('save_groups');
		JToolBarHelper::cancel('cancel_group');
	}

}