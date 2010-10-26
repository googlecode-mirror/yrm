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
 *
 */
JTable::addIncludePath( JPATH_COMPONENT.DS.'tables' );
class YRMModelMapping extends JModel
{
	function getMapping(){
		global $mainframe, $option;
		$db				=& JFactory::getDBO();
		$acl			=& JFactory::getACL();
		
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order_mapping",		'filter_order',		'r.name',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir_mapping",	'filter_order_Dir',	'',			'word' );
		$search				= $mainframe->getUserStateFromRequest( "$option.search_mapping",			'search', 			'',			'string' );
		$search				= JString::strtolower( $search );
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit_mapping', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( $option.'.limitstart_mapping', 'limitstart', 0, 'int' );
		if($filter_order != 'r.id'){
			if ($filter_order != 'g.name') {
				$filter_order = 'jg.name';
			}elseif ($filter_order != 'jg.name'){
				$filter_order = 'g.name';
			}
		}
		$where = array();
		if (isset( $search ) && $search!= '')
		{
			$searchEscaped = $db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where[] = 'g.name LIKE '.$searchEscaped.' OR jg.name LIKE '.$searchEscaped;
		}
		
		$orderby = ' ORDER BY '. $filter_order .' '. $filter_order_Dir;
		$where = ( count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '' );

		$query = 'SELECT COUNT(r.id)'
		. ' FROM #__yos_resources_manager_mapping AS r'
		. $where
		;
		$db->setQuery( $query );
		$total = $db->loadResult();
		jimport('joomla.html.pagination');
		$pagination = new JPagination( $total, $limitstart, $limit );

		$query = 'SELECT r.*, g.name AS gname, jg.name AS jgname '
			. ' FROM #__yos_resources_manager_mapping AS r'
			. ' LEFT JOIN `#__yos_resources_manager_group` AS g'
			. ' ON g.id=r.yrm_group_id'
			. ' LEFT JOIN #__core_acl_aro_groups AS jg' 
			. ' ON jg.id = r.joomla_group_id'
			. $where
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
		// assign
		$res = new stdClass();
		$res->lists = $lists;
		$res->pagination = $pagination;	
		$res->rows = $rows;
		return $res;
	}
}