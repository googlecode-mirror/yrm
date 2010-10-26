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

jimport('joomla.application.component.model');

/**
 * Weblinks Component Weblink Model
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class YRMControllerPayment_method extends JController
{
	
	var $payment_class_name='';
	var $payment_method_id='';
	
	/**
	 * Constructor
	 *
	 * @since 0.9
	 */
	function __construct()
	{			
		$lang					=	JFactory::getLanguage();
		$basePath				=	JPATH_COMPONENT_SITE.DS.'paymentclass';
		$lang->load('payment_method',$basePath,$lang->_lang);
		
		$payment_method_id		=	JRequest::getVar('cid',0,'','array');
		if ($payment_method_id) {
			$this->payment_method_id=$payment_method_id[0];
			$this->getPayment_class_name();
			$this->addLang($basePath);
		}
		
		parent::__construct();
		
		$this->registerTask( 'apply',		'save' );
	}
	function getPayment_class_name()
	{
		if ($this->payment_class_name) {
			return $this->payment_class_name;
		}				
		$db			=	JFactory::getDBO();
		$query		=	'SELECT payment_class 
						FROM `#__yos_resources_manager_payment_method` 
						WHERE id='.$this->payment_method_id;
		$db->setQuery($query);
		$class_name	=	$db->loadResult();
		$this->payment_class_name=$class_name;
		return $this->payment_class_name;
	}
	function addLang($basePath)
	{
		$class_name	=	$this->payment_class_name;
		$basePath=$basePath.'pcl_'.$class_name.DS;		
		$lang 		= 	& JFactory::getLanguage();
		$lang->load($class_name,$basePath,$lang->_lang);
	}
	function  edit(){
		$view =& $this->getView('payment_method','html');	
		
		if ($model=& $this->getModel('payment_method')) {
			$view->setModel($model);
		}
		
		JRequest::setVar( 'hidemainmenu', 1 );
		
		$view->setLayout('form');
		$view->display();
	
	}
//	function  add(){
//		$view =& $this->getView('payment_method','html');	
//		
//		if ($model=& $this->getModel('payment_method')) {
//			$view->setModel($model);
//		}
//		JRequest::setVar( 'hidemainmenu', 1 );
//		$view->setLayout('form');
//		$view->display();
//	
//	}
	function save(){
		global $mainframe, $option;
		
		$task = JRequest::getCmd('task');
		
		JRequest::checkToken() or jexit( 'Invalid Token' );				
		$model=& $this->getModel('payment_method');
		
		$model->payment_id 				=	JRequest::getVar('payment_id');
		$this->payment_method_id		=	$model->payment_id;
		$this->getPayment_class_name();
		$model->payment_enabled 		=	JRequest::getInt('payment_enabled');
		$model->payment_method_name 	=	JRequest::getVar('payment_method_name');		
		$model->payment_method_code 	=	JRequest::getVar('payment_method_code');
//		$model->payment_class 			=	JRequest::getVar('payment_class');
		$enable_processor 				=	JRequest::getVar('enable_processor');		
		$model->enable_processor = $enable_processor;		
		if (empty($enable_processor)) {
			$model->is_creditcard = 1 ;
		}
		else{
		 	$model->is_creditcard = 0 ;
		}
		$model->payment_class = $this->payment_class_name;
		$creditcard = JRequest::getVar('creditcard');		
//		$model->creditcard = implode(',',$creditcard);
		$model->accepted_creditcards = JRequest::getVar('accepted_creditcards');
		$model->payment_extrainfo = JRequest::getVar('payment_extrainfo','', JREQUEST_ALLOWHTML);
		$rs = $model->save();
		
		//write config
		
//		$path = dirname(__FILE__).DS.'..'.DS.'paymentclass';
		$path = JPATH_COMPONENT_SITE.DS.'paymentclass';
		
		//begin hack
		include_once( $path.DS."pcl_payment.php" );
    	$_PAYMENT = new pcl_payment();
    	$_PAYMENT->write_configuration();    	
    	// end hack    	
		if ( $model->payment_class ) {	
	    	if (include_once($path.DS.'pcl_'.$model->payment_class .DS.$model->payment_class .'.php' )) {
	    		eval( "\$_PAYMENT = new ".$model->payment_class ."();");
	    	}
		}else {
	    	include( $path."pcl_payment.php" );
	    	$_PAYMENT = new pcl_payment();
		}		
		$_PAYMENT->write_configuration();
		
		//end write
		$task 					= JRequest::getVar('task');
		switch ($task) {
			case 'apply' :
				$msg = JText::_('Successfully Saved Payment method');
				$mainframe->redirect('index.php?option=com_yos_resources_manager&task=payment_method.edit&cid[]='.$model->payment_id, $msg);
				break;

			case 'save' :
				$msg = JText::_('Successfully Saved Payment method');
				$mainframe->redirect('index.php?option=com_yos_resources_manager&view=payment_method', $msg);
				break;
		}
		//$this->setRedirect('index.php?option='.$option.'&view=payment_method');
		//index.php?option=com_yos_resources_manager&task=payment_method.edit&cid[]=4
	}
	function getPaymentMethods($payment_id){
		$db = JFactory::getDBO();
		
		// Lets load the data if it doesn't already exist
		
		$query = 'SELECT payment_class '
			. ' FROM #__yos_resources_manager_payment_method YRMPM '.
			' WHERE id = '.$payment_id ;
		$db->setQuery($query);		
		$row = $db->loadResult();
		
		return $row; 		
	}
	function executes()
	{
		$path = JPATH_COMPONENT_SITE.DS.'paymentclass';
		$payment_id=JRequest::getVar('cid','','','array');
		$payment_id=$payment_id[0];
		$payment_class=$this->getPaymentMethods($payment_id);
		$payment_action=JRequest::getVar('payment_action','');	
		
		if ( $payment_class ) {
	
	    	if (include_once($path.DS.'pcl_'.$payment_class .DS.$payment_class .'.php' )) {
	    		eval( "\$_PAYMENT = new ".$payment_class ."();");
	    		if ($payment_action and method_exists($_PAYMENT,$payment_action)) {
					 $base_url= JURI::root().'administrator/index.php?option=com_yos_resources_manager&task=payment_method.executes&cid[]='.$payment_id;
					$_PAYMENT->$payment_action($base_url);
				}
				else {
					global $mainframe;
		     		$mainframe->redirect('index.php?option=com_yos_resources_manager&task=payment_method.edit&cid[]='.$payment_id);
				}
	    	}
		}		
	}
	function publish()
	{
		global $mainframe,$option;
		
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$db 	=& JFactory::getDBO();
		$cid		= JRequest::getVar( 'cid', array(), '', 'array' );
		$publish	= ( $this->getTask() == 'publish' ? 1 : 0 );
		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1)
		{
			$action = $publish ? 'publish' : 'unpublish';
			JError::raiseError(500, JText::_( 'Select an item to' .$action, true ) );
		}
		$cids = implode( ',', $cid );

		$query = 'UPDATE #__yos_resources_manager_payment_method'
		. ' SET published = ' . (int) $publish
		. ' WHERE id IN ( '. $cids .' )';
		$db->setQuery( $query );
		if (!$db->query())
		{
			JError::raiseError(500, $db->getErrorMsg() );
		}
		$this->setRedirect('index.php?option='.$option.'&view=payment_method');
	}
