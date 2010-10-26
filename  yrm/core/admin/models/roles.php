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
class YRMModelRoles extends JModel
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

	var $role 		= null;

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
		$row = JTable::getInstance('roles', 'Table');
		$row->load($id);
		$this->role = $row;
	}

	function save_resources(){
		$db =& JFactory::getDBO();
		
		$cid = JRequest::getVar('cid');
		
		//remove all current role's resources
		$query = "DELETE FROM `#__yos_resources_manager_resource_role_xref` 
			WHERE `role_id` = " . $this->role->id;
		$db->setQuery($query);
		if(!$db->query()){
			JError::raiseError(500,$db->getErrorMsg());
		}
		
		if (!count($cid)) {
			return true;
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
		
		return true;
	}
	
	/**
	 * Return xml tree
	 *
	 * @param int $root_id
	 * @param int $level number of tab, do not set this value
	 */
	function getTreeXML($root_id = 0, $level = 0){
		global $mainframe;
		
		$return = '';
		
		$db =& JFactory::getDBO();
		
		$role_id = $this->role->id;
		//load the first level
		$query = "SELECT YRMR.*, YRMRRX.id AS 'xid'
			FROM `#__yos_resources_manager_resource` AS YRMR
			LEFT JOIN `#__yos_resources_manager_resource_role_xref` AS YRMRRX
				ON YRMR.id = YRMRRX.resource_id AND YRMRRX.role_id = $role_id
			WHERE `parent_id` = $root_id 
			ORDER BY `name` ASC";
		$db->setQuery($query);
		//var_dump($db->getQuery());die();
		$arr_obj_res = $db->loadObjectList();
		if (!$arr_obj_res) {
			return '';
		}
		foreach ($arr_obj_res as $obj_res){
			for ($i = 0; $i < $level; $i++){ $return .= "\t";	}
			$return .= "<node text=\"".htmlspecialchars($obj_res->name)."\" open=\"true\" ";
			$return .= $obj_res->xid ? "checked=\"true\" " : "checked=\"false\" ";
			$return .= "id=\"$obj_res->id\" ";
			$return .= "published=\"$obj_res->published\" ";
			$return .= "affected=\"$obj_res->affected\" ";
			
			$child = $this->getTreeXML($obj_res->id, $level+1);
			if ($child != '') {
				$return .= ">\n";
				$return .= $child;
				
				for ($i = 0; $i < $level; $i++){ $return .= "\t"; }
				$return .= "</node>\n";
			}
			else {
				$return .= "/>\n";
			}
			
		}
		
		return $return;
	}

	function getData(){
		$db	=& JFactory::getDBO();
		
		if ($this->_data) {
			return $this->_data;
		}
		
		$where = $this->_build_where();
		$orderby = $this->_build_order_by();
		
		$query = 'SELECT r.*'
		. ' FROM #__yos_resources_manager_role AS r'
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
	
	function getTotal(){
		$db				=& JFactory::getDBO();
		
		if ($this->_total) {
			return $this->_total;
		}
		
		$where = $this->_build_where();
		
		$query = 'SELECT COUNT(r.id)'
		. ' FROM #__yos_resources_manager_role AS r'
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
		
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit_role', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart	= $mainframe->getUserStateFromRequest( $option.'.limitstart_role', 'limitstart', 0, 'int' );

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
		
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order_role",		'filter_order',		'r.id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir_role",	'filter_order_Dir',	'',		'word' );
		$search				= $mainframe->getUserStateFromRequest( "$option.search_role",			'search',			'',		'string' );
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
		$search				= $mainframe->getUserStateFromRequest( "$option.search_role",			'search',			'',		'string' );
		$search				= JString::strtolower( $search );

		$where = array();
		if ($search)
		{
			$where[] = 'LOWER(r.name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}

		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		
		return $where;
	}
	
	function _build_order_by(){
		global $mainframe, $option;
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order_role",		'filter_order',		'r.id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir_role",	'filter_order_Dir',	'',		'word' );
		
		$orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir;

		return $orderby;
	}
	
	function getDataUsers(){
		global $mainframe, $option;
		$cid = JRequest::getVar('cid_role');
		$db				=& JFactory::getDBO();
		$currentUser	=& JFactory::getUser();
		$acl			=& JFactory::getACL();
		
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order_role_user",		'filter_order',		'r.name',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir_role_user",	'filter_order_Dir',	'',			'word' );
		$filter_type		= $mainframe->getUserStateFromRequest( "$option.filter_typ_role_usere",		'filter_type', 		0,			'string' );
		$filter_logged		= $mainframe->getUserStateFromRequest( "$option.filter_logged_role_user",		'filter_logged', 	0,			'int' );
		$search				= $mainframe->getUserStateFromRequest( "$option.search_role_user",			'search', 			'',			'string' );
		$search				= JString::strtolower( $search );
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit_role_user', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( $option.'.limitstart_role_user', 'limitstart', 0, 'int' );

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
		$where[] = 'YRMURX.role_id='. intval($cid[0]);
		$orderby = ' ORDER BY '. $filter_order .' '. $filter_order_Dir;
		$where = ( count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '' );

		$query = 'SELECT COUNT(r.id)'
		. ' FROM #__users AS r'
		. ' INNER JOIN `#__yos_resources_manager_user_role_xref` AS YRMURX'
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
			. ' INNER JOIN `#__yos_resources_manager_user_role_xref` AS YRMURX'
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
		$obj_re = new stdClass();
		$obj_re->rows = $rows;
		$obj_re->lists = $lists;
		$obj_re->pagination = $pagination;
		return $obj_re;
		
	}
	
	function getFormUser(){
		$db 		= & JFactory::getDBO();
		$cmd 		= JRequest::getVar('task');
		$cid 		= JRequest::getVar('cid_user');
		$lists 		= array();
		$role_id	= JRequest::getVar('role_id');
		$user_id 	= intval(JRequest::getVar('user_id'));
		$cid_tmp 	= $cid[0];
		if ($user_id > 0) {
			$cid_tmp = $user_id;
		}

		$user = JTable::getInstance('users', 'Table');
		$user->load(intval($cid[0]));
		$query = ' SELECT r.*, r.id as user_id, g.name as groupname '
				.' FROM `#__users` AS r'
				. ' INNER JOIN #__core_acl_aro AS aro ON aro.value = r.id'
				. ' INNER JOIN #__core_acl_groups_aro_map AS gm ON gm.aro_id = aro.id'
				. ' INNER JOIN #__core_acl_aro_groups AS g ON g.id = gm.group_id'
				.' WHERE r.id='.intval($cid_tmp);
		$db->setQuery($query);
		$row 	= $db->loadObject();

		$javascript = 'onchange="document.adminForm.submit();"';
		// get list of Authors for dropdown filter
		$query = 'SELECT u.id as value, u.username as text'
				.' FROM #__users AS u' 
				;
		$db->setQuery($query);
		$users = $db->loadObjectList();
		$lists['users'] = JHTML::_('select.genericlist',  $users, 'user_id', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', $cid_tmp);
		
		$form = new JParameter('', JPATH_COMPONENT.DS.'views'.DS.'roles'.DS.'tmpl'.DS.'user.xml');
		$set_start = '';
		$set_end = '';
		if ($cmd == 'edit_user') {
			$query = 'SELECT * FROM #__yos_resources_manager_user_role_xref WHERE user_id='.intval($cid[0]).' AND role_id='.$role_id;
			$db->setQuery($query);
			$row1 = $db->loadObject();
			$set_start = $row1->start;
			$set_end = $row1->end;
		}
		$form->set('start', JHTML::_('date', $set_start, '%Y-%m-%d %H:%M:%S'));
		$form->set('end', JHTML::_('date', $set_end, '%Y-%m-%d %H:%M:%S'));
		$obj_re = new stdClass();
		$obj_re->form = $form;
		$obj_re->cmd = $cmd;
		$obj_re->row = $row;
		$obj_re->user = $user;
		$obj_re->lists = $lists;
		$obj_re->role_id = $role_id;
		return $obj_re;
	}
	
	function getDataGroups(){
		global $mainframe, $option;

		$db					=& JFactory::getDBO();
		$cid 				= JRequest::getVar('cid_role');
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order_group_role",		'filter_order',		'r.id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir_group_role",	'filter_order_Dir',	'',		'word' );
		$search				= $mainframe->getUserStateFromRequest( "$option.search_group_role",			'search',			'',		'string' );
		$search				= JString::strtolower( $search );

		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit_group_role', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart	= $mainframe->getUserStateFromRequest( $option.'.limitstart_group_role', 'limitstart', 0, 'int' );

		$where = array();
		if ($search)
		{
			$where[] = 'LOWER(r.name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}

		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		$orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir;

		$query = 'SELECT COUNT(r.id)'
		. ' FROM #__yos_resources_manager_group AS r'
		. ' LEFT JOIN #__yos_resources_manager_group_role_xref AS a'
		. ' ON r.group_id = r.id'
		. $where
		;
		$db->setQuery( $query );
		$total = $db->loadResult();

		jimport('joomla.html.pagination');
		$pagination = new JPagination( $total, $limitstart, $limit );

		$query = 'SELECT r.*'
		. ' FROM #__yos_resources_manager_group AS r'
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
						." WHERE role_id = ".intval($cid[0])
						." AND group_id=".$v->id;
			$db->setQuery($query_v);
			if ($db->loadResult() > 0) {
				$check_rows[] = 'checked';
			}else{
				$check_rows[] = '';
			}
			
		}
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;
		$lists['search']= $search;
		
		$obj_re = new stdClass();
		$obj_re->lists = $lists;
		$obj_re->rows = $rows;
		$obj_re->check_rows = $check_rows;
		$obj_re->pagination = $pagination;
		return $obj_re;
	}
	
	function getDataPackages(){
		global $mainframe, $option;
		
		$cid = JRequest::getVar('cid_role');
		$db				=& JFactory::getDBO();

		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order_rp",		'filter_order',		'r.name',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir_rp",	'filter_order_Dir',	'',			'word' );
		$search				= $mainframe->getUserStateFromRequest( "$option.search_rp",			'search', 			'',			'string' );
		$search				= JString::strtolower( $search );
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit_rp', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( $option.'.limitstart_rp', 'limitstart', 0, 'int' );
		$where = array();
		if (isset( $search ) && $search!= '')
		{
			$searchEscaped = $db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where[] = 'r.username LIKE '.$searchEscaped.' OR r.email LIKE '.$searchEscaped.' OR r.name LIKE '.$searchEscaped;
		}
		
		$where[] = 'b.object_id='. intval($cid[0]);
		$orderby = ' ORDER BY '. $filter_order .' '. $filter_order_Dir;
		$where = ( count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '' );

		$query = 'SELECT COUNT(r.id)'
				. ' FROM #__yos_resources_manager_package AS r'
				. ' INNER JOIN #__yos_resources_manager_package_object_xref AS b'
				. ' ON b.package_id=r.id'
				. $where
				. ' AND type="role"'
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
			. ' AND type="role"'
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
		$obj->rows = $rows;
		$obj->lists = $lists;
		$obj->pagination = $pagination;
		return $obj;
	}
	
	function getFormPackage(){
		global $mainframe;
		
		$db 		= & JFactory::getDBO();
		$cmd 		= JRequest::getVar('task');
		$cid 		= JRequest::getVar('cid_package');
		$lists 		= array();
		$role_id	= JRequest::getVar('role_id');
		$package_id	= intval(JRequest::getVar('package_id'));
		$cid_tmp = $cid[0];
		if ($package_id > 0) {
			$cid_tmp = $package_id;
		}

		$user = JTable::getInstance('users', 'Table');
		$user->load(intval($cid[0]));
		$query = ' SELECT r.*'
				.' FROM `#__yos_resources_manager_package` AS r'
				.' WHERE r.id='.intval($cid_tmp)
				.' AND r.published = 1';
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
			.' AND b.type="role"'
			.' AND b.object_id='.$role_id;
		$db->setQuery($query);
		$row_ob 	= $db->loadObject();	
		
		$obj  = new stdClass();
		$obj->row = $row;
		$obj->lists = $lists;
		$obj->row_ob = $row_ob;
		return $obj;
	}
	
	function getMultiUsers($cid){
		global $mainframe, $option;
		$db				=& JFactory::getDBO();
		$currentUser	=& JFactory::getUser();
		$acl			=& JFactory::getACL();
		
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order_role_user",		'filter_order',		'r.name',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir_role_user",	'filter_order_Dir',	'',			'word' );
		$filter_type		= $mainframe->getUserStateFromRequest( "$option.filter_typ_role_user",		'filter_type', 		0,			'string' );
		$filter_logged		= $mainframe->getUserStateFromRequest( "$option.filter_logged_role_user",		'filter_logged', 	0,			'int' );
		$search				= $mainframe->getUserStateFromRequest( "$option.search_role_user",			'search', 			'',			'string' );
		$search				= JString::strtolower( $search );
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit_role_user', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( $option.'.limitstart_role_user', 'limitstart', 0, 'int' );
		
		// avoid mistake filter. ==> assign
		if (($filter_order !='r.id') && ($filter_order !='r.name') && ($filter_order !='r.username')&& ($filter_order !='r.block')&& ($filter_order !='groupname')&& ($filter_order !='r.email')) {
			$filter_order = 'r.name';
		}
		// select users added to table yos_resources_manager_role_xref.
		
		$query_check = 'SELECT * FROM `#__yos_resources_manager_user_role_xref` WHERE role_id='.$cid[0];
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
		if (JRequest::getVar('ru_start')) {
			$start = JRequest::getVar('ru_start');
		}
		if (JRequest::getVar('ru_end')) {
			$end = JRequest::getVar('ru_end');
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
}
