<?php
/**
 * @version	$Id: table_yos_resources_manager.php $
 * @package	YRM
 * @subpackage	Component
 * @copyright	Copyright (C) 2010 YOS.,JSC. All rights reserved.
 * @license	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );


/**
 * @package		Joomla
 * @subpackage	Test
 */
class TablePackage_Object_xref extends JTable
{
	/** @var int Primary key */
	var $id				= null;
	/** @var string */
	var $object_id 	= null;
	/** @var string */
	var $package_id 	= null;
	/** @var string */
	var $type 	= null;
	/** @var string */
	var $times_access 	= null;
	/** @var string */
	var $seconds		= null;
	
	/**
	* @param database A database connector object
	*/
	function __construct(&$db)
	{
		parent::__construct( '#__yos_resources_manager_package_object_xref', 'id', $db );
	}

}
