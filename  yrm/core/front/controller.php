<?php
/**
 * @version	$Id: controller.php $
 * @package	YRM
 * @subpackage	Component
 * @copyright	Copyright (C) 2010 YOS.,JSC. All rights reserved.
 * @license	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

// Set the table directory
JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');

/**
 * Contact Component Controller
 *
 * @static
 * @package		Joomla
 * @subpackage	amMap
 * @since 1.5
 */
class YRMController extends JController {
	function display(){
		global $mainframe;
		$vName = JRequest::getVar('view');		
		if ($vName) {
			$view = $this->getView('package','html');
			if ($model=& $this->getModel('package')) {
				$view->setModel($model);
			}
			$view->display();
			return ;
		}		
		parent::display();
	}	
}
?>