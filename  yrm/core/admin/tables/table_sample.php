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
class TableSample extends JTable
{
	/** @var int Primary key */
	var $id 				= null;
	/** @var string */
	var $produce_code		= null;
	/** @var string */
	var $produce_name		= null;
	/** @var string */
	var $lastest_version	= null;	
	/** @var int */
	var $checked_out 		= 0;
	/** @var date */
	var $checked_out_time	= null;
	/**
	* @param database A database connector object
	*/
	function __construct(&$db)
	{
		parent::__construct( '#__jlord_produce', 'id', $db );
	}

	/**
	 * Overloaded check function
	 *
	 * @access public
	 * @return boolean
	 * @see JTable::check
	 * @since 1.5
	 */
	function check()
	{
		//$this->default_con = intval( $this->default_con );

//		if (JFilterInput::checkAttribute(array ('href', $this->webpage))) {
//			$this->setError(JText::_('Please provide a valid URL'));
//			return false;
//		}

		// check for http on webpage
//		if (strlen($this->webpage) > 0 && (!(eregi('http://', $this->webpage) || (eregi('https://', $this->webpage)) || (eregi('ftp://', $this->webpage))))) {
//			$this->webpage = 'http://'.$this->webpage;
//		}

//		if(empty($this->alias)) {
//			$this->alias = $this->name;
//		}
//		$this->alias = JFilterOutput::stringURLSafe($this->alias);
//		if(trim(str_replace('-','',$this->alias)) == '') {
//			$datenow =& JFactory::getDate();
//			$this->alias = $datenow->toFormat("%Y-%m-%d-%H-%M-%S");
//		}

		return true;
	}	
}
