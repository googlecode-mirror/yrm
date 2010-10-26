<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" >
<div class="col100">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'You are about to import resources to: '). ($this->resource->res_id ? $this->resource->res_name : 'root'); ?></legend>
		<table class="admintable" width="730px">
		<tr>
			<td colspan="2" width="100" align="center">
				<label for="name">
					<div style="font-size:14px;"> <b>	<?php echo JText::_( 'Select file compress' ); ?></b> </div>
				</label>
			</td>					
		</tr>
		<tr>
			<td width="30%" align="right" class="key">
				<label for="name">
					<?php echo JText::_( 'Location of the compress file' ); ?>
				</label>
			</td>
			<td  width="70%">
				<input size="80" type="file" name="file_import" id="file_import">
			</td>
		</tr>
	</table>
	</fieldset>
</div>
<input type="hidden" name="option" value="com_yos_resources_manager" />
<input type="hidden" name="controller" value="resources" />
<input type="hidden" name="cid" value="<?php echo $this->resource->res_id; ?>" />
<input type="hidden" name="task" value="" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>	