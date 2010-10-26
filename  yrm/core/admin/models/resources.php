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
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.archive');
/**
 * Weblinks Component Weblink Model
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class YRMModelResources extends JModel
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
	
	var $_arrCids=array();
	
	var $import_mess='';
	
	var $res_id 				= 0;
	var $res_parent_id 			= 0;
	var $res_name 				= '';
	var $res_affected			= 'F';
	var $res_type 				= '';
	var $res_option 			= '';
	var $res_task 				= '';
	var $res_view				= '';
	var $res_params				= '';
	var $res_plug_in			= 0;
	var $res_redirect_url		= '';
	var $res_redirect_message	= '';
	var $res_description		= '';
	var $res_sticky				= 1;
	var $res_published			= 1;

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
		$db =& JFactory::getDBO();
		
		//load the first level
		$query = "SELECT * FROM `#__yos_resources_manager_resource` WHERE `id` = $id";
		$db->setQuery($query);
		$obj_res = $db->loadObject();
		if ($obj_res) {
			$this->res_id = $obj_res->id;
			$this->res_parent_id = $obj_res->parent_id;
			$this->res_name = $obj_res->name;
			$this->res_affected = $obj_res->affected;
			$this->res_type = $obj_res->type;
			$this->res_option = $obj_res->option;
			$this->res_task = $obj_res->task;
			$this->res_view = $obj_res->view;
			$this->res_params = $obj_res->params;
			$this->res_plug_in = $obj_res->plug_in;
			$this->res_redirect_url = $obj_res->redirect_url;
			$this->res_redirect_message = $obj_res->redirect_message;
			$this->res_description = $obj_res->description;
			$this->res_sticky = $obj_res->sticky;
			$this->res_published = $obj_res->published;
		}		
	}
	
	function save(){
		$db =& JFactory::getDBO();
		
		$query = '';
		if ($this->res_id){
			$query .= "UPDATE `#__yos_resources_manager_resource` SET \n";
		}
		else {
			$query .= "INSERT INTO `#__yos_resources_manager_resource` SET \n";
		}
		$query .= "`parent_id` = $this->res_parent_id, \n";
		$query .= "`name` = '". $db->getEscaped($this->res_name) ."', \n";
		$query .= "`affected` = '$this->res_affected', \n";
		$query .= "`type` = '$this->res_type', \n";
		$query .= "`option` = '$this->res_option', \n";
		$query .= "`task` = '". $db->getEscaped($this->res_task) . "', \n";
		$query .= "`view` = '". $db->getEscaped($this->res_view) ."', \n";
		$query .= "`params` = '". $db->getEscaped($this->res_params) . "', \n";
		$query .= "`plug_in` = $this->res_plug_in, \n";
		$query .= "`redirect_url` = '". $db->getEscaped($this->res_redirect_url) ."', \n";
		$query .= "`redirect_message` = '". $db->getEscaped($this->res_redirect_message) . "', \n";
		$query .= "`description` = '". $db->getEscaped($this->res_description) ."', \n";
		$query .= "`sticky` = $this->res_sticky, \n";
		$query .= "`published` = $this->res_published \n";
		if ($this->res_id){
			$query .= "WHERE `id` = $this->res_id";
		}
		
		$db->setQuery($query);
		if(!$db->query()){
			JError::raiseError(500,$db->getErrorMsg());
		}
		
		if (!$this->res_id) {
			$this->res_id = $db->insertid();
		}
		
		return $db->getAffectedRows();
	}
	
	function sticky_roles(){
		$db =& JFactory::getDBO();
		//select all roles which has this parent resource
		$query = "SELECT role_id FROM #__yos_resources_manager_resource_role_xref
			WHERE resource_id = $this->res_parent_id";
		$db->setQuery($query);var_dump($db->getQuery());
		$arr_roles = $db->loadResultArray();
		if (count($arr_roles) == 0) {
			return true;
		}
		
		//insert to resource_role_xref table
		foreach ($arr_roles as $role_id){
			$query = "INSERT INTO #__yos_resources_manager_resource_role_xref SET
				`resource_id` = $this->res_id,
				`role_id` = $role_id";
			$db->setQuery($query);
			if(!$db->query()){
				JError::raiseError(500,$db->getErrorMsg());
			}
		}
	}
	
	function sticky_users(){
		$db =& JFactory::getDBO();
		
		//select all users which has this parent resource
		$query = "SELECT * FROM #__yos_resources_manager_user_resource_xref 
			WHERE resource_id = $this->res_parent_id";
		$db->setQuery($query);
		$arr_obj_users = $db->loadObjectList();
		
		if (count($arr_obj_users) == 0) {
			return true;
		}
		
		//insert to user_resource_xref
		foreach ($arr_obj_users as $obj_user){
			$query = "INSERT INTO #__yos_resources_manager_user_resource_xref SET
				`user_id` = $obj_user->user_id,
				`resource_id` = $this->res_id,
				`times_access` = $obj_user->times_access,
				`start` = '$obj_user->start',
				`end` = '$obj_user->end'";
			$db->setQuery($query);
			if(!$db->query()){
				JError::raiseError(500,$db->getErrorMsg());
			}
		}
	}
	
	function unpublish($id = 0){
		$db =& JFactory::getDBO();
		
		$query = "UPDATE `#__yos_resources_manager_resource` SET `published` = 0 WHERE `id` = $id";
		$db->setQuery($query);
		if(!$db->query()){
			JError::raiseError(500,$db->getErrorMsg());
		}
		
		return $db->getAffectedRows();
	}
	
	function publish($id = 0){
		$db =& JFactory::getDBO();
		$query = "UPDATE `#__yos_resources_manager_resource` SET `published` = 1 WHERE `id` = $id";
		$db->setQuery($query);
		if(!$db->query()){
			JError::raiseError(500,$db->getErrorMsg());
		}
		
		return $db->getAffectedRows();
	}
	
	function remove($id = 0){
		$db =& JFactory::getDBO();
		
		//select children
		$query = "SELECT `id` FROM `#__yos_resources_manager_resource` WHERE `parent_id` = $id";
		$db->setQuery($query);
		$arr_child = $db->loadResultArray();
		if (count($arr_child)) {
			//select resource name
			$query = "SELECT `name` FROM `#__yos_resources_manager_resource` WHERE `id` = $id";
			$db->setQuery($query);
			$res_name = $db->loadResult();
			JError::raiseWarning('RESOURCES_TREE_REMOVE_HAS_CHILD', JText::sprintf('RESOURCES_TREE_REMOVE_HAS_CHILD', $res_name));
			return 0;
		}
		
		//delete from resourece_banned
		$query = "DELETE FROM #__yos_resources_manager_user_resource_banned WHERE `resource_id` = $id";
		$db->setQuery($query);
		if(!$db->query()){
			JError::raiseError(500,$db->getErrorMsg());
		}
		
		//delete from resource_role_xref
		$query = "DELETE FROM #__yos_resources_manager_resource_role_xref WHERE `resource_id` = $id";
		$db->setQuery($query);
		if(!$db->query()){
			JError::raiseError(500,$db->getErrorMsg());
		}
		
		//delete from user_resource_xref
		$query = "DELETE FROM #__yos_resources_manager_user_resource_xref WHERE `resource_id` = $id";
		$db->setQuery($query);
		if(!$db->query()){
			JError::raiseError(500,$db->getErrorMsg());
		}
		
		//delete from package_object_xref
		$query = "DELETE FROM #__yos_resources_manager_package_object_xref WHERE `object_id` = $id AND `type` = 'resource'";
		$db->setQuery($query);
		if(!$db->query()){
			JError::raiseError(500,$db->getErrorMsg());
		}
		
		//delete this
		$query = "DELETE FROM `#__yos_resources_manager_resource` WHERE `id` = $id";
		$db->setQuery($query);
		if(!$db->query()){
			JError::raiseError(500,$db->getErrorMsg());
		}
		
		$deleted = $db->getAffectedRows();
		
		return $deleted;
	}
	
	function getList(){
		global $mainframe;
		$db =& JFactory::getDBO();
		
		$list = array();
		
		//components
		$component[] = JHTML::_('select.option', '0', JText::_('RESOURCES_FORM_OPTION_SELECT'));
		$listFront = JFolder::folders(JPATH_COMPONENT_SITE.DS.'../');
		$listBack = JFolder::folders(JPATH_COMPONENT_ADMINISTRATOR.DS.'../');
		
		//get the items those existing in fron-end but not back-end
		$diff = array_diff($listFront, $listBack);
		//meger these item with list back-end
		$components = array_merge($diff, $listBack);
		sort($components);
				
		$arr_obj_component = array();
		
		//add the first select
		$obj_component = new stdClass();
		$obj_component->value = '';
		$obj_component->text = JText::_('RESOURCES_FORM_OPTION_SELECT');
		array_push($arr_obj_component, $obj_component);
		
		foreach ($components as $component){
			$obj_component = new stdClass();
			$obj_component->value = $component;
			$obj_component->text = $component;
			array_push($arr_obj_component, $obj_component);
		}
		
		//list of component		
		$list['component'] = JHTML::_('select.genericlist',  $arr_obj_component, 'res_option', 'class="inputbox" size="1" ', 'value', 'text', $this->res_option);
		
		//affected domain
		$arr_obj_affected = array();
		$obj_affected = new stdClass();
		$obj_affected->value = 'B'; $obj_affected->text = JText::_('RESOURCES_FORM_AFFECTED_VALUE_B');
		array_push($arr_obj_affected, $obj_affected);
		$obj_affected = new stdClass();
		$obj_affected->value = 'F'; $obj_affected->text = JText::_('RESOURCES_FORM_AFFECTED_VALUE_F');
		array_push($arr_obj_affected, $obj_affected);
		$obj_affected = new stdClass();
		$obj_affected->value = 'BF'; $obj_affected->text = JText::_('RESOURCES_FORM_AFFECTED_VALUE_BF');
		array_push($arr_obj_affected, $obj_affected);
		
		//list affected
		$list['affected'] = JHTML::_('select.radiolist', $arr_obj_affected, 'res_affected', 'class="radiobox"', 'value', 'text', $this->res_affected);
		
		//resource type
		$arr_obj_type = array();
		$obj_type = new stdClass();
		$obj_type->value = 'request'; $obj_type->text = JText::_('RESOURCES_FORM_TYPE_VALUE_REQUEST');
		array_push($arr_obj_type, $obj_type);
		$obj_type = new stdClass();
		$obj_type->value = 'module'; $obj_type->text = JText::_('RESOURCES_FORM_TYPE_VALUE_MODULE');
		array_push($arr_obj_type, $obj_type);
		$obj_type = new stdClass();
		$obj_type->value = 'menu'; $obj_type->text = JText::_('RESOURCES_FORM_TYPE_VALUE_MENU');
		array_push($arr_obj_type, $obj_type);
		$obj_type = new stdClass();
		$obj_type->value = 'label'; $obj_type->text = JText::_('RESOURCES_FORM_TYPE_VALUE_LABEL');
		array_push($arr_obj_type, $obj_type);
		
		//list type
		$list['type'] = JHTML::_('select.genericlist', $arr_obj_type, 'res_type', 'class="inputbox" size="1"', 'value', 'text', $this->res_type);

		
		//list parent node option
		$arr_obj_parent = array();
		$obj_parent_select = new stdClass();
		$obj_parent_select->value = 0;
		$obj_parent_select->text = '----'. JText::_('RESOURCES_TREE_ROOT_NODE') . '----';
		array_push($arr_obj_parent, $obj_parent_select);
		
		$cmd = JRequest::getVar('task');
		$cid = JRequest::getVar('cid');
		if ($cmd == 'edit') {
			$arr_obj_parent = array_merge($arr_obj_parent, $this->_getTreeOption(0, $this->res_id));
		}
		else {
			//new
			$arr_obj_parent = array_merge($arr_obj_parent, $this->_getTreeOption(0));
		}
		
		//list of parent node
		$list['parent'] = JHTML::_('select.genericlist',  $arr_obj_parent, 'res_parent_id', 'class="inputbox" size="1" ', 'value', 'text', $this->res_parent_id);
		
		//list of plug-in
		$arr_obj_plugin = array();
		$obj_plugin_select = new stdClass();
		$obj_plugin_select->value = 0;
		$obj_plugin_select->text = JText::_('RESOURCES_FORM_PLUGIN_SELECT');
		array_push($arr_obj_plugin, $obj_plugin_select);
		
		$query = "SELECT `id` AS 'value', `name` AS 'text' FROM `#__plugins` WHERE `folder` = 'yrm'";
		$db->setQuery($query);
		$arr_obj_plugin_db = $db->loadObjectList();
		for ($i = 0; $i < count($arr_obj_plugin_db); $i++){
			array_push($arr_obj_plugin, $arr_obj_plugin_db[$i]);
		}
		
		//list of parent node
		$list['plugin'] = JHTML::_('select.genericlist',  $arr_obj_plugin, 'res_plug_in', 'class="inputbox" size="1" ', 'value', 'text', $this->res_plug_in);
		
		return $list;
	}
	
	function getTree()
	{
		
		return 'tree';
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
		
		//load the first level
		$query = "SELECT * FROM `#__yos_resources_manager_resource` WHERE `parent_id` = $root_id ORDER BY `name` ASC";
		$db->setQuery($query);
		$arr_obj_res = $db->loadObjectList();
		if (!$arr_obj_res) {
			return '';
		}		
		foreach ($arr_obj_res as $obj_res){
			for ($i = 0; $i < $level; $i++){ $return .= "\t";	}
		
			$plug_in=$this->id2element($obj_res->plug_in);
			
			$return .= "<node text=\"".htmlspecialchars($obj_res->name)."\" open=\"true\" ";
			
			$return .= "checked=\"false\" ";
			$return .= "id=\"".htmlspecialchars($obj_res->id)."\" ";
			$return .= "published=\"".htmlspecialchars($obj_res->published)."\" ";
			$return .= "type=\"".htmlspecialchars($obj_res->type)."\" ";
			$return .= "option=\"".htmlspecialchars($obj_res->option)."\" ";
			$return .= "task=\"".htmlspecialchars($obj_res->task)."\" ";
			$return .= "view=\"".htmlspecialchars($obj_res->view)."\" ";
			$return .= "params=\"".htmlspecialchars($obj_res->params)."\" ";
			$return .= "plug_in=\"".htmlspecialchars($plug_in)."\" ";			
			$return .= "redirect_url=\"".htmlspecialchars($obj_res->redirect_url)."\" ";
			$return .= "redirect_message=\"".htmlspecialchars($obj_res->redirect_message)."\" ";
			$return .= "description=\"".htmlspecialchars($obj_res->description)."\" ";
			$return .= "sticky=\"".htmlspecialchars($obj_res->sticky)."\" ";
			$return .= "affected=\"".htmlspecialchars($obj_res->affected)."\" ";
			
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
	/**
	 * Return list of options
	 *
	 * @param int $root_id the root of option tree
	 * @param int $selected_id the selected value
	 * @param int $exclude_id this resource will not appear in the option list
	 * @param int $level number of tab, do not set this value
	 */
	function _getTreeOption($root_id = 0, $exclude_id = -1, $level = 0){
		global $mainframe;
		
		$return = '';
			
		$db =& JFactory::getDBO();
		
		//load the first level
		$query = "SELECT * FROM `#__yos_resources_manager_resource` WHERE `parent_id` = $root_id AND `id` <> $exclude_id 
			ORDER BY `name` ASC";
		$db->setQuery($query);
		$arr_obj_res = $db->loadObjectList();
		$arr_obj_res_2 = array();
		if (!$arr_obj_res) {
			return $arr_obj_res_2;
		}
		foreach ($arr_obj_res as $obj_res){
			$obj_option = new stdClass();
			$obj_option->value = $obj_res->id;
			$tab = '';
			for ($i = 0; $i < $level; $i++){ $tab .= "&nbsp;&nbsp;&nbsp;&nbsp;";	}
			$obj_option->text = $tab . $obj_res->name;
			array_push($arr_obj_res_2, $obj_option);
			$arr_obj_res_2 = array_merge($arr_obj_res_2, $this->_getTreeOption($obj_res->id, $exclude_id, $level+1));
		}
		
		return $arr_obj_res_2;
	}
	
	function getNodeInfo($node_id = 0){
		$db =& JFactory::getDBO();
		
		//load the first level
		$query = "SELECT * FROM `#__yos_resources_manager_resource` WHERE `id` = $node_id";
		$db->setQuery($query);
		$obj_res = $db->loadObject();
		return $obj_res;
	}
	
	function getDescription_plg($plgid){
		$db 	= &JFactory::getDBO();
		$row 	=& JTable::getInstance('plugin');
		// load the row from the db table
		$row->load( $plgid );
		
		$lang =& JFactory::getLanguage();
		$lang->load( 'plg_' . trim( $row->folder ) . '_' . trim( $row->element ), JPATH_ADMINISTRATOR );

		$data = JApplicationHelper::parseXMLInstallFile(JPATH_SITE . DS . 'plugins'. DS .$row->folder . DS . $row->element .'.xml');

		$row->description = JText::_($data['description']);
		return $row;
	}
	/**
	 * get file zip
	 *
	 */
	function export()
	{
		$path_base 				= JPATH_COMPONENT_SITE.DS.'resource';
		if (JFolder::exists($path_base)) {
			JFolder::delete($path_base);
		}
		JFolder::create($path_base);
					
		$content=$this->getXML();
				
		$date=JFactory::getDate();		
		$date=$date->toFormat('%Y_%m_%d_%H_%M_%S');		
		$filename='YRM_resources_'.$date;	
			
		$path_file=$path_base.DS.$filename;
		
		$xml_file 	= $path_file.'.xml';
		JFile::write($xml_file, $content);
		
		$fileinfos				=	array();
		$fileinfos[0]['data']	=	JFile::read($xml_file);
		$fileinfos[0]['name']	=	'resource.xml';
		
		require_once(JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'filesystem'.DS.'archive'.DS.'zip.php');
		$azip 					= 	new JArchiveZip();		
		$azip->create($path_file.'.zip', $fileinfos);		
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: public");
    	header("Content-Description: File Transfer");
		header("Content-Type: application/zip");
		header("Content-Disposition: attachment; filename=".basename($path_file.".zip").";");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($path_file.".zip"));
		@readfile($path_file.".zip");
		exit();
	}
	/**
	 * get xml
	 *
	 * @return string
	 */
	function getXML()
	{		
		$cids				=JRequest::getVar('cid','','','array');
		$this->_arrCids=$cids;	
		header('Content-Type: text/xml');
		ob_start();
		echo '<?xml version="1.0"?>'."\n\n";
		echo '<import type="YRM">'." \n";
		echo "<nodes>\n";
		$data=$this->getTreeXmlExport();
		echo $data;
		echo "</nodes>\n";
		echo "</import>";
		$content				=ob_get_contents();
		ob_end_clean();		
		return $content;
	}
	
	function getTreeXmlExport($root_id = 0, $level = 0)
	{
		global $mainframe;		
		$return = '';		
		$db =& JFactory::getDBO();
		
		//load the first level
		$query = "SELECT * FROM `#__yos_resources_manager_resource` WHERE `parent_id` = $root_id ORDER BY `name` ASC";
		$db->setQuery($query);
		$arr_obj_res = $db->loadObjectList();
			
		if (!$arr_obj_res) {
			return '';
		}		
		
		foreach ($arr_obj_res as $obj_res){
			for ($i = 0; $i < $level; $i++){ $return .= "\t";	}
			if (in_array($obj_res->id,$this->_arrCids)) {			
				
				$plug_in=$this->id2element($obj_res->plug_in);			
				$return .= "<node text=\"".htmlspecialchars($obj_res->name)."\" open=\"true\" ";				
				$return .= "checked=\"false\" ";
				$return .= "id=\"".htmlspecialchars($obj_res->id)."\" ";
				$return .= "parent_id=\"".htmlspecialchars($obj_res->parent_id)."\" ";
				$return .= "published=\"".htmlspecialchars($obj_res->published)."\" ";
				$return .= "type=\"".htmlspecialchars($obj_res->type)."\" ";
				$return .= "option=\"".htmlspecialchars($obj_res->option)."\" ";
				$return .= "task=\"".htmlspecialchars($obj_res->task)."\" ";
				$return .= "view=\"".htmlspecialchars($obj_res->view)."\" ";
				$return .= "params=\"".htmlspecialchars($obj_res->params)."\" ";
				$return .= "plug_in=\"".htmlspecialchars($plug_in)."\" ";
				$return .= "redirect_url=\"".htmlspecialchars($obj_res->redirect_url)."\" ";
				$return .= "redirect_message=\"".htmlspecialchars($obj_res->redirect_message)."\" ";
				$return .= "description=\"".htmlspecialchars($obj_res->description)."\" ";
				$return .= "sticky=\"".htmlspecialchars($obj_res->sticky)."\" ";
				$return .= "affected=\"".htmlspecialchars($obj_res->affected)."\" ";			
			}	
			
			$child = $this->getTreeXmlExport($obj_res->id, $level+1);
			
			if (in_array($obj_res->id, $this->_arrCids)) {
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
			else {
				$return .= $child;
			}			
		}		
		return $return;
	}
	function toDatabase()
	{
		global $option;
		
		$db=JFactory::getDBO();
		$name=$_FILES['file_import']['name'];
		if (!$name) {
			$this->install_mess=JText::_('No fle select');
			return false;
		}
		
		// duong dan toi thu muc tam chua goi cai dat
		$pathBase=JPATH_COMPONENT_SITE.DS.'resource';
		// tao duong dan
		if (JFolder::exists($pathBase)) {
			JFolder::delete($pathBase);
		}
		JFolder::create($pathBase);		
		
		// dua goi vua upload den thu muc tam
		JFile::upload($_FILES['file_import']['tmp_name'],$pathBase.DS.$name);
		
		$result = JArchive::extract( $pathBase.DS.$name, $pathBase);			
		$mess='';
		if (!$result) {
			$this->import_mess=JText::_('Import package is not uncompression');
			return $pathBase;
		}	
		$mess='';
		
		$fileXml=JFolder::files($pathBase.DS,'xml');
		if (!count($fileXml)) {
			$this->import_mess=JText::_('xml file not found');
			return $pathBase;
		}
		$fileXml=$fileXml[0];
		$path=$pathBase.DS.$fileXml;
		
		$xml = & JFactory::getXMLParser('Simple');
        $xml->loadFile($path);
       	$type=$xml->document->attributes('type');
       	if ($type!='YRM') {
       		$this->import_mess=JText::_('Type of filee is no support');
       		return $pathBase;
       	}
		$nodes=$xml->document->nodes[0]->node;
		$cid=JRequest::getVar('cid',0,'','int');
	
		foreach ($nodes as $node)
		{
			$this->insert($cid,$node);
		}
		return $pathBase;
		
	}
	function insert($parent,$node)
	{		
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_yos_resources_manager'.DS.'tables');
		$rrow = &JTable::getInstance('resource','table');		
		
		$plug_in=$node->attributes('plug_in');
		$plug_in=$this->element2id($plug_in);
		
		$rrow->name=$node->attributes('text');
		$rrow->parent_id=$parent;
		$rrow->affected=$node->attributes('affected');
		$rrow->type=$node->attributes('type');
		$rrow->option=$node->attributes('option');
		$rrow->plug_in=$plug_in;		
		$rrow->task=$node->attributes('task');
		$rrow->view=$node->attributes('view');
		$rrow->params=$node->attributes('params');
		$rrow->plug_in=$plug_in;
		$rrow->redirect_url=$node->attributes('redirect_url');
		$rrow->redirect_message=$node->attributes('redirect_message');
		$rrow->sticky=$node->attributes('sticky');
		$rrow->published=$node->attributes('published');
		$rrow->store();
		if (isset($node->node)) {
			$nodeChilds=$node->node;
			foreach ($nodeChilds as $nodeChild)
			{
				$this->insert($rrow->id,$nodeChild);
			}
		}
		else {
			return true;
		}
		
	}
	// FUNCTION CONVERT FOR PLUGIN
	function element2id($elementName)
	{
		$db=JFactory::getDBO();
		$query='SELECT id 
				FROM #__plugins 
				WHERE folder='.$db->quote('yrm').'
				and element='.$db->quote($elementName);
		$db->setQuery($query);		
		$id=$db->loadResult();		
		return $id;
	}	
	function id2element($id)
	{
		$db=JFactory::getDBO();
		$query='SELECT element 
				FROM #__plugins 
				WHERE id='.$id;
		$db->setQuery($query);
		$elementName=$db->loadResult();		
		return $elementName;
	}
}
