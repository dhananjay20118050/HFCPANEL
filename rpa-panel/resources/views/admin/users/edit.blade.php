<link rel="stylesheet" href="{{ asset('assets/css/compiled-4.8.11.min.css') }}">
@extends('layouts.admin-master')

@section('title')
Edit User
@endsection

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Edit User</h1>
  </div>
  <div class="section-body">
   <div class="row">
      <div class="col-12">
         <!----> 
         <div class="message">
          
         </div>
         <div class="card">
            <div class="card-header">
               <h4>Edit User</h4>
            </div>
            @if($users->exists)
            <form id="user_edit_form">
            	@csrf
                @method('POST')
              <input id="uid" name="uid" type="hidden" value="{{$users->id}}">
	            <div class="card-body">
	               <div class="form-group row mb-4">
	                  <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Name</label> 
	                  <div class="col-sm-12 col-md-7"><input  id="uname" name="uname" type="text" class="form-control required" value="{{$users->name}}"></div>
	               </div>

                 <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Email</label> 
                    <div class="col-sm-12 col-md-7">
                      <input  id="uemail" name="uemail" type="text" placeholder="Email address (should be unique)." class="form-control required" value="{{$users->email}}"></div>
                 </div>


                 <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Password</label> 
                    <div class="col-sm-12 col-md-7"><input  id="upassword" name="upassword" type="password" class="form-control required" value="" placeholder="New password (Only if you want to change the password)" autocomplete="new-password"></div>
                 </div>


                <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Confirm Password</label> 
                    <div class="col-sm-12 col-md-7"><input  id="ucpassword" name="ucpassword" type="password" class="form-control required" value="" autocomplete="new-password" ></div>
                 </div>

                 <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Roles</label> 
                   <div class="col-sm-12 col-md-7">
                       <select class="form-control required" id="uroles" name="uroles">
                        <!-- <option value="{{$users->role_name}}" selected="selected">{{$users->role_name}}</option> -->
                        <!--  @foreach($roles as $role)
                          <option value="{{$role->name}}">{{$role->name}}</option>
                          @endforeach -->
                        @foreach($roles as $role)
                        @if($users->role_id == $role->id)
                        <option value="{{$role->id}}" selected="selected">{{$role->name}}</option>
                        @else
                        <option value="{{$role->id}}">{{$role->name}}</option>
                        @endif
                        @endforeach
                       </select>
                    </div>

                 </div>
                <div class="form-group row mb-4">
                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Assign Projects</label> 
                   <div class="col-sm-12 col-md-7">
                       <select class="mdb-select md-form" multiple searchable="Search here.." id="uprojects" name="uprojects">
                        <option value="" disabled selected>Choose Your Projects</option>
                        {{$chkval = ''}}
                         @foreach($projects as $k => $project) 
                             @if(array_key_exists($k,$pids))
                             {{$chkval = $pids[$k] }}
                             @endif
                             @if($chkval == $project->id)
                             <option value="{{$project->id}}" selected="selected">{{$project->name}}</option>
                             @else
                             <option value="{{$project->id}}">{{$project->name}}</option>
                             @endif
                          @endforeach
                        </select>
                    </div>

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
<script src="{{ asset('assets/js/compiled-4.8.11.min.js') }}"></script>
<script src="{{ asset('assets/modules/nicescroll/jquery.nicescroll.min.js') }}"></script>
<script type="text/javascript">

$(document).ready(function() {
//user_edit_form
 $('.mdb-select').materialSelect();
 $('#user_edit_form').submit(function(e) {
    e.preventDefault();
    var id = $('#uid').val();
    var uname = $('#uname').val();
    var uemail = $('#uemail').val();
    var upassword = $('#upassword').val();
    var ucpassword = $('#ucpassword').val();

    $(".error").remove();
    $(".is-invalid").removeClass('is-invalid');
    if (uname.length < 1) {
      $('#uname').after('<span class="error">This field is required</span>');
      $('#uname').addClass('is-invalid');
    }
    else if (uemail.length < 1) {
      $('#uemail').after('<span class="error">This field is required</span>');
      $('#uemail').addClass('is-invalid');
    }
    /*else if (upassword.length < 1) {
      $('#upassword').after('<span class="error">This field is required</span>');
      $('#upassword').addClass('is-invalid');
    }
    else if (ucpassword.length < 1) {
      $('#ucpassword').after('<span class="error">This field is required</span>');
      $('#ucpassword').addClass('is-invalid');
    }*/
    else if(upassword != ucpassword){
      $('#ucpassword').after('<span class="error">Password not match.</span>');
      $('#ucpassword').addClass('is-invalid');
      $('#upassword').after('<span class="error">Password not match.</span>');
      $('#upassword').addClass('is-invalid');
    }
   else{

      var textInput = [];
      textInput = $(".select-dropdown").val();
      //console.log(textInput);

     var url = "{{ route('admin.updateuser', ':id') }}";
     var data = $('#user_edit_form').serializeArray();
     data.push({name: 'uprojects', value:textInput});
      url = url.replace(':id', id);
       $.ajax({
      data: data,
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
    
  });

});
</script>
@endsection

<style type="text/css">.hide{display: none;}</style>