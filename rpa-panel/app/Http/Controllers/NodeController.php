<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\GridTrait;
use DataTables;
use App\Node;
use App\Process;
use Redirect,Response;

class NodeController extends Controller{

    use GridTrait;

    public function __invoke(Request $request){
        if ($request->ajax()) {

            $data = Node::select('*');
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
                
                $row1 = '['.$row.']';
                $process = Process::find($row->process_id);

                $btn = '<div class="dt-actions">
                <a href="nodes/edit/'.$row->id.'" id="editnode_'.$row->id.'" data-id = "'.$row->id.'" class="btn btn-icon btn-primary btn-sm btn-edit" data-name ="'.$row->name.'" data-process ="'.$process->name.'" data-ip ="'.$row->ip.'" data-port ="'.$row->port.'"><i class="far fa-edit"></i>
                </a>

                 <a href="javascript:void(0)" id="deletenode" data-id = "'.$row->id.'" class="btn btn-icon btn-danger btn-sm btn-delete"><i class="far fa-trash-alt"></i>
                </a>
                </div>';
                return $btn;
                //return array($row->name);
            })
            
            ->rawColumns(['status','action'])
            ->make(true);

        }
         $process = Process::all();
        return view('admin.nodes.index',compact('process'));
    }

    //Node Edit
   /*public function editnode($id)
    {   
        if(request()->ajax())
        {
            $data = Node::findOrFail($id);
            return response()->json(['data' => $data]);
        }
    }*/

    public function updatenode(Request $request,$id)
    {    
        $nodeadd = Node::find($id);
        $nodeadd->name = $request->nname;
        $nodeadd->ip = $request->nip;
        $nodeadd->port = $request->nport;
        $nodeadd->process_id = $request->nprocess;        
        $nodeadd->save();
        return response()->json(['success'=>'Data is successfully updated']);
    }


    public function destroy($id)
    {   
        $node = Node::where('id',$id)->delete();
        return Response::json($node);
    }

    public function createnode()
    {
        return view('admin.nodes.create');
    }

    public function store(Request $request)
    {   
        $nodeadd = new Node();
        $nodeadd->name = $request->nname;
        $nodeadd->ip = $request->nip;
        $nodeadd->port = $request->nport;
        $nodeadd->process_id = $request->nprocess;

        $nodeadd->save();
        return response()->json(['success'=>'Data is successfully added']);

    }
    public function editnode($id)
    {   
        $node = Node::find($id);
        $process = Process::all();
        return view('admin.nodes.edit',compact('node','process'));
    }
    

}
