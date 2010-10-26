<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php  
	JHTML::_('behavior.tooltip'); 
	
	JToolBarHelper::title(  JText::_( 'ORDER_LIST' ) ,'order' );	
	
?>
<form action="index.php?option=com_yos_resources_manager" method="post" name="adminForm">
<div id="tablecell">
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
	<table class="adminlist">
	<thead>
		<tr>
			<th width="5">
				<?php echo JText::_( 'NUM' ); ?>
			</th>
			<!--<th width="10">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->data ); ?>);" />
			</th>-->
			<th  class="title" width="20%">				
				<?php echo JHTML::_('grid.sort',  JText::_('USER_NAME'), 'u.username', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			<th  class="title" width="25%" >
				<?php echo JText::_('PACKAGE_NAME'); ?>
			</th>			
			<th  class="title" >
				<?php echo JText::_('PRICE'); ?>
			</th>
			<th  class="title" >				
				<?php echo JHTML::_('grid.sort',   JText::_('ORDER_DATE'), 'a.date', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			<th  class="title" >
				<?php echo JText::_('STATUS'); ?>
			</th>
		</tr>
	</thead>
	<tfoot>
			<tr>
				<td colspan="10">
					<?php echo $this->pageNav->getListFooter(); ?>
				</td>
			</tr>
	</tfoot>
	<tbody>
	<?php
	$k = 0;
	for ($i=0, $n=count( $this->data ); $i < $n; $i++)
	{
		$row = &$this->data[$i];		
	?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $this->pageNav->getRowOffset( $i ); ?>
			</td>
			<!--<td>
				<input id="cb<?php echo $i; ?>" type="checkbox" onclick="isChecked(this.checked);" value="<?php echo $row->id; ?>" name="cid[]"/>
			</td>-->
			<td align="left">			
				<?php echo $row->username;?>
			</td>
			<td align="left">
				<?php echo $row->packagename; ?>				
			</td>
			<td align="left">
				<?php echo $row->value; echo ' '; echo $row->currency_code; ?>				
			</td>
			<td align="center">
				<?php echo $row->date; ?>
			</td>
			<td align="center">
				<?php echo $row->status ; ?>
			</td>
		</tr>
		<?php
			$k = 1 - $k;
		}
		?>
	</tbody>
	</table>
	<br />
</div>
	<input type="hidden" name="option" value="com_yos_resources_manager" />
<!--	<input type="hidden" name="controller" value="order" />-->
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="" />
	<input type="hidden" name="view" value="order" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>