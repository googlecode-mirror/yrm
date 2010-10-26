<?php
/**
 * @version	$Id: yos_resources_manager.php $
 * @package	YRM
 * @subpackage	Component
 * @copyright	Copyright (C) 2010 YOS.,JSC. All rights reserved.
 * @license	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class YRMControllerUser extends  YRMController
{
	function __construct()
	{
		parent::__construct();		
	}
	
	function updateinfo(){
		global $mainframe, $option;
		
		$model=& $this->getModel('user');
		$packageid= JRequest::getInt('package');		
		
		$view =$this->getView('user','html');	
		$view->setModel($model);
		$view->setLayout('form');
		$view->display();
	}
	
	function save(){
		
		global $mainframe, $option;
		
		$model=& $this->getModel('user');
		
		$model->save();
		$model=& $this->getModel('package');		
		$view=$this->getView('package','html');	
		$session =& JFactory::getSession();
		$order = $session->get('order');	

		$mainframe->Redirect('index.php?option=com_yos_resources_manager&task=package.checkout&packageid=' . $order['packageid']);
	}	
}


?>
