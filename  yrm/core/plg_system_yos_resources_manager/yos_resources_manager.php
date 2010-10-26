<?php
/**
 * @version		$Id: yos_resources_manager.php 2009-10-07 minhna$
 * @package		YRM
 * @subpackage	plugin system
 * @copyright	Copyright (C) 2010 YOS.,JSC. All rights reserved.
 * @license	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * YOS Resources Manager System Plugin
 *
 * @package		YOS Resources Manager
 * @subpackage	System plugin
 * @since 		1.5
 */
class plgSystemYos_resources_manager extends JPlugin
{

	/**
	* Constructor
	*
	* For php4 compatability we must not use the __constructor as a constructor for
	* plugins because func_get_args ( void ) returns a copy of all passed arguments
	* NOT references.  This causes problems with cross-referencing necessary for the
	* observer design pattern.
	*/
	function plgSystemYos_resources_manager( &$subject, $config )
	{
		parent::__construct($subject, $config);
	}
	
	function onAfterRoute()
	{
		global $mainframe;
		
		$lang = & JFactory::getLanguage();
		$lang->load('plg_system_yos_resources_manager', JPATH_ADMINISTRATOR);
		$db = &JFactory::getDBO();
		
		// check current link is link to Administrator. 
	    if ($mainframe->isAdmin()) {
	    	$back_end = true;
	    }else{ 
	    	$back_end = false;
	    }

	    $string_url = $this->get_fullurl();
	    $arr_url = $this->get_arr_url();
		$yos_yrm_cfg = JComponentHelper::getParams('com_yos_resources_manager');
		$user	=& JFactory::getUser();
		
		//debug
		if ($yos_yrm_cfg->get('debugRequest') && $user->usertype == 'Super Administrator') {
			$this->_getDebugInfo($yos_yrm_cfg->get('offsetTimeMax'));
			$this->_renderDebugInfo( $yos_yrm_cfg->get('ignoreVar'));
		}
		
		/************** step 1*******************/
		if($user->gid == 25){
			return true;
		}else{
			/***************** step 2******************/
			$arr_resources = $this->step2($back_end, $arr_url);
			if ($arr_resources == false) {
				return true;
			}
			
			$arr_resources_id = array();
			foreach ($arr_resources as $resource){
				$arr_resources_id[]=$resource->id; 
			}
			
			if ($user->id > 0){
				/**************** step 3 ******************/
				if ($this->step3($user, $arr_resources_id, $back_end) !== false) {
					return true;
				}

				/**************** step 4 ******************/
				if($this->step4($user, $arr_resources_id) === true){ 
					return true;
				}
			}
			
			/***************** step 5 *****************/
			$arr_roles_id = $this->step5($user, $arr_resources_id);
			if ($arr_roles_id === true) {
				return true;
			}
		
			/***************** step 6 *****************/			
			$arr_grs_id = $this->step6($user, $arr_roles_id);
			if ($arr_grs_id === true) {
				return true;
			}						
						
			/***************** step 7 *****************/
			$arr_packages = $this->step7($arr_resources_id, $arr_roles_id, $arr_grs_id);
			if ($arr_packages === false) {
				$this->redirect_yos($arr_resources_id);
			}
			
			/***************** step 8 *****************/
			$arr_valid_packages = $this->step8($arr_packages);
			if ($arr_valid_packages === false) {
				$this->redirect_yos($arr_resources_id);
			}
			
			/***************** step 9 *****************/
			$this->step9($string_url, $arr_valid_packages);
		}		
		return true;
	}
	
