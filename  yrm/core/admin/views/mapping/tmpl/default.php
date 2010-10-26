<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php  JHTML::_('behavior.tooltip');  ?>

<form action="index.php" method="post" name="adminForm">
	<table>
		<tr>
			<td width="100%">
				<?php echo JText::_( 'Filter' ); ?>:
				<input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_type').value='0';this.form.getElementById('filter_logged').value='0';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
			</td>
		</tr>
	</table>

	<table class="adminlist" cellpadding="1">
		<thead>
			<tr>
				<th width="3%" class="title">
					<?php echo JText::_( 'NUM' ); ?>
				</th>
				<th width="3%" class="title">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
				</th>
				<th width="40%">
					<?php echo JHTML::_('grid.sort',   'Joomla Group', 'r.joomla_group_id', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th width="40%">
					<?php echo JHTML::_('grid.sort',   'YRM Group', 'r.yrm_group_id', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th width="10%">
					<?php echo JHTML::_('grid.sort',   'Time', 'r.time', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th width="4%" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort',   'ID', 'r.id', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="10">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
			$k = 0;
			for ($i=0, $n=count( $this->items ); $i < $n; $i++)
			{
				$row 	=& $this->items[$i];

				$link 	= 'index.php?option=com_yos_resources_manager&amp;controller=mapping&amp;task=edit&amp;cid[]='. $row->id;
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo $i+1+$this->pagination->limitstart;?>
				</td>
				<td align="center">
					<input id="cb<?php echo $i;?>" type="checkbox" onclick="isChecked(this.checked);" value="<?php echo $row->id;?>" name="cid_user[]"/>
				</td>
				<td>
					<a href="<?php echo $link; ?>">
						<?php echo $row->jgname; ?></a>
				</td>
				<td>
					<?php echo $row->gname; ?>
				</td>
				<td nowrap="nowrap">
					<?php echo $row->time; ?>
				</td>
				<td>
					<?php echo $row->id; ?>
				</td>
			</tr>
			<?php
				$k = 1 - $k;
				}
			?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="com_yos_resources_manager" />
	<input type="hidden" name="cid[]" value="<?php echo $this->group_id;?>" />
	<input type="hidden" name="task" value="<?php echo $this->task;?>" />
	<input type="hidden" name="view" value="mapping" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>