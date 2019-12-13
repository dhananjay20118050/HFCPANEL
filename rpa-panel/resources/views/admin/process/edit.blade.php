@extends('layouts.admin-master')

@section('title')
Edit Process
@endsection

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Edit Process</h1>
  </div>
  <div class="section-body">
   <div class="row">
      <div class="col-12">
         <!----> 
         <div class="message">
          
         </div>
         <div class="card">
            <div class="card-header">
               <h4>Edit a Process</h4>
            </div>
            @if($processdata->exists)
            <form id="process_edit_form">
                @csrf
                @method('POST')
                <input id="pid" name="pid" type="hidden" value="{{$processdata->id}}">
                <div class="card-body">
                   <div class="form-group row mb-4">
                      <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Name</label> 
                      <div class="col-sm-12 col-md-7"><input  id="pname" name="pname" type="text" placeholder="Name of the Node" class="form-control required" value="{{$processdata->name}}"></div>
                   </div>
                   <div class="form-group row mb-4">
                      <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Description</label> 
                       <div class="col-sm-12 col-md-7">
                         <textarea name="pdesc" id="pdesc"  type="text" placeholder="Description" class="form-control required">{{$processdata->description}}</textarea>
                        </div>
                   </div>
                   <div class="form-group row mb-4">
                      <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">URL</label> 
                      <div class="col-sm-12 col-md-7"><input id="purl" name="purl" type="text" placeholder="URL" value="{{$processdata->url}}" class="form-control required"></div>
                   </div>
                   
                   <div class="form-group row mb-4">
                      <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Username</label> 
                      <div class="col-sm-12 col-md-7"><input id="pusername" name="pusername"  type="text" placeholder="Username" value="{{$processdata->username}}" class="form-control required"></div>
                   </div>
                   <div class="form-group row mb-4">
                      <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Password</label> 
                      <div class="col-sm-12 col-md-7"><input id="ppass" name="ppass"  type="password" placeholder="Password" value="{{$processdata->password}}" class="form-control required"></div>
                   </div>

                   <div class="form-group row mb-4">
                      <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Browser Name</label> 
                      <div class="col-sm-12 col-md-7">
                       <select class="form-control required" id="sprocess" name="sprocess">
                        @foreach($browsers as $key=>$value)
                        @if($value->id == $processdata->browserId)
                        <option value="{{$value->id}}" selected="selected">{{$value->name}}</option>
                        @else
                        <option value="{{$value->id}}">{{$value->name}}</option>
                        @endif
                        @endforeach
                        
                       </select>
                      </div>
                   </div>

                   <div class="form-group row mb-4">
                      <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Download Dir</label> 
                      <div class="col-sm-12 col-md-7"><input id="pdownloaddir" name="pdownloaddir"  type="text" placeholder="Download Dir" value="{{$processdata->downloadDir}}" class="form-control required"></div>
                   </div>
                   <div class="form-group row mb-4">
                      <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">App ID</label> 
                      <div class="col-sm-12 col-md-7"><input id="pappid" name="pappid"  type="text" placeholder="IP Address" value="{{$processdata->appId}}" class="form-control required"></div>
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

  $('#process_edit_form').submit(function(e) {
    e.preventDefault();
    var id = $('#pid').val();
    var pname = $('#pname').val();
    var pdesc = $('#pdesc').val();
    var purl = $('#purl').val();
    var pusername = $('#pusername').val();
    var ppass = $('#ppass').val();
    var pbrowserid = $('#pbrowserid').val();
    var pdownloaddir = $('#pdownloaddir').val();
    var pappid = $('#pappid').val();

    $(".error").remove();
    $(".is-invalid").removeClass('is-invalid');
    if (pname.length < 1) {
      $('#pname').after('<span class="error">This field is required</span>');
      $('#pname').addClass('is-invalid');
    }
    else if (pdesc.length < 1) {
      $('#pdesc').after('<span class="error">This field is required</span>');
      $('#pdesc').addClass('is-invalid');
    }
    else if (purl.length < 1) {
      $('#purl').after('<span class="error">This field is required</span>');
      $('#purl').addClass('is-invalid');
    }
    else if (pusername.length < 1) {
      $('#pusername').after('<span class="error">This field is required</span>');
      $('#pusername').addClass('is-invalid');
    }
    /*else if (ppass.length < 1) {
      $('#ppass').after('<span class="error">This field is required</span>');
      $('#ppass').addClass('is-invalid');
    }*/
    /*else if (pbrowserid.length < 1) {
      $('#pbrowserid').after('<span class="error">This field is required</span>');
      $('#pbrowserid').addClass('is-invalid');
    }*/
    /* else if (pdownloaddir.length < 1) {
      $('#pdownloaddir').after('<span class="error">This field is required</span>');
      $('#pdownloaddir').addClass('is-invalid');
    }*/
     else if (pappid.length < 1) {
      $('#pappid').after('<span class="error">This field is required</span>');
      $('#pappid').addClass('is-invalid');
    }
    else{
        var url = "{{ route('admin.updateprocess', ':id') }}";
        url = url.replace(':id', id);
         $.ajax({
            data: $('#process_edit_form').serialize(),
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

