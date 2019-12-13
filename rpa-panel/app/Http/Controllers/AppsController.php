<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\GridTrait;
use App\Apps;
use DataTables;
use Redirect,Response;

class AppsController extends Controller{

    public function __invoke(Request $request){
        if ($request->ajax()) {

            $data = Apps::select('*');
            
            return Datatables::of($data)
            ->addIndexColumn()
            
             ->addColumn('action', function($row){ 

               
                $btn = '<div class="dt-actions">
                <a href="apps/edit/'.$row->id.'" id="apps_'.$row->id.'" data-id = "'.$row->id.'" class="btn btn-icon btn-primary btn-sm btn-edit"><i class="far fa-edit"></i>
                </a>
                 <a href="javascript:void(0)" id="deleteapps" data-id = "'.$row->id.'" class="btn btn-icon btn-danger btn-sm btn-delete"><i class="far fa-trash-alt"></i>
                </a>
                </div>';
                return $btn;
               
            })
            ->rawColumns(['action'])
            ->make(true);
        }

        return view('admin.apps.index');   
    }

    public function store(Request $request)
    {
        $apps = new Apps();
        $apps->name = $request->aname;
        $apps->db_username = $request->adbusername;
        $apps->db_password = $request->adbpassword;
        $apps->db_host = $request->adbhost;
        $apps->db_port = $request->adbport;
        $apps->db_name = $request->adbname;
        $apps->save();
        return response()->json(['success'=>'Data is successfully Added']);
    }

    public function createapps()
    {
        return view('admin.apps.create');
    }

     public function destroy($id)
    {   
        $apps = Apps::where('id',$id)->delete();
        return Response::json($apps);
    }
    
    public function editapps($id)
    {   
        $apps = Apps::find($id);
        return view('admin.apps.edit',compact('apps'));
    }

    public function updateapps(Request $request,$id)
    {  
        $apps = Apps::find($id);
        $apps->name = $request->aname;
        $apps->db_username = $request->adbusername;
        $apps->db_password = $request->adbpassword;
        $apps->db_host = $request->adbhost;
        $apps->db_port = $request->adbport;
        $apps->db_name = $request->adbname;      
        $apps->save();
        return response()->json(['success'=>'Data is updated Successfully']);
    }


}