//	function remove(){
//		global $mainframe,$option;
//		$db = &JFactory::getDBO();
//		$cid = JRequest::getVar('cid');
//		$cid_del = implode(',',$cid);
//		$query = 'DELETE FROM #__yos_resources_manager_package WHERE id IN('.$cid_del.')';
//		$db->setQuery($query);
//		if (!$db->query()) {
//			JError::raiseError(500, $db->getErrorMsg());			
//		}
//		$query = 'DELETE FROM #__yos_resources_manager_payment_method WHERE id IN('.$cid_del.')';
//		$db->setQuery($query);
//		if (!$db->query()) {
//			JError::raiseError(500, $db->getErrorMsg());			
//		}
//		
//		$this->setRedirect('index.php?option='.$option.'&view=payment_method');
//	}
	function loadLanguage($payment_method_id=null)
	{
		if (!$payment_method_id) {
			$payment_method_id=JRequest::getVar('cid');
		}
	}
	// for install and uninstall
	function installPayment()
	{
		$view =& $this->getView('payment_method','html');	
		
		if ($model=& $this->getModel('payment_method')) {
			$view->setModel($model);
		}
		JRequest::setVar( 'hidemainmenu', 1 );
		$view->setLayout('installpayment');
		$view->installPayment();
	}
	function doInstall()
	{
		global $mainframe;
		if ($model=& $this->getModel('payment_method')) {
			$pathBase=$model->installPayment();
			jimport('joomla.filesystem.folder');			
			if ($pathBase) {
				JFolder::delete($pathBase);
			}
			$install_mess=$model->install_mess;
			$install_status=$model->install_status;
			$mess='';
			if ($install_status) {
				$mess=$install_mess;
			}
			else {
				JError::raiseWarning('d',$install_mess);				
			}
			$mainframe->redirect('index.php?option=com_yos_resources_manager&view=payment_method',$mess);				
		}		
	}
	function uninstallPayment()
	{
		global $mainframe,$option;
		$model=& $this->getModel('payment_method');
		$mess=$model->unInstall();		
		$this->setRedirect('index.php?option='.$option.'&view=payment_method',$mess);
	}	
}