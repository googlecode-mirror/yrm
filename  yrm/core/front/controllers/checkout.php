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

class YRMControllerCheckout extends  YRMController
{
	var $return_url='';
	
	var $notify_url='';
	
	var $cancel_url='';
	
	var $order_id='';
	
	var $package='';
	
	var $payment_method_id='';
	
	var $payment='';
	
	var $payment_class='';
	
	var $_PAYMENT='';
	
	function __construct()
	{	
		parent::__construct();
		
		$sesion						=	JFactory::getSession();
		//  get order_id
		$order_id					=	JRequest::getVar('order_id',0);
		$order_id					=	$order_id?$order_id:$sesion->get('order_id');
		$this->order_id				=	$order_id;
		// get payment_method_id
		$payment_method_id			=	JRequest::getVar('pm_id',0);
		$payment_method_id			=	$payment_method_id?$payment_method_id:$sesion->get('payment_method_id');
		$this->payment_method_id	=	$payment_method_id;
		// get package_id
		$package_id					=	$this->getPackage_id($order_id);
		//set data
		$this->setData($order_id,$package_id,$payment_method_id);
	}
	function getPackage_id($order_id)
	{		
		$db				=JFactory::getDBO();
		$query			='SELECT package_id 
							FROM #__yos_resources_manager_order 
							WHERE id='.$order_id;
		$db->setQuery($query);
		$package_id		=$db->loadResult();
		return $package_id;		
	}
	/**
	 * set data
	 *
	 * @param int $order_id
	 * @param int $package_id
	 * @param int $payment_method_id
	 */
	function setData($order_id,$package_id,$payment_method_id)
	{		
		$model					= 	&$this->getModel('package');
		
		$package 				= 	$model->getPackage($package_id);
		
		$this->package			=	$package;
		
		$payment 				= 	$model->getPaymentMethod($payment_method_id);
		
		$this->payment_class	=	$payment->payment_class;
		
		$this->payment			=	$payment;
		
		$path 					= 	JPATH_COMPONENT_SITE.DS.'paymentclass';
	    
		if ( $payment->payment_class ) {
	
	    	if (include_once($path.DS.'pcl_'.$this->payment_class.DS.$this->payment_class.'.php')) {
	    		eval( "\$_PAYMENT = new ".$this->payment_class ."();");	    		
			    $this->return_url = JURI::base() ."index.php?option=com_yos_resources_manager&task=package.result&order_id=$this->order_id";
			    $this->cancel_url = JURI::base();
	    	}
		}else {
	    	include( $path."pcl_payment.php" );
	    	$_PAYMENT 			= 	new pcl_payment();	    	
		}
		$this->_PAYMENT			=	$_PAYMENT;
	}
	/**
	 * Enter description here...
	 *
	 */
	function confirm(){		
		global $option, $mainframe;
		JRequest::setVar('payment_action','showPayment');		
		$this->executes();
	}
	function executes()	{	
		global $option, $mainframe;
			
		$payment_action					=	JRequest::getVar('payment_action','process_payment');
			
		if (method_exists($this->_PAYMENT,$payment_action)) {
			$user 						= 	JFactory::getUser();
			//get userinfo
			$db 						=& JFactory::getDBO();
			$query 						= 'SELECT * 
											FROM #__yos_resources_manager_user_info
											WHERE `user_id` ='.$user->id;
			$db->setQuery($query);
			
			$user_order 				= $db->loadObject();			
			// get model package
			$model						= &	$this->getModel('package');
		
			$base_url= JURI::root().'index.php?option=com_yos_resources_manager&task=checkout.executes&pm_id='.$this->payment_method_id;
			
			// build data
			$data=array();
			$data['base_url']			=	$base_url;
			$data['model']				=	$model;
			$data['user']				=	$user_order;
			$data['order_id']			=	$this->order_id;
			$data['amount']				=	$this->package->value;
			$data['currency_code']		=	$this->package->currency_code;
			$data['return_url']			=	$this->return_url;
			$data['cancel_url']			=	 $this->cancel_url;
			$data['description']		=	$this->package->description;
			$data['name']				=	$this->package->name ;
			// execute action
			$this->_PAYMENT->$payment_action($data);	
		}
	}
}