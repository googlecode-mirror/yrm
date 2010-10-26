<?php
/**
 * @version	$Id: models/users.php $
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
class YRMModelUsers extends JModel
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
		$db				=& JFactory::getDBO();
		
		if ($this->_data) {
			return $this->_data;
		}
		
		$filter = $this->_build_filter();
		$where = $this->_build_where();
		$orderby = $this->_build_order_by();
		
		$query = 'SELECT a.*, g.name AS groupname'
			. ' FROM #__users AS a'
			. ' INNER JOIN #__core_acl_aro AS aro ON aro.value = a.id'
			. ' INNER JOIN #__core_acl_groups_aro_map AS gm ON gm.aro_id = aro.id'
			. ' INNER JOIN #__core_acl_aro_groups AS g ON g.id = gm.group_id'
			. $filter
			. $where
			. ' GROUP BY a.id'
			. $orderby
		;
		
		$pagination = $this->getPagination();
		
		$db->setQuery( $query, $pagination->limitstart, $pagination->limit );
		$rows = $db->loadObjectList();
		
		$n = count( $rows );
		$template = 'SELECT COUNT(s.userid)'
			. ' FROM #__session AS s'
			. ' WHERE s.userid = %d'
		;
		for ($i = 0; $i < $n; $i++)
		{
			$row = &$rows[$i];
			$query = sprintf( $template, intval( $row->id ) );
			$db->setQuery( $query );
			$row->loggedin = $db->loadResult();
		}
		
		$this->_data = $rows;

		return $this->_data;	
	}
	
	function getTotal(){
		$db				=& JFactory::getDBO();
		
		if ($this->_total) {
			return $this->_total;
		}
		
		$filter = $this->_build_filter();
		$where = $this->_build_where();
		
		$query = 'SELECT COUNT(a.id)'
		. ' FROM #__users AS a'
		. $filter
		. $where
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
		global $mainframe, $option;
		
		$db				=& JFactory::getDBO();
		
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order_user",		'filter_order',		'a.name',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir_user",	'filter_order_Dir',	'',			'word' );
		$filter_type		= $mainframe->getUserStateFromRequest( "$option.filter_type_user",		'filter_type', 		0,			'string' );
		$filter_logged		= $mainframe->getUserStateFromRequest( "$option.filter_logged_user",	'filter_logged', 	0,			'int' );
		$search				= $mainframe->getUserStateFromRequest( "$option.search_user",			'search', 			'',			'string' );
		$search				= JString::strtolower( $search );
		
		// get list of Groups for dropdown filter
		$query = 'SELECT name AS value, name AS text'
			. ' FROM #__core_acl_aro_groups'
			. ' WHERE name != "ROOT"'
			. ' AND name != "USERS"'
		;
		$db->setQuery( $query );
		$types[] 		= JHTML::_('select.option',  '0', '- '. JText::_( 'Select Group' ) .' -' );
		foreach( $db->loadObjectList() as $obj )
		{
			$types[] = JHTML::_('select.option',  $obj->value, JText::_( $obj->text ) );
		}
		$lists['type'] 	= JHTML::_('select.genericlist',   $types, 'filter_type', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', "$filter_type" );

		// get list of Log Status for dropdown filter
		$logged[] = JHTML::_('select.option',  0, '- '. JText::_( 'Select Log Status' ) .' -');
		$logged[] = JHTML::_('select.option',  1, JText::_( 'Logged In' ) );
		$lists['logged'] = JHTML::_('select.genericlist',   $logged, 'filter_logged', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', "$filter_logged" );

		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;

		// search filter
		$lists['search']= $search;
		
		return $lists;
	}
	
	function _build_where(){
		global $mainframe, $option;
		
		$db				=& JFactory::getDBO();
		$currentUser	=& JFactory::getUser();
		$acl			=& JFactory::getACL();
		
		$filter_type		= $mainframe->getUserStateFromRequest( "$option.filter_type_user",		'filter_type', 		0,			'string' );
		$filter_logged		= $mainframe->getUserStateFromRequest( "$option.filter_logged_user",		'filter_logged', 	0,			'int' );
		$search				= $mainframe->getUserStateFromRequest( "$option.search_user",			'search', 			'',			'string' );
		$search				= JString::strtolower( $search );

		$where = array();
		if (isset( $search ) && $search!= '')
		{
			$searchEscaped = $db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where[] = 'a.username LIKE '.$searchEscaped.' OR a.email LIKE '.$searchEscaped.' OR a.name LIKE '.$searchEscaped;
		}
		if ( $filter_type )
		{
			if ( $filter_type == 'Public Frontend' )
			{
				$where[] = ' a.usertype = \'Registered\' OR a.usertype = \'Author\' OR a.usertype = \'Editor\' OR a.usertype = \'Publisher\' ';
			}
			else if ( $filter_type == 'Public Backend' )
			{
				$where[] = 'a.usertype = \'Manager\' OR a.usertype = \'Administrator\' OR a.usertype = \'Super Administrator\' ';
			}
			else
			{
				$where[] = 'a.usertype = LOWER( '.$db->Quote($filter_type).' ) ';
			}
		}
		if ( $filter_logged == 1 )
		{
			$where[] = 's.userid = a.id';
		}
		else if ($filter_logged == 2)
		{
			$where[] = 's.userid IS NULL';
		}
		
		// exclude any child group id's for this user
		$pgids = $acl->get_group_children( $currentUser->get('gid'), 'ARO', 'RECURSE' );

		if (is_array( $pgids ) && count( $pgids ) > 0)
		{
			JArrayHelper::toInteger($pgids);
			$where[] = 'a.gid NOT IN (' . implode( ',', $pgids ) . ')';
		}
		
		$where = ( count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '' );
		
		return $where;
	}
	
	function _build_filter(){
		global $mainframe, $option;
		
		$filter_logged		= $mainframe->getUserStateFromRequest( "$option.filter_logged_user",		'filter_logged', 	0,			'int' );
		
		$filter = '';
		if ($filter_logged == 1 || $filter_logged == 2)
		{
			$filter = ' INNER JOIN #__session AS s ON s.userid = a.id';
		}
		
		return $filter;
	}
	
	function _build_order_by(){
		global $mainframe, $option;
		
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order_user",		'filter_order',		'a.name',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir_user",	'filter_order_Dir',	'',			'word' );
		
		$orderby = ' ORDER BY '. $filter_order .' '. $filter_order_Dir;
		
		return $orderby;
	}
}