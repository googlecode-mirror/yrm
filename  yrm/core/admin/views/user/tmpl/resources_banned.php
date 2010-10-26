<?php defined('_JEXEC') or die('Restricted access');
JHTML::stylesheet( 'mootree.css', 'administrator/components/com_yos_resources_manager/assets/css/' );
JHTML::stylesheet( 'style.css', 'administrator/components/com_yos_resources_manager/assets/css/' );
JHTML::stylesheet( 'users.css', 'administrator/components/com_yos_resources_manager/assets/css/' );
$document =& JFactory::getDocument();
$document->addScript('components/com_yos_resources_manager/assets/js/mootools-1.2-core.js');
$document->addScript('components/com_yos_resources_manager/assets/js/mootools-1.2-more.js');
$document->addScript('components/com_yos_resources_manager/assets/js/mootree.js');
$document->addScript('components/com_yos_resources_manager/assets/js/user_resources_banned.js');

//disable mootools 1.1
$new_script = array();
foreach ($document->_scripts as $key => $value){
	if (!preg_match('/mootools.js/', $key)) {
		$new_script[$key] = $value;
	}
}
$document->_scripts = $new_script;

$xmlUrl = 'index.php?option=' . $this->option . '&controller=user&task=xmlrsbtree&uid=' . $this->uid;

ob_start();
?>
<!--
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
//-->
<?php
$javaScript = ob_get_contents();
ob_end_clean();

$document->addScriptDeclaration($javaScript);
?>

<script type="text/javascript">
	var user_id = <?php echo $this->uid; ?>;
    var f = function(){loadtip();};
    f.delay(3000);
</script>

<script type="text/javascript">
function calendarSetup(id){
	if(id == 'eform_start_date'){
		Calendar.setup({
	        inputField     :    "eform_start_date",     // id of the input field
	        ifFormat       :    "%Y-%m-%d %H:%M:%S",      // format of the input field
	        button         :    "eform_start_date_img",  // trigger for the calendar (button ID)
	        align          :    "Tl",           // alignment (defaults to "Bl")
	        singleClick    :    true
	    });
	}else{
		if(id == 'eform_end_date'){
			Calendar.setup({
		        inputField     :    "eform_end_date",     // id of the input field
		        ifFormat       :    "%Y-%m-%d %H:%M:%S",      // format of the input field
		        button         :    "eform_end_date_img",  // trigger for the calendar (button ID)
		        align          :    "Tl",           // alignment (defaults to "Bl")
		        singleClick    :    true
		    });
		}
	}
}
</script>
<form method="post" id="adminForm" name="adminForm" action="">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td valign="top">
	<?php echo "<h3>$this->say</h3>"; ?>
	<div id="mytree">
	</div>

	<p>
		<input type="button" value=" expand all " onclick="tree.expand(); loadtip();" />
		<input type="button" value=" collapse all " onclick="tree.collapse();" />
        <input type="button" value=" load info " onclick="loadtip();" />
	</p>
	<?php echo JHTML::_( 'form.token' ); ?>
	</td>
	<td width="400" valign="top">
		<fieldset id="global_cfg">
		<legend><?php echo JText::_('USER_RESOURCES_CONFIGURATION'); ?></legend>
		<table>
		<tr>
			<td><?php echo JText::_('Description'); ?></td>
			<td>
				<textarea name="description" cols="40" rows="2"><?php ?></textarea>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_('Redirect Message'); ?></td>
			<td><textarea name="redirect_message" cols="40" rows="2"><?php ?></textarea></td>
		</tr>
		<tr>
			<td><?php echo JText::_('Redirect URL'); ?></td>
			<td><input type="text" name="redirect_url" value="<?php ?>" size="47" /> </td>
		</tr>
		<tr>
			<td><?php echo JText::_('Start Date'); ?></td>
			<td>
			<?php
				echo JHTML::_('calendar', "$this->nowdate", 'start_date', 'start_date', '%Y-%m-%d %H:%M:%S', array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19')); 
			?>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_('End Date'); ?></td>
			<td>
			<?php 
				echo JHTML::_('calendar', '0000-00-00 00:00:00', 'end_date', 'end_date', '%Y-%m-%d %H:%M:%S', array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19')); 
			?>
			</td>
		</tr>
		<tr>
			<td colspan="2"><input type="radio" name="keep_original" value="1" checked="checked" /> <?php echo JText::_('USER_RESOURCES_KEEP_ORIGINAL'); ?> &nbsp;&nbsp;&nbsp;
		<input type="radio" name="keep_original" value="0" /> <?php echo JText::_('USER_RESOURCES_OVERWRITE_ORIGINAL'); ?></td>
		</tr>
		</table>
		</fieldset>
		
		<fieldset id="item_cfg" style="display: none;">
		<legend><?php echo JText::_('USER_RESOURCES_ITEM_CFG'); ?></legend>
		<div id="item_cfg_content">
		</div>
		</fieldset>
	</td>
</tr>
<tr>
	<td colspan="2" align="center">
		<table class="yrm_comment">
		<tr>
			<td><img src="components/com_yos_resources_manager/assets/images/affected_F.png" class="yos_tree_img" alt="Affected domain"/></td>
			<td><?php echo JText::_('COMMENT_AFFECTED_DOMAIN_F'); ?></td>
			<td><img src="components/com_yos_resources_manager/assets/images/affected_B.png" class="yos_tree_img" alt="Affected domain"/></td>
			<td><?php echo JText::_('COMMENT_AFFECTED_DOMAIN_B'); ?></td>
			<td><img src="components/com_yos_resources_manager/assets/images/affected_BF.png" class="yos_tree_img" alt="Affected domain"/></td>
			<td><?php echo JText::_('COMMENT_AFFECTED_DOMAIN_BF'); ?></td>
		</tr>
		<tr>
			<td><img src="components/com_yos_resources_manager/assets/images/tick.png" class="yos_tree_img" alt="status"/></td>
			<td><?php echo JText::_('COMMENT_RES_STATUS_PUBLISHED'); ?></td>
			<td><img src="components/com_yos_resources_manager/assets/images/publish_x.png" class="yos_tree_img" alt="status"/></td>
			<td colspan="3"><?php echo JText::_('COMMENT_RES_STATUS_UNPUBLISHED'); ?></td>
		</tr>
		<tr>
			<td><img src="components/com_yos_resources_manager/assets/images/edit.png" alt="" /></td>
			<td colspan="5"><?php echo JText::_('COMMENT_EDIT_ITEM'); ?></td>
		</tr>
		</table>
	</td>
</tr>
</table>
<input type="hidden" name="uid" value="<?php echo $this->uid; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
<input type="hidden" name="controller" value="user" />
<input type="hidden" name="view" value="user" />
<input type="hidden" name="boxchecked" value="" />
</form>