<?php

error_reporting( E_ALL );
ini_set( 'display_errors', 1);

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . 'libraries/REST_Controller.php';

//Selenium Library
require APPPATH . 'libraries/selenium/vendor/autoload.php';

class BOT_AUTO_QC extends REST_Controller
{
    function __construct() {
        parent::__construct();
		if (isset($_SERVER['HTTP_ORIGIN'])){
			header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
 		    header('Access-Control-Allow-Credentials: true');
  		    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
  		    header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding,X-Custom-Header");   
   			header('Access-Control-Max-Age: 86400');    // cache for 1 day
   			if ( "OPTIONS" === $_SERVER['REQUEST_METHOD'] ){
    			die();
   			}
		}
		$this->load->database();
		$this->load->model('BOT_AUTO_QC_model');
	}
	public function startAutoQC_get($apps){
		$response = $this->BOT_AUTO_QC_model->startAutoQC($apps);
		return $this->set_response(array("status"=>"success","message"=>"Done!","result"=>$response));
	}
	public function getDifference_get($app){
		$response = $this->BOT_AUTO_QC_model->getDifference($app);
		return $this->set_response(array("status"=>"success","message"=>"Done!","result"=>$response));
	}
	public function showQCExceptions_get($app){
		$response = $this->BOT_AUTO_QC_model->showQCExceptions($app);
		return $this->set_response(array("status"=>"success","message"=>"Done!","result"=>$response));
	}
}
?>