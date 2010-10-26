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
class YRMViewResources extends JView
{
	function display($tpl = null){
		
		global $mainframe;
		$text = JText::_( 'Import' );
		JRequest::setVar('hidemainmenu', 1);
		JToolBarHelper::title(   JText::_( 'Resources' ).': <small><small>[ ' . $text.' ]</small></small>' ,'import');
		JToolBarHelper::save('toDatabase');
		JToolBarHelper::cancel('cancelImport');
		$cids=JRequest::getVar('cid','0','','array');
		
		$resource =& $this->getModel('resources');
		$resource->load(intval($cids[0]));
		$this->assignRef('resource', $resource);
		parent::display();
	}
}