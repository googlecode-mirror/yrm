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
class TableUser_role_xref extends JTable
{
	/** @var int Primary key */
	var $id				= null;
	/** @var string */
	var $user_id 	= null;
	/** @var string */
	var $role_id		= null;
	/** @var string */
	var $start		= null;
	/** @var string */
	var $end		= null;
	
	/**
	* @param database A database connector object
	*/
	function __construct(&$db)
	{
		parent::__construct( '#__yos_resources_manager_user_role_xref', 'id', $db );
	}

}
