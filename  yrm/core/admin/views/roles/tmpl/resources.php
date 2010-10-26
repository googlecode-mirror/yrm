<?php defined('_JEXEC') or die('Restricted access');
JHTML::stylesheet( 'mootree.css', 'administrator/components/com_yos_resources_manager/assets/css/' );
$document =& JFactory::getDocument();
$document->addScript('components/com_yos_resources_manager/assets/js/mootools-1.2-core.js');
$document->addScript('components/com_yos_resources_manager/assets/js/mootree.js');
//disable mootools 1.1
$new_script = array();
foreach ($document->_scripts as $key => $value){
	if (!preg_match('/mootools.js/', $key)) {
		$new_script[$key] = $value;
	}
}
$document->_scripts = $new_script;
$xmlUrl = 'index.php?option=' . $this->option . '&controller=roles&task=xmltree&role_id=' . $this->role_id;
ob_start();

?>
var tree;
window.onload = function() {
	tree = new MooTreeControl(
		{
			div: 'mytree',
			mode: 'files',
			grid: true,
			onSelect: function(node, state) {
				if (state && node.data.alert) window.alert(node.data.alert);
			}
		},
		{
			text: '<?php echo $this->root_node; ?>',
			open: true
		}
	);
	tree.root.load('<?php echo $xmlUrl; ?>');	
}
<?php
$javaScript = ob_get_contents();
ob_end_clean();

$document->addScriptDeclaration($javaScript);
?>
<form method="post" id="adminForm" name="adminForm" action="">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td>
	<?php echo "<h3>$this->say</h3>"; ?>
	<div id="mytree">
	</div>
	
	<p>
		<input type="button" value=" expand all " onclick="tree.expand()" />
		<input type="button" value=" collapse all " onclick="tree.collapse()" />
	</p>
	
	<input type="hidden" name="role_id" value="<?php echo $this->role_id; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="roles" />
	<input type="hidden" name="view" value="roles" />
	<input type="hidden" name="boxchecked" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
	</form>
	</td>
</tr>
</table>