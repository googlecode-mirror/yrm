<table>
<tr>
	<td><?php echo JText::_('USER_RESOURCES_RID'); ?></td>
	<td><?php echo $this->res_xref->resource_id; ?></td>
</tr>
<tr>
	<td><?php echo JText::_('USER_RESOURCES_TIMES_ACCESS'); ?></td>
	<td><input type="text" id="eform_times_access" name="eform_times_access" value="<?php echo $this->res_xref->times_access; ?>" size="5" /></td>
</tr>
<tr>
	<td><?php echo JText::_('USER_RESOURCES_START_DATE'); ?></td>
	<td>
		<input name="eform_start_date" id="eform_start_date" value="<?php echo $this->res_xref->start;?>" class="inputbox" size="25" maxlength="19" type="text"><img class="calendar" src="/minhna/0904_yrm/templates/system/images/calendar.png" alt="calendar" id="eform_start_date_img" onclick="calendarSetup('eform_start_date');">
	</td>
</tr>
<tr>
	<td><?php echo JText::_('USER_RESOURCES_END_DATE'); ?></td>
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