	function get_arr_url(){
		$arr_url 	= $_REQUEST;
		foreach ($arr_url as &$v){
			if(!is_array($v)){
				if (preg_match('/:/', $v) > 0) {
					$arr_url_temp = explode(':',$v);
					$v = $arr_url_temp[0];
				}
			} else {
				foreach ($v as &$str_v){
					$yos_tmp = '';
					if(!is_array($str_v)){
						if (preg_match('/:/', $str_v) > 0) {
							$arr_url_temp = explode(':',$str_v);
						}
					}
				}
			}
		}
		return $arr_url;
	}
	
	
	function step2($back_end, $arr_url){
		$db 		= &JFactory::getDBO();
		
		$option = JRequest::getVar('option');
		$view 	= JRequest::getVar('view');
		$task 	= JRequest::getVar('task');
		$and 	= ' AND `type`="request" ';
		$query_resource = ' SELECT * FROM #__yos_resources_manager_resource WHERE `option`="'.$option.'" AND `published` = 1 AND ';
		if ($back_end) {
			$query_resource .= ' (`affected` IN("B", "BF"))';
		}else{
			$query_resource .= ' (`affected` IN("F", "BF"))';
		}
		if ($view) {
			$query_resource .=' AND (`view`="'.$view.'" OR `view`="")';
		}
		
		if ($task) {
			$query_resource .=' AND (`task`="'.$task.'" OR `task`="")';
		}
		$query_resource .= $and;
		$db->setQuery($query_resource);
		$rows = $db->loadObjectList();
		if (count($rows)) {
			$arr_resources 	= array();
			$arr_resources_id = array();
			foreach ($rows as $row){
				$str_in 	= 'option='.$option;
				if ($row->view != '') {
					$str_in .= '&view='.$row->view;
				}
				if ($row->task != '') {
					$str_in .= '&task='.$row->task;
				}
				$str_in .= '&'.$row->params;
				$arr_url2 = $arr_url;
				//plug-in processing
				if (intval($row->plug_in)>0) {
					$dispatcher	=& JDispatcher::getInstance();
					JPluginHelper::importPlugin('yrm');
					//plug-in must return a string or an array of strings. else return false.
					$p_returns = $dispatcher->trigger('onPrepareResource', array (&$str_in, intval($row->plug_in) ));
					
					if ($p_returns) {
						for ($i = 0; $i < count($p_returns); $i++){
							if ($p_returns[$i]) {
								$arr_url2['PLUGIN'] = $p_returns[$i];
								break;
							}
						}
					}
				}
				
				if ($this->Validate_Url($str_in, $arr_url2)) {
				 	$arr_resources[$row->id] = $row;
					$arr_resources_id[] = $row->id;
				}
			}

			//find the heighest level of resource by removing their parent resources
			$arr_resources_be_removed = array();
			foreach($arr_resources as $key => $value){
				if(in_array($value->parent_id, $arr_resources_id)){
					$arr_resources_be_removed[] = $value->parent_id;
				}
			}
			//removing
			$new_arr_resources = array();
			foreach($arr_resources as $key => $value){
				if(!in_array($key, $arr_resources_be_removed)){
					$new_arr_resources[$key] = $value;
				}
			}

			return $new_arr_resources;
		}
		return false;
	}
	
	function step3($user, $arr_resources_id, $back_end){
		$db = &JFactory::getDBO();
		global $mainframe;
		
		$now = JFactory::getDate();
		$nowsql = $now->toMySQL();
		
		$and  = ' AND `resource_id` IN (';
		$and .= implode(',',$arr_resources_id).')';
		$and .= ' AND (TIMESTAMP("'.$nowsql.'") >= TIMESTAMP(`start`)) AND ( (TIMESTAMP("'.$nowsql.'") <= TIMESTAMP(`end`)) OR (`end` = \'0000-00-00 00:00:00\') )';
		$query_step3 = 	' SELECT * FROM `#__yos_resources_manager_user_resource_banned`' 
						.' WHERE `user_id`='.$user->id.$and;
		$db->setQuery($query_step3);
		$rows = $db->loadObjectList();
		if (count($rows)) { // if there are one or more resources is banned. ==> redirect.
			// select redirect url.
			$count = 0;
			foreach ($rows as $row){
				if ($row->redirect_url !='') {
					$count = 1; // ==> check exists resource_banned's redirect_url.
					// redirect.
					$mainframe->redirect($row->redirect_url, $row->redirect_message);
				}
			}
			
			if ($count == 0) { //==> not exists resource_banned's redirect_url
				if ($back_end) {
					$and_affected = ' AND (`affected` IN("B", "BF"))';
				}else{
					$and_affected = ' AND (`affected` IN("F", "BF"))';
				}
				foreach ($rows as $row){
					$query_step3 = ' SELECT * FROM `#__yos_resources_manager_resource` WHERE `id`='.$row->resource_id.' AND published = 1 '.$and_affected.' AND `type`="request"';
					$db->setQuery($query_step3);
					$row = $db->loadObject();
					if ($row->redirect_url !='') {
						$count = 1;
						$mainframe->redirect($row->redirect_url, $row->redirect_message);
					}
				}
			}
			if ($count == 0 ) { // ==> not exists resource_banned's redirect_url and resource's redirect_url
				$mainframe->redirect('index.php');
			}
		}
		return false;
	}
	
