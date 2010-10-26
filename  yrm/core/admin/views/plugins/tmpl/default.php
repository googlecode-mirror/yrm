<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php //JHTML::_('behavior.tooltip');  ?>
<div align="center">
<?php echo JText::sprintf('PLUGIN_REDIRECT_MSG', 3); ?>
</div>
<?php
$newUrl = JURI::root() . 'administrator/index.php?option=com_plugins&filter_type=yrm';
?>
<script type="text/javascript">
function redirect(){
    window.location = "<?php echo $newUrl; ?>";
}

setTimeout('redirect()', 3000);
</script>

