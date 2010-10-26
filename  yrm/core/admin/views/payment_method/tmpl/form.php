<?php 
	defined('_JEXEC') or die('Restricted access'); 

	jimport('joomla.filesystem.file');
	jimport('joomla.filesystem.folder');	
	
	JHTML::_('behavior.tooltip'); 
	JToolBarHelper::title(  JText::_( 'Payment Method Form' ) ,'Payment Method Form' );
	JToolBarHelper::save();
	JToolBarHelper::apply();
	JToolBarHelper::cancel();
	$row = null;
	for ($i=0, $n=count( $this->data ); $i < $n; $i++)
	{	
		if ( $this->data[$i]->id == $this->payment_id[0]) {
			$row = &$this->data[$i];
			break;
		}		
	}

//	$path = dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'paymentclass';
	$path = JPATH_COMPONENT_SITE.DS.'paymentclass';
	if ( $row) {
    	if (include_once($path.DS.'pcl_'.$row->payment_class.DS.$row->payment_class.'.php' )) {
    		eval( "\$_PAYMENT = new ".$row->payment_class."();");
    	}
	}else {
    	include_once( $path.DS."pcl_payment.php" );
    	$_PAYMENT = new pcl_payment();
	}	
?>
<form action="index.php?option=com_yos_resources_manager" method="post" name="adminForm" id="adminForm">	
<?php
jimport('joomla.html.pane');

$pane =& JPane::getInstance('tabs',array('startOffset'=>0)); 
echo $pane->startPane( 'pane' );

// PANEL 1
echo $pane->startPanel( JText::_('Payment Method Form'), 'panel1' );
?>
<table class="adminform">
<tr class="row0">
	<td width="15%"><?php echo JText::_('Active?'); ?>:</td>
	<td><input class="inputbox" type="checkbox" <?php if($row) {if($row->published =='1') { echo 'checked=cheched'; }} ?> value="1" name="payment_enabled"/></td>
</tr>
<tr class="row1"> 
	<td><?php echo JText::_('Payment Method Name'); ?>:</td>
	<td><input class="inputbox" type="text" size="20" value="<?php if($row) {echo $row->name;} ?>" name="payment_method_name"/></td>
</tr>
<tr  class="row0"> 
	<td><?php echo JText::_('Code'); ?>:</td>
	<td><input class="inputbox" type="text" size="20" value="<?php if($row) {echo $row->payment_method_code; } ?>" name="payment_method_code"/></td>
</tr>
<tr class="row1">
	<td valign="top"><?php echo JText::_('Payment class name'); ?>:</td>
	<td>	
		<div style="float:left;"><?php  echo $row->payment_class; ?></div>
		<div style="margin: 0 0 0 150px; float:left;"><?php  echo $row->description; ?></div>		
	</td>	
</tr>
</table>
<?php
echo $pane->endPanel();
// PANEL 2
echo $pane->startPanel( JText::_('Payment Configuration'), 'panel2' );
	
	 $_PAYMENT->show_configuration();
?>
	<!--<textarea class="inputbox" rows="50" cols="120" name="payment_extrainfo"><?php echo base64_decode($row->payment_extrainfo); ?></textarea>-->
<?php
echo $pane->endPanel();
// PANEL 3
echo $pane->startPanel( JText::_('Configuration'), 'panel3' );
	 	
		include_once( $path.DS."pcl_payment.php" );
    	$_PAYMENT = new pcl_payment();
	 	$_PAYMENT->show_global_configuration();
?>
	<!--<textarea class="inputbox" rows="50" cols="120" name="payment_extrainfo"><?php echo base64_decode($row->payment_extrainfo); ?></textarea>-->
<?php
echo $pane->endPanel();	
echo $pane->endPane();

?>
	<input type="hidden" name="option" value="com_yos_resources_manager" />
	<input type="hidden" name="controller" value="payment_method" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="payment_method" />
	<input type="hidden" name="payment_id" value="<?php echo $this->payment_id[0]; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>