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
class YRMViewPayment_method extends JView
{
	function display( $tpl = null )
	{
		global $mainframe, $option;
		
		$payment_id = JRequest::getVar('cid');		
		$model	=	$this->getModel('payment_method');
		$data 	=	$model->getData();		
		$pageNav 	=	$model->getPagination();		
		$this->assignRef('data', $data);
		$this->assignRef('pageNav', $pageNav);	
		$this->assignRef('payment_id', $payment_id);
		
		parent::display($tpl);
	}
	function installPayment( $tpl = null)
	{
		global $mainframe, $option;
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		
		JToolBarHelper::title(  JText::_( 'Install Payment method' ));
		JToolBarHelper::back('Back','index.php?option=com_yos_resources_manager&view=payment_method');
		
		$model	=	$this->getModel('payment_method');
		
		parent::display($tpl);
	}	
	function uninstallpayment( $tpl = null)
	{
		global $mainframe, $option;
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$model	=	$this->getModel('payment_method');		
		parent::display($tpl);
	}
}