<?php
/**
 * @version		$Id: upgrade.php 189 2008-09-16 08:42:00 sonlv $
 * @package		YOS
 * @subpackage	Upgrade
 * @author 		Sonlv
 * @copyright	Copyright (C) 2010 YOS.,JSC. All rights reserved.
 * @license	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.archive');

// Set the table directory
JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');

// Include class autoupdate
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'autoupdate.php' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'yos_utility.php' );


/**
 * YOS_Upgrade_Controller
 *
 * @package		YOS
 * @subpackage	Upgrade
 * @since 1.5
 */
class YRMControllerUpgrade extends YRMController
{
	function doupdate(){
		global $mainframe;
		// Get/Create the model	
		$checkversion	=	&YOS_utility::getVersion();
		$version	=	$checkversion['version'];
		$url		=	$checkversion['url'];
		$pc			=	$checkversion['productcode'];	

		$config =& JComponentHelper::getParams('com_yos_resources_manager');
		$config_lic= $config->get('licence');		

		$engine		=	new AutoUpdateEngine($url, $pc, $version, JURI::root(), $config_lic);
		
		if (!$autoupdate	=	& $engine->getInstance()) {
			$mainframe->redirect('index.php?option=com_yos_resources_manager&view=version');
			return false;
		}
		
		$autoupdate->upgradeFile();
		$autoupdate->upgradeSql();
		$autoupdate->cleanFileUpdate();
		$mainframe->redirect('index.php?option=com_yos_resources_manager&view=version', $autoupdate->getReport());
	}
	
	function getbackup(){
		global $mainframe;		
		
		if (!JFile::exists(JPATH_COMPONENT_ADMINISTRATOR.DS.'backup'.DS.'backup.php')) {
			JError::raiseWarning(304,'Backup file doesn\'t exist!');
			$mainframe->redirect('index.php?option=com_yos_resources_manager&view=version');
			return false;
		}
		
		$version		=		JRequest::getCmd('version');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'backup'.DS.'backup.php');
		if (!JFile::exists($urlbackup)) {
			JError::raiseWarning(400,'File backup doesn\'t exist!');
			$mainframe->redirect('index.php?option=com_yos_resources_manager&view=version');
			return false;
		}
		
		// do the unpacking of the archive		
		$extractdir		=	JPATH_COMPONENT_ADMINISTRATOR.DS.'backup'.DS.uniqid($version.'_');
		$archivename	=	$urlbackup;
		JFolder::create($extractdir);
		$result = JArchive::extract( $archivename, $extractdir);		
		
		// Get Instance
		$autoupdate	=	new AutoupdateHelper($extractdir.DS.'update.xml',false ,true , false, $extractdir);
		
		$autoupdate->upgradeFile();
		$autoupdate->cleanFileUpdate();
		$mainframe->redirect('index.php?option=com_yos_resources_manager&view=version', $autoupdate->getReport());
	}
	
	function getFileBackup(){
		
		global $mainframe;
		if (!JFile::exists(JPATH_COMPONENT_ADMINISTRATOR.DS.'backup'.DS.'backup.php')) {
			JError::raiseWarning(304,'Backup file doesn\'t exist!');
			$mainframe->redirect('index.php?option=com_yos_resources_manager&view=version');
			return false;
		}
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'backup'.DS.'backup.php');
		if (!JFile::exists($urlbackup)) {
			JError::raiseWarning(400,'File backup doesn\'t exist!');
			$mainframe->redirect('index.php?option=com_yos_resources_manager&view=version');
			return false;
		}
		$mainframe->redirect(preg_replace('/\\\/','/',str_replace(JPATH_ADMINISTRATOR.DS,'',$urlbackup)));
	}

}





?>
