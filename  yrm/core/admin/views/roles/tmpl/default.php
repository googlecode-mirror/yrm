<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php  
JHTML::stylesheet( 'roles.css', 'administrator/components/com_yos_resources_manager/assets/css/' );
?>
<?php JHTML::_('behavior.tooltip'); ?>

<form action="index.php?option=com_yos_resources_manager" method="post" name="adminForm">
<table>
	<tr>
		<td align="left" width="100%">
			<?php echo JText::_( 'Filter' ); ?>:
			<input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
			<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
			<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
		</td>
	</tr>
</table>
<div id="tablecell">
	<table class="adminlist">
	<thead>
		<tr>
			<th width="3%">
				<?php echo JText::_( 'NUM' ); ?>
			</th>
			<th width="5%">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
			</th>
			<th  class="title">
				<?php echo JHTML::_('grid.sort',   JText::_('ROLES_NAME'), 'r.name', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			<th  width="20%">
				<?php echo JText::_('ROLES_RESOURCES'); ?>
			</th>
			<th  width="20%">
				<?php echo JText::_('ROLES_USERS'); ?>
			</th>
			<th  class="title">
				<?php echo JText::_('ROLES_PACKAGES'); ?>
			</th>
			<th  class="title">
				<?php echo JText::_('ROLES_GROUPS'); ?>
			</th>
			<th width="3%" nowrap="nowrap">
				<?php echo JHTML::_('grid.sort',   'ID', 'r.id', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="9">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
	<?php
	$k = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)
	{
		$row = &$this->items[$i];

		$link 		= JRoute::_( 'index.php?option=com_yos_resources_manager&view=roles&controller=roles&task=edit&cid_role[]='. $row->id );

		$link1 		=  JRoute::_( 'index.php?option=com_yos_resources_manager&view=roles&controller=roles&task=resources&cid_role[]='. $row->id );
		$link2 		=  JRoute::_( 'index.php?option=com_yos_resources_manager&view=roles&controller=roles&task=users&cid_role[]='. $row->id );
		$link3 		=  JRoute::_( 'index.php?option=com_yos_resources_manager&view=roles&controller=roles&task=packages&cid_role[]='. $row->id );
		$link4 		=  JRoute::_( 'index.php?option=com_yos_resources_manager&view=roles&controller=roles&task=groups&cid_role[]='. $row->id );
	?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $this->pagination->getRowOffset( $i ); ?>
			</td>
			<td align="center">
				<input id="cb<?php echo $i;?>" type="checkbox" onclick="isChecked(this.checked);" value="<?php echo $row->id;?>" name="cid_role[]"/>
			</td>
			<td>
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'EDIT_ROLE' );?>::<?php echo $row->name; ?>">
				<a href="<?php echo $link  ?>">
					<?php echo $row->name; ?></a></span>
			</td>
			<td align="center">
				<a href="<?php echo $link1; ?>" title="<?php echo JText::_( 'ROLES_EDIT_RESOURCES' ); ?>">
					<img src="<?php echo JURI::root(); ?>administrator/components/com_yos_resources_manager/assets/images/mainmenu.png" border="0" /></a>
			</td>
			<td align="center">
				<a href="<?php echo $link2; ?>" title="<?php echo JText::_( 'ROLES_EDIT_USERS' ); ?>">
					<img src="<?php echo JURI::root(); ?>administrator/components/com_yos_resources_manager/assets/images/mainmenu.png" border="0" /></a>
			</td>
			<td align="center">
				<a href="<?php echo $link3; ?>" title="<?php echo JText::_( 'ROLES_EDIT_PACKAGES' ); ?>">
					<img src="<?php echo JURI::root(); ?>administrator/components/com_yos_resources_manager/assets/images/mainmenu.png" border="0" /></a>
			</td>
			<td align="center">
				<a href="<?php echo $link4; ?>" title="<?php echo JText::_( 'ROLES_EDIT_GROUPS' ); ?>">
					<img src="<?php echo JURI::root(); ?>administrator/components/com_yos_resources_manager/assets/images/mainmenu.png" border="0" /></a>
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
</div>

	<input type="hidden" name="option" value="com_yos_resources_manager" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="roles" />
	<input type="hidden" name="view" value="roles" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>