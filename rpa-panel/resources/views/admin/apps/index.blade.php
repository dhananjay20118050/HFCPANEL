@extends('layouts.admin-master')
@section('title')
Manage Apps
@endsection

@section('content') 
<section class="section">
    <div class="section-header">
        <h1>Manage Apps</h1><div class="card-header-action" style="margin-left:auto"><a href="createapps" id="create-contact" class="btn btn-primary">Add App <i class="fas fa-plus"></i></a></div>
    </div>
    <div class="section-body">
		<table class="data-table table table-striped dataTable no-footer" id="app-datatable">
			<thead>
				<tr>
					<th>No.</th>
					<th>Name</th>
					<th>DB Username</th>
					<th>DB Host</th>
					<th>DB Port</th>
					<th>DB Name</th>
          <th>Action</th>
				</tr>
			</thead>
		</table>
    </div>

</section>
 
@endsection

@section('scripts')
<script type="text/javascript">
  $(function () {
    
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });
    
    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.apps') }}",        
        columns: [
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'db_username', name: 'db_username'},
            {data: 'db_host', name: 'db_host'},
            {data: 'db_port', name: 'db_port'},
            {data: 'db_name', name: 'db_name'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });
    
  

$(document).on('click', '.btn-delete', function(e){ 
        var url = "{{ route('admin.apps.destroy',':id') }}";
        url = url.replace(':id',$(this).attr('data-id')); 
      swal({
        title: "Are you sure you want delete this contact?",        
        icon: "warning",
        buttons: true,
        dangerMode: true,
        buttons: ["No", "Yes"]        
      })
      .then((willDelete) => {
        if (willDelete) {
          ajaxRequest(              
              url,
              'POST',
              [],
              function(response){               
                var mydatatable = $("#app-datatable").DataTable() ;
                mydatatable.ajax.reload(null, false );
              });
        } 
      });
  }); 


function ajaxRequest(url, type, data, successFunction){
    $.ajax({
        url: url,
        method: type,
        data: data,
        success: successFunction
      });
 }
  
  });
</script>

@endsection