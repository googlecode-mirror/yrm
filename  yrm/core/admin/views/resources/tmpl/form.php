<?php defined('_JEXEC') or die('Restricted access');
$document =& JFactory::getDocument();
$document->addScript('components/com_yos_resources_manager/assets/js/mootools-1.2-core.js');
//disable mootools 1.1
$new_script = array();
foreach ($document->_scripts as $key => $value){
	if (!preg_match('/mootools.js/', $key)) {
		$new_script[$key] = $value;
	}
}
$document->_scripts = $new_script;

$document->addScript('components/com_yos_resources_manager/assets/js/resource.js');
?>
<form method="post" id="adminForm" name="adminForm" action="">
<table class="adminform">
<tr>
	<td width="60%" valign="top">
		<table>
		<tr>
			<td><b><?php echo JText::_('RESOURCES_FORM_PARENT_NODE'); ?></b></td>
			<td>
				<?php echo $this->list['parent']; ?>
			</td>
		</tr>
		<tr>
			<td><b><?php echo JText::_('RESOURCES_FORM_NAME'); ?></b></td>
			<td><input type="text" name="res_name" id="res_name" value="<?php echo $this->res->res_name; ?>"/></td>
		</tr>
		<tr>
			<td><b><?php echo JText::_('RESOURCES_FORM_AFFECTED_DOMAIN'); ?></b></td>
			<td>
				<?php echo $this->list['affected']; ?>
			</td>
		</tr>
		<tr>
			<td><b><?php echo JText::_('RESOURCES_FORM_TYPE'); ?></b></td>
			<td>
				<?php echo $this->list['type']; ?>
			</td>
		</tr>
		<tr>
			<td><b><?php echo JText::_('RESOURCES_FORM_OPTION'); ?></b></td>
			<td>
				<?php echo $this->list['component']; ?>
			</td>
		</tr>
		<!--<tr>
			<td><b><?php echo JText::_('RESOURCES_FORM_TASK'); ?></b></td>
			<td><input type="text" name="res_task" id="res_task" value="<?php echo $this->res->res_task; ?>" /></td>
		</tr>
		<tr>
			<td><b><?php echo JText::_('RESOURCES_FORM_VIEW'); ?></b></td>
			<td><input type="text" name="res_view" id="res_view" value="<?php echo $this->res->res_view; ?>" /></td>
		</tr>-->
		<tr>
			<td><b><?php echo JText::_('RESOURCES_FORM_PARAMS'); ?></b></td>
			<td><input type="text" name="res_params" id="res_params" value="<?php echo $this->res->res_params; ?>" size="60" /></td>
		</tr>
		<tr>
			<td><b><?php echo JText::_('RESOURCES_FORM_PLUG_IN'); ?></b></td>
			<td><?php echo $this->list['plugin']; ?></td>
		</tr>
		<tr>
			<td><b><?php echo JText::_('RESOURCES_FORM_REDIRECT_URL'); ?></b></td>
			<td><input type="text" name="res_redirect_url" id="res_redirect_url" value="<?php echo $this->res->res_redirect_url; ?>" size="60" /></td>
		</tr>
		<tr>
			<td><b><?php echo JText::_('RESOURCES_FORM_REDIRECT_MSG'); ?></b></td>
			<td><input type="text" name="res_redirect_msg" id="res_redirect_msg" value="<?php echo $this->res->res_redirect_message; ?>" size="60" /></td>
		</tr>
		<tr>
			<td valign="top"><b><?php echo JText::_('RESOURCES_FORM_DESCRIPTION'); ?></b></td>
			<td><textarea cols="30" rows="3" name="res_description" id="res_description"><?php echo $this->res->res_description; ?></textarea></td>
		</tr>
		<tr>
			<td><b><?php echo JText::_('RESOURCES_FORM_STICKY'); ?></b></td>
			<td>
				<input type="radio" name="res_sticky" id="res_sticky0" value="1" <?php echo $this->res->res_sticky == 1 ? 'checked' : ''; ?> /> Yes &nbsp;&nbsp;&nbsp;
				<input type="radio" name="res_sticky" id="res_sticky1" value="0" <?php echo $this->res->res_sticky == 0 ? 'checked' : ''; ?> /> No
			</td>
		</tr>
		<tr>
			<td><b><?php echo JText::_('RESOURCES_FORM_PUBLISHED'); ?></b></td>
			<td>
				<input type="radio" name="res_published" id="res_published0" value="1" <?php echo $this->res->res_published == 1 ? 'checked' : ''; ?> /> Yes &nbsp;&nbsp;&nbsp;
				<input type="radio" name="res_published" id="res_published1" value="0" <?php echo $this->res->res_published == 0 ? 'checked' : ''; ?> /> No
			</td>
		</tr>
		</table>
	</td>
	<td width="40%" valign="top">
	<div class="yrm_comment">
		<div class="yos_hidden" id="res_parent_node_comment">
			<?php echo JText::_('RESOURCES_COMMENT_PARENT_NODE'); ?>
		</div>
		<div class="yos_hidden" id="res_name_comment">
			<?php echo JText::_('RESOURCES_COMMENT_NAME'); ?>
		</div>
		<div class="yos_hidden" id="res_affected_comment">
			<?php echo JText::_('RESOURCES_COMMENT_AFFECTED_DOMAIN'); ?>
		</div>
		<div class="yos_hidden" id="res_type_comment">
			<?php echo JText::_('RESOURCES_COMMENT_TYPE'); ?>
		</div>
		<div class="yos_hidden" id="res_option_comment">
			<?php echo JText::_('RESOURCES_COMMENT_OPTION'); ?>
		</div>
		<div class="yos_hidden" id="res_task_comment">
			<?php echo JText::_('RESOURCES_COMMENT_TASK'); ?>
		</div>
		<div class="yos_hidden" id="res_view_comment">
			<?php echo JText::_('RESOURCES_COMMENT_VIEW'); ?>
		</div>
		<div class="yos_hidden" id="res_params_comment">
			<?php echo JText::_('RESOURCES_COMMENT_PARAMS'); ?>
		</div>
		<div class="yos_hidden" id="res_plugin_comment">
			<div id="res_plg_content"><?php echo JText::_('RESOURCES_COMMENT_PLUG_IN'); ?></div>
		</div>
		<div class="yos_hidden" id="res_redirect_url_comment">
			<?php echo JText::_('RESOURCES_COMMENT_REDIRECT_URL'); ?>
		</div>
		<div class="yos_hidden" id="res_redirect_msg_comment">
			<?php echo JText::_('RESOURCES_COMMENT_REDIRECT_MSG'); ?>
		</div>
		<div class="yos_hidden" id="res_description_comment">
			<?php echo JText::_('RESOURCES_COMMENT_DESCRIPTION'); ?>
		</div>
		<div class="yos_hidden" id="res_sticky_comment">
			<?php echo JText::_('RESOURCES_COMMENT_STICKY'); ?>
		</div>
		<div class="yos_hidden" id="res_published_comment">
			<?php echo JText::_('RESOURCES_COMMENT_PUBLISHED'); ?>
		</div>
	</div>
	</td>
</tr>
</table>


<input type="hidden" name="res_id" value="<?php echo $this->res->res_id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="resources" />
<input type="hidden" name="boxchecked" value="" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>