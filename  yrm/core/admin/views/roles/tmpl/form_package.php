<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
	$cid = JRequest::getVar( 'cid_package', array(0), '', 'array' );
	$package_id = JRequest::getVar('package_id');
	if ($cid[0]>0) {
		$package_id = $cid[0];
	}
	JArrayHelper::toInteger($cid, array(0));
	$text = ( $this->cmd == 'edit_package') ? JText::_( 'EDIT' ) : JText::_( 'NEW' );

	JToolBarHelper::title(  JText::_( 'ROLE_PACKAGE' ).': <small><small>[ ' . $text.' ]</small></small>' );
	
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
					<?php echo JText::_( 'SELECT_PACKAGE' ); ?>:
				</label>
			</td>
			<td>
				<?php echo $this->lists['packages'];?>
			</td>
		</tr>
		<?php if($package_id > 0){
			?>
		<tr>
			<td width="110" class="key">
				<label for="title">
					<?php echo JText::_( 'PACKAGE_ID' ); ?>:
				</label>
			</td>
			<td>
				<strong><?php echo $this->row->id;?></strong>
			</td>
		</tr>
		<tr>
			<td width="110" class="key">
				<label for="title">
					<?php echo JText::_( 'PACKAGE_VALUE' ); ?>:
				</label>
			</td>
			<td>
				<?php  echo $this->row->value; ?>
			</td>
		</tr>
		<tr>
			<td width="110" class="key">
				<label for="title">
					<?php echo JText::_( 'PACKAGE_PUBLISHED' ); ?>:
				</label>
			</td>
			<td>
				<?php if($this->row->published ==1){echo JText::_('YES');}else{ echo JText::_('NO');} ?>
			</td>
		</tr>
		<?php } ?>
		<tr>
			<td width="110" class="key">
				<label for="title">
					<?php echo JText::_( 'OBJECT_ID' ); ?>:
				</label>
			</td>
			<td>
				<?php  echo $this->role_id; ?>
			</td>
		</tr>
		<tr>
			<td width="110" class="key">
				<label for="title">
					<?php echo JText::_( 'OBJECT_TYPE' ); ?>:
				</label>
			</td>
			<td>
				<?php  echo JText::_('OBJECT_TYPE_ROLE'); ?>
			</td>
		</tr>
		<tr>
			<td width="110" class="key">
				<label for="title">
					<?php echo JText::_( 'TIMES_ACCESS' ); ?>:
				</label>
			</td>
			<td>
				<input type="text" name="times_access" value="<?php if($this->cmd=='edit_package') echo $this->row_ob->times_access;?>" size="10">
			</td>
		</tr>
		<tr>
			<td width="110" class="key">
				<label for="title">
					<?php echo JText::_( 'SECONDS' ); ?>:
				</label>
			</td>
			<td>
				<input type="text" name="seconds" value="<?php if($this->cmd=='edit_package') echo $this->row_ob->seconds;?>" size="10">
			</td>
		</tr>
	</table>

	<input type="hidden" name="task" value="<?php echo $this->cmd;?>" />
	<input type="hidden" name="cmd" value="<?php echo $this->cmd;?>" />
	<input type="hidden" name="cid_package[]" value="<?php echo $cid[0];?>" />
	<input type="hidden" name="option" value="com_yos_resources_manager" />
	<input type="hidden" name="controller" value="roles" />
	<input type="hidden" name="view" value="roles" />
	<input type="hidden" name="role_id" value="<?php echo $this->role_id; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
