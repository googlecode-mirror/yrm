<?php
/**
 * @version	$Id: yos_resources_manager.php $
 * @package	YRM
 * @subpackage	Component
 * @copyright	Copyright (C) 2010 YOS.,JSC. All rights reserved.
 * @license	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');

/**
 * Weblinks Component Weblink Model
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class YRMModelOrder extends JModel
{
	/**
	 * Events data array
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * uri total
	 *
	 * @var integer
	 */
	var $_total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;
	
	var $payment_id = null;
	var $payment_enabled = 0;
	var $payment_method_name = '';
	var $payment_method_code = '';
	var $payment_class = '';
	var $is_creditcard = 0;
	var $enable_processor = '';
	var $creditcard = null;
	var $accepted_creditcards = null;
	var $payment_extrainfo = '';
	/**
	 * Constructor
	 *
	 * @since 0.9
	 */
	function __construct()
	{
		parent::__construct();
	}
	function getData(){
		$db = $this->_db;
		if ($this->_data) {
			return $this->_data;
		}
		// Lets load the data if it doesn't already exist
		
		$query = 'SELECT * '
			. ' FROM #__yos_resources_manager_payment_method AS a';
		$db->setQuery($query, $this->getState('limitstart'), $this->getState('limit'));
		$this->_data = $db->loadObjectList();
		
		return $this->_data;
	}
	function getTotal(){
		$db	=& JFactory::getDBO();
		
		if ($this->_total) {
			return $this->_total;
		}
		
//		$filter = $this->_build_filter();
//		$where = $this->_build_where();
		
		$query = 'SELECT COUNT(a.id)'
		. ' FROM #__yos_resources_manager_payment_method AS a'
		//. $filter
		//. $where
		;
		$db->setQuery( $query );
		$total = $db->loadResult();
		
		$this->_total = $total;
		
		return $this->_total;
	}

	
	function getPagination(){
		global $mainframe, $option;
		
		if ($this->_pagination) {
			return $this->_pagination;
		}
		
		jimport('joomla.html.pagination');
		
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );
		
		if (!$this->_total) {
			$this->getTotal();
		}
		
		$pagination = new JPagination( $this->_total, $limitstart, $limit );
		
		$this->_pagination = $pagination;
		
		return $this->_pagination;
	}
	function getList(){
			
		return $lists;
	}
	function save(){
		$db =& JFactory::getDBO();
		$query = '';
		
		if ($this->payment_id){
			$query .= "UPDATE `#__yos_resources_manager_payment_method` SET \n";			
		}
		else {
			$query .= "INSERT INTO `#__yos_resources_manager_payment_method` SET \n";
		}
		
		$query .= "`name` = '$this->payment_method_name', \n";
		$query .= "`payment_method_code` = '$this->payment_method_code', \n";
		$query .= "`payment_class` = '$this->payment_class', \n";
		$query .= "`published` = $this->payment_enabled, \n";
		$query .= "`is_creditcard` = $this->is_creditcard, \n";
		$query .= "`enable_processor` = '$this->enable_processor', \n";
		$query .= "`accepted_creditcards` = '$this->creditcard' \n";	
//		$payment_extrainfo = base64_encode($this->payment_extrainfo).",";
//		$query .= "`payment_extrainfo` = '$payment_extrainfo' \n";
		if ($this->payment_id){
			$query .= "WHERE `id` = $this->payment_id";
		}		
		$db->setQuery($query);
		
		if(!$db->query()){
			JError::raiseError(500,$db->getErrorMsg());
		}
	
		return true;		
	}

}