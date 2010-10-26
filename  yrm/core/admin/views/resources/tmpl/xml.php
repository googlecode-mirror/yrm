<?php defined('_JEXEC') or die('Restricted access');
header('Content-Type: text/xml');
echo '<?xml version="1.0"?>'."\n\n";
echo "<nodes>\n";
echo $this->xml;
echo "</nodes>";
die();
