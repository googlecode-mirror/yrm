<?php defined('_JEXEC') or die( 'Restricted access' ); ?>
<?php
echo JText::_('CHECKOUT');
?>

<form action="<?php echo JRoute::_('index.php?option=com_yos_resources_manager&task=package.confirm'); ?>" name="checkout" id="checkout" method="post">
	<table>
	<tr>		
		<td class="sectiontableheader">
			<?php echo JText::_('NAME')?>
		</td>
		<td class="sectiontableheader">
			<?php echo JText::_('DESCRIPTION')?>
		</td>
		<td class="sectiontableheader">
			<?php echo JText::_('PRICE')?>
		</td>		
	</tr>
	<tr>		
		<td width="30%">
			<?php echo $this->package->name; ?>
		</td>
		<td width="60%">
			<?php echo nl2br($this->package->description); ?>
		</td>
		<td>
			<?php echo $this->package->value.' '.$this->package->currency_code ?>
		</td>			
	</tr>		
 </table>
 <hr />
 <a href="index.php?option=com_yos_resources_manager&task=user.updateinfo"><?php echo JText::_('UPDATE_USER_INFOMATION');?></a>
 <hr />
 <?php
 echo JText::_('PAYMENT_METHODS');
 	$k = 0;
		for ($i=0, $n=count( $this->payment ); $i < $n; $i++)
		{
			$row 	=& $this->payment[$i]; 
		
 ?>
 	<input type="radio" name="payment_method_id" onclick=" formSubmit.disabled = false; " value="<?php echo $row->id ?>" />
 	<label><?php echo $row->name; ?></label>
 	<br/>
 <?php
		}
 ?>
 <br/>
 	<input class="button" type="submit" id="formSubmit" value="<?php echo JText::_('BUY_NOW')?>" name="formSubmit" disabled />
	<input type="hidden" name="packageid" value="<?php echo $this->package->id; ?>" />
	<input type="hidden" name="return_url" value="<?php echo $this->return_url; ?>" />
</form>
