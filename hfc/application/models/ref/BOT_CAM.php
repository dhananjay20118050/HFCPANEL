<?php

error_reporting( E_ALL );
ini_set( 'display_errors', 1);

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . 'libraries/REST_Controller.php';

//Selenium Library
require APPPATH . 'libraries/selenium/vendor/autoload.php';
//require APPPATH . 'libraries/tesseract/vendor/autoload.php';

class BOT_CAM extends REST_Controller
{
	public $host = 'http://localhost:4444/wd/hub';

    function __construct() {
        parent::__construct();
		if (isset($_SERVER['HTTP_ORIGIN']))
		 {
			
			header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
 		    header('Access-Control-Allow-Credentials: true');
  		    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
  		    header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding,X-Custom-Header");   
   			header('Access-Control-Max-Age: 86400');    // cache for 1 day

   		if ( "OPTIONS" === $_SERVER['REQUEST_METHOD'] )
			 {
    			die();
   			 }
		}
		$this->load->database();
		$this->load->model('BOT_CAM_model');
	}
	public function index_get()
	{
		return 'Bot Working!';
	}
	public function saveCamData_get($apps)
	{
		$response = $this->BOT_CAM_model->saveCamData($apps);
		return $this->set_response(array("status"=>"success","message"=>"Saved!","result"=>$response));
	}
}
?>