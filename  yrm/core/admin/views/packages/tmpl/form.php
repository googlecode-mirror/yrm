<?php defined('_JEXEC') or die('Restricted access'); 
JHTML::stylesheet( 'mootree.css', 'administrator/components/com_yos_resources_manager/assets/css/' );
JHTML::stylesheet( 'users.css', 'administrator/components/com_yos_resources_manager/assets/css/' );
JHTML::stylesheet( 'style.css', 'administrator/components/com_yos_resources_manager/assets/css/' );

JFilterOutput::objectHTMLSafe( $this->row );

$document =& JFactory::getDocument();
$document->addScript('components/com_yos_resources_manager/assets/js/mootools-1.2-core.js');
$document->addScript('components/com_yos_resources_manager/assets/js/mootools-1.2-more.js');
$document->addScript('components/com_yos_resources_manager/assets/js/mootree.js');
$document->addScript('components/com_yos_resources_manager/assets/js/package.js');
//disable mootools 1.1
$new_script = array();
foreach ($document->_scripts as $key => $value){
	if (!preg_match('/mootools.js/', $key)) {
		$new_script[$key] = $value;
	}
}
$document->_scripts = $new_script;
$xmlUrl = 'index.php?option=com_yos_resources_manager&controller=packages&task=xmltree&id=' . $this->package_id;
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
$open_tab_name = JRequest::getVar('open_tab_name','panelpackage');
?>
<script type="text/javascript">
 var package_id = <?php echo $this->row->id; ?>;
    var f = function(){loadtip();};
    f.delay(3000);
</script>
<form action="index.php" method="post" name="adminForm">

<dl id="Pane" class="tabs">
	<dt id="panelpackage" class="<?php if ($this->open_tab_name =='panelpackage') { echo 'open';}elseif($this->open_tab_name==''){echo 'open';}else{echo 'closed';};?>" style="cursor: pointer;" >
		<span><?php echo JText::_('PACKAGE_FORM_GENERAL_PACKAGE');?></span>
	</dt>
	<dt id="panelresource" class="<?php if ($this->open_tab_name =='panelresource') { echo 'open';}else{echo 'closed';};?>" style="cursor: pointer;">
		<span><?php echo JText::_('PACKAGE_FORM_RESOURCES');?></span>
	</dt>
	<dt id="panelrole" class="<?php if ($this->open_tab_name =='panelrole') { echo 'open';}else{echo 'closed';};?>" style="cursor: pointer;">
		<span><?php echo JText::_('PACKAGE_FORM_ROLES');?></span>
	</dt>
	<dt id="panelgroup" class="<?php if ($this->open_tab_name =='panelgroup') { echo 'open';}else{echo 'closed';};?>" style="cursor: pointer;">
		<span><?php echo JText::_('PACKAGE_FORM_GROUPS');?></span>
	</dt>
	<dt id="panelpayment" class="<?php if ($this->open_tab_name =='panelpayment') { echo 'open';}else{echo 'closed';};?>" style="cursor: pointer;">
		<span><?php echo JText::_('PACKAGE_FORM_PAYMENT_METHODS');?></span>
	</dt>
	<dt id="panelthankyou_page" class="<?php if ($this->open_tab_name =='panelthankyou_page') { echo 'open';}else{echo 'closed';};?>" style="cursor: pointer;">
		<span><?php echo JText::_('PACKAGE_FORM_THANK_YOU_PAGE');?></span>
	</dt>
</dl>
<div class="current" id="current">
<dd <?php if ($this->open_tab_name =='panelpackage') { echo 'style="display: block;"';}elseif($this->open_tab_name==''){echo 'style="display: block;"';}else{echo 'style="display: none;"';};?>  id="panelpackage_yos">
	<table class="admintable">
		<tr>
			<td width="110" class="key">
				<label for="title">
					<?php echo JText::_( 'PACKAGE_NAME' ); ?>:
				</label>
			</td>
			<td>
				<input type="text" name="name" value="<?php echo $this->row->name;?>" size="60" />
			</td>
		</tr>
		<tr>
			<td width="110" class="key">
				<label for="title">
					<?php echo JText::_( 'DESCRIPTION' ); ?>:
				</label>
			</td>
			<td>
				<textarea name="description" cols="51" rows="5" ><?php echo $this->row->description;?></textarea>
			</td>
		</tr>
		<tr>
			<td width="110" class="key">
				<label for="title">
					<?php echo JText::_( 'PACKAGE_VALUE' ); ?>:
				</label>
			</td>
			<td>
				<input type="text" name="value" value="<?php  echo $this->row->value; ?>" />
			</td>
		</tr>
		<tr>
			<td width="110" class="key">
				<label for="title">
					<?php echo JText::_( 'CURRENCY' ); ?>:
				</label>
			</td>
			<td>
				<?php  echo $this->currency; ?>
			</td>
		</tr>
		<tr>
			<td width="120" class="key">
				<?php echo JText::_( 'PACKAGE_PUBLISHED' ); ?>:
			</td>
			<td>
				<?php echo JHTML::_( 'select.booleanlist',  'published', 'class="inputbox"', $this->row->published ); ?>
			</td>
		</tr>
		
	</table>
