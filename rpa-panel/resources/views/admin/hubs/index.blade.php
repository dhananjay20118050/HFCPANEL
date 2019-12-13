@extends('layouts.admin-master')

@section('title')
Manage Servers
@endsection

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Manage Servers</h1><div class="card-header-action" style="margin-left:auto"><a href="createhub" id="create-server" class="btn btn-primary">Add Server <i class="fas fa-plus"></i></a></div>
    </div>
    <div class="section-body">
		<table class="data-table table table-striped dataTable no-footer" id="data-hubserver">
			<thead>
				<tr>
					<th>No.</th>
					<th>Name</th>
					<th>Process</th>
					<th>IP</th>
					<th>Port</th>
					<th>Status</th>
					<th>Actions</th>
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
	
      var table = $('#data-hubserver').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.hubs')}}",
        columns: [

        	{data: 'id', name: 'id'},
        	{data: 'name', name: 'name'},
        	{data: 'processName', name: 'processName'},
        	{data: 'ip', name: 'ip'},
        	{data: 'port', name: 'port'},
        	{data: 'status', name: 'status'},
        	{data: 'action', name: 'action', orderable: false, searchable: false}
        ]
      
      });
  
      $(document).on('click', '.btn-delete', function(e){ 
        var url = "{{ route('admin.hubs.destroy',':id') }}";
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
                  var mydatatable = $("#data-hubserver").DataTable() ;
                  mydatatable.ajax.reload(null,false);
                });
          } 

        });
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
  
</script>
@endsection