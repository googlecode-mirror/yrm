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
class YRMViewSample extends JView
{
	function display($tpl = null){
		global $mainframe;
		
		$this->_setToolBar();
		$option		= JRequest::getCmd( 'option' );	
			
		$say 		= $this->get('Sample');		
		
		$this->assign('say',$say);	
			
		parent::display();
		
		echo JHTML::_('behavior.keepalive');
	}
	
	function _setToolBar()
	{
		JToolBarHelper::title( JText::_( 'Sample' ), 'addedit.png' );		
	}
}