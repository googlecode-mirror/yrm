<?php
/**
 * @copyright	Copyright (C) 2010 YOS.,JSC. All rights reserved.
 * @license	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

// Set the table directory
JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');

/**
 * HTML View class for the Jlord_amMap component
 *
 * @static
 * @package		Joomla
 * @subpackage	Jlord_checkversion
 * @since 1.0
 */
class YRMViewUser extends JView
{
	function display($tpl=null){
		global $mainframe;
		$package = JRequest::getVar('package');
		
		$user = JFactory::getUser();
		if (!$user->id) {
			$mainframe->redirect('index.php',JText::_('YOU_MUST_BE_LOGIN'));
		}			
		$model	= $this->getModel('user');	
	
		$data	= $model->getData();	
	
		$this->assign('data', $data);		
		$this->assign('package', $package);			
		
		parent::display($tpl);
	}	
}

?>