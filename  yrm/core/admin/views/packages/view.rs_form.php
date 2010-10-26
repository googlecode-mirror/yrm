<?php
/**
 * @version	$Id: view.html.php $
 * @package	YRM
 * @subpackage	Component
 * @copyright	Copyright (C) 2010 YOS.,JSC. All rights reserved.
 * @license	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

// Set the table directory
JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');

/**
 * HTML View class for the YRM component
 *
 * @static
 * @package	YRM
 * @subpackage	Component
 * @since 1.0
 */
class YRMViewPackages extends JView
{
	function display($tpl = null){
		global $mainframe, $option;
		
		$package_id = JRequest::getVar('package_id');
		$rid = JRequest::getVar('rid');
		$model =& $this->getModel('packages');
		$model->load($package_id);
		$obj_resource_xref = $model->getResource($rid);
		
		$this->assignRef('res_xref', $obj_resource_xref);
		parent::display();
	}
}