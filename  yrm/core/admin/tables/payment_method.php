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
class TablePayment_method extends JTable
{
	
	//id 	name 	description 	published 	payment_class 	payment_method_code 	is_creditcard 	accepted_creditcards 	payment_extrainfo 	payment_passkey
	/** @var int Primary key */
	var $id 				= null;
	/** @var string */
	var $name		= null;
	
	/** @var string */
	var $description		= null;
	
	var $published		= null;
	
	var $payment_class		= null;
	
	var $payment_method_code		= null;
	
	var $is_creditcard		= null;
	
	var $accepted_creditcards		= null;
	
	var $payment_extrainfo		= null;
	
	var $payment_passkey		= null;
	
	
	
	/**
	* @param database A database connector object
	*/
	function __construct(&$db)
	{
		parent::__construct( '#__yos_resources_manager_payment_method', 'id', $db );
	}

}
