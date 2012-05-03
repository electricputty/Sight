<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * Sight accessability Plugin
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Plugin
 * @author		Rob Hodges
 * @link		http://www.electricputty.co.uk
 */

$plugin_info = array(
	'pi_name'		=> 'Sight',
	'pi_version'	=> '1.0',
	'pi_author'		=> 'Rob Hodges',
	'pi_author_url'	=> 'http://www.electricputty.co.uk',
	'pi_description'=> 'Switches font sizes and high contrast mode',
	'pi_usage'		=> Sight::usage()
);


class Sight {

	public $return_data;
	public $is_high = false;
	public $font_size = 'one';
    
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
				
		if ($this->EE->uri->uri_string == "")
		{
			$this_url = $this->EE->config->item('site_url');
		}
		else
		{
			$this_url = $this->EE->config->item('site_url').$this->EE->uri->uri_string;
		}

		$display_seg = $this->EE->input->get('display') ? $this->EE->input->get('display') : '';
		$this->is_high = false;
		
		//if no active session we start a new one
		if (session_id() == "") session_start();
	
		if(strlen($display_seg))
		{
			switch ($display_seg)
			{
				case "one":
					$_SESSION["live_display"] = "one";
					$this->font_size = "one";
					break;
				case "two":
					$_SESSION["live_display"] = "two";
					$this->font_size = "two";
					break;
				case "three":
					$_SESSION["live_display"] = "three";
					$this->font_size = "three";
					break;
				case "high":
					$_SESSION["live_display"] = "high";
					$this->is_high = true;
					break;
			}
		}
		else
		{
			if (isset($_SESSION["live_display"]))
			{
				if($_SESSION["live_display"] == "one") $this->font_size = "one";
				if($_SESSION["live_display"] == "two") $this->font_size = "two";
				if($_SESSION["live_display"] == "three") $this->font_size = "three";
				if($_SESSION["live_display"] == "high") $this->is_high = true;
			}
		}
		
	}
	
	public function current_url() {
		$this_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$this_url = explode('?', $this_url);
		return $this_url[0];
	}
	
	public function font_size() {
		if (!$this->is_high) {
			$content = array('size' => $this->font_size);
			return $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, array($content));
		}
	}
	
	public function high_con() {
		if($this->is_high) {
			return $this->EE->TMPL->tagdata;
		}
	}
	
	// High contrast switching
	public function high_con_switcher() {
		$this_url = $this->current_url();
		$con_version = '?display=high';
		$con_name = 'High contrast';
		
		if (isset($_SESSION["live_display"]) && $_SESSION["live_display"] == "high")
		{
			$con_version = '?display=one';
			$con_name = 'Normal contrast';
		}
		
		$html = "<a href=\"".$this_url.$con_version."\">".$con_name."</a>";

		return $html;
	}
		
	// ----------------------------------------------------------------
	
	/**
	 * Plugin Usage
	 */
	public static function usage()
	{
		ob_start();
?>
Sight
===========================
Sight is a plugin for enabling/switching between certain accessability CSS file options.


Usage
===========================
This plugin gets initially activated by one of the four query strings, ?display=high/one/two/three which will then activate the plugin and set a session variable so that the setting persists across pages (without having to keep the query string). The default value it returns is 'one' for the {size} parameter inside the {exp:sight:font_size} tag pair. Only one of the 4 CSS files is active at any time.


Tags
===========================
There is are four tags for Sight, the font size controller, the high contrast controller, the high contrast link controller and the current URL (for easy linking).

Tag pairs:
------------
{exp:sight:font_size} - Used for controlling which font-size CSS to load, uses the variable {size} which has three values, 'one', 'two' and 'three'. These should correspond to your CSS files, e.g. size-one.css. If the high contrast CSS is active, this tag returns nothing.

Example Usage:
{exp:sight:font_size}
<link rel="stylesheet" href="{site_url}assets/site_css/size-{size}.css?v=2">
{/exp:sight:font_size}

---

{exp:sight:high_con} - Used for controlling whether or not the high contrast CSS is loaded. If it is, the tag pair simply returns its contents. If not, then it returns nothing.

Example Usage:
{exp:sight:high_con}
<link rel="stylesheet" href="{site_url}assets/site_css/high-contrast.css" />
{/exp:sight:high_con}

Single tags:
------------
{exp:sight:high_con_switcher} - Returns a link with the current URL (plus ?display=high) and the text 'Normal Contrast' or 'High Contrast' depending on what mode the plugin is in.

---

{exp:sight:current_url} - Returns the current URL, useful for the font sizing links. 

Example Usage:
<li>Change text size: <a href="{exp:sight:current_url}?display=one" id="sizeOne">A</a><a href="{exp:sight:current_url}?display=two" id="sizeTwo">A</a><a href="{exp:sight:current_url}?display=three" id="sizeThree">A</a></li>


<?php
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	}
}


/* End of file pi.high_con.php */
/* Location: /system/expressionengine/third_party/high_con/pi.high_con.php */