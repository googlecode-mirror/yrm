<?php defined('_JEXEC') or die( 'Restricted access' ); ?>
<?php
	echo JText::_('THANK_YOU');
	echo nl2br($this->data->text) .'<br />';
	echo JText::_('NAME').': '.$this->data->name.'<br />'; 
	echo JText::_('DESCRIPTION').': '.$this->data->description.'<br />'; 
	echo JText::_('PRICE').': '.$this->data->value.' '.$this->data->currency_code.'<br />';
	if ($this->data->return_url) {
		echo '<a href="'.$this->data->return_url.'">';
		echo JText::_('CLICK_HERE_TO_VIEW_RESOURCE').'</a>';
	}	
	
?>
