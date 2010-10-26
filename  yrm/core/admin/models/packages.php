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
class YRMModelPackages extends JModel
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

	var $package 		= null;
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
		$row = JTable::getInstance('package', 'Table');
		$row->load($id);
		$this->package = $row;
	}
	
	function getData(){
		$db	=& JFactory::getDBO();
		
		if ($this->_data) {
			return $this->_data;
		}
		
		$where = $this->_build_where();
		$orderby = $this->_build_order_by();
		
		$query = 'SELECT a.*, b.id as cid, CONCAT(b.currency_name," (", b.currency_code,")") as currency_name' 
			. ' FROM #__yos_resources_manager_package AS a'
			. ' LEFT JOIN `#__yos_resources_manager_currency` AS b ON a.currency = b.id'
			. $where
			. ' GROUP BY a.id'
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
		
		$query = 'SELECT COUNT(a.id)'
				. ' FROM #__yos_resources_manager_package AS a'
				. $where;
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
		$limitstart	= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );

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

		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'a.name',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'',			'word' );
		$search				= $mainframe->getUserStateFromRequest( "$option.search",			'search', 			'',			'string' );
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

		$search				= $mainframe->getUserStateFromRequest( "$option.search",			'search', 			'',			'string' );
		$search				= JString::strtolower( $search );
		$where = array();
		if (isset( $search ) && $search!= '')
		{
			$searchEscaped = $db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where[] = 'a.name LIKE '.$searchEscaped.' OR a.description LIKE '.$searchEscaped;
		}
		
		$where = ( count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '' );

		return $where;
	}
	
	function _build_order_by(){
		global $mainframe, $option;
		
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'a.name',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'',			'word' );
		$orderby = ' ORDER BY '. $filter_order .' '. $filter_order_Dir;
		
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
		
		$package_id = $this->package->id;
		//load the first level
		$query = "SELECT YRMR.*, YRMPOX.id AS 'xid', YRMPOX.times_access AS 'xtimes_access', YRMPOX.seconds AS 'seconds'
			FROM `#__yos_resources_manager_resource` AS YRMR
			LEFT JOIN `#__yos_resources_manager_package_object_xref` AS YRMPOX
				ON YRMPOX.object_id = YRMR.id
				AND YRMPOX.type='resource'
				AND YRMPOX.package_id=".$package_id
			." WHERE `parent_id` = $root_id 
			ORDER BY YRMR.`name` ASC";
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
                ?><table>
                <tr>
                    <td><?php echo JText::_('TIMES_ACCESS'); ?></td>
                    <td><?php echo $obj_res->xtimes_access; ?></td>
                </tr>
                <tr>
                    <td><?php echo JText::_('SECONDS'); ?></td>
                    <td><?php echo $obj_res->seconds; ?></td>
                </tr>
                </table><?php
                $etitle = htmlspecialchars(ob_get_contents());
                ob_end_clean();
                $return .= "etitle=\"$etitle\"";
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
	function object_yos ($id, $type){
		global $mainframe, $option;
		
		$id = intval($id);

		$db					=& JFactory::getDBO();
		$cid 				= JRequest::getVar('cid_role');
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart	= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );

		$where = array();
		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		$query = 'SELECT COUNT(id)'
		. ' FROM #__yos_resources_manager_'.$type
		. $where . ' AND published = 1'
		;
		$db->setQuery( $query );
		$total = $db->loadResult();

		jimport('joomla.html.pagination');
		$pagination = new JPagination( $total, $limitstart, $limit );

		$query = 'SELECT r.*, xr.seconds'
		. ' FROM #__yos_resources_manager_'.$type. ' AS r'
		. ' LEFT JOIN #__yos_resources_manager_package_object_xref AS xr ON xr.package_id='.$id.' AND xr.type="'.$type.'" AND r.id=xr.object_id'
		. $where
		. ' GROUP BY r.id'
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
			$query_v = ' SELECT COUNT(*) FROM #__yos_resources_manager_package_object_xref as a'
						. ' WHERE a.type="'.$type.'"'
						. ' AND a.package_id='.$id
						. ' AND a.object_id='.$v->id;
			$db->setQuery($query_v);
			if ($db->loadResult() > 0) {
				$check_rows[] = 'checked';
			}else{
				$check_rows[] = '';
			}
			
		}
		$return_yos = new stdClass();
		
		$return_yos->items = $rows;
		$return_yos->check_rows = $check_rows;
		$return_yos->pagination = $pagination;
		return  $return_yos;
	}
	
	function payment_method ($id){
		global $mainframe, $option;

		$db					=& JFactory::getDBO();
		$cid 				= JRequest::getVar('cid_role');
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart	= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );

		$query = 'SELECT COUNT(id)'
		. ' FROM #__yos_resources_manager_payment_method'
		. ' WHERE published = 1'
		;
		$db->setQuery( $query );
		$total = $db->loadResult();

		jimport('joomla.html.pagination');
		$pagination = new JPagination( $total, $limitstart, $limit );

		$query = 'SELECT r.*'
		. ' FROM #__yos_resources_manager_payment_method AS r'
		. ' WHERE published = 1'
		. ' GROUP BY r.id'
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
			$query_v = ' SELECT COUNT(*) FROM #__yos_resources_manager_package_payment_method_xref as a'
						. ' WHERE '
						. ' a.package_id='.$id
						. ' AND a.payment_method_id='.$v->id;
			$db->setQuery($query_v);
			if ($db->loadResult() > 0) {
				$check_rows[] = 'checked';
			}else{
				$check_rows[] = '';
			}
			
		}
		$return_yos 			= new stdClass();
		
		$return_yos->items 		= $rows;
		$return_yos->check_rows = $check_rows;
		$return_yos->pagination = $pagination;
		return  $return_yos;
	}
	
	function save($resource_arr, $role_arr, $group_arr, $method_arr,$package_id){
		$db = &JFactory::getDBO();
		$keep_original 	= JRequest::getVar('keep_original');
		$times_access 	= JRequest::getVar('times_access');
		$seconds		= JRequest::getVar('seconds');
		$role_seconds 	= JRequest::getVar('role_seconds');
		$group_seconds 	= JRequest::getVar('group_seconds');
		$cmd 			= JRequest::getVar('cmd');
		
		$query_resource = '';
		$query_role = '';
		$query_group = '';
		$query_payment_methods = '';
		$resource_ids = implode(',', $resource_arr);
		$role_ids = implode(',', $role_arr);
		$group_ids = implode(',', $group_arr);
		$payment_methods_ids = implode(',', $method_arr);
		// resource save
		if (count($resource_arr) > 0) {
			if ($cmd == 'edit') {
				if ($keep_original == 1) {
					$query = ' DELETE FROM #__yos_resources_manager_package_object_xref '
							  .' WHERE package_id = '.$package_id
							  .' AND type="resource" AND object_id NOT IN('.$resource_ids.')';	
					$db->setQuery($query);
					if(!$db->query()){
						JError::raiseError(500,$db->getErrorMsg());
					} 
					foreach ($resource_arr as $resource) {
						$query = ' SELECT COUNT(*) FROM `#__yos_resources_manager_package_object_xref`'
								.' WHERE object_id='.intval($resource)
								.' AND type="resource"'
								.' AND package_id='.$package_id;
						$db->setQuery($query);
						$count_resource = $db->loadResult();
						if (intval($count_resource) == 0) {
							$query_insert_resource = '
								 INSERT INTO #__yos_resources_manager_package_object_xref(`package_id`,`object_id`,`type`,`times_access`, `seconds`)'
								.' VALUES('.$package_id.', '.$resource.',"resource", '.$times_access.', '.$seconds.')';
							$db->setQuery($query_insert_resource);
							if(!$db->query()){
								JError::raiseError(500,$db->getErrorMsg());
							} 
						}
						
					}
				}else {
					$query =  ' DELETE FROM #__yos_resources_manager_package_object_xref '
							  .' WHERE package_id = '.$package_id
							  .' AND type="resource"';			
					$db->setQuery($query);
					if(!$db->query()){
						JError::raiseError(500,$db->getErrorMsg());
					}
					$value = array();
					$query_resource .= 'INSERT INTO `#__yos_resources_manager_package_object_xref` (`package_id`,`object_id`,`type`, `times_access`, `seconds`)';
					foreach ($resource_arr as $resource) {
						$value[] = ' ('.$package_id.', '.$resource.',"resource", '.$times_access.', '.$seconds.')';
					}
					$values = implode(',',$value);
					$query_resource .= ' VALUES '.$values;
					$db->setQuery($query_resource);
					if(!$db->query()){
						JError::raiseError(500,$db->getErrorMsg());
					}
				}
			}else {
				$value = array();
				$query_resource .= 'INSERT INTO `#__yos_resources_manager_package_object_xref` (`package_id`,`object_id`,`type`, `times_access`, `seconds`)';
				foreach ($resource_arr as $resource) {
					$value[] = ' ('.$package_id.', '.$resource.',"resource", "'.$times_access.'", "'.$seconds.'")';
				}
				$values = implode(',',$value);
				$query_resource .= ' VALUES '.$values;
				$db->setQuery($query_resource);
				if(!$db->query()){
					JError::raiseError(500,$db->getErrorMsg());
				}
			}
		}else{
			$query =  ' DELETE FROM #__yos_resources_manager_package_object_xref '
					  .' WHERE package_id = '.$package_id
					  .' AND type="resource"';			
			$db->setQuery($query);
			if(!$db->query()){
				JError::raiseError(500,$db->getErrorMsg());
			}
		}
		// save role
		if (count($role_arr) > 0) {
			if ($cmd == 'edit') {
				$query =  ' DELETE FROM #__yos_resources_manager_package_object_xref '
						  .' WHERE package_id = '.$package_id
						  .' AND type="role"';			
				$db->setQuery($query);
				if(!$db->query()){
					JError::raiseError(500,$db->getErrorMsg());
				}
				$value_role = array();
				$query_role .= 'INSERT INTO `#__yos_resources_manager_package_object_xref` (`package_id`,`object_id`,`type`, `seconds`)';
				foreach ($role_arr as $role) {
					$tmp_role_second = $role_seconds[$role];
					$value_role[] = ' ('.$package_id.', '.$role.',"role","'.$tmp_role_second.'")';
				}
				$values = implode(',',$value_role);
				$query_role .= ' VALUES '.$values;
				$db->setQuery($query_role);
				if(!$db->query()){
					JError::raiseError(500,$db->getErrorMsg());
				}
				
			}else {
				$value_role = array();
				$query_role .= 'INSERT INTO `#__yos_resources_manager_package_object_xref` (`package_id`,`object_id`,`type`, `seconds`)';
				foreach ($role_arr as $role) {
					$tmp_role_second = $role_seconds[$role];
					$value_role[] = ' ('.$package_id.', '.$role.',"role","'.$tmp_role_second.'")';
				}
				$values = implode(',',$value_role);
				$query_role .= ' VALUES '.$values;
				$db->setQuery($query_role);
				if(!$db->query()){
					JError::raiseError(500,$db->getErrorMsg());
				}
			}
		}else{
			$query =  ' DELETE FROM #__yos_resources_manager_package_object_xref '
					  .' WHERE package_id = '.$package_id
					  .' AND type="role"';			
			$db->setQuery($query);
			if(!$db->query()){
				JError::raiseError(500,$db->getErrorMsg());
			}
		}
		//save group
		if (count($group_arr) > 0) {
			if ($cmd == 'edit') {
				$query =  ' DELETE FROM #__yos_resources_manager_package_object_xref '
						  .' WHERE package_id = '.$package_id
						  .' AND type="group"';			
				$db->setQuery($query);
				if(!$db->query()){
					JError::raiseError(500,$db->getErrorMsg());
				}
				$value_group = array();
				$query_group .= 'INSERT INTO `#__yos_resources_manager_package_object_xref` (`package_id`,`object_id`,`type`,`seconds`)';
				foreach ($group_arr as $group) {
					$tmp_group_second = $group_seconds[$group];
					$value_group[] = ' ('.$package_id.', '.$group.',"group", "'.$tmp_group_second.'")';
				}
				$values = implode(',',$value_group);
				$query_group .= ' VALUES '.$values;
				$db->setQuery($query_group);
				if(!$db->query()){
					JError::raiseError(500,$db->getErrorMsg());
				}
				
			}else {
				$value_group = array();
				$query_group .= 'INSERT INTO `#__yos_resources_manager_package_object_xref` (`package_id`,`object_id`,`type`,`seconds`)';
				foreach ($group_arr as $group) {
					$tmp_group_second = $group_seconds[$group];
					$value_group[] = ' ('.$package_id.', '.$group.',"group", "'.$tmp_group_second.'")';
				}
				$values = implode(',',$value_group);
				$query_group .= ' VALUES '.$values;
				$db->setQuery($query_group);
				if(!$db->query()){
					JError::raiseError(500,$db->getErrorMsg());
				}
			}
		}else{
			$query =  ' DELETE FROM #__yos_resources_manager_package_object_xref '
					  .' WHERE package_id = '.$package_id
					  .' AND type="group"';			
			$db->setQuery($query);
			if(!$db->query()){
				JError::raiseError(500,$db->getErrorMsg());
			}
		}
		//save payment method
		if (count($method_arr) > 0) {
			if ($cmd == 'edit') {
				$query =  ' DELETE FROM #__yos_resources_manager_package_payment_method_xref '
						  .' WHERE package_id = '.$package_id;			
				$db->setQuery($query);
				if(!$db->query()){
					JError::raiseError(500,$db->getErrorMsg());
				}
				$value_method = array();
				$query_payment_methods .= 'INSERT INTO `#__yos_resources_manager_package_payment_method_xref` (`package_id`,`payment_method_id`)';
				foreach ($method_arr as $payment_method) {
					$value_method[] = ' ('.$package_id.', '.$payment_method.')';
				}
				$values = implode(',',$value_method);
				$query_payment_methods .= ' VALUES '.$values;
				$db->setQuery($query_payment_methods);
				if(!$db->query()){
					JError::raiseError(500,$db->getErrorMsg());
				}
			}else {
				$value_method = array();
				$query_payment_methods .= 'INSERT INTO `#__yos_resources_manager_package_payment_method_xref` (`package_id`,`payment_method_id`)';
				foreach ($method_arr as $payment_method) {
					$value_method[] = ' ('.$package_id.', '.$payment_method.')';
				}
				$values = implode(',',$value_method);
				$query_payment_methods .= ' VALUES '.$values;
				$db->setQuery($query_payment_methods);
				if(!$db->query()){
					JError::raiseError(500,$db->getErrorMsg());
				}
			}
			
		}else{
			$query =  ' DELETE FROM #__yos_resources_manager_package_payment_method_xref '
					  .' WHERE package_id = '.$package_id;
			$db->setQuery($query);
			if(!$db->query()){
				JError::raiseError(500,$db->getErrorMsg());
			}
		}
	}
	
	function save_rs_form(){
		$db =& JFactory::getDBO();
		
		$package_id = JRequest::getInt('package_id');
		$res_id = JRequest::getInt('rid');
		$times_access = JRequest::getInt('times_access');
		$seconds = JRequest::getVar('seconds');
		
		$query = "UPDATE `#__yos_resources_manager_package_object_xref` SET 
			`times_access` = $times_access,
			`seconds` = '$seconds'
			WHERE `package_id` = $package_id AND `object_id` = $res_id AND `type`='resource'";
		$db->setQuery($query);
		if(!$db->query()){
			JError::raiseError(500,$db->getErrorMsg());
		}
		
		return $db->getAffectedRows();
	}
	
	function getResource($res_id){
		$db =& JFactory::getDBO();
		$query = "SELECT * FROM `#__yos_resources_manager_package_object_xref`
			WHERE `package_id` = ".$this->package->id." AND `object_id` = ".$res_id." AND `type`='resource'";
		$db->setQuery($query);
		$obj_re = $db->loadObject();
		return $obj_re;
	}
	
	function getCurrency(){
		$db =& JFactory::getDBO();
		$query = "SELECT c.`id` AS value, CONCAT(c.`currency_name`, ' (',c.`currency_code`,')') AS text FROM `#__yos_resources_manager_currency` AS c ORDER BY c.`currency_name`";
		$db->setQuery($query);
		foreach( $db->loadObjectList() as $obj )
		{
			$currences[] = JHTML::_('select.option',  $obj->value, JText::_( $obj->text ) );
		}
		$lists['currency'] 	= JHTML::_('select.genericlist',   $currences, 'currency', 'class="inputbox" size="1" ', 'value', 'text', $this->package->currency );

		return $lists['currency'];
	}
}