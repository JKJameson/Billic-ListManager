<?php
class ListManager {
	public $settings = array(
		'name' => 'ListManager',
		'description' => 'This allows other modules to provide a search of data as well as optionally add new items.',
	);
	public $config;
	public $id;
	public $show_search = false;
	public $show_add = false;
	function configure($config) {
		$this->config = $config;
		$this->id = md5(serialize($this->settings));
		if (isset($this->config['search'])) {
			$hasGETValues = false;
			foreach ($this->config['search'] as $k => $v) {
				if (isset($_GET[$k])) {
					$hasGETValues = true;
					$_POST[$k] = urldecode($_GET[urlencode($k)]);
				}
			}
			if ($hasGETValues) {
				$_POST['search'] = '';
				$this->show_search = true;
			}
		}
		if (isset($_POST['add'])) {
			$this->show_add = true;
		}
	}
	function search_link() {
		return '<div id="search-click-' . $this->id . '"' . ($this->show_search === true ? ' style="display:none;"' : '') . '><i class="icon-search"></i> Press <b>S</b> on your keyboard to ' . (isset($this->config['search_title']) ? $this->config['search_title'] : 'Search') . '</div>' . '<script>addLoadEvent(function() { function searchbox_' . $this->id . '() { if ($("#search-' . $this->id . '").is(":hidden")) { $("#search-' . $this->id . '").slideDown("slow"); $("#search-click-' . $this->id . '").hide("slow"); } else { $("#search-' . $this->id . '").slideUp("slow"); $("#search-click-' . $this->id . '").show("slow"); } } $(\'#search-click-' . $this->id . '\').click(function() { searchbox_' . $this->id . '(); }); $(document).keypress(function(event) { if ($(event.target).is(\'input, textarea\')) { return; } if(event.charCode==115) { searchbox_' . $this->id . '(); } }); });</script>';
	}
	function search_box() {
		if (!isset($this->config['search'])) {
			return;
		}
		$return = '<div id="search-' . $this->id . '"' . ($this->show_search === true ? '' : ' style="display:none;"') . '><form method="POST" action="' . preg_replace('~/POST/(.*?)/~', '/', $_SERVER['REQUEST_URI']) . '"><table class="table table-striped"><tr><th colspan="8">' . (isset($this->config['search_title']) ? $this->config['search_title'] : 'Search') . '</th></tr><tr>';
		$i = 0;
		foreach ($this->config['search'] as $setting => $type) {
			$i++;
			if ($i == 4) {
				$return.= '</tr><tr>';
			}
			$return.= '<td>' . ucwords(str_replace('_', ' ', $setting)) . '</td><td>';
			if (is_array($type)) {
				$return.= '<select class="form-control" name="' . $setting . '">';
				foreach ($type as $v) {
					$return.= '<option value="' . $v . '"' . ($_POST[$setting] == $v ? ' selected' : '') . '>' . ucwords($v) . '</option>';
				}
				$return.= '</select>';
			} else {
				switch ($type) {
					case 'text':
						$return.= '<input type="text" class="form-control" name="' . $setting . '" value="' . safe($_POST[$setting]) . '">';
					break;
					case 'date':
						$return.= '<input type="text" class="form-control" name="' . $setting . '" value="' . safe($_POST[$setting]) . '" id="' . $setting . '">';
						$return.= '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.0/css/bootstrap-datepicker.min.css">';
						$return.= '<script>addLoadEvent(function() { $.getScript( "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.0/js/bootstrap-datepicker.min.js", function( data, textStatus, jqxhr ) { $( "#' . $setting . '" ).datepicker({ format: "yyyy-mm-dd" }); }); });</script>';
					break;
				}
			}
			$return.= '</td>';
		}
		$return.= '</tr><tr><td colspan="6" align="center"><button type="submit" class="btn btn-success" name="search"><i class="icon-search-find"></i> Search &raquo;</button></td></tr></table></form><br><br></div>';
		return $return;
	}
	function add_link() {
		echo '<div id="add-click-' . $this->id . '"' . ($this->show_add === true ? ' style="display:none;"' : '') . '><i class="icon-plus-sign"></i> Press <b>A</b> on your keyboard to ' . (isset($this->config['add_title']) ? $this->config['add_title'] : 'Add a row') . '</div>';
		echo '<script>addLoadEvent(function() { function addbox_' . $this->id . '() { if ($("#add-' . $this->id . '").is(":hidden")) { $("#add-' . $this->id . '").slideDown("slow"); $("#add-click-' . $this->id . '").hide("slow"); } else { $("#add-' . $this->id . '").slideUp("slow"); $("#add-click-' . $this->id . '").show("slow"); }	} $(\'#add-click-' . $this->id . '\').click(function() { addbox_' . $this->id . '(); }); $(document).keypress(function(event) { if ($(event.target).is(\'input, textarea\')) { return; } if(event.charCode==97) { addbox_' . $this->id . '(); } }); });</script>';
	}
	function add_box() {
		if (!isset($this->config['add'])) {
			return;
		}
		$return = '<div id="add-' . $this->id . '"' . ($this->show_add === true ? '' : ' style="display:none;"') . '><form method="POST"><table class="table table-striped"><tr><th colspan="2">' . (isset($this->config['add_title']) ? $this->config['add_title'] : 'Add') . '</th></tr><tr>';
		foreach ($this->config['add'] as $setting => $type) {
			$return.= '<tr><td>' . ucwords(str_replace('_', ' ', $setting)) . '</td><td>';
			if (is_array($type)) {
				$return.= '<select class="form-control" name="' . $setting . '">';
				foreach ($type as $v) {
					$return.= '<option value="' . $v . '"' . ($_POST[$setting] == $v ? ' selected' : '') . '>' . ucwords($v) . '</option>';
				}
				$return.= '</select>';
			} else {
				switch ($type) {
					case 'text':
						$return.= '<input type="text" class="form-control" name="' . $setting . '" value="' . safe($_POST[$setting]) . '">';
					break;
					case 'checkbox':
						$return.= '<input type="checkbox" name="' . $setting . '" value="1"' . ($_POST[$setting] == 1 ? ' checked' : '') . '>';
					break;
					case 'date':
						$return.= '<input type="text" class="form-control" name="' . $setting . '" value="' . safe($_POST[$setting]) . '" id="' . $setting . '">';
						$return.= '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.0/css/bootstrap-datepicker.min.css">';
						$return.= '<script>addLoadEvent(function() { $.getScript( "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.0/js/bootstrap-datepicker.min.js", function( data, textStatus, jqxhr ) { $( "#' . $setting . '" ).datepicker({ format: "yyyy-mm-dd" }); }); });</script>';
					break;
				}
			}
			$return.= '</td></tr>';
		}
		$return.= '<tr><td colspan="2" align="center"><input type="submit" class="btn btn-success" name="add" value="Add &raquo;"></td></tr></table></form><br><br></div>';
		return $return;
	}
}
