<?php


namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\CC_model;
use Validator;


class CCController extends BaseController{

    public function downloadImages(Request $request){
        $cc_model = new CC_model();
        $data = $cc_model->downloadImages($request);
        return $this->sendResponse($data, 'CC retrieved successfully.');
    }
}