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
		$order_id = JRequest::getVar('order_id');
		$model	= $this->getModel('package');			
		$data	= $model->getPackageOrder($order_id);
		
		$this->assign('data', $data);
		$this->assign('Itemid', $Itemid);	
		parent::display($tpl);
	}	
}

?>