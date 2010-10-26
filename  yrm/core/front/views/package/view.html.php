<?php
/**
 * @copyright	Copyright (C) 2010 YOS.,JSC. All rights reserved.
 * @license	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

// Set the table directory
JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');

/**
 * HTML View class for the Jlord_amMap component
 *
 * @static
 * @package		Joomla
 * @subpackage	Jlord_checkversion
 * @since 1.0
 */
class YRMViewPackage extends JView
{
	function display($tpl=null){
		global $mainframe,$Itemid;
		
		$session =& JFactory::getSession();
		$package = $session->get('yrm_arr_package');
		$return_url = $session->get('yrm_return_url');
		
		$show = JRequest::getVar('show');
				
		$model	= $this->getModel('package');
		
		if ($package && !$show) {
			$data	= $model->getPackages1($package);
		}
		
		if ($this->getLayout()=='checkout') {
			$packageid= JRequest::getInt('packageid');			
			$package = $model->getPackage($packageid);
			$payment = $model->getPaymentMethods($packageid);
			$this->assign('package', $package);		
			$this->assign('payment', $payment);			
			$this->assign('return_url', $return_url);	
			$this->assign('return_url', $return_url);	
			parent::display($tpl);
			return ;
		}
		$data	= $model->getPackages();	
		
		$this->assign('data', $data);		
		$this->assign('package', $package);	
		$this->assign('return_url', $return_url);	
		$this->assign('show', $show);	
		$this->assign('Itemid', $Itemid);	
		parent::display($tpl);
	}	
}

?>