	function step4($user, $arr_resources_id){
		$db = &JFactory::getDBO();
		global $mainframe;
		
		$now = JFactory::getDate();
		$nowsql = $now->toMySQL();

		$and = ' AND `resource_id` IN (';
		$and .= implode(',',$arr_resources_id).')';
		$and .= ' AND (TIMESTAMP("'.$nowsql.'") >= TIMESTAMP(`start`)) AND ( (TIMESTAMP("'.$nowsql.'") <= TIMESTAMP(`end`)) OR (`end` = \'0000-00-00 00:00:00\') )';
		$and .= ' AND (`times_access` > 0 OR `times_access` = -1)';
		// check table resource_user_xref.
		$query_step4 = 'SELECT * FROM `#__yos_resources_manager_user_resource_xref` WHERE `user_id`='.$user->id.$and;
		$db->setQuery($query_step4);
		$user_rs_rows = $db->loadObjectList();	
		if (count($user_rs_rows)) {
			$query_step4_update = ' UPDATE `#__yos_resources_manager_user_resource_xref` '
								  .' SET `times_access` = `times_access`-1'
								  .' WHERE `user_id`='.$user->id.$and. ' AND `times_access` NOT IN (-1, 0)' ;
			$db->setQuery($query_step4_update);
			$db->query();
			return true;
		}else { 
			return false;
		}
	}
	
	function step5($user, $arr_resources_id){
		$db = &JFactory::getDBO();
		global $mainframe;
		
		if(count($arr_resources_id) == 0){
			return false;
		}
		
		$now = JFactory::getDate();
		$nowsql = $now->toMySQL();

		// if not have any resources of current user.
		// first. ==> get role_resource.
		$where = ' `resource_id` IN ('.implode(',',$arr_resources_id).')';
		
		$query_step5_rs_role = ' SELECT * FROM `#__yos_resources_manager_resource_role_xref` WHERE '.$where;
		$db->setQuery($query_step5_rs_role);
		$rs_role_rows = $db->loadObjectList();
		if (count($rs_role_rows)) {
			$arr_roles_id = array();
			foreach ($rs_role_rows as $role){
				$arr_roles_id[] = $role->role_id; 
			}
			
			if($user->id > 0){
				// then. ==> get user_role.
				$where_ur = ' `role_id` IN (';			
				$where_ur .= implode(',',$arr_roles_id).')';
				$where_ur .= ' AND (TIMESTAMP("'.$nowsql.'") >= TIMESTAMP(`start`)) AND ( (TIMESTAMP("'.$nowsql.'") <= TIMESTAMP(`end`)) OR (`end` = \'0000-00-00 00:00:00\') )';
			
				$query_step5_ur = 'SELECT * FROM `#__yos_resources_manager_user_role_xref` WHERE `user_id`='.$user->id.' AND '.$where_ur;
				$db->setQuery($query_step5_ur);
				$ur_rows = $db->loadObjectList();
			
				if (count($ur_rows)) {
					return true;
				}
			}
			
			return $arr_roles_id;
		} 
		return false;
		
	}
	
	function step6($user, $arr_roles_id){
		$db = &JFactory::getDBO();
		global $mainframe;
		
		$now = JFactory::getDate();
		$nowsql = $now->toMySQL();

		if(!$arr_roles_id){
			return false;
		}		
		
		$where_rg = ' `role_id` IN (';
		$where_rg .= implode(',',$arr_roles_id).')';
		
		$query_step6_rg = 'SELECT * FROM `#__yos_resources_manager_group_role_xref` WHERE '.$where_rg;
		$db->setQuery($query_step6_rg);
		$rg_rows = $db->loadObjectList();
		if (count($rg_rows)) {
			$arr_grs_id = array();
			foreach ($rg_rows as $group){
				$arr_grs_id[] = $group->group_id; 
			}
			
			if($user->id > 0){
				$where_ug = ' `group_id` IN (';
				$where_ug .= implode(',',$arr_grs_id).')';
				$where_ug .= ' AND (TIMESTAMP("'.$nowsql.'") >= TIMESTAMP(`start`)) AND ( (TIMESTAMP("'.$nowsql.'") <= TIMESTAMP(`end`)) OR (`end` = \'0000-00-00 00:00:00\') )';
			
				$query_step6_ug = 'SELECT * FROM `#__yos_resources_manager_user_group_xref` WHERE `user_id`='.$user->id.' AND '.$where_ug;
				$db->setQuery($query_step6_ug);
				$ug_rows = $db->loadObjectList();
				if (count($ug_rows)) {
					return true;						
				}
			}
			
			return $arr_grs_id;
		}
		return false;
	}
	
