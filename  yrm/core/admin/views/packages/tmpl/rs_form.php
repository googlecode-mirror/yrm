<table>
<tr>
	<td><?php echo JText::_('RESOURCE_ID'); ?></td>
	<td><?php echo $this->res_xref->object_id; ?></td>
</tr>
<tr>
	<td><?php echo JText::_('TIMES_ACCESS'); ?></td>
	<td><input type="text" id="eform_times_access" name="eform_times_access" value="<?php echo $this->res_xref->times_access; ?>" size="5" /></td>
</tr>
<tr>
	<td><?php echo JText::_('SECONDS'); ?></td>
	<td><input type="text" id="eform_seconds" name="eform_seconds" size="20" value="<?php echo $this->res_xref->seconds; ?>" /></td>
</tr>
<tr>
	<td colspan="2" align="right">
		<input type="button" name="eform_submit" onclick="submit_eform();" value="<?php echo JText::_('SAVE'); ?>" />
	</td>
</tr>
</table>
<input type="hidden" id="eform_res_id" name="eform_res_id" value="<?php echo $this->res_xref->object_id; ?>" />