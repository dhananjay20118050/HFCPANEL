<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Index extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->helper('url');
	}

	public function index()
	{
		

		$data = array('WEBSITE_URL' => WEBSITE_URL, "WEBSITE_INSTANCE" => WEBSITE_INSTANCE);		
		$this->load->view('index');
	}
}
