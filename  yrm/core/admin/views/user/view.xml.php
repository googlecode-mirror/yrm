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
class YRMViewUser extends JView
{
	function display($tpl = null){
		global $mainframe;
		
		$user_id = JRequest::getInt('uid', 0);
		
		$model =& $this->getModel('user');
		$model->load($user_id);
		
		$root_id = JRequest::getInt('root', 0);
		$task = JRequest::getVar('task');
		if ($task == 'xmlrsbtree') {
			$xml = $model->getRSBTreeXML($root_id);
		}else{
			$xml = $model->getTreeXML($root_id);
		}
		
		$this->assign('xml',$xml);
		
		parent::display();
	}
}