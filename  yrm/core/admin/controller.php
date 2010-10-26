<?php
/**
 * @version		$Id: controller.php $
 * @package		YRM
 * @subpackage		component
 * @copyright	Copyright (C) 2010 YOS.,JSC. All rights reserved.
 * @license	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );
jimport('joomla.client.helper');


/**
 * Main Controller
 *
 * @package	YRM
 * @subpackage	component
 * @version 	1.0
 */
class YRMController extends JController
{
	/**
	 * Display the view
	 */
	function display()
	{
		$vName = JRequest::getVar('view');
		if ($vName=='version') {
			global $mainframe;
		
			$document 	= &JFactory::getDocument();
			$vType		= $document->getType();
			$vLayout 	= 'about';
			$mName		= 'version';
			
			// Get/Create the view
			$view = &$this->getView( 'version', $vType);
			
			// Get/Create the model	
			$checkversion	=	&YOS_utility::getVersion();
			$version	=	$checkversion['version'];
			$url		=	$checkversion['url'];
			$pc			=	$checkversion['productcode'];
			if ($model = &$this->getModel($mName,'', array('version'=> $version, 'url'=> $url , 'pc'=> $pc ))) {
				// Push the model into the view (as default)
				
				$view->setModel($model, true);
			}
					
			// Set the layout
			$view->setLayout($vLayout);
			
			//display view
			$view->display();
			return ;
		}
		
		if ($vName) {
			parent::display();
			return ;
		}
		$view = $this->getView('cpanel','html');
		
		$view->display();
		
	}
	
	
}
