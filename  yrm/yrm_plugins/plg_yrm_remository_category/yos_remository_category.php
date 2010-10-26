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

class plgYRMYos_remository_category extends JPlugin
{
	var $plugin_id = 0;
	function plgYRMYos_remository_category( &$subject, $params )
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
		$arr_container_id= array();
		$container_id='';
		$query='';
		// check current link is link to Administrator.
		 if ($mainframe->isAdmin()) {
	    	$act = JRequest::getVar('act');
	    	switch ($act)
	    	{
	    		case 'containers':	// manager containers
	    		{
	    			$i=0;
	    			$cfid=array();
	    			// get cfid[] container
	    			$cfid1=JRequest::getVar("cfid",null,'','array') ;
	    			// get parent id
	    			$parent_id=JRequest::getVar("parentid"); 	    			
	    			$parent_id?($cfid[0]=$parent_id):($cfid=count($cfid1)?$cfid1:null);	    			
	    			if(count($cfid))
	    			{	    				
	    				for($i=0;$i<count($cfid);$i++)
	    				{
	    					$container_id=$cfid[$i];
		    				while ($container_id)
		    				{
		    					$arr_container_id[]=$container_id;
		    					
		    					$query="select parentid
			    						from #__downloads_containers
			    						where id= $container_id";
		    					$db->setQuery($query);		
								$container_id=$db->LoadResult();
		    				}
	    				}
	    				return $arr_container_id;
	    			}	
	    			break;
	    		}  
    			case 'files':  // manager files
    				{
    					$i=0;
    					// get cfid[] files       					 					
    					$cfid=JRequest::getVar("cfid",null,'','array') ;  
		    			if(count($cfid))
		    			{	  				
		    				for($i=0;$i<count($cfid);$i++)
		    				{
		    					$query="select containerid
		    							from `#__downloads_files`
		    							where id= $cfid[$i]";
    							$db->setQuery($query);
    							$container_id=$db->LoadResult();  
			    				while ($container_id)
			    				{
			    					$arr_container_id[]=$container_id;
			    					$query="select parentid
				    						from #__downloads_containers
				    						where id= $container_id";
			    					$db->setQuery($query);		
									$container_id=$db->LoadResult();
			    				}
		    				}
		    				return $arr_container_id;
		    			}
    					break;
    				}	
    			case 'uploads':  // manager files uploads
    				{ 
    					// get cfid[] files
    					$arr=JRequest::getVar('cfid') ;
    					is_array($arr)?$cfid=$arr:$cfid[0]=$arr;  
		    			if(count($cfid))
		    			{	    				
		    				for($i=0;$i<count($cfid);$i++)
		    				{
		    					$query="select containerid
		    							from `#__downloads_files`
		    							where id= $cfid[$i]";
    							$db->setQuery($query);
    							$container_id=(int)$db->LoadResult();
    							$container_id=-$container_id;      											
			    				while ($container_id)
			    				{
			    					$arr_container_id[]=$container_id;
			    					$query="select parentid
				    						from #__downloads_containers
				    						where id= $container_id";
			    					$db->setQuery($query);		
									$container_id=$db->LoadResult();
			    				}
		    				}
		    				return $arr_container_id;
		    			}    					
    					break;    					
    				}
    			default:
    				return false;
	    	}
	    }
	    // front-end.
    	//get func
    	$style = JRequest::getVar('func');
    	if ($style=='fileinfo' ||$style=='startdown' || $style=='finishdown' || $style=='download') {
    		// download file
    		//get file id
    		$id = JRequest::getInt('id');    		
    		$query="select containerid
		    		from `#__downloads_files`
		    		where id= $id";
    		$db->setQuery($query);
    		$container_id=$db->LoadResult();    		
    	}
    	else { 
    		 // func=addfile, select(show) or savefile
    		$id = JRequest::getInt('id');  // addfile, select
    		$cid = JRequest::getInt('containerid'); // savefile
    		$container_id=$id?$id:$cid;
    	}
		
		while($container_id)
		{
			$arr_container_id[]=$container_id;
			$query="select parentid
					from #__downloads_containers
					where id=$container_id";
			$db->setQuery($query);		
			$container_id=$db->LoadResult();				
		}		
    	return $arr_container_id;
	}	
} 