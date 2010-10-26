<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php  
	JHTML::_('behavior.tooltip'); 
	
	JToolBarHelper::title(  JText::_( 'PAYMENT_METHOD_LIST' ) ,'payment_method' );	
	
	JToolBarHelper::addNewX('installPayment','Install');		
	JToolBarHelper::addNewX('uninstallPayment','Uninstall');		
	JToolBarHelper::publish();	
	JToolBarHelper::unpublish();
	//JToolBarHelper::editListX();
?>
<script>
function submitbutton(pressbutton)
{	
	if(pressbutton=='uninstallPayment')
	{
		var form = eval( 'document.adminForm');
		var boxchecked=form.boxchecked.value;
		if(boxchecked<=0 || boxchecked== '')
		{
			var mess="<?php echo JText::_('Select an item to Uninstall Payment')?>";
			alert(mess);
			return;
		}
		else{
			var mess="<?php echo JText::_('Are you sure ?')?>";
			var select=confirm(mess);
			if(!select)
			{
				return;
			}
		}
	}	
	submitform( pressbutton );
}
</script>
<form action="index.php?option=com_yos_resources_manager" method="post" name="adminForm">
<div id="tablecell">
	<table class="adminlist">
	<thead>
		<tr>
			<th width="5">
				<?php echo JText::_( 'NUM' ); ?>
			</th>
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->data ); ?>);" />
			</th>
			<th  class="title" width="20%">
				<?php echo JText::_('Name'); ?>
			</th>
			<th  class="title">
				<?php echo JText::_('Description'); ?>
			</th>
			
			<th  class="title" width="3%">
				<?php echo JText::_('Published'); ?>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="9">
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
		$published 	= JHTML::_('grid.published', $row, $i );
	?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $this->pageNav->getRowOffset( $i ); ?>
			</td>
			<td>
				<input id="cb<?php echo $i; ?>" type="checkbox" onclick="isChecked(this.checked);" value="<?php echo $row->id; ?>" name="cid[]"/>
			</td>
			<td align="left">			
				<a href="index.php?option=<?php echo $option; ?>&task=payment_method.edit&cid[]=<?php echo $row->id; ?>"><?php echo $row->name;?></a>
			</td>
			<td align="left">
				<?php echo $row->description; ?>
			</td>
			
			
			<td align="center">
				<?php echo $published; ?>
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
	<input type="hidden" name="controller" value="payment_method" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="" />
	<input type="hidden" name="view" value="payment_method" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>