</dd>
<dd id="panelresource_yos" <?php if ($this->open_tab_name =='panelresource') { echo 'style="display: block;"';}else{echo 'style="display: none;"';};?>>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td>
	<?php echo "<h3>$this->say</h3>"; ?>
	<div id="mytree">
	</div>
	
	<p>
		<input type="button" value=" expand all " onclick="tree.expand(); loadtip();" />
		<input type="button" value=" collapse all " onclick="tree.collapse();" />
        <input type="button" value=" load info " onclick="loadtip();" />
	</p>

	</td>
	<td width="250" valign="top">
	<fieldset>
	<legend><?php echo JText::_('RESOURCE_CONFIGURATION'); ?></legend>
	<table>
	<tr>
		<td><?php echo JText::_('TIMES_ACCESS'); ?></td>
		<td><input type="text" name="times_access" value="-1" size="5" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_('SECONDS'); ?></td>
		<td><input type="text" name="seconds" size="20" value="60" /></td>
	</tr>
	<tr>
		<td colspan="2"><input type="radio" name="keep_original" value="1" checked /> <?php echo JText::_('USER_RESOURCES_KEEP_ORIGINAL'); ?> &nbsp;&nbsp;&nbsp;
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
</table>
</dd>
<dd id="panelrole_yos" <?php if ($this->open_tab_name =='panelrole') { echo 'style="display: block;"';}else{echo 'style="display: none;"';};?>>
	<table class="adminlist">
	<thead>
		<tr>
			<th width="3%">
				<?php echo JText::_( 'NUM' ); ?>
			</th>
			<th width="5%">
				<input type="checkbox" name="toggle_role" value="" onclick="checkAll_role(<?php echo count( $this->roles->items ); ?>,'cb_role');" />
			</th>
			<th  class="title">
				<?php echo JText::_('NAME');?>
			</th>
			<th width="10%">
				<?php echo JText::_('SECONDS');?>
			</th>
			<th width="50%">
				<?php echo JText::_('DESCRIPTION'); ?>
			</th>
			<th width="5%" nowrap="nowrap">
				<?php echo JText::_('ID'); ?>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="9">
				<?php echo $this->roles->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
	<?php
	$k = 0;
	for ($i=0, $n=count( $this->roles->items ); $i < $n; $i++)
	{
		$row = &$this->roles->items[$i];

	?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $this->roles->pagination->getRowOffset( $i ); ?>
			</td>
			<td align="center">
				<input <?php echo $this->roles->check_rows[$i];?> id="cb_role<?php echo $i;?>" type="checkbox" onclick="isChecked(this.checked);" value="<?php echo $row->id;?>" name="cid_role[]"/>
			</td>
			<td style="color:blue;">
				<?php echo $row->name; ?>
			</td>
			<td >
				<input type="text" size="20" name="role_seconds[<?php echo $row->id?>]" value="<?php echo $row->seconds; ?>" >
			</td>
			<td >
				<?php echo $row->description;?>
			</td>
			<td align="center">
				<?php echo $row->id; ?>
			</td>
		</tr>
		<?php
			$k = 1 - $k;
		}
		?>
	</tbody>
	</table>
