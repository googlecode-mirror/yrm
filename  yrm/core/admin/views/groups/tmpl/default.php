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
			<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_type').value='0';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
		</td>
		<td>
			<?php echo $this->lists['type'];?>
		</td>
	</tr>
</table>
<div id="tablecell">
	<table class="adminlist">
	<thead>
		<tr>
			<th width="3%">
				<?php echo JText::_( 'GROUP_NUM' ); ?>
			</th>
			<th width="4%">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
			</th>
			<th  width="30%">
				<?php echo JHTML::_('grid.sort',   'Name', 'r.name', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			<th  width="20%">
				<?php echo JText::_('GROUP_USERS'); ?>
			</th>
			<th  width="20%">
				<?php echo JText::_('GROUP_PACKAGES'); ?>
			</th>
			<th width="20%">
				<?php echo JText::_('GROUP_ROLES'); ?>
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

		$link 		= JRoute::_( 'index.php?option=com_yos_resources_manager&view=groups&controller=groups&task=edit&cid_group[]='. $row->id );

//		$link1 		=  JRoute::_( 'index.php?option=com_yos_resources_manager&view=groups&controller=groups&task=resources&cid_group[]='. $row->id );
		$link2 		=  JRoute::_( 'index.php?option=com_yos_resources_manager&view=groups&controller=groups&task=users&cid_group[]='. $row->id );
		$link3 		=  JRoute::_( 'index.php?option=com_yos_resources_manager&view=groups&controller=groups&task=packages&cid_group[]='. $row->id );
		$link4 		=  JRoute::_( 'index.php?option=com_yos_resources_manager&view=groups&controller=groups&task=roles&cid_group[]='. $row->id );
	?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $this->pagination->getRowOffset( $i ); ?>
			</td>
			<td align="center">
				<input id="cb<?php echo $i;?>" type="checkbox" onclick="isChecked(this.checked);" value="<?php echo $row->id;?>" name="cid_group[]"/>
			</td>
			<td>
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'EDIT_GROUP' );?>::<?php echo $row->name; ?>">
				<a href="<?php echo $link  ?>">
					<?php echo $row->name; ?></a></span>
			</td>
			<td align="center">
				<a href="<?php echo $link2; ?>" title="<?php echo JText::_( 'EDIT_GROUP_USERS' ); ?>">
					<img src="<?php echo JURI::root(); ?>administrator/components/com_yos_resources_manager/assets/images/mainmenu.png" border="0" /></a>
			</td>
			<td align="center">
				<a href="<?php echo $link3; ?>" title="<?php echo JText::_( 'EDIT_GROUP_PACKAGES' ); ?>">
					<img src="<?php echo JURI::root(); ?>administrator/components/com_yos_resources_manager/assets/images/mainmenu.png" border="0" /></a>
			</td>
			<td align="center">
				<a href="<?php echo $link4; ?>" title="<?php echo JText::_( 'EDIT_GROUP_ROLES' ); ?>">
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
	<input type="hidden" name="controller" value="groups" />
	<input type="hidden" name="view" value="groups" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>