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
class YRMControllerOrder extends JController
{
	
	
	/**
	 * Constructor
	 *
	 * @since 0.9
	 */
	function __construct()
	{
		parent::__construct();
		$this->registerTask( 'apply',		'save' );
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
	
	function  add(){
		$view =& $this->getView('payment_method','html');	
		
		if ($model=& $this->getModel('payment_method')) {
			$view->setModel($model);
		}
		JRequest::setVar( 'hidemainmenu', 1 );
		$view->setLayout('form');
		$view->display();
	
	}
	function save(){
		global $mainframe, $option;
		
		$task = JRequest::getCmd('task');
		
		JRequest::checkToken() or jexit( 'Invalid Token' );				
		$model=& $this->getModel('payment_method');
		
		$model->payment_id = JRequest::getVar('payment_id');
		
		$model->payment_enabled = JRequest::getInt('payment_enabled');
		$model->payment_method_name = JRequest::getVar('payment_method_name');		
		$model->payment_method_code = JRequest::getVar('payment_method_code');
		$model->payment_class = JRequest::getVar('payment_class');
		$enable_processor = JRequest::getVar('enable_processor');	
		$model->enable_processor = $enable_processor;	
		if (empty($enable_processor)) {
			$model->is_creditcard = 1 ;
		}
		else{
		 	$model->is_creditcard = 0 ;
		}
		$model->payment_class = JRequest::getVar('payment_class');
		$creditcard = JRequest::getVar('creditcard');		
		$model->creditcard = implode(',',$creditcard);				
		$model->accepted_creditcards = JRequest::getVar('accepted_creditcards');
		$model->payment_extrainfo = JRequest::getVar('payment_extrainfo','', JREQUEST_ALLOWHTML);
		$rs = $model->save();
		
		//write config
		
		$path = dirname(__FILE__).DS.'..'.DS.'paymentclass';
		
		if ( $model->payment_class ) {
	
	    	if (include_once($path.DS.$model->payment_class )) {
	    		eval( "\$_PAYMENT = new ".str_replace('.php', '', $model->payment_class)."();");
	    	}
		}else {
	    	include( $path."ps_payment.php" );
	    	$_PAYMENT = new ps_payment();
		}
		$_PAYMENT->write_configuration();
		
		//end write
		
		$this->setRedirect('index.php?option='.$option.'&view=payment_method');
		
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
		. ' WHERE id IN ( '. $cids .' )'
		;
		
		$db->setQuery( $query );
		if (!$db->query())
		{
			JError::raiseError(500, $db->getErrorMsg() );
		}
		
		$this->setRedirect('index.php?option='.$option.'&view=payment_method');
	}
	
	function remove(){
		global $mainframe,$option;
		$db = &JFactory::getDBO();
		$cid = JRequest::getVar('cid');
		$cid_del = implode(',',$cid);
		$query = 'DELETE FROM #__yos_resources_manager_package WHERE id IN('.$cid_del.')';
		$db->setQuery($query);
		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg());			
		}
		$query = 'DELETE FROM #__yos_resources_manager_payment_method WHERE id IN('.$cid_del.')';
		$db->setQuery($query);
		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg());			
		}
		
		$this->setRedirect('index.php?option='.$option.'&view=payment_method');
	}

}