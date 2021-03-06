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
class YRMViewPackages extends JView
{
	function display( $tpl = null )
	{
		global $mainframe, $option;
		
		$model	=	$this->getModel('packages');
		
		$lists = $model->getList();
		$rows = $model->getData();
		$pagination = $model->getPagination();
		
		$this->assignRef('lists',		$lists);
		$this->assignRef('items',		$rows);
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('option',	$option);
		$this->_setToolBar();

		parent::display($tpl);
	}
	
	function _setToolBar()
	{
		JToolBarHelper::addNewX();
		JToolBarHelper::publish();
		JToolBarHelper::unpublishList();
		JToolBarHelper::editListX();
		JToolBarHelper::deleteList('Do you want to delete these packages?');
	}
}