	function step7($arr_resources_id, $arr_roles_id, $arr_grs_id){
		$db = &JFactory::getDBO();
		global $mainframe;
		
		/************* step 7**************/
		// input: arrays : resources, roles, groups. 
		// output :packages 
		$where = '';
		$where_rs = ' `object_id` IN ('.implode(',',$arr_resources_id).') AND `type`="resource" ';
		$where_rl = (is_array($arr_roles_id) && count($arr_roles_id)) ? ' `object_id` IN ('.implode(',',$arr_roles_id).') AND `type`="role" ' : '';
		$where_gr = (is_array($arr_grs_id) && count($arr_grs_id)) ? ' `object_id` IN ('.implode(',',$arr_grs_id).') AND `type`="group" ' : '';
		$where = ' ( '.$where_rs.' )';
		$where .= $where_rl ? 'OR  ( '.$where_rl.' )' : '';
		$where .= $where_gr ? ' OR ( '.$where_gr.' ) ' : '';
		$query_step7_pk = 'SELECT * FROM `#__yos_resources_manager_package_object_xref` WHERE '.$where;
		$db->setQuery($query_step7_pk);
		$pk_rows = $db->loadObjectList();
		if (count($pk_rows)) {
			$arr_pk_id = array();
			foreach ($pk_rows as $pk ){
				$arr_pk_id[] = $pk->package_id;											
			}
			// check packages from table jos_yos_resources_manager_package.
			$query_step7_pk_table = ' SELECT * FROM `#__yos_resources_manager_package` WHERE `id` IN ('.implode(',',$arr_pk_id).') AND published = 1';
			$db->setQuery($query_step7_pk_table);
			$arr_packages = $db->loadObjectList();
			if (count($arr_packages)) {
				return $arr_packages;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	function step8($arr_packages){
		$db = &JFactory::getDBO();
		global $mainframe;
		
		//check payment method for each package.
		$arr_valid_packages = array();
		foreach ($arr_packages as $pk ){
			$query = 'SELECT COUNT(PPMX.`id`) FROM `#__yos_resources_manager_package_payment_method_xref` AS PPMX 
				LEFT JOIN `#__yos_resources_manager_payment_method` AS PM ON PPMX.payment_method_id = PM.id
				WHERE package_id = '. $pk->id . ' AND PM.published = 1';
			$db->setQuery($query);
			if($db->loadResult()){
				$arr_valid_packages[] = $pk->id;
			}
		}
		
		if (count($arr_valid_packages)) {
			return $arr_valid_packages;
		}
		else {
			return false;
		}
	}
	
	function step9($return_url, $arr_valid_packages){
		global $mainframe;
		$user = &JFactory::getUser();
				
		$session =& JFactory::getSession();
		$session->set('yrm_arr_package', $arr_valid_packages);
		$session->set('yrm_return_url', base64_encode($return_url));
		
		$Itemid = JRequest::getVar('Itemid');
		$msg = JText::_('PLEASE BUY FOLLOW PACKAGES TO ACCESS YOUR RESOURCES');
		$mainframe->redirect(JURI::root().'index.php?option=com_yos_resources_manager&view=package&Itemid='.$Itemid, $msg);
	}
	
	/**
	 * method redirect with array resouces, array roles, array groups.
	*/
	function redirect_yos($arr_resources_id){
		global $mainframe;
		$db = &JFactory::getDBO();
		
		$and  = ' `id` IN (';
		$and .= implode(',',$arr_resources_id).')';
		$query = ' SELECT * FROM `#__yos_resources_manager_resource`' 
				.' WHERE '.$and;
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		$count = 0;
		if (count($rows)) { // if there are one or more resources ==> redirect.
			foreach ($rows as $row){
				if ($row->redirect_url !='') {
					$count = 1;
					$mainframe->redirect($row->redirect_url, $row->redirect_message);
				}
			}
		}
		if ($count == 0 ) { // ==> not exists redirect_url and resource's redirect_url
			$mainframe->redirect('index.php');
		}
	}
	/**
	 *  method get param of resources
	*/
	
	function Validate_Url($str_in, $arr_request){		
		
        //removing something      
        $str_in 	= preg_replace('/\s+|;|{|}/', '', $str_in);     
        $str_in 	= str_replace('&', '&&', $str_in);      
        $str_in 	= str_replace('|', '||', $str_in);
        $arr_cmp 	= preg_split('/&&|\(|\)|\|\|/', $str_in);
     
        $arr_replace = array();
        for ($i = 0; $i < count($arr_cmp); $i++){
			if ($arr_cmp[$i]) {
				if(!preg_match('/^([^><=!]+)((>=)|(<=)|(!=)|(<)|(>)|(=))([a-z0-9A-Z-_]*)$/', $arr_cmp[$i], $match)){
					continue;
				}	
				
				$comparation = $match[2];
				if ($comparation == '=') {
					$comparation = '==';			
				}			
				
				//$arr_key_value = preg_split('/(>=)|(<=)|(!=)|(<)|(>)|(=)/', $arr_cmp[$i]);
				$key = $match[1];
				$value = $match[9];
				
				$r_key = 'r_key_'. sprintf('%03d', $i);
				
				//remove [] from key
				$key = str_replace('[]', '', $key);
							
				if (!isset($arr_request[$key])) {
					$arr_request[$key] = null;
				}
				
				if (!is_array($arr_request[$key])) {
					if ($comparation == '==' || $comparation == '!=') {
						$r_replace = "'".md5($arr_request[$key])."'".$comparation."'".md5($value)."'";
					}
					else {
						$r_replace = "'".floatval($arr_request[$key])."'".$comparation."'".floatval($value)."'";
					}
				}
				else {
					//is array
					$arr_tmp = array();
					for ($j = 0; $j < count($arr_request[$key]); $j++){
						if ($comparation == '==' || $comparation == '!=') {
							$str_tmp ="'".md5($arr_request[$key][$j])."'".$comparation."'".md5($value)."'";
						}
						else {
							$str_tmp ="'".floatval($arr_request[$key][$j])."'".$comparation."'".floatval($value)."'";
							//$str_tmp = "'".floatval($arr_cmp[$i])."'".$comparation."'".floatval(($key . '[]=' . $arr_request[$key][$j])."'";
						}
						array_push($arr_tmp, $str_tmp);
					}
					
					if ($comparation == '==') {
						$r_replace = "(" . implode('||', $arr_tmp) . ")";
					}
					else {
						$r_replace = "(" . implode('&&', $arr_tmp) . ")";
					}
				}
				
				$arr_replace[$r_key] = $r_replace;
				
				$str_in = preg_replace('/'.preg_quote($arr_cmp[$i]).'/', $r_key, $str_in, 1);
			}
		}
        if (!count($arr_replace)) {
            return null;
        }
        
        foreach ($arr_replace as $key=>$value){
            $str_in = str_replace($key, $value, $str_in);
        }
        $str_eval = "return ($str_in);";
//        var_dump($str_eval, @eval($str_eval)); echo '<hr />';
        return @eval($str_eval);
	}
	
	
	

	/**
	 * Get debug infomation
	 *
	 * @return array of request
	 */
	function _getDebugInfo($offsetTimeMax){		
		$current_time = time();
		
		$session =& JFactory::getSession();
		//store request
		if ($session->get('yos_yrm_timestamp') && (($current_time - $session->get('yos_yrm_timestamp')) < 3)) {
			$arr_new_debug = array_merge($session->get('yos_yrm_debug'), array(array('<br />OTHER REQUEST'=>'###################################################################################<br />')));
			$arr_new_debug = array_merge($arr_new_debug, array($_REQUEST));
			$session->set('yos_yrm_debug', $arr_new_debug);
		}
		else {
			$session->set('yos_yrm_debug', array($_REQUEST));
		}
		//store timestamp
		$session->set('yos_yrm_timestamp', $current_time);
	}
	
	function _renderDebugInfo($str_ignoreVar){
		$arr_ignoreVar = explode('||', $str_ignoreVar);
		
		$session =& JFactory::getSession();
		$arr_debug = $session->get('yos_yrm_debug');
		$session->set('yos_yrm_debug', array());
		
		$arr_special_var = array('option', 'view', 'controller', 'task', 'cid', 'id');
		
		//render debug info
		$str_debug = '';
		foreach ($arr_debug as $debug){
			foreach ($debug as $key=>$value){
				if (!in_array($key, $arr_ignoreVar) && !preg_match('/[a-zA-Z0-9]{32}/', $key)) {
					if (is_array($value)) {
						ob_start();
						var_dump($value);
						$value = ob_get_contents();
						ob_end_clean();
					}
					
					if (in_array($key, $arr_special_var)) {
						$str_debug .= '<font color="green">'.$key.'</font> => '.$value.'<br />';
					}
					else{
						$str_debug .= '<font color="blue">'.$key.'</font> => '.$value.'<br />';
					}
				}
			}
		}
		JError::raiseNotice( 'debug', $str_debug);
	}
	
	function get_fullurl() {
		$webune_s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
		$WebuneProtocol = substr(strtolower($_SERVER["SERVER_PROTOCOL"]), 0, strpos(strtolower($_SERVER["SERVER_PROTOCOL"]), "/")) . $webune_s;
		$WebunePort = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
		return $WebuneProtocol . "://" . $_SERVER['SERVER_NAME'] . $WebunePort . $_SERVER['REQUEST_URI'];
	} 
}

