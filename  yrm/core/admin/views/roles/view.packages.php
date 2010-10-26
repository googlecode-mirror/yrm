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
		$model = $this->getModel('roles');
		$model->load(intval($cid[0]));
		
		$role = $model->role;
		$obj = $model->getDataPackages();
		$this->assignRef('role', $role);
		$task = JRequest::getVar('task');
		$this->assignRef('task',$task);
		// search filter
		$this->assignRef('lists',		$obj->lists);
		$this->assignRef('items',		$obj->rows);
		$this->assignRef('pagination',	$obj->pagination);
		$this->assignRef('role_id', $cid[0]);
		$this->_setToolBar();
		parent::display($tpl);
	}
	
	function _setToolBar(){
		JToolBarHelper::title( JText::_( 'MANAGE_ROLE_PACKAGES').' <small>['.$this->role->name.']</small>' , 'roles' );
		JToolBarHelper::addNewX('add_package', 'Add');
		JToolBarHelper::editListX('edit_package');
		JToolBarHelper::deleteList('Do you want to remove these packages?','remove_package','Remove');
		JToolBarHelper::cancel();
	}
}