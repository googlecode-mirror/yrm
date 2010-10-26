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
		global $mainframe, $option;

		$db					=& JFactory::getDBO();
		$cid 				= JRequest::getVar('cid_group');
		$model	=	$this->getModel('groups');
		$model->load(intval($cid[0]));
		$group = $model->group;
		$this->assignRef('group', $group);
		// get model
		
		
		$task = JRequest::getVar('task');
		$this->assignRef('task',$task);
		$obj = $model->getDataRoles();
		
		$this->assignRef('lists',		$obj->lists);
		$this->assignRef('items',		$obj->rows);
		$this->assignRef('check_rows',	$obj->check_rows);
		$this->assignRef('pagination',	$obj->pagination);
		$this->_setToolBar();
		parent::display($tpl);
	}
	
	function _setToolBar(){
		JToolBarHelper::save('save_roles');
		JToolBarHelper::cancel('cancel_role');
	}

}