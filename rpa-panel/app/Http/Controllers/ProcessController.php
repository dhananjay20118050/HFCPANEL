<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Traits\GridTrait;
use DataTables;
use App\Process;
use Redirect,Response;

class ProcessController extends Controller
{
    use GridTrait;
    public function __invoke(Request $request){
        if ($request->ajax()) {

            $data = Process::select('*');
            return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('browserName', function($row){
                $browser = DB::table('browsers')->find($row->id);
                return $browser->name;
            })
            ->addColumn('action', function($row){
                $process = Process::find($row->process_id);

                $btn = '<div class="dt-actions">
                <a href="process/edit/'.$row->id.'" id="editnode_'.$row->id.'" class="btn btn-icon btn-primary btn-sm btn-edit"><i class="far fa-edit"></i>
                </a>

                 <a href="javascript:void(0)" id="deletenode" data-id = "'.$row->id.'" class="btn btn-icon btn-danger btn-sm btn-delete"><i class="far fa-trash-alt"></i>
                </a>
                </div>';

                return $btn;
            })
            ->rawColumns(['status','action'])
            ->make(true);

        }
        return view('admin.process.index');
    }

    public function createprocess()
    {
        return view('admin.process.create');
    }

    public function store(Request $request)
    {   
        $process = new Process();
        $process->name = $request->pname;
        $process->description = $request->pdesc;
        $process->url = $request->purl;
        $process->username = $request->pusername;
        $process->password = $request->ppass;
        $process->browserId = $request->pbrowserid;
        $process->downloadDir = $request->pdownloaddir;    
        $process->appId = $request->pappid;   
        $process->save();
        return response()->json(['success'=>'Data is successfully Added']);

    }

    public function editprocess($id)
    {   
        $processdata = Process::find($id);
        $browsers = DB::table('browsers')->get();

        //print_r($browsers);exit;
        return view('admin.process.edit',compact('processdata','browsers'));
    }

    public function updateprocess(Request $request,$id)
    {    
        $process = Process::find($id);
        $process->name = $request->pname;
        $process->description = $request->pdesc;
        $process->url = $request->purl;
        $process->username = $request->pusername;
        $process->password = $request->ppass;
        $process->browserId = $request->pbrowserid;
        $process->downloadDir = $request->pdownloaddir;    
        $process->appId = $request->pappid;      
        $process->save();
        return response()->json(['success'=>'Data is successfully Updated']);
    }

    public function destroy($id)
    {
        $process = Process::where('id',$id)->delete();
        return Response::json($process);
    }

    public function showProcess($id)
    {         
        if($id == 1){
            return view('admin.process.hfc.index');
        }
    }
}