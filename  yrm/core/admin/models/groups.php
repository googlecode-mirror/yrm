<?php
/**
 * @version	$Id: models/roles.php $
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
JTable::addIncludePath( JPATH_COMPONENT.DS.'tables' );
class YRMModelGroups extends JModel
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

	var $group 		= null;
	/**
	 * Constructor
	 *
	 * @since 0.9
	 */

	function __construct()
	{
		parent::__construct();
	}
	
	function load($id = 0){		
		$row = JTable::getInstance('groups', 'Table');
		$row->load($id);
		$this->group = $row;
	}
	
	function getData(){
		$db	=& JFactory::getDBO();
		
		if ($this->_data) {
			return $this->_data;
		}
		
		$where = $this->_build_where();
		$orderby = $this->_build_order_by();
		
		$query = 'SELECT r.*, m.joomla_group_id'
		. ' FROM #__yos_resources_manager_group AS r'
		. ' LEFT JOIN `#__yos_resources_manager_mapping` AS m ON '
		. ' r.id = m.yrm_group_id '
		. $where
		. ' GROUP BY r.id'
		. $orderby
		;
		
		$pagination = $this->getPagination();
		
		$db->setQuery( $query, $pagination->limitstart, $pagination->limit );
		$rows = $db->loadObjectList();
		if ($db->getErrorNum())
		{
			echo $db->stderr();
			return false;
		}
		
		$this->_data = $rows;

		return $this->_data;	
	}
	
	function getTypeGroup(){
		global $mainframe, $option;
		$db 	= & JFactory::getDBO();
		$filter_type	= $mainframe->getUserStateFromRequest( "$option.filter_type",		'filter_type', 		0,			'string' );
		// get list of Groups for dropdown filter
		$query = 'SELECT id AS value, name AS text'
			. ' FROM #__core_acl_aro_groups'
			. ' WHERE name != "ROOT"'
			. ' AND name != "USERS"'
			. ' ORDER BY lft'
		;
		$db->setQuery( $query );
		$types[] 		= JHTML::_('select.option',  '0', '- '. JText::_( 'SELECT JOOMLA GROUP' ) .' -' );
		foreach( $db->loadObjectList() as $obj )
		{
			$types[] = JHTML::_('select.option',  $obj->value, JText::_( $obj->text ) );
		}
		$lists['type'] 	= JHTML::_('select.genericlist',   $types, 'filter_type', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', "$filter_type" );
		return $lists['type'];
	}
	function getTotal(){
		$db				=& JFactory::getDBO();
		
		if ($this->_total) {
			return $this->_total;
		}
		
		$where = $this->_build_where();
		
		$query = 'SELECT COUNT(r.id)'
		. ' FROM `#__yos_resources_manager_group` AS r'
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
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit_group', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart	= $mainframe->getUserStateFromRequest( $option.'.limitstart_group', 'limitstart', 0, 'int' );

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
		
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order_group",		'filter_order',		'r.id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir_group",	'filter_order_Dir',	'',		'word' );
		$search				= $mainframe->getUserStateFromRequest( "$option.search_group",			'search',			'',		'string' );
		$search				= JString::strtolower( $search );

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
		$filter_type		= $mainframe->getUserStateFromRequest( "$option.filter_type",		'filter_type', 		0,			'int' );
		$search				= $mainframe->getUserStateFromRequest( "$option.search_group",			'search',			'',		'string' );
		$search				= JString::strtolower( $search );
	
		$where = array();
		if ( $filter_type )
		{
			$where[] = ' m.joomla_group_id = '.$filter_type;
		}
		if ($search)
		{
			$where[] = 'LOWER(r.name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}

		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		
		return $where;
	}
	
	function _build_order_by(){
		global $mainframe, $option;
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order_group",		'filter_order',		'r.id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir_group",	'filter_order_Dir',	'',		'word' );
		
		$orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir;

		return $orderby;
	}
	
	function save_resources(){
		$db =& JFactory::getDBO();
		
		$cid = JRequest::getVar('cid');
		
		if (!is_array($cid)) {
			return 0;
		}
		
		//remove all current role's resources
		$query = "DELETE FROM `#__yos_resources_manager_resource_role_xref` 
			WHERE `role_id` = " . $this->role->id;
		$db->setQuery($query);
		if(!$db->query()){
			JError::raiseError(500,$db->getErrorMsg());
		}
		foreach ($cid as $res_id){
			$query = "INSERT INTO `#__yos_resources_manager_resource_role_xref` SET 
				`role_id` = " . $this->role->id .",
				`resource_id` = " . intval($res_id) ;
			$db->setQuery($query);
			if(!$db->query()){
				JError::raiseError(500,$db->getErrorMsg());
			}
		}
		
		return 1;
	}
	
	function getDataRoles(){
		global $mainframe, $option;
		$db					=& JFactory::getDBO();
		$cid 				= JRequest::getVar('cid_group');
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order_gr",		'filter_order',		'r.id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir_gr",	'filter_order_Dir',	'',		'word' );
		$search				= $mainframe->getUserStateFromRequest( "$option.search_gr",			'search',			'',		'string' );
		$search				= JString::strtolower( $search );

		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit_gr', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart	= $mainframe->getUserStateFromRequest( $option.'.limitstart_gr', 'limitstart', 0, 'int' );

		$where = array();
		if ($search)
		{
			$where[] = 'LOWER(r.name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}

		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		$orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir;

		$query = 'SELECT COUNT(r.id)'
		. ' FROM #__yos_resources_manager_role AS r'
		. ' LEFT JOIN #__yos_resources_manager_group_role_xref AS a'
		. ' ON a.group_id = r.id'
		. $where
		;
		$db->setQuery( $query );
		$total = $db->loadResult();

		jimport('joomla.html.pagination');
		$pagination = new JPagination( $total, $limitstart, $limit );

		$query = 'SELECT r.*'
		. ' FROM #__yos_resources_manager_role AS r'
		. ' LEFT JOIN #__yos_resources_manager_group_role_xref AS a'
		. ' ON a.group_id = r.id'
		. $where
		. ' GROUP BY r.id'
		. $orderby
		;
		
		$check_rows = array();
		
		$db->setQuery( $query, $pagination->limitstart, $pagination->limit );
		$rows = $db->loadObjectList();
		if ($db->getErrorNum())
		{
			echo $db->stderr();
			return false;
		}
		foreach ($rows as $v) {
			$query_v = "SELECT COUNT(*) FROM #__yos_resources_manager_group_role_xref"
						." WHERE group_id = ".intval($cid[0])
						." AND role_id=".$v->id;
			$db->setQuery($query_v);
			if ($db->loadResult() > 0) {
				$check_rows[] = 'checked';
			}else{
				$check_rows[] = '';
			}
			
		}
		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;

		// search filter
		$lists['search']= $search;
		$obj = new stdClass();
		$obj->lists = $lists;
		$obj->rows = $rows;
		$obj->pagination = $pagination;
		$obj->check_rows = $check_rows;
		return $obj;
	}
	
	function getDataPackages(){
		global $mainframe, $option;
		
		$cid = JRequest::getVar('cid_group');
		$db				=& JFactory::getDBO();

		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order_gp",		'filter_order',		'r.name',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir_gp",	'filter_order_Dir',	'',			'word' );
		$search				= $mainframe->getUserStateFromRequest( "$option.search_gp",			'search', 			'',			'string' );
		$search				= JString::strtolower( $search );
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit_gp', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( $option.'.limitstart_gp', 'limitstart', 0, 'int' );

		$where = array();
		if (isset( $search ) && $search!= '')
		{
			$searchEscaped = $db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where[] = 'r.name LIKE '.$searchEscaped;
		}
		
		$where[] = 'b.object_id='. intval($cid[0]);
		$orderby = ' ORDER BY '. $filter_order .' '. $filter_order_Dir;
		$where = ( count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '' );

		$query = 'SELECT COUNT(r.id)'
				. ' FROM #__yos_resources_manager_package AS r'
				. ' INNER JOIN #__yos_resources_manager_package_object_xref AS b'
				. ' ON b.package_id=r.id'
				. $where
				. ' AND type="group"'
				. ' AND r.published = 1'
			;
		$db->setQuery( $query );
		$total = $db->loadResult();
		jimport('joomla.html.pagination');
		$pagination = new JPagination( $total, $limitstart, $limit );

		$query = 'SELECT r.*'
			. ' FROM #__yos_resources_manager_package AS r'
			. ' INNER JOIN #__yos_resources_manager_package_object_xref AS b'
			. ' ON b.package_id=r.id'
			. $where
			. ' AND type="group"'
			. ' AND r.published = 1'
			. ' GROUP BY r.id'
			. $orderby
		;
		$db->setQuery( $query, $pagination->limitstart, $pagination->limit );
		$rows = $db->loadObjectList();
		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;
		// search filter
		$lists['search']= $search;
		
		$obj = new stdClass();
		$obj->lists = $lists;
		$obj->rows = $rows;
		$obj->pagination = $pagination;
		return $obj;
	}
	
	function getFormPackage(){
		global $mainframe;
		
		$db 		= & JFactory::getDBO();
		$cid 		= JRequest::getVar('cid_package');
		$lists 		= array();
		$group_id	= JRequest::getVar('group_id');
		$package_id	= intval(JRequest::getVar('package_id'));
		$cid_tmp = $cid[0];
		if ($package_id > 0) {
			$cid_tmp = $package_id;
		}

		$user = JTable::getInstance('users', 'Table');
		$user->load(intval($cid[0]));
		$query = ' SELECT r.*'
				.' FROM `#__yos_resources_manager_package` AS r'
				.' WHERE r.id='.intval($cid_tmp);
		$db->setQuery($query);
		$row 	= $db->loadObject();
		$javascript = 'onchange="document.adminForm.submit();"';
		// get list of Authors for dropdown filter
		$query = 'SELECT u.id as value, u.name as text'
				.' FROM #__yos_resources_manager_package AS u' 
				.' WHERE u.published = 1'
				;
		$db->setQuery($query);
		$packages = $db->loadObjectList();
		$lists['packages'] = JHTML::_('select.genericlist',  $packages, 'package_id', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', $cid_tmp);
	
		$query = ' SELECT b.*'
			.' FROM  #__yos_resources_manager_package_object_xref AS b'
			.' WHERE b.package_id='.intval($cid[0])
			.' AND b.type="group"'
			.' AND b.object_id='.$group_id;
		$db->setQuery($query);
		$row_ob 		= $db->loadObject();
		$obj			= new stdClass();
		$obj->row 		= $row;
		$obj->row_ob 	= $row_ob;
		$obj->user 		= $user;
		$obj->lists 	= $lists;
		return $obj;
	}
	
	function getUsers($cid){
		global $mainframe, $option;
		$db				=& JFactory::getDBO();
		$currentUser	=& JFactory::getUser();
		$acl			=& JFactory::getACL();
		
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order_group_user",		'filter_order',		'r.name',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir_group_user",	'filter_order_Dir',	'',			'word' );
		$filter_type		= $mainframe->getUserStateFromRequest( "$option.filter_typ_group_user",		'filter_type', 		0,			'string' );
		$filter_logged		= $mainframe->getUserStateFromRequest( "$option.filter_logged_group_user",		'filter_logged', 	0,			'int' );
		$search				= $mainframe->getUserStateFromRequest( "$option.search_group_user",			'search', 			'',			'string' );
		$search				= JString::strtolower( $search );
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit_group_user', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( $option.'.limitstart_group_user', 'limitstart', 0, 'int' );

		if (($filter_order !='r.id') && ($filter_order !='r.name') && ($filter_order !='r.username')&& ($filter_order !='r.block')&& ($filter_order !='groupname')&& ($filter_order !='r.email')) {
			$filter_order = 'r.name';
		}
		$where = array();
		if (isset( $search ) && $search!= '')
		{
			$searchEscaped = $db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where[] = 'r.username LIKE '.$searchEscaped.' OR r.email LIKE '.$searchEscaped.' OR r.name LIKE '.$searchEscaped;
		}
		if ( $filter_type )
		{
			if ( $filter_type == 'Public Frontend' )
			{
				$where[] = ' r.usertype = \'Registered\' OR r.usertype = \'Author\' OR r.usertype = \'Editor\' OR r.usertype = \'Publisher\' ';
			}
			else if ( $filter_type == 'Public Backend' )
			{
				$where[] = 'r.usertype = \'Manager\' OR r.usertype = \'Administrator\' OR r.usertype = \'Super Administrator\' ';
			}
			else
			{
				$where[] = 'r.usertype = LOWER( '.$db->Quote($filter_type).' ) ';
			}
		}
		if ( $filter_logged == 1 )
		{
			$where[] = 's.userid = r.id';
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
			$where[] = 'r.gid NOT IN (' . implode( ',', $pgids ) . ')';
		}
		$filter = '';
		if ($filter_logged == 1 || $filter_logged == 2)
		{
			$filter = ' INNER JOIN #__session AS s ON s.userid = r.id';
		}
		$where[] = 'YRMURX.group_id='. intval($cid[0]);
		$orderby = ' ORDER BY '. $filter_order .' '. $filter_order_Dir;
		$where = ( count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '' );

		$query = 'SELECT COUNT(r.id)'
		. ' FROM #__users AS r'
		. ' INNER JOIN `#__yos_resources_manager_user_group_xref` AS YRMURX'
		. ' ON YRMURX.user_id=r.id'
		. $filter
		. $where
		;
		$db->setQuery( $query );
		$total = $db->loadResult();
		jimport('joomla.html.pagination');
		$pagination = new JPagination( $total, $limitstart, $limit );

		$query = 'SELECT r.*, g.name AS groupname, YRMURX.start, YRMURX.end'
			. ' FROM #__users AS r'
			. ' INNER JOIN `#__yos_resources_manager_user_group_xref` AS YRMURX'
			. ' ON YRMURX.user_id=r.id'
			. ' INNER JOIN #__core_acl_aro AS aro ON aro.value = r.id'
			. ' INNER JOIN #__core_acl_groups_aro_map AS gm ON gm.aro_id = aro.id'
			. ' INNER JOIN #__core_acl_aro_groups AS g ON g.id = gm.group_id'
			. $filter
			. $where
			. ' GROUP BY r.id'
			. $orderby
		;
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

		// get list of Groups for dropdown filter
		$query = 'SELECT name AS value, name AS text'
			. ' FROM #__core_acl_aro_groups'
			. ' WHERE name != "ROOT"'
			. ' AND name != "USERS"'
		;
		$db->setQuery( $query );
		$types[] 		= JHTML::_('select.option',  '0', '- '. JText::_( 'SELECT_GROUP' ) .' -' );
		foreach( $db->loadObjectList() as $obj )
		{
			$types[] = JHTML::_('select.option',  $obj->value, JText::_( $obj->text ) );
		}
		$lists['type'] 	= JHTML::_('select.genericlist',   $types, 'filter_type', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', "$filter_type" );

		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;
		// search filter
		$lists['search']= $search;
		// assign
		$res = new stdClass();
		$res->lists = $lists;
		$res->pagination = $pagination;	
		$res->rows = $rows;
		return $res;
	}
	
	function getMultiUsers($cid){
		global $mainframe, $option;
		$db				=& JFactory::getDBO();
		$currentUser	=& JFactory::getUser();
		$acl			=& JFactory::getACL();
		
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order_group_user",		'filter_order',		'r.name',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir_group_user",	'filter_order_Dir',	'',			'word' );
		$filter_type		= $mainframe->getUserStateFromRequest( "$option.filter_typ_group_user",		'filter_type', 		0,			'string' );
		$filter_logged		= $mainframe->getUserStateFromRequest( "$option.filter_logged_group_user",		'filter_logged', 	0,			'int' );
		$search				= $mainframe->getUserStateFromRequest( "$option.search_group_user",			'search', 			'',			'string' );
		$search				= JString::strtolower( $search );
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit_group_user', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( $option.'.limitstart_group_user', 'limitstart', 0, 'int' );
		
		// avoid mistake filter. ==> assign
		if (($filter_order !='r.id') && ($filter_order !='r.name') && ($filter_order !='r.username')&& ($filter_order !='r.block')&& ($filter_order !='groupname')&& ($filter_order !='r.email')) {
			$filter_order = 'r.name';
		}
		// select users added to table yos_resources_manager_group_xref.
		
		$query_check = 'SELECT * FROM `#__yos_resources_manager_user_group_xref` WHERE group_id='.$cid[0];
		$db->setQuery($query_check);
		$c_users = $db->loadObjectList();
		// append to where string.
		$and_tmp = array();
		
		$and = '';
		if (count($c_users)){
			foreach ($c_users as $c_user){
				$and_tmp[] = $c_user->user_id;
			}
			$and .= ' AND r.`id` NOT IN('.implode(',', $and_tmp).')';
		}
		
		$where = array();
		if (isset( $search ) && $search!= '')
		{
			$searchEscaped = $db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where[] = 'r.username LIKE '.$searchEscaped.' OR r.email LIKE '.$searchEscaped.' OR r.name LIKE '.$searchEscaped;
		}
		if ( $filter_type )
		{
			if ( $filter_type == 'Public Frontend' )
			{
				$where[] = ' r.usertype = \'Registered\' OR r.usertype = \'Author\' OR r.usertype = \'Editor\' OR r.usertype = \'Publisher\' ';
			}
			else if ( $filter_type == 'Public Backend' )
			{
				$where[] = 'r.usertype = \'Manager\' OR r.usertype = \'Administrator\' OR r.usertype = \'Super Administrator\' ';
			}
			else
			{
				$where[] = 'r.usertype = LOWER( '.$db->Quote($filter_type).' ) ';
			}
		}
		if ( $filter_logged == 1 )
		{
			$where[] = 's.userid = r.id';
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
			$where[] = 'r.gid NOT IN (' . implode( ',', $pgids ) . ')';
		}
		$filter = '';
		if ($filter_logged == 1 || $filter_logged == 2)
		{
			$filter = ' INNER JOIN #__session AS s ON s.userid = r.id';
		}
		$orderby = ' ORDER BY '. $filter_order .' '. $filter_order_Dir;
		$where = ( count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '' );

		$query = 'SELECT COUNT(r.id)'
		. ' FROM #__users AS r'
		. $filter
		. $where.$and;
		;
		$db->setQuery( $query );
		$total = $db->loadResult();
		jimport('joomla.html.pagination');
		$pagination = new JPagination( $total, $limitstart, $limit );

		$query = 'SELECT r.*, g.name AS groupname'
			. ' FROM #__users AS r'
			. ' INNER JOIN #__core_acl_aro AS aro ON aro.value = r.id'
			. ' INNER JOIN #__core_acl_groups_aro_map AS gm ON gm.aro_id = aro.id'
			. ' INNER JOIN #__core_acl_aro_groups AS g ON g.id = gm.group_id'
			. $filter
			. $where. $and
			. ' GROUP BY r.id'
			. $orderby
		;
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

		// get list of Groups for dropdown filter
		$query = 'SELECT name AS value, name AS text'
			. ' FROM #__core_acl_aro_groups'
			. ' WHERE name != "ROOT"'
			. ' AND name != "USERS"'
		;
		$db->setQuery( $query );
		$types[] 		= JHTML::_('select.option',  '0', '- '. JText::_( 'SELECT_GROUP' ) .' -' );
		foreach( $db->loadObjectList() as $obj )
		{
			$types[] = JHTML::_('select.option',  $obj->value, JText::_( $obj->text ) );
		}
		$lists['type'] 	= JHTML::_('select.genericlist',   $types, 'filter_type', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', "$filter_type" );

		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;
		// search filter
		$lists['search']= $search;
		
		$now = JFactory::getDate();
		$nowsql = $now->toMySQL();
		
		$start = $nowsql;
		$end = '0000-00-00 00:00:00';
		if (JRequest::getVar('gu_start')) {
			$start = JRequest::getVar('gu_start');
		}
		if (JRequest::getVar('gu_end')) {
			$end = JRequest::getVar('gu_end');
		}
		// assign
		$res = new stdClass();
		$res->lists = $lists;
		$res->pagination = $pagination;	
		$res->rows = $rows;
		$res->start = $start;
		$res->end = $end;
		
		return $res;
	}
	
	function list_gid($yrm_group_id = 0 )
	{
		$yrm_group_id = intval($yrm_group_id);
		$db 		= JFactory::getDBO();
		$acl		=& JFactory::getACL();
		if ($yrm_group_id) {
			$query = ' SELECT `joomla_group_id` FROM `#__yos_resources_manager_mapping` WHERE `yrm_group_id`='.$yrm_group_id;
			$db->setQuery($query);
			$arr_gid =  $db->loadResultArray();
		}else {
			$arr_gid = null;
		}
		$gtree = $acl->get_group_children_tree( null, 'USERS', false );
		$lists['gid'] 	= JHTML::_('select.genericlist',   $gtree, 'gid[]', 'size="10" multiple="true"', 'value', 'text', $arr_gid );
		return $lists['gid'];
	}
	
	function store_mapping($arr_gid, $yrm_gid, $time){
		$db = &JFactory::getDBO();
		
		$query = 'DELETE FROM `#__yos_resources_manager_mapping` WHERE `yrm_group_id`='.$yrm_gid;
		$db->setQuery($query);
		if(!$db->query()){
			JError::raiseError(500,$db->getErrorMsg());
		}
		$values = array();
		$query_insert = ' INSERT INTO `#__yos_resources_manager_mapping`(`joomla_group_id`, `yrm_group_id`) VALUES ';
		foreach ($arr_gid as $gid) {
			$values[] = '('.$gid.', '.$yrm_gid.')';
		}
		$append = implode(',', $values);
		$query_insert .= $append;
		$db->setQuery($query_insert);
		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg());		
		}
	}
}