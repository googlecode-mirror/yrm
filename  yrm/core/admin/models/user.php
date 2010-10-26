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
class YRMModelUser extends JModel
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
	
	var $user = null;
	
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
		$user =& JFactory::getUser($id);
		
		$this->user = $user;
	}
	
	function save_resources(){
		$db =& JFactory::getDBO();
		
		$cid = JRequest::getVar('cid');
		$times_access = JRequest::getInt('times_access', -1);
		
		$nowdate = JFactory::getDate();
		$str_now = JHTML::_('date', $nowdate->toMySQL(), '%Y-%m-%d %H:%M:%S');
		$str_nulldate = JDatabase::getNullDate();
		$start_date = JRequest::getVar('start_date', $str_now);
		$end_date = JRequest::getVar('end_date', $str_nulldate);
		$keep_original = JRequest::getInt('keep_original', 1);
		
		/*if (!is_array($cid)) {
			return 0;
		}*/
		
		//overwrite original
		if ($keep_original == 0) {
			//remove all current user's resources
			$query = "DELETE FROM `#__yos_resources_manager_user_resource_xref` 
				WHERE `user_id` = " . $this->user->id;
			$db->setQuery($query);
			if(!$db->query()){
				JError::raiseError(500,$db->getErrorMsg());
			}
			foreach ($cid as $res_id){
				$query = "INSERT INTO `#__yos_resources_manager_user_resource_xref` SET 
					`user_id` = " . $this->user->id . ', 
					`resource_id` = ' . intval($res_id) . ", 
					`times_access` = $times_access,
					`start` = '$start_date', 
					`end` = '$end_date'";
				$db->setQuery($query);
				if(!$db->query()){
					JError::raiseError(500,$db->getErrorMsg());
				}
			}
			
			return 1;
		}
		
		//keep original
		//select all resource
		$query = "SELECT `id` FROM `#__yos_resources_manager_resource`";
		$db->setQuery($query);
		$arr_res = $db->loadResultArray();
		
		if (!count($arr_res)) {
			return 0;
		}
			
		foreach ($arr_res as $res_id){
			if (!in_array($res_id, $cid)) {
				//remove this resource from user
				$query = "DELETE FROM `#__yos_resources_manager_user_resource_xref` 
					WHERE `user_id` = " . $this->user->id . " AND `resource_id` = $res_id";
				$db->setQuery($query);
				if(!$db->query()){
					JError::raiseError(500,$db->getErrorMsg());
				}
			}
			else {
				//check is existing in database
				$query = "SELECT COUNT(id) FROM `#__yos_resources_manager_user_resource_xref`
					WHERE `user_id` = " . $this->user->id . " AND resource_id = $res_id";
				$db->setQuery($query);
				if (!$db->loadResult()) {
					$query = "INSERT INTO `#__yos_resources_manager_user_resource_xref` SET 
						`user_id` = " . $this->user->id . ', 
						`resource_id` = ' . intval($res_id) . ", 
						`times_access` = $times_access,
						`start` = '$start_date', 
						`end` = '$end_date'";
					$db->setQuery($query);
					if(!$db->query()){
						JError::raiseError(500,$db->getErrorMsg());
					}
				}
			}
		}
		
		return 1;
		
	}
	
	function getResource($res_id){
		$db =& JFactory::getDBO();
		
		$query = "SELECT * FROM `#__yos_resources_manager_user_resource_xref`
			WHERE `user_id` = " . $this->user->id . " AND resource_id = $res_id";
		$db->setQuery($query);
		return $db->loadObject();
	}
	
	function getResource_banned($res_id){
		$db =& JFactory::getDBO();
		
		$query = "SELECT * FROM `#__yos_resources_manager_user_resource_banned`
			WHERE `user_id` = " . $this->user->id . " AND resource_id = $res_id";
		$db->setQuery($query);
		return $db->loadObject();
	}
	
	function save_rs_form(){
		$db =& JFactory::getDBO();
		
		$user_id = JRequest::getInt('user_id');
		$res_id = JRequest::getInt('rid');
		$times_access = JRequest::getInt('times_access');
		$start_date = JRequest::getVar('start_date', $db->getNullDate());
		$end_date = JRequest::getVar('end_date', $db->getNullDate());
		
		$query = "UPDATE `#__yos_resources_manager_user_resource_xref` SET 
			`times_access` = $times_access,
			`start` = '$start_date', 
			`end` = '$end_date'
			WHERE `user_id` = $user_id AND `resource_id` = $res_id";
		$db->setQuery($query);
		if(!$db->query()){
			JError::raiseError(500,$db->getErrorMsg());
		}
		
		return $db->getAffectedRows();
	}
	
	function save_rsb_form(){
		$db =& JFactory::getDBO();
		$user_id = JRequest::getInt('user_id');
		$res_id = JRequest::getInt('rid');
		$description = JRequest::getVar('description');
		$redirect_url = JRequest::getVar('redirect_url');
		$redirect_message = JRequest::getVar('redirect_message');
		$start_date = JRequest::getVar('start_date', $db->getNullDate());
		$end_date = JRequest::getVar('end_date', $db->getNullDate());
		
		$query = "UPDATE `#__yos_resources_manager_user_resource_banned` SET 
			`description` = '".$description."',
			`redirect_url` = '".$redirect_url."',
			`redirect_message` = '".$redirect_message."',
			`start` = '$start_date', 
			`end` = '$end_date'
			WHERE `user_id` = $user_id AND `resource_id` = $res_id";
		$db->setQuery($query);
		if(!$db->query()){
			JError::raiseError(500,$db->getErrorMsg());
		}
		
		return $db->getAffectedRows();
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
		
		$user_id = $this->user->id;
		
		//load the first level
		$query = "SELECT YRMR.*, YRMURX.id AS 'xid', YRMURX.times_access AS 'xtimes_access', YRMURX.start AS 'xstart', YRMURX.end AS 'xend'
			FROM `#__yos_resources_manager_resource` AS YRMR
			LEFT JOIN `#__yos_resources_manager_user_resource_xref` AS YRMURX
				ON YRMR.id = YRMURX.resource_id AND YRMURX.user_id = $user_id
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

            if($obj_res->xid){
                $return .= "elink=\"#\" ";
                ob_start();
                ?>
                <table>
                <tr>
                    <td><?php echo JText::_('USER_RESOURCES_TIMES_ACCESS'); ?></td>
                    <td><?php echo $obj_res->xtimes_access; ?></td>
                </tr>
                <tr>
                    <td><?php echo JText::_('USER_RESOURCES_START_DATE'); ?></td>
                    <td><?php echo $obj_res->xstart; ?></td>
                </tr>
                <tr>
                    <td><?php echo JText::_('USER_RESOURCES_END_DATE'); ?></td>
                    <td><?php echo $obj_res->xend; ?></td>
                </tr>
                </table>
                <?php
                $etitle = htmlspecialchars(ob_get_contents());
                ob_end_clean();
                $return .= "etitle=\"$etitle\" ";
            }
			
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
	
	function getRSBTreeXML($root_id = 0, $level = 0){
		global $mainframe;
		
		$return = '';
		
		$db =& JFactory::getDBO();
		
		$user_id = $this->user->id;
		
		//load the first level
		$query = "SELECT YRMR.*, YRMURB.id AS 'xid', YRMURB.start AS 'xstart', YRMURB.end AS 'xend', 
			YRMURB.description as description, YRMURB.redirect_url as redirect_url, YRMURB.redirect_message as redirect_message
			FROM `#__yos_resources_manager_resource` AS YRMR
			LEFT JOIN `#__yos_resources_manager_user_resource_banned` AS YRMURB
				ON YRMR.id = YRMURB.resource_id AND YRMURB.user_id = $user_id
			WHERE `parent_id` = $root_id 
			ORDER BY `name` ASC";
		$db->setQuery($query);
		$arr_obj_res = $db->loadObjectList();
		if (!$arr_obj_res) {
			return '';
		}
		foreach ($arr_obj_res as $obj_res){
			for ($i = 0; $i < $level; $i++){ $return .= "\t";	}
			$return .= "<node text=\"$obj_res->name\" open=\"true\" ";
			$return .= $obj_res->xid ? "checked=\"true\" " : "checked=\"false\" ";
			$return .= "id=\"$obj_res->id\" ";
			$return .= "published=\"$obj_res->published\" ";
			$return .= "affected=\"$obj_res->affected\" ";

            if($obj_res->xid){
                $return .= "elink=\"#\" ";
                ob_start();
                ?>
                <table>
                <tr>
                    <td><?php echo JText::_('DESCRIPTION'); ?></td>
                    <td><?php echo $obj_res->description; ?></td>
                </tr>
                <tr>
                    <td><?php echo JText::_('REDIRECT_URL'); ?></td>
                    <td><?php echo $obj_res->redirect_url; ?></td>
                </tr>
                <tr>
                    <td><?php echo JText::_('REDIRECT_MESSAGE'); ?></td>
                    <td><?php echo $obj_res->redirect_message; ?></td>
                </tr>
                <tr>
                    <td><?php echo JText::_('USER_RESOURCES_START_DATE'); ?></td>
                    <td><?php echo $obj_res->xstart; ?></td>
                </tr>
                <tr>
                    <td><?php echo JText::_('USER_RESOURCES_END_DATE'); ?></td>
                    <td><?php echo $obj_res->xend; ?></td>
                </tr>
                </table>
                <?php
                $etitle = htmlspecialchars(ob_get_contents());
                ob_end_clean();
                $return .= "etitle=\"$etitle\" ";
            }
			
			$child = $this->getRSBTreeXML($obj_res->id, $level+1);
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
	
	function getDataGroups(){
		global $mainframe, $option;

		$db					=& JFactory::getDBO();
		$cid 				= JRequest::getVar('cid_user');
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order_group_user",		'filter_order',		'r.id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir_group_user",	'filter_order_Dir',	'',		'word' );
		$search				= $mainframe->getUserStateFromRequest( "$option.search_group_user",			'search',			'',		'string' );
		$search				= JString::strtolower( $search );

		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit_group_user', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart	= $mainframe->getUserStateFromRequest( $option.'.limitstart_group_user', 'limitstart', 0, 'int' );

		$where = array();
		if ($search)
		{
			$where[] = 'LOWER(r.name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}
		if ($filter_order !='r.id') {
			$filter_order = 'r.name';
		}
		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		$orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir;

		$query = 'SELECT COUNT(r.id)'
		. ' FROM #__yos_resources_manager_group AS r'
		. ' LEFT JOIN #__yos_resources_manager_user_group_xref AS a'
		. ' ON a.group_id = r.id'
		. $where
		;
		$db->setQuery( $query );
		$total = $db->loadResult();

		jimport('joomla.html.pagination');
		$pagination = new JPagination( $total, $limitstart, $limit );

		$query = 'SELECT r.*, a.start, a.end'
		. ' FROM #__yos_resources_manager_group AS r'
		. ' LEFT JOIN #__yos_resources_manager_user_group_xref AS a'
		. ' ON a.group_id = r.id AND user_id = '.intval($cid[0])
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
			$query_v = "SELECT COUNT(*) FROM #__yos_resources_manager_user_group_xref"
						." WHERE user_id = ".intval($cid[0])
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
	
	function save_groups(){
		$db = &JFactory::getDBO();
		$user_id = JRequest::getVar('user_id');
		$cids = JRequest::getVar('cid_group');
		$group_start = JRequest::getVar('start');
		$group_end = JRequest::getVar('end');
		$query = 'DELETE FROM `#__yos_resources_manager_user_group_xref` WHERE user_id='.$user_id;
		$db->setQuery($query);
		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg() );
		}
		if (count($cids)) {
			$value = array();
			foreach ($cids as $cid) {
				$tmp_group_start = $group_start[$cid];
				$tmp_group_end = $group_end[$cid];
				$value[] =' ('.$cid.', '.$user_id.', "'.$tmp_group_start.'", "'.$tmp_group_end.'")';
			}
			$value = ' VALUES '. implode( ', ', $value );
			$query_insert = 'INSERT INTO `#__yos_resources_manager_user_group_xref`(`group_id`, `user_id`, `start`, `end`) ';
			$query_insert.=$value;
			$db->setQuery($query_insert);
			if (!$db->query()) {
				JError::raiseError(500, $db->getErrorMsg());
			}
		}
	}
	
	function getDataRoles(){
		global $mainframe, $option;

		$db					=& JFactory::getDBO();
		$cid 				= JRequest::getVar('cid_user');
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order_role_user",		'filter_order',		'r.id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir_role_user",	'filter_order_Dir',	'',		'word' );
		$search				= $mainframe->getUserStateFromRequest( "$option.search_role_user",			'search',			'',		'string' );
		$search				= JString::strtolower( $search );

		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit_role_user', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart	= $mainframe->getUserStateFromRequest( $option.'.limitstart_role_user', 'limitstart', 0, 'int' );

		$where = array();
		if ($search)
		{
			$where[] = 'LOWER(r.name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}
		if ($filter_order !='r.id') {
			$filter_order = 'r.name';
		}
		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		$orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir;

		$query = 'SELECT COUNT(r.id)'
		. ' FROM #__yos_resources_manager_role AS r'
		. $where
		;
		$db->setQuery( $query );
		$total = $db->loadResult();

		jimport('joomla.html.pagination');
		$pagination = new JPagination( $total, $limitstart, $limit );

		$query = 'SELECT r.*, a.start, a.end'
		. ' FROM #__yos_resources_manager_role AS r'
		. ' LEFT JOIN #__yos_resources_manager_user_role_xref AS a'
		. ' ON a.role_id = r.id AND user_id = '.intval($cid[0])
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
			$query_v = "SELECT COUNT(*) FROM #__yos_resources_manager_user_role_xref"
						." WHERE user_id = ".intval($cid[0])
						." AND role_id=".$v->id;
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
	
	function save_roles(){
		$db 		= &JFactory::getDBO();
		$user_id 	= JRequest::getVar('user_id');
		$cids 		= JRequest::getVar('cid_role');
		$role_start = JRequest::getVar('start');
		$role_end 	= JRequest::getVar('end');
		$query 		= 'DELETE FROM `#__yos_resources_manager_user_role_xref` WHERE user_id='.$user_id;
		$db->setQuery($query);
		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg() );
		}
		if (count($cids)) {
			$value 		= array();
			foreach ($cids as $cid) {
				$tmp_role_start = $role_start[$cid];
				$tmp_role_end 	= $role_end[$cid];
				$value[]		=' ('.$cid.', '.$user_id.', "'.$tmp_role_start.'", "'.$tmp_role_end.'")';
			}
			$value 			= ' VALUES '. implode( ', ', $value );
			$query_insert 	= 'INSERT INTO `#__yos_resources_manager_user_role_xref`(`role_id`, `user_id`, `start`, `end`) ';
			$query_insert	.=$value;
			$db->setQuery($query_insert);
			if (!$db->query()) {
				JError::raiseError(500, $db->getErrorMsg());
			}
		}
	}
	
	function save_resources_banned(){
		$db =& JFactory::getDBO();
		$cid = JRequest::getVar('cid');
		$description = JRequest::getVar('description');
		$redirect_message = JRequest::getVar('redirect_message');
		$redirect_url = JRequest::getVar('redirect_url');
		$nowdate = JFactory::getDate();
		$str_now = JHTML::_('date', $nowdate->toMySQL(), '%Y-%m-%d %H:%M:%S');
		$str_nulldate = $db->getNullDate();
		$start_date = JRequest::getVar('start_date', $str_now);
		$end_date = JRequest::getVar('end_date', $str_nulldate);
		$keep_original = JRequest::getInt('keep_original', 1);
		
		/*if (!is_array($cid)) {
			return 0;
		}*/
		
		//overwrite original
		if ($keep_original == 0) {
			//remove all current user's resources
			$query = "DELETE FROM `#__yos_resources_manager_user_resource_banned` 
				WHERE `user_id` = " . $this->user->id;
			$db->setQuery($query);
			if(!$db->query()){
				JError::raiseError(500,$db->getErrorMsg());
			}
			foreach ($cid as $res_id){
				$query = "INSERT INTO `#__yos_resources_manager_user_resource_banned` SET 
						`user_id` = " . $this->user->id . ', 
						`resource_id` = ' . intval($res_id) . ", 
						`description` = '".$description."',
						`redirect_url` = '".$redirect_url."',
						`redirect_message` = '".$redirect_message."',
						`start` = '$start_date', 
						`end` = '$end_date'";
				$db->setQuery($query);
				if(!$db->query()){
					JError::raiseError(500,$db->getErrorMsg());
				}
			}
			
			return 1;
		}
		
		//keep original
		//select all resource
		$query = "SELECT `id` FROM `#__yos_resources_manager_resource`";
		$db->setQuery($query);
		$arr_res = $db->loadResultArray();
		
		if (!count($arr_res)) {
			return 0;
		}
			
		foreach ($arr_res as $res_id){
			if (!in_array($res_id, $cid)) {
				//remove this resource from user
				$query = "DELETE FROM `#__yos_resources_manager_user_resource_banned` 
					WHERE `user_id` = " . $this->user->id . " AND `resource_id` = $res_id";
				$db->setQuery($query);
				if(!$db->query()){
					JError::raiseError(500,$db->getErrorMsg());
				}
			}
			else {
				//check is existing in database
				$query = "SELECT COUNT(id) FROM `#__yos_resources_manager_user_resource_banned`
					WHERE `user_id` = " . $this->user->id . " AND resource_id = $res_id";
				$db->setQuery($query);
				if (!$db->loadResult()) {
					$query = "INSERT INTO `#__yos_resources_manager_user_resource_banned` SET 
							`user_id` = " . $this->user->id . ', 
							`resource_id` = ' . intval($res_id) . ", 
							`description` = '".$description."',
							`redirect_url` = '".$redirect_url."',
							`redirect_message` = '".$redirect_message."',
							`start` = '$start_date', 
							`end` = '$end_date'";
					$db->setQuery($query);
					if(!$db->query()){
						JError::raiseError(500,$db->getErrorMsg());
					}
				}
			}
		}
		return 1;
		
	}
}
