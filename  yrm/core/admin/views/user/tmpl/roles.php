<?php defined('_JEXEC') or die('Restricted access'); 
	JHTML::_('behavior.tooltip'); 
	JToolBarHelper::title(  JText::_( 'USER_ROLES_MANAGER').' [<small><small>'.$this->user->username.'</small></small>]' );
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
			<th width="15%">
				<?php echo JHTML::_('grid.sort',   'Start', 'a.start', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			<th width="15%">
				<?php echo JHTML::_('grid.sort',   'End', 'a.end', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			<th width="40%">
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
				<input <?php echo $this->check_rows[$i];?> id="cb<?php echo $i;?>" type="checkbox" onclick="isChecked(this.checked);" value="<?php echo $row->id;?>" name="cid_role[]"/>
			</td>
			<td style="color:blue;">
				<?php echo $row->name; ?>
			</td>
			<td>
				<?php 
					echo JHTML::_('calendar', $row->start, 'start['.$row->id.']', 'start['.$row->id.']', '%Y-%m-%d %H:%M:%S', array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19')); 
				?>
			</td>
			<td>
				<?php 
					echo JHTML::_('calendar', $row->end, 'end['.$row->id.']', 'end['.$row->id.']', '%Y-%m-%d %H:%M:%S', array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19')); 
				?>
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
	<input type="hidden" name="cid_user[]" value="<?php echo $this->user->id;?>" />
	<input type="hidden" name="user_id" value="<?php echo $this->user->id;?>" />
	<input type="hidden" name="controller" value="user" />
	<input type="hidden" name="view" value="user" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>