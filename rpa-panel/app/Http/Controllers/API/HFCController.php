<?php


namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\HFC_model;
use Validator;


class HFCController extends BaseController{

    public function start(Request $request){
        $hcc_model = new HFC_model();
        $data = $hcc_model->start($request);
        return $this->sendResponse($data, 'Automation Started');
    }
}