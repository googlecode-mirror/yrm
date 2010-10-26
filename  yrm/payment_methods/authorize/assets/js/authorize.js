window.addEvent('domready', function() { // wait for the content
	// our uploader instance
		$('authorize_next').addEvent('click', function(e) {
			validatedetails();			
		});		
	});
	
	function validatedetails()
	{
		if($('order_payment_name').value==""){
			alert ("Please enter Name On Card");
			$('order_payment_name').focus();
			return ;
		}
		if($('order_payment_number').value==""){
			alert ("Please enter Credit Card Number");
			$('order_payment_number').focus();
			return ;
		}	
		card=$('order_payment_number').value;
		if(isNaN(card)){
			alert("Enter Number of Credit Card Number");
			$('order_payment_number').focus();
			$('order_payment_number').select();
			return ;
		}		
		if($('credit_card_code').value==""){
			alert ("Please enter  Credit Card Security Code");
			$('credit_card_code').focus();
			return ;
		}
		card=$('credit_card_code').value;
		if(isNaN(card)){
			alert("Enter Number of credit card code");				
			$('credit_card_code').focus();
			$('credit_card_code').select();
			return ;
		}
		$('payment').submit();
	}