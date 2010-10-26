<?php
/**
 * @version	$Id: yos_ccboard_forum.php $
 * @package	YRM
 * @subpackage	YRM plugin
 * @copyright	Copyright (C) 2009 YOS.,JSC. All rights reserved.
 * @license		GNU/GPL
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class plgYRMYos_ccboard_forum extends JPlugin
{
	var $plugin_id = 0;
	
	
	function plgYRMYos_ccboard_forum( &$subject, $params )
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
		 	// get forum id
	    	$cids = JRequest::getVar('cid');	    	
	    	if (count($cids)){
	    		$view = JRequest::getVar('view');
	    		if($view=='forums'){	    				
	    				return $cids;	    				
	    		}
	    		return false;
	    	}
	    }	    
    	//get post id
    	$post_id = JRequest::getInt('post');
    	if ($post_id) {
    		//select cat id
    		$query = "SELECT forum_id 
    			FROM #__ccb_posts     			
    			WHERE id = $post_id";
    		$db->setQuery($query);
    		$arr_forum_id = $db->loadResultArray();    		
    		return $arr_forum_id;
    	}
    	
    	//get topic id
    	$topic_id = JRequest::getInt('topic');
    	if ($topic_id) {  
    		//select cat id
    		$query = "SELECT forum_id 
    			FROM #__ccb_topics     			
    			WHERE id = $topic_id";
    		$db->setQuery($query);
    		$arr_forum_id = $db->loadResultArray();    		
    		return $arr_forum_id;
    	}
    	
    	//get forum id
    	$forum_id = JRequest::getInt('forum'); 
    	if ($forum_id) {    
    		 $arr_forum_id=array();
    		 $arr_forum_id=$forum_id;    			  		
    		 return $arr_forum_id;
    	}      	
    
    	return false;		
	}
}
