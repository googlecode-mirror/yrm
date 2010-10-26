<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php  
JHTML::stylesheet( 'roles.css', 'administrator/components/com_yos_resources_manager/assets/css/' );
?>
<?php JHTML::_('behavior.tooltip'); ?>

<?php
	JToolBarHelper::title(  JText::_( 'MANAGE_ROLE_GROUPS').' [<small><small>'.$this->role->name.'</small></small>]' );
?>

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
				<?php echo JHTML::_('grid.sort',   'Name', 'r.name', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			<th width="50%">
				<?php echo JText::_('DESCRIPTION'); ?>
			</th>
			<th width="5%" nowrap="nowrap">
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

	?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $this->pagination->getRowOffset( $i ); ?>
			</td>
			<td align="center">
				<input <?php echo $this->check_rows[$i];?> id="cb<?php echo $i;?>" type="checkbox" onclick="isChecked(this.checked);" value="<?php echo $row->id;?>" name="cid_group[]"/>
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
</div>
	<input type="hidden" name="task" value="<?php echo $this->task;?>" />
	<input type="hidden" name="option" value="com_yos_resources_manager" />
	<input type="hidden" name="cid_role[]" value="<?php echo $this->role->id;?>" />
	<input type="hidden" name="role_id" value="<?php echo $this->role->id;?>" />
	<input type="hidden" name="controller" value="roles" />
	<input type="hidden" name="view" value="roles" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>