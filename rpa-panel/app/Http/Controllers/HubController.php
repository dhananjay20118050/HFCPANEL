<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\GridTrait;
use App\Hub;
use App\Process;
use DataTables;
use Redirect,Response;

class HubController extends Controller{

    use GridTrait;


    public function __invoke(Request $request){
        if ($request->ajax()) {

            $data = Hub::select('*');
            
            return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('processName', function($row){ 

                $process = Process::find($row->process_id);
                return $process->name;
            })
            ->addColumn('status', function($row){
           
                $status = $this->isServerActive($row->ip, $row->port);

                
                if($status == true){
                    $html = '<div class="badge badge-success">Active</div>';
                }else{
                    $html = '<div class="badge badge-danger">Not Active</div>';
                }
                return $html;
            })
             ->addColumn('action', function($row){ 

                $process = Process::find($row->process_id);
               
                $btn = '<div class="dt-actions">
                <a href="hubs/edit/'.$row->id.'" id="edithub_'.$row->id.'" data-id = "'.$row->id.'" class="btn btn-icon btn-primary btn-sm btn-edit"><i class="far fa-edit"></i>
                </a>
                 <a href="javascript:void(0)" id="deletehub" data-id = "'.$row->id.'" class="btn btn-icon btn-danger btn-sm btn-delete"><i class="far fa-trash-alt"></i>
                </a>
                </div>';
                return $btn;
               
            })
            ->rawColumns(['status','action','processName'])
            ->make(true);
        }

        $process = Process::all();
        return view('admin.hubs.index',compact('process'));   
    }

    public function store(Request $request)
    {
        $hub = new Hub();
        $hub->name = $request->sname;
        $hub->ip = $request->sip;
        $hub->port = $request->sport;
        $hub->process_id = $request->sprocess;
        $hub->save();
        return response()->json(['success'=>'Data is successfully added']);
    }

    public function createhub()
    {
        return view('admin.hubs.create');
    }

     public function destroy($id)
    {   
        $node = Hub::where('id',$id)->delete();
        return Response::json($node);
    }
    
    public function edithub($id)
    {   
        $hub = Hub::find($id);
        $process = Process::all();
        return view('admin.hubs.edit',compact('hub','process'));
    }

    public function update(Request $request,$id)
    {  
        $hub = Hub::find($id);
        $hub->name = $request->sname;
        $hub->ip = $request->sip;
        $hub->port = $request->sport;
        $hub->process_id = $request->sprocess;        
        $hub->save();
        return response()->json(['success'=>'Data is updated successfully']);
    }


}
