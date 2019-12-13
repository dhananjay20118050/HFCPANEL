@extends('layouts.admin-master')

@section('title')
Create Node
@endsection

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Edit Node</h1>
  </div>
  <div class="section-body">
   <div class="row">
      <div class="col-12">
         <!----> 
         <div class="message">
          
         </div>
         <div class="card">
            <div class="card-header">
               <h4>Edit a Node</h4>
            </div>
            @if($node->exists)
            <form id="node_edit_form">
            	@csrf
                @method('POST')
                <input id="nid" name="nid" type="hidden" value="{{$node->id}}">
	            <div class="card-body">
	               <div class="form-group row mb-4">
	                  <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Name</label> 
	                  <div class="col-sm-12 col-md-7"><input  id="nname" name="nname" type="text" placeholder="Name of the Node" class="form-control required" value="{{$node->name}}"></div>
	               </div>
	               <div class="form-group row mb-4">
	                  <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">process</label> 
	                   <div class="col-sm-12 col-md-7">
	                     <select class="form-control required" id="nprocess" name="nprocess">
	                     	@foreach($process as $key=>$value)
	                     	@if($value->id == $node->process_id)
		                    <option value="{{$value->id}}" selected="selected">{{$value->name}}</option>
		                    @else
		                    <option value="{{$value->id}}">{{$value->name}}</option>
		                    @endif
		                    @endforeach
		                    
	                     </select>
	                  	</div>
	               </div>
	               <div class="form-group row mb-4">
	                  <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Port</label> 
	                  <div class="col-sm-12 col-md-7"><input id="nport" name="nport" type="text" placeholder="Port Number" value="{{$node->port}}" class="form-control required"></div>
	               </div>
	               
	               <div class="form-group row mb-4">
	                  <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">IP</label> 
	                  <div class="col-sm-12 col-md-7"><input id="nip" name="nip"  type="text" placeholder="IP Address" value="{{$node->ip}}" class="form-control required"></div>
	               </div>
	               <div class="form-group row mb-4">
	                  <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label> 
	                  <div class="col-sm-12 col-md-7"><button type="submit" class="btn btn-primary"><span>Update</span></button></div>
	               </div>
	            </div>
        	</form>
        	@endif
         </div>
      </div>
   </div>
</div>
</section>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {

  $('#node_edit_form').submit(function(e) {
    e.preventDefault();
    var id = $('#nid').val();
    var nname = $('#nname').val();
    var nprocess = $('#nprocess').val();
    var nip = $('#nip').val();
    var nport = $('#nport').val();

    $(".error").remove();
	$(".is-invalid").removeClass('is-invalid');
    if (nname.length < 1) {
      $('#nname').after('<span class="error">This field is required</span>');
      $('#nname').addClass('is-invalid');
    }
    else if (nprocess.length < 1) {
      $('#sprocess').after('<span class="error">This field is required</span>');
      $('#sprocess').addClass('is-invalid');
    }
    else if (nport.length < 1) {
      $('#nport').after('<span class="error">This field is required</span>');
      $('#nport').addClass('is-invalid');
    }
    else if (nip.length < 1) {
      $('#nip').after('<span class="error">This field is required</span>');
      $('#nip').addClass('is-invalid');
    }else{
		var url = "{{ route('admin.updatenode', ':id') }}";
    	url = url.replace(':id', id);
    	 $.ajax({
			data: $('#node_edit_form').serialize(),
			url: url,
			type: "POST",
			dataType: 'json',
			success: function (data) {      
			    $('.message').addClass('alert alert-primary');
			    $('.message').html(data.success);
          $('html, body').animate({
              scrollTop: $(".alert-primary").offset().top
          }, 2000);
			},
			error: function (data) {
				console.log('Error:', data);
			}
      })
    }
    
  })

});
</script>
@endsection

