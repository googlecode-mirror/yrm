<?php 
	defined('_JEXEC') or die('Restricted access');
	?>
	
	<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" >
		<table class="adminform">
			<tbody>
			<tr>
				<th colspan="2"><?php echo JText::_('Upload Package File'); ?></th>
			</tr>
			<tr>
				<td width="120">
					<label for="install_package"><?php echo JText::_('Package File:'); ?></label>
				</td>
				<td>
					<input type="file" size="57" name="install_package" id="install_package" class="input_box">
					<input type="button" onclick="submitbutton()" value="<?php echo JText::_('Upload File & Install'); ?>" class="button">
				</td>
			</tr>
			</tbody>
		</table>
	<input type="hidden" name="option" value="com_yos_resources_manager" />
	<input type="hidden" name="task" value="payment_method.doInstall" />
		</form>	