<?php defined('_JEXEC') or die('Restricted access'); ?>

<form action="index.php" method="post" name="adminForm">
	<table class="admintable">
		<tr>
			<td width="110" class="key">
				<label for="title">
					<?php echo JText::_( 'GROUP_EDIT_TITLE' ); ?>:
				</label>
			</td>
			<td>
				<input class="inputbox" type="text" name="name" id="name" size="47" value="<?php if($this->cmd == 'edit') echo $this->group->name; ?>" />
			</td>
		</tr>
		<tr>
			<td width="110" class="key">
				<label for="alias">
					<?php echo JText::_( 'GROUP_EDIT_DESCRIPTION' ); ?>:
				</label>
			</td>
			<td>
				<textarea name="description" id="description" rows="3" cols="40" ><?php if($this->cmd == 'edit') echo $this->group->description; ?></textarea>
			</td>
		</tr>
		<tr>
			<td valign="top" class="key">
				<label for="gid">
					<?php echo JText::_( 'GROUP_FORM_GID' ); ?>
				</label>
			</td>
			<td>
				<?php echo $this->list_gid; ?>
			</td>
		</tr>
		<tr>
			<td valign="top" class="key">
				<label for="gid">
					<?php echo JText::_( 'GROUP_FORM_TIME_MAPPING' ); ?>
				</label>
			</td>
			<td>
				<input type="text" name="time_mapping" size="28" value="<?php if($this->cmd == 'edit') echo $this->group->time_mapping; ?>" />
			</td>
		</tr>
		
	</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_yos_resources_manager" />
	<input type="hidden" name="controller" value="groups" />
	<input type="hidden" name="view" value="groups" />
	<input type="hidden" name="id" value="<?php if ($this->cmd == 'edit') echo $this->group->id; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
