<?php
/**
 * @version	$Id: resources.php $
 * @package	YRM
 * @subpackage	Component
 * @copyright	Copyright (C) 2010 YOS.,JSC. All rights reserved.
 * @license	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );
// Set the table directory
JTable::addIncludePath( JPATH_COMPONENT.DS.'tables' );
/**
 * @package		Joomla
 * @subpackage	Banners
 */

class YRMControllerMapping extends JController
{
	/**
	 * Constructor
	 */
	
	function __construct( $config = array() )
	{
		parent::__construct( $config );
		// Register Extra tasks
		$this->registerTask( 'apply',		'save' );
	}

	/**
	 * Display the list of banners
	 */
	function display()
	{
		parent::display();
	}
	
	function cancel()
	{
		global $option;
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$this->setRedirect( 'index.php?option='. $option .'&view=mapping' );
	}
}
