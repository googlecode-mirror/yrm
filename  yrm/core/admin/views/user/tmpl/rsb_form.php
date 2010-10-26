<table>
<tr>
	<td><?php echo JText::_('RESOURCE_BANNED_ID'); ?></td>
	<td><?php echo $this->res_xref->resource_id; ?></td>
</tr>
<tr>
	<td><?php echo JText::_('DESCRIPTION'); ?></td>
	<td>
		<textarea name="eform_description" id="eform_description" cols="40" rows="2"><?php echo $this->res_xref->description;?></textarea>
	</td>
</tr>
<tr>
	<td><?php echo JText::_('REDIRECT_MESSAGE'); ?></td>
	<td><textarea name="eform_redirect_message" id="eform_redirect_message" cols="40" rows="2"><?php echo $this->res_xref->redirect_message;?></textarea></td>
</tr>
<tr>
	<td><?php echo JText::_('REDIRECT_URL'); ?></td>
	<td><input type="text" name="eform_redirect_url" id="eform_redirect_url" value="<?php echo $this->res_xref->redirect_url;?>" size="47"> </td>
</tr>
<tr>
	<td><?php echo JText::_('START_DATE'); ?></td>
	<td>
		<input name="eform_start_date" id="eform_start_date" value="<?php echo $this->res_xref->start;?>" class="inputbox" size="25" maxlength="19" type="text"><img class="calendar" src="/minhna/0904_yrm/templates/system/images/calendar.png" alt="calendar" id="eform_start_date_img" onclick="calendarSetup('eform_start_date');">
	</td>
</tr>
<tr>
	<td><?php echo JText::_('END_DATE'); ?></td>
	<td>
		<input name="eform_end_date" id="eform_end_date" value="<?php echo $this->res_xref->end;?>" class="inputbox" size="25" maxlength="19" type="text"><img class="calendar" src="/minhna/0904_yrm/templates/system/images/calendar.png" alt="calendar" id="eform_end_date_img" onclick="calendarSetup('eform_end_date');">
	</td>	
	</td>
</tr>
<tr>
	<td colspan="2" align="right">
		<input type="button" name="eform_submit" onclick="submit_eform();" value="<?php echo JText::_('USER_RESOURCES_SAVE'); ?>" />
	</td>
</tr>
</table>
<input type="hidden" id="eform_res_id" name="eform_res_id" value="<?php echo $this->res_xref->resource_id; ?>" />