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
		$where = $this->_build_where();
		$orderby = $this->_build_order_by();

		// Lets load the data if it doesn't already exist
		$pagination = $this->getPagination();
		$query = 'SELECT a.*, u.username as username, p.name AS packagename , p.value , c.currency_code '
			. ' FROM #__yos_resources_manager_order AS a LEFT JOIN #__users AS u ON a.user_id = u.id '
			. ' LEFT JOIN #__yos_resources_manager_package p ON a.package_id = p.id  LEFT JOIN #__yos_resources_manager_currency c ON p.currency = c.id '
			.$where.$orderby
			;
		$db->setQuery($query,  $pagination->limitstart, $pagination->limit);
		$this->_data = $db->loadObjectList();
		
		return $this->_data;
	}
	function getTotal(){
		$db	=& JFactory::getDBO();
		
		if ($this->_total) {
			return $this->_total;
		}
		
		$where = $this->_build_where();
		$orderby = $this->_build_order_by();

		// Lets load the data if it doesn't already exist
		
		$query = 'SELECT COUNT(a.id) '
			. ' FROM #__yos_resources_manager_order AS a LEFT JOIN #__users AS u ON a.user_id = u.id '
			. ' LEFT JOIN #__yos_resources_manager_package p ON a.package_id = p.id  LEFT JOIN #__yos_resources_manager_currency c ON p.currency = c.id '
			.$where.$orderby
			;			
		
		$db->setQuery( $query );
		$total = $db->loadResult();
		
		$this->_total = $total;
		
		return $this->_total;
	}

	function _build_where(){
		global $mainframe, $option;
		
		$db				=& JFactory::getDBO();

		$search				= $mainframe->getUserStateFromRequest( "$option.search",			'search', 			'',			'string' );
		$search				= JString::strtolower( $search );
		$where = '';
		$wheres= '';
		if (isset( $search ) && $search!= '')
		{
			$searchEscaped = $db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where = $searchEscaped;
			$where_user = 'WHERE u.username LIKE '.$where;
			$where_package = 'OR p.name LIKE '.$where;
			$wheres =  $where_user.$where_package;
		}
				
		return $wheres;
	}
	
	function _build_order_by(){
		global $mainframe, $option;
		
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.order.filter_order",		'filter_order',		'u.username',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.order.filter_order_Dir",	'filter_order_Dir',	'',			'word' );
		
		$orderby = ' ORDER BY '. $filter_order .' '. $filter_order_Dir;
			
		return $orderby;
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
		global $mainframe, $option;		
		$db				=& JFactory::getDBO();

		$filter_order		= $mainframe->getUserStateFromRequest( "$option.order.filter_order",		'filter_order',		'u.username',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.order.filter_order_Dir",	'filter_order_Dir',	'',			'word' );
		$search				= $mainframe->getUserStateFromRequest( "$option.order.search",			'search', 			'',			'string' );
		$search				= JString::strtolower( $search );
		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;
		// search filter
		$lists['search']= $search;
		
		return $lists;
	}	
}