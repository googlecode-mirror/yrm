<?php defined('_JEXEC') or die( 'Restricted access' ); ?>

<?php
	echo $this->say ;
	echo '<br /> <br />';
	
?>
<form action="index.php?option=com_yos_resources_manager" name="buypackage" id="buypackage" method="post">
	<table>
	<tr>
		<td class="sectiontableheader">
			#
		</td>
		<td class="sectiontableheader">
			<?php echo JText::_('Name')?>
		</td>
		<td class="sectiontableheader">
			<?php echo JText::_('Description')?>
		</td>
		<td class="sectiontableheader">
			<?php echo JText::_('Price')?>
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
			<td align="center">
					<?php echo $i+1; ?>
			</td>
			<td>
					<?php echo $row->name; ?>
			</td>
			<td>
					<?php echo $row->description; ?>
			</td>
			<td>
					<?php echo $row->value.' '.$row->currency_code ?>
			</td>
			<td>
				<input type="button" value="Buy Now" onclick="submit_form('<?php echo $row->id; ?>')"/>				
			</td>
		</tr>
		<?php	
		}
	
	?>
	</table>
	<input type="hidden" name="packageid" value="" />
	<input type="hidden" name="task" value="checkout" />	
	<input type="hidden" name="controller" value="package" />
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