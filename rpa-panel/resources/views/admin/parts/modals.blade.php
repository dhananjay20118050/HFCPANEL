<!-- Create Modal -->
<div class="modal fade" id="modal-create-contact" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Node</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-create-contact" >
                   @csrf 
                   {{ csrf_field() }}
                    <div class="form-group">                        
                        <input type="text" class="form-control" name="pname" placeholder="Name" required>
                    </div>
                    <div class="form-group">
                  <select class="form-control" name="pprocess">  
                    <option value="0">Select</option>                  
                    @foreach($process as $key=>$value)
                    <option value="{{$value->id}}">{{$value->name}}</option>
                    @endforeach
                    
                  </select>
                    </div>
                    <div class="form-group">                        
                        <input type="text" class="form-control" name="pip" placeholder="IP" required>
                    </div>
                    <div class="form-group">                        
                        <input type="text" class="form-control" name="pport" placeholder="Port" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary btn-sm" id="btn-save-contact">Submit</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="modal-edit-contact" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit a Node</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-edit-contact">                   
                <input type="hidden" class="form-control" id="node_id">
                <div class="col-12"> 
                <div class="card"> 
                <div class="card-body">
                <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Name</label> 
                    <div class="col-sm-12 col-md-7">
                    <input type="text" name="systemname" class="form-control">
                    </div>
                </div> 

                <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Process</label> 
                    <div class="col-sm-12 col-md-7"> 

                   <select class="form-control" name="pprocess">                    
                    @foreach($process as $key=>$value)
                    <option value="{{$value->id}}">{{$value->name}}</option>
                    @endforeach
                  </select>

                    </div>
                </div> 

                <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">IP</label> 
                    <div class="col-sm-12 col-md-7">
                        <input type="text" name="systemip" placeholder="IP Address" class="form-control">
                    </div>
                </div>

                <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Port</label> 
                    <div class="col-sm-12 col-md-7">
                        <input type="text" name="port" placeholder="System Port" class="form-control">
                    </div>
                </div>
             </div>
             </div>
             </div>
            </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary btn-sm" id="btn-update-contact">Update Contact</button>
            </div>
        </div>
    </div>
</div>