</dd>
<dd id="panelgroup_yos" <?php if ($this->open_tab_name =='panelgroup') { echo 'style="display: block;"';}else{echo 'style="display: none;"';};?>>
	<table class="adminlist">
	<thead>
		<tr>
			<th width="3%">
				<?php echo JText::_( 'NUM' ); ?>
			</th>
			<th width="5%">
				<input type="checkbox" name="toggle_group" value="" onclick="checkAll_group(<?php echo count( $this->groups->items ); ?>,'cb_group');" />
			</th>
			<th  class="title">
				<?php echo JText::_('NAME');?>
			</th>
			<th width="10%">
				<?php echo JText::_('SECONDS');?>
			</th>
			<th width="50%">
				<?php echo JText::_('DESCRIPTION'); ?>
			</th>
			<th width="5%" nowrap="nowrap">
				<?php echo JText::_('ID'); ?>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="9">
				<?php echo $this->groups->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
	<?php
	$k = 0;
	for ($i=0, $n=count( $this->groups->items ); $i < $n; $i++)
	{
		$row = &$this->groups->items[$i];

	?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $this->groups->pagination->getRowOffset( $i ); ?>
			</td>
			<td align="center">
				<input <?php echo $this->groups->check_rows[$i];?> id="cb_group<?php echo $i;?>" type="checkbox" onclick="isChecked(this.checked);" value="<?php echo $row->id;?>" name="cid_group[]"/>
			</td>
			<td style="color:blue;">
				<?php echo $row->name; ?>
			</td>
			<td >
				<input type="text" size="20" name="group_seconds[<?php echo $row->id;?>]" value="<?php echo $row->seconds; ?>" >
			</td>
			<td >
				<?php echo $row->description;?>
			</td>
			<td align="center">
				<?php echo $row->id; ?>
			</td>
		</tr>
		<?php
			$k = 1 - $k;
		}
		?>
	</tbody>
	</table>
</dd>
<dd id="panelpayment_yos" <?php if ($this->open_tab_name =='panelpayment') { echo 'style="display: block;"';}else{echo 'style="display: none;"';};?>>

	<table class="adminlist">
	<thead>
		<tr>
			<th width="3%">
				<?php echo JText::_( 'NUM' ); ?>
			</th>
			<th width="5%">
				<input type="checkbox" name="toggle_payment_methods" value="" onclick="checkAll_payment_methods(<?php echo count( $this->payment_methods->items ); ?>,'cb_payment_methods');" />
			</th>
			<th  class="title">
				<?php echo JText::_('NAME');?>
			</th>
			<th width="50%">
				<?php echo JText::_('DESCRIPTION'); ?>
			</th>
			<th width="5%" nowrap="nowrap">
				<?php echo JText::_('ID'); ?>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="9">
				<?php echo $this->payment_methods->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
	<?php
	$k = 0;
	for ($i=0, $n=count( $this->payment_methods->items ); $i < $n; $i++)
	{
		$row = &$this->payment_methods->items[$i];

	?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $this->payment_methods->pagination->getRowOffset( $i ); ?>
			</td>
			<td align="center">
				<input <?php echo $this->payment_methods->check_rows[$i];?> id="cb_payment_methods<?php echo $i;?>" type="checkbox" onclick="isChecked(this.checked);" value="<?php echo $row->id;?>" name="cid_payment_methods[]"/>
			</td>
			<td style="color:blue;">
				<?php echo $row->name; ?>
			</td>
			<td >
				<?php echo $row->description;?>
			</td>
			<td align="center">
				<?php echo $row->id; ?>
			</td>
		</tr>
		<?php
			$k = 1 - $k;
		}
		?>
	</tbody>
	</table>
</dd>
<dd id="panelthankyou_page_yos" <?php if ($this->open_tab_name =='panelthankyou_page') { echo 'style="display: block;"';}else{echo 'style="display: none;"';};?>>

	<table class="adminform">
		<tr>
			<td>
			<?php 
				$editor =& JFactory::getEditor();
				echo $editor->display( 'text',  $this->row->text , '80%', '400', '50', '20', false ) ;
			?>
			</td>
		</tr>
	</table>
</dd>
</div>
	<input type="hidden" name="task" value="<?php echo $this->task;?>" />
	<input type="hidden" name="cmd" value="<?php echo $this->task;?>" />
	<input type="hidden" name="cid_package" value="<?php echo $this->row->id;?>" />
	<input type="hidden" name="open_tab_name" id="open_tab_name" value="<?php echo $open_tab_name;?>" />
	<input type="hidden" name="id" value="<?php echo $this->row->id;?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_yos_resources_manager" />
	<input type="hidden" name="controller" value="packages" />
	<input type="hidden" name="view" value="packages" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>

