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
class TableGroups extends JTable
{
	/** @var int Primary key */
	var $id 			= null;
	var $name			= null;
	var $description	= null;
	var $time_mapping	= null;
	/**
	* @param database A database connector object
	*/
	function __construct(&$db)
	{
		parent::__construct( '#__yos_resources_manager_group', 'id', $db );
	}

}
