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
class TableResource extends JTable
{
	var $id 				= null;
	var $name				= null;	
	var $parent_id			= null;
	var $affected			= null;
	var $type				= null;
	var $option				= null;
	var $task				= null;
	var $view				= null;
	var $params				= null;
	var $plug_in			= null;
	var $redirect_url		= null;
	var $redirect_message	= null;
	var $description		= null;
	var $sticky				= null;
	var $published			= null;

	function __construct(&$db)
	{
		parent::__construct( '#__yos_resources_manager_resource', 'id', $db );
	}

}
