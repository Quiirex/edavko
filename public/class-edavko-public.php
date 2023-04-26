<?php
class edavko_Public
{
	private $edavko;
	private $version;

	public function __construct($edavko, $version)
	{

		$this->edavko = $edavko;
		$this->version = $version;
	}

	public function enqueue_styles()
	{
		wp_enqueue_style($this->edavko, plugin_dir_url(__FILE__) . 'css/edavko-public.css', array(), $this->version, 'all');
	}

	public function enqueue_scripts()
	{
		wp_enqueue_script($this->edavko, plugin_dir_url(__FILE__) . 'js/edavko-public.js', array('jquery'), $this->version, false);
	}
}
