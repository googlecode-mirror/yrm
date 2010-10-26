function loadtip(){
	var myTips = new Tips('.yos_tree_e_img');
	
	//after tips loaded, call the function init ajax request
	init_ajax_request();
}

function init_ajax_request(){
	$$('img.yos_tree_e_img').addEvent('click', function(e){
		e.stop();
		var img_id = this.get('id');
		var rsb_id = img_id.substr(10);
		
		$('item_cfg').setStyle('display', 'block');
		$('item_cfg_content').addClass('loading');
		$('item_cfg_content').set('html', '');
		//innit ajax
		var myRequest = new Request({
			url: 'index.php', 
			method: 'get', 
			evalScripts: true, 
			onSuccess: function(responseText, responseXML) {
				$('item_cfg_content').removeClass('loading');
		    	$('item_cfg_content').set('html', responseText);
			}
		});
		var dummy = $time() + $random(0, 100);
		myRequest.send('option=com_yos_resources_manager&controller=user&task=get_rsb_form&user_id='+user_id+'&rid='+rsb_id+'&tmpl=component&r='+dummy);
	});
}
function submit_eform(){
	var rsb_id			 = $('eform_res_id').get('value');
	var description 	 = $('eform_description').get('value');
	var redirect_message = $('eform_redirect_message').get('value');
	var redirect_url 	 = $('eform_redirect_url').get('value');
	var start_date 		 = $('eform_start_date').get('value');
	var end_date 		 = $('eform_end_date').get('value');
	$('item_cfg_content').addClass('loading');
	$('item_cfg_content').set('html', '');
	var myRequest = new Request({
		url: 'index.php', 
		method: 'post', 
		evalScripts: true, 
		onSuccess: function(responseText, responseXML) {
			$('item_cfg_content').removeClass('loading');
	    	$('item_cfg_content').set('html', responseText);
	    	//hide block after 3 seconds
	    	var f = function(){$('item_cfg').setStyle('display', 'none')};
	    	f.delay(3000);
		}
	});
	var dummy = $time() + $random(0, 100);
	myRequest.send('option=com_yos_resources_manager&controller=user&task=save_rsb_form&user_id='+user_id+'&rid='+rsb_id+'&description='+description+'&redirect_message='+redirect_message+'&redirect_url='+redirect_url+'&start_date='+start_date+'&end_date='+end_date+'&tmpl=component&r='+dummy);
}


