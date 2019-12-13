<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\User;
use DataTables;
use App\Process;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\{UserUpdateRequest,UserAddRequest};
use Spatie\Permission\Models\Role;
use Redirect,Response;
use App;
use DB;

class UserController extends Controller
{
    // public function __construct()
    // {
    //     $this->authorizeResource(User::class);
    // }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /*public function index(Request $request)
    {
        $this->authorize(User::class, 'index');
        if($request->ajax())
        {
            $users = new User;
            if($request->q)
            {
                $users = $users->where('name', 'like', '%'.$request->q.'%')->orWhere('email', $request->q);
            }
            $users = $users->paginate(config('constants.perpage'))->appends(['q' => $request->q]);
            return response()->json($users);
        }
        return view('admin.users.index');
    }*/


    public function index(Request $request){
        if ($request->ajax()) {

            $data = DB::table('users')
                    ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
                    ->select('users.*','roles.name as role_name');
            return Datatables::of($data)
            ->addIndexColumn()
            
            ->addColumn('action', function($row){ 
                $btn = '<div class="dt-actions">
                <a href="users/edit/'.$row->id.'" id="editusers_'.$row->id.'" data-id = "'.$row->id.'" class="btn btn-icon btn-primary btn-sm btn-edit"><i class="far fa-edit"></i>
                </a>

                 <a href="javascript:void(0)" id="deleteuser" data-id = "'.$row->id.'" class="btn btn-icon btn-danger btn-sm btn-delete"><i class="far fa-trash-alt"></i>
                </a>
                </div>';
                return $btn;
                //return array($row->name);
            })
            
            ->rawColumns(['status','action'])
            ->make(true);

        }
        $roles = DB::table('roles')->get();
        return view('admin.users.index',compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create()
    // {
    //     $roles = DB::table('roles')->get();
    //     return view('admin.users.create', ['roles' => $roles]);
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(UserAddRequest $request)
    // {
    //     $user = User::create($request->all());
    //     $role = Role::find($request->role);
    //     if($role)
    //     {
    //         $user->assignRole($role);
    //     }
    //     return response()->json($user);
    // }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function edit(User $user)
    // {
    //     return view('admin.users.edit', compact('user'));
    // }

    public function edituser($id)
    {   
        $users = User::find($id);
        $process = Process::all();
        $roles = DB::table('roles')->get();
        $projects = DB::table('apps')->get();

        $pids = array();


        $projects_permisson = DB::table('project_permissions')->where('user_id', '=',$id)->get();

        $projects_permisson = json_decode($projects_permisson);

        if(!empty($projects_permisson)){
            $pids = explode(',',$projects_permisson[0]->apps_ids);
        }
        
       
       //print_r($projects_permisson);exit;


        return view('admin.users.edit',compact('users','process','roles','projects','pids'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        if(!App::environment('demo'))
        {
            $user->update($request->only([
                'name', 'email'
            ]));

            if($request->password)
            {
                $user->update(['password' => Hash::make($request->password)]);
            }

            if($request->role && $request->user()->can('edit-users') && !$user->isme)
            {
                $role = Role::find($request->role);
                if($role)
                {
                    $user->syncRoles([$role]);
                }
            }
        }

        return response()->json($user);
    }


     public function updateuser(Request $request,$id)
    {  
     
        $useradd = User::find($id); 
        $useradd->name = $request->uname;        
        $useradd->email = $request->uemail;
        $useradd->role_id = $request->uroles;
        $useradd->update($request->only([
                'name', 'email','role_id'
        ]));
        if($request->upassword)
        {
            $useradd->update(['password' => Hash::make($request->upassword)]);
        }
        //$useradd->save();
        $projects = $this->assignprojects($request->uprojects);
        try{
            $last_id = $app_perm_insert = DB::table('project_permissions')->insertGetId(
                ['user_id' => $id, 'apps_ids' => $projects]
            );
        }
        catch (QueryException $e) {
                $sqlState = $e->errorInfo[0];
                $errorCode  = $e->errorInfo[1];
                 if ($sqlState === "23000" && $errorCode === 1062) {
                        DB::table('project_permissions')
                        ->where('user_id', $id)
                        ->update(['apps_ids' => $projects]);
                 }
                    
            }
        return response()->json(['success'=>'Data is Updated Successfully']);
    }


    public function assignprojects($projects){

        $allpval = explode(',', trim($projects));
        $newallpval = array();
        foreach ($allpval as $k => $v){
            $newallpval[] = trim($v);
        }

        $allapps = DB::table('apps')->select('id','name')->get()->pluck('name', 'id');

        $objarr = json_decode(json_encode($allapps), true);

        $allids = '';

        foreach ($objarr as $k => $v) {
           if(in_array($v,$newallpval)){
                $allids .= $k.',';
           }  
        }

        $projectassign_ids = chop($allids,',');
     
        return $projectassign_ids;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function destroy(User $user)
    // {
    //     if(!App::environment('demo') && !$user->isme)
    //     {
    //         $user->delete();
    //     } else
    //     {
    //         return response()->json(['message' => 'User accounts cannot be deleted in demo mode.'], 400);
    //     }
    // }

    public function destroy($id)
    {   
        $user = User::where('id',$id)->delete();
        return Response::json($user);
    }

    public function createuser()
    {
        $roles = DB::table('roles')->get();
        return view('admin.users.create', ['roles' => $roles]);
    }

  public function store(Request $request)
    {   
        $useradd = new User();
        $useradd->name = $request->uname;
        $useradd->email = $request->uemail;
        $useradd->password =Hash::make($useradd->password);         
        //$useradd->ucpassword = $request->ucpassword;
        $useradd->role_name = $request->uroles;
        $useradd->save();
        return response()->json(['success'=>'Data is successfully added']);
    }

    public function roles()
    {
        return response()->json(Role::get());
    }
}
