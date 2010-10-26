<?php defined('_JEXEC') or die('Restricted access'); ?>
<form action="index.php" method="post" name="adminForm">
	<table class="admintable">
		<tr>
			<td width="110" class="key">
				<label for="title">
					<?php echo JText::_( 'Title' ); ?>:
				</label>
			</td>
			<td>
				<input class="inputbox" type="text" name="name" id="name" size="47" value="<?php if($this->cmd == 'edit') echo $this->role->name; ?>" />
			</td>
		</tr>
		<tr>
			<td width="110" class="key">
				<label for="alias">
					<?php echo JText::_( 'DESCRIPTION' ); ?>:
				</label>
			</td>
			<td>
				<textarea name="description" id="description" rows="10" cols="40" ><?php if($this->cmd == 'edit') echo $this->role->description; ?></textarea>
			</td>
		</tr>
	</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_yos_resources_manager" />
	<input type="hidden" name="controller" value="roles" />
	<input type="hidden" name="view" value="roles" />
	<input type="hidden" name="id" value="<?php if ($this->cmd == 'edit') echo $this->role->id; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
