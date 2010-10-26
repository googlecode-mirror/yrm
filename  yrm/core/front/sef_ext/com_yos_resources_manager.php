<?php
/**
* @version		$Id: com_ccboard.php 10214 2009-08-20 08:59:04Z minhna $
* @package namechange component
* @copyright	Copyright (C) 2010 YOS.,JSC. All rights reserved.
* @license	GNU/GPL http://www.gnu.org/copyleft/gpl.html
* 
* http://yopensource.com
*/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

if(!function_exists('get_package_name')){
	function get_package_name($pid = 0) {
		$db =& JFactory::getDBO();
		$query = "SELECT `name` FROM #__yos_resources_manager_package WHERE id = $pid";
		$db->setQuery($query);
		return $db->loadResult();
	}
}

// ------------------  standard plugin initialize function - don't change ---------------------------
global $sh_LANG;
$sefConfig = & shRouter::shGetConfig();  
$shLangName = '';
$shLangIso = '';
$title = array();
$shItemidString = '';
$dosef = shInitializePlugin( $lang, $shLangName, $shLangIso, $option);
if ($dosef == false) return;
// ------------------  standard plugin initialize function - don't change ---------------------------

$task = isset($task) ? @$task : null;
$Itemid = isset($Itemid) ? @$Itemid : null;

$title[] = 'resources manager';

if (isset($controller)){
	
}

if (isset($task)){
	$tmp_title = '';
	switch ($task){
		case 'package.checkout':
			$tmp_title = 'checkout';
			if(isset($packageid)){
				$package_name = get_package_name($packageid);
				if($package_name){
					$tmp_title .= '/' . $package_name;
				}
				shRemoveFromGETVarsList('packageid');
			}
			break;
		case 'user.updateinfo':
			$tmp_title = 'update information';
			break;
		case 'user.save':
			$tmp_title = 'save information';
			break;
		case 'package.confirm':
			$tmp_title = 'confirm checkout';
			break;
		
		default:
			break;
	}
	if ($tmp_title) {
		$title[] = $tmp_title;
		shRemoveFromGETVarsList('option');
		shRemoveFromGETVarsList('task');
	}
}
else {
	if (isset($view)) {
		$tmp_title = '';
		switch ($view){
			case 'package':
				$tmp_title = 'packages';
				if(isset($show) && $show == 1){
					$tmp_title = 'all packages';
					shRemoveFromGETVarsList('show');
				}
				break;
		}
		if ($tmp_title) {
			$title[] = $tmp_title;
			shRemoveFromGETVarsList('option');
			shRemoveFromGETVarsList('view');	
		}
	}
}

if (!empty($Itemid)){
	shRemoveFromGETVarsList('Itemid');
}
shRemoveFromGETVarsList('lang');
 
// ------------------  standard plugin finalize function - don't change ---------------------------  
if ($dosef){
   $string = shFinalizePlugin( $string, $title, $shAppendString, $shItemidString, 
      (isset($limit) ? @$limit : null), (isset($limitstart) ? @$limitstart : null), 
      (isset($shLangName) ? @$shLangName : null));
}      
// ------------------  standard plugin finalize function - don't change ---------------------------	

