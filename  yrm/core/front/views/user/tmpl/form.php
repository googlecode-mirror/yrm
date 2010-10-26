<?php defined('_JEXEC') or die( 'Restricted access' ); 
$document =& JFactory::getDocument();
$document->addStyleDeclaration("			
		.missing {
			color:red;
			font-weight:bold;

	");

?>

<?php
	echo JText::_('BILL_TO_INFORMATION') ;
	echo '<br /> <br />';
	
?>


<form action="<?php echo JRoute::_('index.php?option=com_yos_resources_manager&task=user.save'); ?>" name="userinfo" id="userinfo" method="post">
	<table>
	<tr>		
		<td>
			<?php echo JText::_('FIRST_NAME')?> *
		</td>
		<td>
			<input id="first_name_field" class="inputbox" type="text" maxlength="32" value="<?php if ($this->data ) echo $this->data->first_name; ?>" size="30" name="first_name"/>
		</td>		
	</tr>
	<tr>		
		<td >
			<?php echo JText::_('LAST_NAME')?> *
		</td>
		<td>
			<input id="last_name_field" name="last_name" size="30" value="<?php if ($this->data ) echo $this->data->last_name; ?>" class="inputbox" maxlength="32" type="text" />
		</td>		
	</tr>
	<tr>		
		<td >
			<?php echo JText::_('MIDDLE_NAME')?>
		</td>
		<td>
			<input id="middle_name_field" name="middle_name" size="30" value="<?php if ($this->data ) echo $this->data->middle_name; ?>" class="inputbox" maxlength="32" type="text" />
		</td>		
	</tr><tr>		
		<td >
			<?php echo JText::_('ADDRESS_1')?> *
		</td>
		<td>
			<input id="address_1_field" name="address_1" size="30" value="<?php if ($this->data ) echo $this->data->address_1; ?>" class="inputbox" maxlength="64" type="text" />
		</td>		
	</tr>
	<tr>		
		<td >
			<?php echo JText::_('ADDRESS_2')?>
		</td>
		<td>
			<input id="address_2_field" name="address_2" size="30" value="<?php if ($this->data ) echo $this->data->address_2; ?>" class="inputbox" maxlength="64" type="text" />
		</td>		
	</tr>
	<tr>		
		<td >
			<?php echo JText::_('CITY')?> *
		</td>
		<td>
			<input id="city_field" name="city" size="30" value="<?php if ($this->data ) echo $this->data->city; ?>" class="inputbox" maxlength="32" type="text" />
		</td>		
	</tr>
	<tr>		
		<td >
			<?php echo JText::_('ZIP_CODE')?> *
		</td>
		<td>
			<input id="zip_field" name="zip" size="30" value="<?php if ($this->data ) echo $this->data->zip; ?>" class="inputbox" maxlength="32" type="text" />
		</td>		
	</tr>	
	<tr>		
		<td >
			<?php echo JText::_('STATE')?> *
		</td>
		<td>
			<input id="state_field" name="state" size="30" value="<?php if ($this->data ) echo $this->data->state; ?>" class="inputbox" maxlength="32" type="text" />
		</td>		
	</tr>
	<tr>		
		<td >
			<?php echo JText::_('COUNTRY')?> *
		</td>
		<td>
			<input id="country_field" name="country" size="30" value="<?php if ($this->data ) echo $this->data->country; ?>" class="inputbox" maxlength="32" type="text" />
		</td>		
	</tr>
	<tr>		
		<td >
			<?php echo JText::_('PHONE')?>
		</td>
		<td>
			<input id="phone_field" name="phone" size="30" value="<?php if ($this->data ) echo $this->data->phone; ?>" class="inputbox" maxlength="32" type="text" />
		</td>		
	</tr>
	<tr>		
		<td >
			<?php echo JText::_('REQUIRED')?>
		</td>
		<td align="center">
			<input class="button" type="submit" onclick="return submitregistration();" value="<?php echo JText::_('SAVE')?>" />
		</td>		
	</tr>
	</table>	
	<input type="hidden" name="id" value="<?php  if ($this->data ) echo $this->data->id; ?>" />
</form>

<script language="javascript" type="text/javascript">
	function submitregistration() {
    	var first_name = document.userinfo.first_name.value;
		if(first_name == ''){		
			alert ('<?php echo JText::_('PLEASE_ENTER_FIRST_NAME'); ?>');			
			document.userinfo.first_name.focus();
			return false;
		}
	
	var last_name = document.userinfo.last_name.value;
	if(last_name == ''){
		alert('<?php echo JText::_('PLEASE_ENTER_LAST_NAME'); ?>');
		document.userinfo.last_name.focus();
		return false;
	}
		
	var address_1 = document.userinfo.address_1.value;
	if(address_1 == ''){
		alert('<?php echo JText::_('PLEASE_ENTER_ADDRESS'); ?>');
		document.userinfo.address_1.focus();
		return false;
	}
		
	
	var city = document.userinfo.city.value;
	if(city == ''){
		alert('<?php echo JText::_('PLEASE_ENTER_CITY'); ?>');
		document.userinfo.city.focus();
		return false;
	}
		
	var zipRegEx = /^([0-9])+$/;
	var zip = document.userinfo.zip.value;
	if(!zipRegEx.test(zip)){
		alert('<?php echo JText::_('PLEASE_ENTER_VALID_ZIP'); ?>');
		document.userinfo.zip.focus();
		return false;
	}
	
	var state = document.userinfo.state.value;
	if(state == ''){
		alert('<?php echo JText::_('PLEASE_SELECT_STATE'); ?>');
		document.userinfo.state.focus();
		return false;
	}	
	
	var country = document.userinfo.country.value;
	if(country == ''){
		alert('<?php echo JText::_('PLEASE_SELECT_COUNTRY'); ?>');
		document.userinfo.country.focus();
		return false;
	}	 
	
	document.checkout.submit();
}
	           
</script>
