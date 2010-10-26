<?php
/**
* @version		$Id: com_ccboard.php 10214 2009-08-20 08:59:04Z minhna $
* @package YRM
* @copyright	Copyright (C) 2010 YOS.,JSC. All rights reserved.
* @license	GNU/GPL http://www.gnu.org/copyleft/gpl.html
* 
* http://yopensource.com
*/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

global $shMosConfig_locale, $sh_LANG, $mainframe;

// get DB
$database =& JFactory::getDBO();

// V 1.2.4.q must comply with translation restrictions
$shLangName = empty($lang) ? $shMosConfig_locale : shGetNameFromIsoCode( $lang);
$shLangIso = isset($lang) ? $lang : shGetIsoCodeFromName( $shMosConfig_locale);
//$shLangIso = shLoadPluginLanguage( 'com_virtuemart', $shLangIso, '_PHPSHOP_LIST_ALL_PRODUCTS');
//-------------------------------------------------------------

global $shCustomTitleTag, $shCustomDescriptionTag, $shCustomKeywordsTag, $shCustomLangTag, $shCustomRobotsTag;
 
$shCustomLangTag = $shLangIso; // V 1.2.4.t bug #127


