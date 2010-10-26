function loadtip(){
	var myTips = new Tips('.yos_tree_e_img');
	
	//after tips loaded, call the function init ajax request
	init_ajax_request();
}

function init_ajax_request(){
	$$('img.yos_tree_e_img').addEvent('click', function(e){
		e.stop();
		var img_id = this.get('id');
		var resource_id = img_id.substr(10);
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
		myRequest.send('option=com_yos_resources_manager&controller=packages&task=get_rs_form&package_id='+package_id+'&rid='+resource_id+'&tmpl=component&r='+dummy);
	});
}

function submit_eform(){
	var resource_id = $('eform_res_id').get('value');
	var times_access = $('eform_times_access').get('value');
	var seconds = $('eform_seconds').get('value');
	
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
	myRequest.send('option=com_yos_resources_manager&controller=packages&task=save_rs_form&package_id='+package_id+'&rid='+resource_id+'&times_access='+times_access+'&seconds='+seconds+'&tmpl=component&r='+dummy);
}

function checkAll_role( n, fldName ) {
  if (!fldName) {
     fldName = 'cb';
  }
	var f = document.adminForm;
	var c = f.toggle_role.checked;
	var n2 = 0;
	for (i=0; i < n; i++) {
		cb = eval( 'f.' + fldName + '' + i );
		if (cb) {
			cb.checked = c;
			n2++;
		}
	}
	if (c) {
		document.adminForm.boxchecked.value = n2;
	} else {
		document.adminForm.boxchecked.value = 0;
	}
}

function checkAll_group( n, fldName ) {
  if (!fldName) {
     fldName = 'cb';
  }
	var f = document.adminForm;
	var c = f.toggle_group.checked;
	var n2 = 0;
	for (i=0; i < n; i++) {
		cb = eval( 'f.' + fldName + '' + i );
		if (cb) {
			cb.checked = c;
			n2++;
		}
	}
	if (c) {
		document.adminForm.boxchecked.value = n2;
	} else {
		document.adminForm.boxchecked.value = 0;
	}
}

function checkAll_payment_methods( n, fldName ) {
  if (!fldName) {
     fldName = 'cb';
  }
	var f = document.adminForm;
	var c = f.toggle_payment_methods.checked;
	var n2 = 0;
	for (i=0; i < n; i++) {
		cb = eval( 'f.' + fldName + '' + i );
		if (cb) {
			cb.checked = c;
			n2++;
		}
	}
	if (c) {
		document.adminForm.boxchecked.value = n2;
	} else {
		document.adminForm.boxchecked.value = 0;
	}
}

window.addEvent('domready', function() {
	$('panelpackage').addEvent('click', function(e) {
		block_pane();
		$('panelpackage_yos').style.display = '';
		$('panelpackage').setAttribute('class','open');
		$('open_tab_name').value = 'panelpackage';
		e.stop();
	});
	
	$('panelresource').addEvent('click', function(e) {
		block_pane();
		$('panelresource_yos').style.display = '';
		$('panelresource').setAttribute('class','open');
		$('open_tab_name').value = 'panelresource';
		e.stop();
	});
	
	$('panelrole').addEvent('click', function(e) {
		block_pane();
		$('panelrole_yos').style.display = '';
		$('panelrole').setAttribute('class','open');
		$('open_tab_name').value = 'panelrole';
		e.stop();
	});
	
	$('panelgroup').addEvent('click', function(e) {
		block_pane();
		$('panelgroup_yos').style.display = '';
		$('panelgroup').setAttribute('class','open');
		$('open_tab_name').value = 'panelgroup';
		e.stop();
	});
	
	$('panelpayment').addEvent('click', function(e) {
		block_pane();
		$('panelpayment_yos').style.display = '';
		$('panelpayment').setAttribute('class','open');
		$('open_tab_name').value = 'panelpayment';
		e.stop();
	});
	
	$('panelthankyou_page').addEvent('click', function(e) {
		block_pane();
		$('panelthankyou_page_yos').style.display = '';
		$('panelthankyou_page').setAttribute('class','open');
		$('open_tab_name').value = 'panelthankyou_page';
		e.stop();
	});
});

function block_pane(){
	var array_dt = $('Pane').getElementsByTagName("dt");
	for(i = 0; i < $('Pane').getElementsByTagName("dt").length; i++) {
		array_dt[i].setAttribute('class','closed');
		$('current').getElementsByTagName("dd")[i].style.display = 'none';
	}
	
}