<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/**
*
* @copyright	Copyright (C) 2010 YOS.,JSC. All rights reserved.
* @license	GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/


/**
*
* The ps_payment class, containing the default payment processing code
* for payment methods that have no own class
*
*/
class pcl_payment {

    var $classname = "pcl_payment";
  
    /**
    * Show all configuration parameters for this payment method
    * @returns boolean False when the Payment method has no configration
    */
     function show_configuration() {
     	 /* ... */
     }
    function show_global_configuration() {
    	$path = dirname(__FILE__);
       
        include_once($path.DS.$this->classname.".cfg.php");
        $document =& JFactory::getDocument();
		JHTML::_('behavior.mootools');
		$document->addStyleSheet(JURI::root().'components/com_yos_resources_manager/paymentclass/assets/css/payment.css');
    	?>    	
    <div class="payment">
	    	<table class="adminform">      
	         <tr class="row0">
	            <td width="20%"><strong><?php echo JText::_('Order Status for successful transactions') ?></strong></td>
	            <td>
	                <select class="inputbox" name="COMPLETED">
						<option <?php if (@COMPLETED == 'P') echo "selected=\"selected\""; ?> value="P">P</option>
						<option <?php if (@COMPLETED == 'C') echo "selected=\"selected\""; ?> value="C">C</option>
						<option <?php if (@COMPLETED == 'X') echo "selected=\"selected\""; ?> value="X">X</option>
						<option <?php if (@COMPLETED == 'R') echo "selected=\"selected\""; ?> value="R">R</option>					
					</select>
	            </td>
	            <td><?php echo JText::_('Select the order status to which the actual order is set. If using download selling options: select the status which enables the download (then the customer is instantly notified about the download via e-mail).') ?>
	            </td>
	        </tr>
	         <tr class="row1">
	            <td><strong><?php echo JText::_('Order Status for Pending Payments') ?></strong></td>
	            <td>
	                <select class="inputbox" name="PENDING">
						<option <?php if (@PENDING == 'P') echo "selected=\"selected\""; ?> value="P">P</option>
						<option <?php if (@PENDING == 'C') echo "selected=\"selected\""; ?> value="C">C</option>
						<option <?php if (@PENDING == 'X') echo "selected=\"selected\""; ?> value="X">X</option>
						<option <?php if (@PENDING == 'R') echo "selected=\"selected\""; ?> value="R">R</option>					
					</select>
	            </td>
	            <td><?php echo JText::_('The order Status to which Orders are set, which have no completed Payment Transaction. The transaction was not cancelled in this case, but it is just pending and waiting for completion.') ?>
	            </td>
	        </tr>
	         <tr class="row0">
	            <td><strong><?php echo JText::_('Order Status for Refunded transactions') ?></strong></td>
	            <td>
	                <select class="inputbox" name="REFUNDED">
						<option <?php if (@REFUNDED == 'P') echo "selected=\"selected\""; ?> value="P">P</option>
						<option <?php if (@REFUNDED == 'C') echo "selected=\"selected\""; ?> value="C">C</option>
						<option <?php if (@REFUNDED == 'X') echo "selected=\"selected\""; ?> value="X">X</option>
						<option <?php if (@REFUNDED == 'R') echo "selected=\"selected\""; ?> value="R">R</option>					
					</select>
	            </td>
	            <td><?php echo JText::_('Select an order status for Refunded Payment transactions.') ?>
	            </td>
	        </tr>
	         <tr class="row1">
	            <td><strong><?php echo JText::_('Order Status for Failed transactions') ?></strong></td>
	            <td>
	                <select class="inputbox" name="CANCELLED">
						<option <?php if (@CANCELLED == 'P') echo "selected=\"selected\""; ?> value="P">P</option>
						<option <?php if (@CANCELLED == 'C') echo "selected=\"selected\""; ?> value="C">C</option>
						<option <?php if (@CANCELLED == 'X') echo "selected=\"selected\""; ?> value="X">X</option>
						<option <?php if (@CANCELLED == 'R') echo "selected=\"selected\""; ?> value="R">R</option>					
					</select>
	            </td>
	            <td><?php echo JText::_('Select an order status for failed Payment transactions.') ?>
	            </td>
	        </tr>
	        
	      </table>
      </div>
    	<?php
      /* ... */
    }
    
    function has_configuration() {
      // return false if there's no configuration
      return false;
   }
   
  /**
	* Returns the "is_writeable" status of the configuration file
	* @param void
	* @returns boolean True when the configuration file is writeable, false when not
	*/
   function configfile_writeable() {
      return is_writeable( CLASSPATH."payment/".$this->classname.".cfg.php" );
   }
   
  /**
	* Returns the "is_readable" status of the configuration file
	* @param void
	* @returns boolean True when the configuration file is writeable, false when not
	*/
   function configfile_readable() {
      return is_readable( CLASSPATH."payment/".$this->classname.".cfg.php" );
   }
   
  /**
	* Writes the configuration file for this payment method
	* @param array An array of objects
	* @returns boolean True when writing was successful
	*/
   function write_configuration( &$d=null ) {
       $my_config_array = array(                             
                              "COMPLETED" => JRequest::getVar('COMPLETED'),
                              "PENDING" => JRequest::getVar('PENDING'),
                              "REFUNDED" => JRequest::getVar('REFUNDED'),
                              "CANCELLED" => JRequest::getVar('CANCELLED')
                            );
      $config = "<?php\n";
      $config .= "if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); \n\n";
      foreach( $my_config_array as $key => $value ) {
        $config .= "define ('$key', '$value');\n";
      }
     
      $config .= "?>";  
      //var_dump(htmlspecialchars($config));   die();  
      if ($fp = fopen(dirname(__FILE__).DS.$this->classname.".cfg.php", "w")) {      	 
          fputs($fp, $config, strlen($config));
         
          fclose ($fp);     
          return true;
     }
     else
        return false;
   }
   
  /**************************************************************************
  ** name: process_payment()
  ** returns: 
  ***************************************************************************/
   function process_payment($order_number, $order_total, &$d,$return_url=null,$notify_url=null,$cancel_url=null) {
        return true;
    }
   
}
