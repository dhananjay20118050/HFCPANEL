<?php

error_reporting( E_ALL );
ini_set( 'display_errors', 1);

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . 'libraries/REST_Controller.php';

//Selenium Library
require APPPATH . 'libraries/selenium/vendor/autoload.php';

class BOT extends REST_Controller
{
	public $host = 'http://localhost:5555/wd/hub';

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
		$this->load->model('BOT_model');
	}
	public function index_get()
	{
		return 'Bot Working!';
	}
	function test_get()
	{
		$response = $this->BOT_model->test();
		return $this->set_response(array("status"=>"success","message"=>"Bot Test","result"=>$response));
	}
	function finalfun_get($userId = 0, $appNo = 0)
	{
		$response = $this->BOT_model->finalfun($userId, $appNo);
		return $this->set_response(array("status"=>"success","message"=>"Bot Executed","result"=>$response));
	}
	function start_get($userId = 0, $appNo = 0)
	{
		$response = $this->BOT_model->start($userId, $appNo);
		return $this->set_response(array("status"=>"success","message"=>"Bot Executed","result"=>$response));
	}
	function resume_get($processId = 0, $userId = 0, $appNo = 0, $process_dtl_id = 3, $end_process_dtl_id = 0)
	{
		$response = $this->BOT_model->resume($processId, $userId, $appNo, $process_dtl_id, $end_process_dtl_id);
		return $this->set_response(array("status"=>"success","message"=>"Bot Executed","result"=>$response));
	}
	function refresh_get()
	{
		$response = $this->BOT_model->refresh();
		return $this->set_response(array("status"=>"success","message"=>"Refreshed!","result"=>$response));
	}
	function downloadCibilReport_get($txtAppFormNo, $refNo = 0)
	{
		$response = $this->BOT_model->downloadCibilReport($txtAppFormNo, $refNo);
		return $this->set_response(array("status"=>"success","message"=>"Downloaded!","result"=>$response));
	}
	function downloadImages_post()
	{
		$response = $this->BOT_model->uploadCSV($_FILES,$_POST['type']);
		echo $response;
	}
	function exportCibil_get($date)
	{
		$data = $this->BOT_model->exportCibil($date);
		header("Content-type: application/csv");
		header("Content-Disposition: attachment; filename=\"Cibil-Downloaded-".$date.".csv\"");
		header("Pragma: no-cache");
		header("Expires: 0");
		$handle = fopen('php://output', 'w');
		foreach ($data as $data) {
			fputcsv($handle, $data);
		}
		fclose($handle);
	}
	function rejectApplication_post()
	{
		$response = $this->BOT_model->rejectApplication($_POST);
		echo $response;
	}
	function getRejectedData_get($txtAppFormNo){
		$response = $this->BOT_model->getRejectedData($txtAppFormNo);
		return $this->set_response($response);
	}
	function login_get($user, $pass){
		$response = $this->BOT_model->login($user, $pass);
		return $this->set_response(array("status"=>"success","message"=>"Login","result"=>$response));
	}
	function exportReport_get($report, $dt_start, $dt_end){
		$data = $this->BOT_model->exportReport($report, $dt_start, $dt_end);
		header("Content-type: application/csv");
		header("Content-Disposition: attachment; filename=\"Report-".$report.".csv\"");
		header("Pragma: no-cache");
		header("Expires: 0");
		$handle = fopen('php://output', 'w');
		if(count($data) > 0){
			$cols = array_keys($data[0]);
			fputcsv($handle, $cols);
		}
		foreach ($data as $data) {
			fputcsv($handle, $data);
		}
		fclose($handle);
	}
	function saveCibilRef_get($apps)
	{
		$response = $this->BOT_model->saveCibilRef($apps);
		return $this->set_response(array("status"=>"success","message"=>"Saved!","result"=>$response));
	}
}
?>