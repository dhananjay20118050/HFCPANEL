@extends('layouts.admin-master')

@section('title')
Create Server
@endsection

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Edit Server</h1>
  </div>
  <div class="section-body">
   <div class="row">
      <div class="col-12">
         <!----> 
         <div class="message">
          
         </div>
         <div class="card">
            <div class="card-header">
               <h4>Edit a Server</h4>
            </div>
            @if($hub->exists)
            <form id="server_edit_form">
            	@csrf
                @method('POST')
                <input id="sid" name="sid" type="hidden" value="{{$hub->id}}">
	            <div class="card-body">
	               <div class="form-group row mb-4">
	                  <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Name</label> 
	                  <div class="col-sm-12 col-md-7"><input  id="sname" name="sname" type="text" placeholder="Name of the Server" class="form-control required" value="{{$hub->name}}"></div>
	               </div>
	               <div class="form-group row mb-4">
	                  <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">process</label> 
	                   <div class="col-sm-12 col-md-7">
	                     <select class="form-control required" id="sprocess" name="sprocess">
	                     	@foreach($process as $key=>$value)
	                     	@if($value->id == $hub->process_id)
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
	                  <div class="col-sm-12 col-md-7"><input id="sport" name="sport" type="text" placeholder="Port Number" value="{{$hub->port}}" class="form-control required"></div>
	               </div>
	               
	               <div class="form-group row mb-4">
	                  <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">IP</label> 
	                  <div class="col-sm-12 col-md-7"><input id="sip" name="sip"  type="text" placeholder="IP Address" value="{{$hub->ip}}" class="form-control required"></div>
	               </div>
	               <div class="form-group row mb-4">
	                  <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label> 
	                  <div class="col-sm-12 col-md-7"><button type="submit" data-id="{{$hub->id}}" class="btn btn-primary"><span>Update</span></button></div>
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

  $('#server_edit_form').submit(function(e) {
    e.preventDefault();
    var id = $('#sid').val();
   
    var sname = $('#sname').val();
    var sprocess = $('#sprocess').val();
    var sip = $('#sip').val();
    var sport = $('#sport').val();

    $(".error").remove();
	$(".is-invalid").removeClass('is-invalid');
    if (sname.length < 1) {
      $('#sname').after('<span class="error">This field is required</span>');
      $('#sname').addClass('is-invalid');
    }
    else if (sprocess.length < 1) {
      $('#sprocess').after('<span class="error">This field is required</span>');
      $('#sprocess').addClass('is-invalid');
    }
    else if (sport.length < 1) {
      $('#sport').after('<span class="error">This field is required</span>');
      $('#sport').addClass('is-invalid');
    }
    else if (sip.length < 1) {
      $('#sip').after('<span class="error">This field is required</span>');
      $('#sip').addClass('is-invalid');
    }else{
		var url = "{{ route('admin.update', ':id') }}";
    	url = url.replace(':id', id);
    	 $.ajax({
			data: $('#server_edit_form').serialize(),
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

