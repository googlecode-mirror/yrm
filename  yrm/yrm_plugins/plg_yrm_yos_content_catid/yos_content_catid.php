<?php
/**
 * @version	$Id: yos_resources_manager.php $
 * @package	YRM
 * @subpackage	Component
 * @copyright	Copyright (C) 2009 YOS.,JSC. All rights reserved.
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class plgYRMYos_content_catid extends JPlugin
{
	var $plugin_id = 0;
	
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @param object $params  The object that holds the plugin parameters
	 * @since 1.5
	 */
	function plgYRMYos_content_catid( &$subject, $params )
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
		// check current link is link to Administrator. 
	    if ($mainframe->isAdmin()) {
	    	$cids = JRequest::getVar('cid');
	    	if (count($cids)) {
	    		foreach ($cids as $cid)
	    		$arr_id[] = intval($cid);
	    	}
	    }else{ 
	    	$arr_id[] = intval(JRequest::getVar('id'));
	    	$view = JRequest::getVar('view');
	    	if ($view == 'category') {
	    		return $arr_id;
	    	} elseif ($view != 'article'){
	    		return false;
	    	}
	    }
		$im_id = implode(',', $arr_id);
		//select category id here
		
		$query = 'SELECT `catid` FROM `#__content` WHERE `id` IN('.$im_id.')';
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		
		if(count($rows)){
			foreach ($rows as $row){
				$arr_cat[] = $row->catid;	
			}
			return $arr_cat;
		}else {
			return false;
		}
		
	}
}


