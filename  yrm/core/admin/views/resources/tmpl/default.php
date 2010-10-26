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

$xmlUrl = "index.php?option=com_yos_resources_manager&controller=resources&task=xmltree";
ob_start();
?>
<script type="text/javascript">
var tree;
window.onload = function() {
	//trying to disable checkall button firstly
	$('toggle_btm').disabled = true;

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
	
	////////////////////////////////////////////////////
	
	var init_checkall_btn = (function(){
		//toggle check/uncheck all
		$('toggle_btm').addEvent('click', function(e){
			if($('boxchecked').get('value') == 0){
				$$('.yos_tree_checkbox').each(function(el){
					el.checked = true;
				});
				$('boxchecked').set('value', 1);
			}
			else{
				$$('.yos_tree_checkbox').each(function(el){
					el.checked = false;
				});
				$('boxchecked').set('value', 0);
			}
		});
		//enable button
		$('toggle_btm').disabled = false;
	});
	
	//wait until tree is loaded
	var periodical;
	var wait_tree_loaded = (function() {
		if(tree.root.loading != 'true'){
			$clear(periodical);
			init_checkall_btn();
		}
	});
	periodical = wait_tree_loaded.periodical(500, this); 
}
</script>
<?php
$javaScript = ob_get_contents();
ob_end_clean();
$mainframe->addCustomHeadTag($javaScript);
//$document->addScriptDeclaration($javaScript);

echo "<h3>$this->say</h3>";
?>
<form method="post" id="adminForm" name="adminForm" action="">
<div id="mytree">
</div>

<p>
	<input type="button" value=" expand all " onclick="tree.expand()" />
	<input type="button" value=" collapse all " onclick="tree.collapse()" />
	<input type="button" value=" Check all " id="toggle_btm" />
</p>

<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="resources" />
<input type="hidden" name="boxchecked" value="0" id="boxchecked" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
<div align="center">
<table class="yrm_comment">
<tr>
	<td><img src="components/com_yos_resources_manager/assets/images/affected_F.png" class="yos_tree_img" title="Affected domain"/></td>
	<td><?php echo JText::_('COMMENT_AFFECTED_DOMAIN_F'); ?></td>
	<td><img src="components/com_yos_resources_manager/assets/images/affected_B.png" class="yos_tree_img" title="Affected domain"/></td>
	<td><?php echo JText::_('COMMENT_AFFECTED_DOMAIN_B'); ?></td>
	<td><img src="components/com_yos_resources_manager/assets/images/affected_BF.png" class="yos_tree_img" title="Affected domain"/></td>
	<td><?php echo JText::_('COMMENT_AFFECTED_DOMAIN_BF'); ?></td>
</tr>
<tr>
	<td><img src="components/com_yos_resources_manager/assets/images/tick.png" class="yos_tree_img" title="status"/></td>
	<td><?php echo JText::_('COMMENT_RES_STATUS_PUBLISHED'); ?></td>
	<td><img src="components/com_yos_resources_manager/assets/images/publish_x.png" class="yos_tree_img" title="status"/></td>
	<td colspan="3"><?php echo JText::_('COMMENT_RES_STATUS_UNPUBLISHED'); ?></td>
</tr>
</table>
</div>