<?php
/**
 * @version	$Id: yos_ccboard_category.php $
 * @package	YRM
 * @subpackage	YRM plugin
 * @copyright	Copyright (C) 2009 YOS.,JSC. All rights reserved.
 * @license		GNU/GPL
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class plgYRMYos_ccboard_category extends JPlugin
{
	var $plugin_id = 0;
	
	
	function plgYRMYos_ccboard_category( &$subject, $params )
	{
		/***DO NOT MODIFY THIS BLOCK IF YOU ARE NOT SURE WHAT YOU ARE DOING: BEGIN***/
		// Get plugin id
		$db =& JFactory::getDBO();
		$query	= 'SELECT id FROM #__plugins WHERE folder = "' . $params['type'] . '" AND element = "' . $params['name'].'"';
		$db->setQuery($query);
		$plugin_id = $db->loadResult();
		$this->plugin_id = intval($plugin_id);
		/***DO NOT MODIFY THIS BLOCK IF YOU ARE NOT SURE WHAT YOU ARE DOING: END***/
		
		parent::__construct( $subject, $params );
	}
	
	function onPrepareResource(&$str_in, $plugin_id){
		/***DO NOT MODIFY THIS BLOCK IF YOU ARE NOT SURE WHAT YOU ARE DOING: BEGIN***/
		// Check plug-in id
		if ($plugin_id !== $this->plugin_id) {
			return false;
		}
		
		
		
		/***DO NOT MODIFY THIS BLOCK IF YOU ARE NOT SURE WHAT YOU ARE DOING: END***/
		global $mainframe;
		$db = &JFactory::getDBO();
		$arr_id = array();
		$arr_cat = array();
		$query='';
		// check current link is link to Administrator.
		 if ($mainframe->isAdmin()) {
	    	$cids = JRequest::getVar('cid');
	    	
	    	if (count($cids)) {
	    		$view = JRequest::getVar('view');
	    		switch ($view){
	    			case 'categories':
	    				return $cids;
	    				break;
	    			case 'forums':
	    				//select cat id
	    				$sql_in = implode(',', $cids);
	    				$query = "SELECT cat_id FROM #__ccb_forums
	    					WHERE id IN ($sql_in)";
	    				$db->setQuery($query);
	    				$arr_cat_id = $db->loadResultArray();
	    				return $arr_cat_id;
	    				break;
	    			default:
	    				return false;
	    		}
	    	}
	    }
	    
    	//get post id
    	$post_id = JRequest::getInt('post');
    	if ($post_id) {
    		//select cat id
    		$query = "SELECT CF.cat_id 
    			FROM #__ccb_posts AS CP
    			LEFT JOIN #__ccb_forums AS CF ON CP.forum_id = CF.id
    			WHERE CP.id = $post_id";
    		$db->setQuery($query);
    		$arr_cat_id = $db->loadResultArray();
    		
    		return $arr_cat_id;
    	}
    	
    	//get topic id
    	$topic_id = JRequest::getInt('topic');
    	if ($topic_id) {
    		//select cat id
    		$query = "SELECT CF.cat_id 
    			FROM #__ccb_topics AS CT
    			LEFT JOIN #__ccb_forums AS CF ON CT.forum_id = CF.id
    			WHERE CT.id = $topic_id";
    		$db->setQuery($query);
    		$arr_cat_id = $db->loadResultArray();
    		
    		return $arr_cat_id;
    	}
    	
    	//get forum id
    	$forum_id = JRequest::getInt('forum');
    	if ($forum_id) {
    		//select cat id
    		$query = "SELECT cat_id
    			FROM #__ccb_forums
    			WHERE id = $forum_id";
    		$db->setQuery($query);
    		$arr_cat_id = $db->loadResultArray();
    		
    		return $arr_cat_id;
    	}
    	
    	//get cat id
    	$cat_id = JRequest::getInt('cat');
    	if ($cat_id) {
    		$arr_cat_id = array($cat_id);    		
    		return $arr_cat_id;
    	}
    	
    	return false;		
	}
} 



