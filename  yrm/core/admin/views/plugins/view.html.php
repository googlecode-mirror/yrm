<?php
/**
 * @version	$Id: view.html.php $
 * @package	YRM
 * @subpackage	Component
 * @copyright	Copyright (C) 2010 YOS.,JSC. All rights reserved.
 * @license	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the YRM component
 *
 * @static
 * @package	YRM
 * @subpackage	Component
 * @since 1.0
 */
class YRMViewPlugins extends JView
{
	function display($tpl = null){
		global $mainframe, $option;
		
		$this->assignRef('option',	$option);
		
		//hide the menu
		JRequest::setVar( 'hidemainmenu', 1 );

		parent::display($tpl);
	}
}