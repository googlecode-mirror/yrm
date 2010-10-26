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
class YRMViewOrder extends JView
{
	function display( $tpl = null )
	{
		global $mainframe, $option;
		
		$payment_id = JRequest::getVar('cid');
		$model	=	$this->getModel('order');
		$data 	=	$model->getData();
		$pageNav 	=	$model->getPagination();
		$lists = $model->getList();
		$this->assignRef('lists',		$lists);
		
		$this->assignRef('data', $data);
		$this->assignRef('pageNav', $pageNav);	
		$this->assignRef('payment_id', $payment_id);
		parent::display($tpl);
	}	
}