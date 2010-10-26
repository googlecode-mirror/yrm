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
class YRMModelPayment_method extends JModel
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
	
	var $payment_id = null;
	var $payment_enabled = 0;
	var $payment_method_name = '';
	var $payment_method_code = '';
	var $payment_class = '';
	var $is_creditcard = 0;
	var $enable_processor = '';
	var $creditcard = null;
	var $accepted_creditcards = null;
	var $payment_extrainfo = '';
	var $description ='';
	
	var $install_mess='';
	var $install_status=false;
	/**
	 * Constructor
	 *
	 * @since 0.9
	 */
	function __construct()
	{
		$lang					=	JFactory::getLanguage();
		$basePath				=	JPATH_COMPONENT_SITE.DS.'paymentclass';
		$lang->load('payment_method',$basePath,$lang->_lang);
		parent::__construct();
	}
	function getData(){
		$db = $this->_db;
		if ($this->_data) {
			return $this->_data;
		}
		// Lets load the data if it doesn't already exist
		
		$query = 'SELECT * '
			. ' FROM #__yos_resources_manager_payment_method AS a';
		$db->setQuery($query, $this->getState('limitstart'), $this->getState('limit'));		
		$this->_data = $db->loadObjectList();	
		return $this->_data;
	}
	function getTotal(){
		$db	=& JFactory::getDBO();
		
		if ($this->_total) {
			return $this->_total;
		}
		
//		$filter = $this->_build_filter();
//		$where = $this->_build_where();
		
		$query = 'SELECT COUNT(a.id)'
		. ' FROM #__yos_resources_manager_payment_method AS a'
		//. $filter
		//. $where
		;
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
		$limitstart = $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );
		
		if (!$this->_total) {
			$this->getTotal();
		}
		
		$pagination = new JPagination( $this->_total, $limitstart, $limit );
		
		$this->_pagination = $pagination;
		
		return $this->_pagination;
	}
	function getList(){
			
		return $lists;
	}
	function save(){
		$db =& JFactory::getDBO();
		$query = '';
		
		if ($this->payment_id){
			$query .= "UPDATE `#__yos_resources_manager_payment_method` SET \n";			
		}
		else {
			$query .= "INSERT INTO `#__yos_resources_manager_payment_method` SET \n";
		}
		
		$query .= "`name` = '$this->payment_method_name', \n";
		$query .= "`payment_method_code` = '$this->payment_method_code', \n";
		if ($this->payment_class) {
			$query .= "`payment_class` = '$this->payment_class', \n";
		}		
		$query .= "`published` = $this->payment_enabled, \n";
		$query .= "`is_creditcard` = $this->is_creditcard, \n";
		if ($this->description) {
			$this->description=$db->quote($this->description);
			$query .= "`description` =$this->description, \n";		
		}		
		$query .= "`enable_processor` = '$this->enable_processor', \n";
		$query .= "`accepted_creditcards` = '$this->creditcard' \n";		
//		$payment_extrainfo = base64_encode($this->payment_extrainfo).",";
//		$query .= "`payment_extrainfo` = '$payment_extrainfo' \n";
		if ($this->payment_id){
			$query .= "WHERE `id` = $this->payment_id";
		}		
		$db->setQuery($query);
		if(!$db->query()){
			JError::raiseError(500,$db->getErrorMsg());
		}
		
		if (!$this->payment_id) {
			$this->payment_id = $db->insertid();
		}		
		return true;		
	}
	// FOR INSTALL PAYMENT METHOD
	function installPayment()
	{
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.archive');
		jimport('joomla.filesystem.archive.zip');
		
		$db=JFactory::getDBO();
		$name=$_FILES['install_package']['name'];
		if (!$name) {
			$this->install_mess=JText::_('No fle select');
			return false;
		}
		$nameArr=explode('.',$name);
		$ext=$nameArr[1];
		$file=$_FILES['install_package']['tmp_name'];
		// duong dan toi thu muc tam chua goi cai dat
		$pathBase=JPATH_COMPONENT_SITE.DS.'paymentclass'.DS.'tmp';
		// tao duong dan
		JFolder::create($pathBase);
		$filename=$_FILES["install_package"]["name"];
		
		
		// dua goi vua upload den thu muc tam
		JFile::upload($_FILES["install_package"]["tmp_name"],$pathBase.DS.$filename);	
		$result = JArchive::extract( $pathBase.DS.$filename, $pathBase.DS.'class');
//		JFile::delete($pathBase.DS.$filename);
		$mess='';
		if (!$result) {
			$this->install_mess=JText::_('Installation package is not uncompression');
			return $pathBase;
		}
		$fileXml=JFolder::files($pathBase.DS.'class','xml');
		if (!count($fileXml)) {
			$this->install_mess=JText::_('xml file not found');
			return $pathBase;
		}
		$fileXml=$fileXml[0];
		$path=$pathBase.DS.'class'.DS.$fileXml;
		
		$xml = & JFactory::getXMLParser('Simple');
        $xml->loadFile($path);
       	$type=$xml->document->attributes('type');
       	if ($type!='paymentclass') {
       		$this->install_mess=JText::_('Type of installation package is no support');
       		return $pathBase;
       	}
       	// ktra classname
       	if (!isset($xml->document->classname[0])) {
       		$this->install_mess=JText::_('Class name is require in XML file');
       		return $pathBase;
       	}
       	$className=$xml->document->classname[0]->data();
       	 // ktra su ton tai cua folder   
       	if (JFolder::exists(JPATH_COMPONENT_SITE.DS.'paymentclass'.DS.'pcl_'.$className)) {
       		$this->install_mess=JText::_('Payment method is exits');       		
       		return $pathBase;
       	}
       	// tao thu muc cho payment
       	JFolder::create(JPATH_COMPONENT_SITE.DS.'paymentclass'.DS.'pcl_'.$className);
		$result = JArchive::extract( $pathBase.DS.$filename, JPATH_COMPONENT_SITE.DS.'paymentclass'.DS.'pcl_'.$className);
		
		// ktra ten payment
		if (isset($xml->document->name[0])) {
			$methodName=$xml->document->name[0]->data();
			$this->payment_method_name=$methodName;
		}
		else {
			$this->payment_method_name=$className;
		}
		// ktra va gan description
		if (isset($xml->document->description[0])) {
			$description =$xml->document->description[0]->data();
			$this->description =$description;
		}
		$this->payment_class=$className;
		
		// luu database
		$this->save();
		// thong bao thanh cong
		$this->install_mess=JText::_('Install Payment mothod Success');
		// trang thai
		$this->install_status=true;
		return $pathBase;		
	}
	function unInstall()
	{
		global $mainframe,$option;
		
		jimport('joomla.filesystem.folder');
		$db = &JFactory::getDBO();
		$cid = JRequest::getVar('cid');
		$cid_del = implode(',',$cid);
		$mess='';
		for ($i=0;$i<count($cid);$i++)
		{
			$query='SELECT payment_class 
					FROM #__yos_resources_manager_payment_method 
					WHERE id='.$cid[$i];
			$db->setQuery($query);
			$className=$db->loadResult();				
			if (JFolder::exists(JPATH_COMPONENT_SITE.DS.'paymentclass'.DS.'pcl_'.$className)) {
				JFolder::delete(JPATH_COMPONENT_SITE.DS.'paymentclass'.DS.'pcl_'.$className);
			}		
					
			$query = 'DELETE FROM #__yos_resources_manager_payment_method WHERE id ='.$cid[$i];
			$db->setQuery($query);
			if (!$db->query()) {
				JError::raiseError(500, $db->getErrorMsg());
				continue;		
			}
			$query = 'DELETE FROM #__yos_resources_manager_package_payment_method_xref WHERE payment_method_id ='.$cid[$i];
			$db->setQuery($query);
			if (!$db->query()) {
				JError::raiseError(500, $db->getErrorMsg());			
				continue;
			}			
			else {
				$mess=JText::_('Uninstall Payment mothod Success');
			}
		}
		return $mess;
	}
}