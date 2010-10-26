<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
	$user_id = JRequest::getVar('user_id');
	JArrayHelper::toInteger($cid, array(0));
	$text = ( $this->cmd == 'edit_user') ? JText::_( 'EDIT' ) : JText::_( 'NEW' );

	JToolBarHelper::title(  JText::_( 'GROUP_USER' ).': <small><small>[ ' . $text.' ]</small></small>' );
	JToolBarHelper::save('save_user');
	JToolBarHelper::apply('apply_user');
	JToolBarHelper::cancel('cancel_user');
	
	jimport('joomla.html.pane');
	JFilterOutput::objectHTMLSafe( $this->row );
	$pane	= &JPane::getInstance('sliders', array('allowAllClose' => true));
	JHTML::_('behavior.tooltip');
?>

<form action="index.php" method="post" name="adminForm">
	<table class="admintable">
		<tr>
			<td width="110" class="key">
				<label for="title">
					<?php echo JText::_( 'SELECT_USER' ); ?>:
				</label>
			</td>
			<td>
				<?php echo $this->lists['users'];?>
			</td>
		</tr>
		<?php if($user_id > 0){
			?>
		<tr>
			<td width="110" class="key">
				<label for="title">
					<?php echo JText::_( 'USER_NAME' ); ?>:
				</label>
			</td>
			<td>
				<?php echo $this->row->name; ?>
			</td>
		</tr>
		<tr>
			<td width="110" class="key">
				<label for="title">
					<?php echo JText::_( 'USER_BLOCK' ); ?>:
				</label>
			</td>
			<td>
				<?php if($this->row->block == 0){ echo 'No';}else echo 'Yes'; ?>
			</td>
		</tr>
		<tr>
			<td width="110" class="key">
				<label for="title">
					<?php echo JText::_( 'USER_EMAIL' ); ?>:
				</label>
			</td>
			<td>
				<?php  echo $this->row->email; ?>
			</td>
		</tr>
		<tr>
			<td width="110" class="key">
				<label for="title">
					<?php echo JText::_( 'GROUP' ); ?>:
				</label>
			</td>
			<td>
				<?php echo $this->row->groupname; ?>
			</td>
		</tr>
		<?php } ?>
		<tr>
				<?php
				echo $pane->startPane("content-pane");
				echo $this->form->render('details'); 
				echo $pane->endPane();
			?>
		</tr>
	</table>

	<input type="hidden" name="task" value="<?php echo $this->cmd;?>" />
	<input type="hidden" name="cmd" value="<?php echo $this->cmd;?>" />
	<input type="hidden" name="cid_user[]" value="<?php echo $this->user->id;?>" />
	<input type="hidden" name="option" value="com_yos_resources_manager" />
	<input type="hidden" name="controller" value="groups" />
	<input type="hidden" name="view" value="groups" />
	<input type="hidden" name="group_id" value="<?php echo $this->group_id; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
