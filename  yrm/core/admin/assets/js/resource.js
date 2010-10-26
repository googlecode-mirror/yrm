window.addEvent('domready', function() {
	$('res_parent_id').addEvent('focus', function(e) {
		$$('.yos_hidden').each(function(yelement){
			yelement.style.display = 'none';
		});
		$('res_parent_node_comment').style.display = 'block';
		e.stop();
	});
	
	$('res_name').addEvent('focus', function(e) {
		$$('.yos_hidden').each(function(yelement){
			yelement.style.display = 'none';
		});
		$('res_name_comment').style.display = 'block';
		e.stop();
	});
	
	$('res_affectedF').addEvent('focus', function(e) {
		$$('.yos_hidden').each(function(yelement){
			yelement.style.display = 'none';
		});
		$('res_affected_comment').style.display = 'block';
		e.stop();
	});
	
	$('res_affectedB').addEvent('focus', function(e) {
		$$('.yos_hidden').each(function(yelement){
			yelement.style.display = 'none';
		});
		$('res_affected_comment').style.display = 'block';
		e.stop();
	});
	
	$('res_affectedBF').addEvent('focus', function(e) {
		$$('.yos_hidden').each(function(yelement){
			yelement.style.display = 'none';
		});
		$('res_affected_comment').style.display = 'block';
		e.stop();
	});
	
	$('res_type').addEvent('focus', function(e) {
		$$('.yos_hidden').each(function(yelement){
			yelement.style.display = 'none';
		});
		$('res_type_comment').style.display = 'block';
		e.stop();
	});
	
	$('res_option').addEvent('focus', function(e) {
		$$('.yos_hidden').each(function(yelement){
			yelement.style.display = 'none';
		});
		$('res_option_comment').style.display = 'block';
		e.stop();
	});
	
//	$('res_task').addEvent('focus', function(e) {
//		$$('.yos_hidden').each(function(yelement){
//			yelement.style.display = 'none';
//		});
//		$('res_task_comment').style.display = 'block';
//		e.stop();
//	});
	
//	$('res_view').addEvent('focus', function(e) {
//		$$('.yos_hidden').each(function(yelement){
//			yelement.style.display = 'none';
//		});
//		$('res_view_comment').style.display = 'block';
//		
//		e.stop();
//	});
	
	$('res_params').addEvent('focus', function(e) {
		$$('.yos_hidden').each(function(yelement){
			yelement.style.display = 'none';
		});
		$('res_params_comment').style.display = 'block';
		e.stop();
	});
	
	$('res_plug_in').addEvent('focus', function(e) {
		$$('.yos_hidden').each(function(yelement){
			yelement.style.display = 'none';
		});
		$('res_plugin_comment').style.display = 'block';
		
		var plgid = $('res_plug_in').value;
		if(plgid > 0){
			//innit ajax
			
			var myRequest = new Request({
				url: 'index.php', 
				method: 'get', 
				evalScripts: true, 
				onSuccess: function(responseText, responseXML) {
					$('res_plg_content').removeClass('loading');
			    	$('res_plg_content').set('html', responseText);
				}
			});
			var dummy = $time() + $random(0, 100);
			myRequest.send('option=com_yos_resources_manager&controller=resources&task=desplg_form&plgid='+plgid+'&tmpl=component&r='+dummy);
		}
		e.stop();
	});
	
	$('res_plug_in').addEvent('change', function(e) {
		var plgid = $('res_plug_in').value;
		if(plgid > 0){
			//innit ajax
			
			var myRequest = new Request({
				url: 'index.php', 
				method: 'get', 
				evalScripts: true, 
				onSuccess: function(responseText, responseXML) {
					$('res_plg_content').removeClass('loading');
			    	$('res_plg_content').set('html', responseText);
				}
			});
			var dummy = $time() + $random(0, 100);
			myRequest.send('option=com_yos_resources_manager&controller=resources&task=desplg_form&plgid='+plgid+'&tmpl=component&r='+dummy);
		}
		e.stop();
	});
	
	$('res_redirect_url').addEvent('focus', function(e) {
		$$('.yos_hidden').each(function(yelement){
			yelement.style.display = 'none';
		});
		$('res_redirect_url_comment').style.display = 'block';
		e.stop();
	});
	
	$('res_redirect_msg').addEvent('focus', function(e) {
		$$('.yos_hidden').each(function(yelement){
			yelement.style.display = 'none';
		});
		$('res_redirect_msg_comment').style.display = 'block';
		e.stop();
	});
	
	$('res_description').addEvent('focus', function(e) {
		$$('.yos_hidden').each(function(yelement){
			yelement.style.display = 'none';
		});
		$('res_description_comment').style.display = 'block';
		e.stop();
	});
	
	$('res_sticky0').addEvent('focus', function(e) {
		$$('.yos_hidden').each(function(yelement){
			yelement.style.display = 'none';
		});
		$('res_sticky_comment').style.display = 'block';
		e.stop();
	});
	
	$('res_sticky1').addEvent('focus', function(e) {
		$$('.yos_hidden').each(function(yelement){
			yelement.style.display = 'none';
		});
		$('res_sticky_comment').style.display = 'block';
		e.stop();
	});
	
	$('res_published0').addEvent('focus', function(e) {
		$$('.yos_hidden').each(function(yelement){
			yelement.style.display = 'none';
		});
		$('res_published_comment').style.display = 'block';
		e.stop();
	});
	
	$('res_published1').addEvent('focus', function(e) {
		$$('.yos_hidden').each(function(yelement){
			yelement.style.display = 'none';
		});
		$('res_published_comment').style.display = 'block';
		e.stop();
	});
	
});
