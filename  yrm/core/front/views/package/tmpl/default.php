<?php defined('_JEXEC') or die( 'Restricted access' ); ?>

<?php
	echo JText::_('SELECT_PACKAGE') ;
	echo '<br /> <br />';
	
?>
<form action="<?php echo JRoute::_('index.php?option=com_yos_resources_manager&task=package.checkout'); ?>" name="buypackage" id="buypackage" method="post">
	<table>
	<tr>
		<td class="sectiontableheader" style="width: 20px;">
			#
		</td>
		<td class="sectiontableheader" style="width: 25%;">
			<?php echo JText::_('NAME')?>
		</td>
		<td class="sectiontableheader">
			<?php echo JText::_('DESCRIPTION')?>
		</td>
		<td class="sectiontableheader" style="width: 100px;">
			<?php echo JText::_('PRICE')?>
		</td>
		<td class="sectiontableheader">
			
		</td>
	</tr>
	<?php
		$k = 0;
		for ($i=0, $n=count( $this->data ); $i < $n; $i++)
		{
			$row 	=& $this->data[$i];		
		?>
		<tr>
			<td>
					<?php echo $i+1; ?>
			</td>
			<td>
					<?php echo $row->name; ?>
			</td>
			<td>
					<?php echo nl2br($row->description); ?>
			</td>
			<td>
					<?php echo $row->value.' '.$row->currency_code ?>
			</td>
			<td>
				<input type="button" value="<?php echo JText::_('BUY_NOW')?>" onclick="submit_form('<?php echo $row->id; ?>')"/>				
			</td>
		</tr>
		<?php	
		}
		if (!$this->show) {
			
		
	?>
		<tr>
			<td colspan="3">
				<a href="index.php?option=com_yos_resources_manager&view=package&show=1&Itemid=<?php echo $this->Itemid?>"><?php echo JText::_('SHOW_ALL');?></a>
			</td>
		</tr>
		<?php
			}
		?>
	</table>
	
	<input type="hidden" name="packageid" value="" />	
	<input type="hidden" name="return_url" value="<?php echo $this->return_url; ?>" />	
</form>
<script type="text/javascript">
<!--
function submit_form(packageid){	
	document.buypackage.packageid.value = packageid;
	document.buypackage.submit();
}
-->
</script>
<!--if(document.checkout.couponcode.value == ''){ document.checkout.couponcode.focus();	return false; } document.checkout.task.value = 'giftcards.coupon'; document.checkout.submit();-->
