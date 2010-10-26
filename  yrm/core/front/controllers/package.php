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

class YRMControllerPackage extends  YRMController
{
	
	function checkout(){
		global $mainframe, $option;
		$model=& $this->getModel('package');
		$packageid= JRequest::getInt('packageid');
		$return_url = JRequest::getVar('return_url'); 
		$order = array();
		$order['packageid'] = $packageid;
		$order['return_url'] = $return_url;
		
		$session =& JFactory::getSession();
		$session->set('order', $order);		
		$user = JFactory::getUser();
		if (!$user->id) {
			if ($return_url) {
				$return = $return_url;
			}else {
				$return = base64_encode('index.php?option=com_yos_resources_manager&view=package');
			}
			
			$mainframe->redirect('index.php?option=com_user&view=login&return='.$return,JText::_('YOU_MUST_BE_LOGIN'));
		}
	
		$hasInfo = $model->isUpdateUserInfo();
		
		if (!$hasInfo) {
			$seesion = JFactory::getSession();
			$seesion->set();
			$mainframe->redirect('index.php?option=com_yos_resources_manager&task=user.updateinfo&package='.$packageid);
		}
	
		$view=$this->getView('package','html');	
		$view->setModel($model);
		$view->setLayout('checkout');
		$view->display();
	}
	function confirm(){	
		global $option, $mainframe;		
		$d = array();
		$d['payment_method_id'] = JRequest::getInt('payment_method_id');
		$d['packageid'] = JRequest::getInt('packageid');
		$d['return_url'] = base64_decode(JRequest::getString('return_url'));
		
		$model=& $this->getModel('package');
		$package = $model->getPackage($d['packageid']);
		$payment = $model->getPaymentMethod($d['payment_method_id']);
		
		$d['amount'] = $package->value;
		$d['currency_code'] = $package->currency_code;		
		
		$order_id = $model->insertOrder($d['packageid'],$d['return_url']);
		$sesion=JFactory::getSession();
		$sesion->set('order_id',$order_id);
		$sesion->set('payment_method_id',$d['payment_method_id']);		
		global $mainframe;
		$url=JRoute::_('index.php?option=com_yos_resources_manager&task=checkout.confirm',false);		
		$mainframe->redirect($url);
	}	
	function result(){		
		$model		= &	$this->getModel('package');
		$view=$this->getView('package','result');	
		$view->setModel($model);
		$view->setLayout('result');
		$view->display();
	}
}


?>
