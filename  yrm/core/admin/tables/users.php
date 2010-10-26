
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
class TableUsers extends JTable
{
	 var $id			= null;
	 var $name			= null;
	 var $username		= null;
	 var $email			= null;
	 var $password		= null;
	 var $usertype		= null;
	 var $block			= null;
	 var $sendEmail		= null;
	 var $gid			= null;
	 var $registerDate	= null;
	 var $lastvisitDate	= null;
	 var $activation	= null;
	 var $params		= null;
	/**
	* @param database A database connector object
	*/
	function __construct(&$db)
	{
		parent::__construct( '#__users', 'id', $db );
	}

}
