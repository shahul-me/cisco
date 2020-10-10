<!DOCTYPE html>
<html>
<head>
<title>Router - Crud</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
<link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
<script>
error=false

function validate()
{
if(document.userForm.sapid.value !='' && document.userForm.hostname.value !='' )
document.userForm.btnsave.disabled=false
else
document.userForm.btnsave.disabled=true
}
</script>
</head>
<body>

<div class="container">
<h1 align="center">Router - Crud</h1>
<br/>
<div class="row">
<div class="col-lg-12 margin-tb">
<div class="pull-right">
<a class="btn btn-success mb-2" id="new-user" data-toggle="modal">New Router</a>
</div>
</div>
</div>

<table class="table table-bordered data-table" >
<thead>
<tr id="">
<th width="5%">No</th>
<th width="15%">Id</th>
<th width="15%">sapid</th>
<th width="15%">hostname</th>

<th width="15%">loopback</th>
<th width="15%">mac</th>
<th width="5%">type</th>

<th width="15%">Action</th>
</tr>
</thead>
<tbody>
</tbody>
</table>
</div>

<!-- Add and Edit customer modal -->
<div class="modal fade" id="crud-modal" aria-hidden="true" >
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<h4 class="modal-title" id="userCrudModal"></h4>
</div>
<div class="modal-body">
<form name="userForm" action="{{ route('router.store') }}" method="POST">
<input type="hidden" name="user_id" id="user_id" >
@csrf
<div class="row">

<div class="col-xs-12 col-sm-12 col-md-12">
<div class="form-group">
<strong>Name:</strong>
<input type="text" name="sapid" id="sapid" class="form-control" placeholder="sapid" onchange="validate()" >
</div>
</div>

<div class="col-xs-12 col-sm-12 col-md-12">
<div class="form-group">
<strong>hostname:</strong>
<input type="text" name="hostname" id="hostname" class="form-control" placeholder="hostname" onchange="validate()" >
</div>
</div>

<div class="col-xs-12 col-sm-12 col-md-12">
<div class="form-group">
<strong>loopback:</strong>
<input type="text" name="loopback" id="loopback" class="form-control" placeholder="loopback" onchange="validate()" >
</div>
</div>

<div class="col-xs-12 col-sm-12 col-md-12">
<div class="form-group">
<strong>mac:</strong>
<input type="text" name="mac" id="mac" class="form-control" placeholder="mac" onchange="validate()" >
</div>
</div>

<div class="col-xs-12 col-sm-12 col-md-12">
<div class="form-group">
<strong>type:</strong>
<!-- <input type="text" name="type" id="type" class="form-control" placeholder="type" onchange="validate()" > -->

<select name="type" id="type">
  <option value="AG1">AG1</option>
  <option value="CSS">CSS</option> onchange="validate()
</select>
</div>
</div>


<div class="col-xs-12 col-sm-12 col-md-12 text-center">
<button type="submit" id="btn-save" name="btnsave" class="btn btn-primary" disabled>Save</button>
<a href="{{ route('router.index') }}" class="btn btn-danger">Cancel</a>
</div>
</div>
</form>
</div>
</div>
</div>
</div>

<!-- Show user modal -->
<div class="modal fade" id="crud-modal-show" aria-hidden="true" >
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<h4 class="modal-title" id="userCrudModal-show"></h4>
</div>
<div class="modal-body">
<div class="row">
<div class="col-xs-2 col-sm-2 col-md-2"></div>
<div class="col-xs-10 col-sm-10 col-md-10 ">


<table class="table-responsive ">
<tr height="50px"><td><strong>sapid:</strong></td><td id="ssapid"></td></tr>
<tr height="50px"><td><strong>hostname:</strong></td><td id="shostname"></td></tr>
<tr height="50px"><td><strong>loopback:</strong></td><td id="sloopback"></td></tr>
<tr height="50px"><td><strong>mac:</strong></td><td id="smac"></td></tr>
<tr height="50px"><td><strong>type:</strong></td><td id="stype"></td></tr>

<tr><td></td><td style="text-align: right "><a href="{{ route('router.index') }}" class="btn btn-danger">OK</a> </td></tr>
</table>
</div>
</div>
</div>
</div>
</div>
</div>

</body>

<script type="text/javascript">

$(document).ready(function () {

var table = $('.data-table').DataTable({
processing: true,
serverSide: true,
ajax: "{{ route('router.index') }}",
columns: [
{data: 'DT_RowIndex', name: 'DT_RowIndex'},
{data: 'id', name: 'id'},
{data: 'sapid', name: 'sapid'},
{data: 'hostname', name: 'hostname'},

{data: 'loopback', name: 'loopback'},
{data: 'mac', name: 'mac'},
{data: 'type', name: 'type'},



{data: 'action', name: 'action', orderable: false, searchable: false},
]
});

/* When click New customer button */
$('#new-user').click(function () {
$('#btn-save').val("create-user");
$('#user').trigger("reset");
$('#userCrudModal').html("Add New User");
$('#crud-modal').modal('show');
});

/* Edit customer */
$('body').on('click', '#edit-user', function () {
var user_id = $(this).data('id');
alert(user_id);
$.get('router/'+user_id+'/edit', function (data) {
$('#userCrudModal').html("Edit User");
$('#btn-update').val("Update");
$('#btn-save').prop('disabled',false);
$('#crud-modal').modal('show');
$('#user_id').val(data.id);

$('#sapid').val(data.sapid);
$('#hostname').val(data.hostname);
$('#loopback').val(data.loopback);
$('#mac').val(data.mac);
$('#type').val(data.type);

})
});
/* Show customer */
$('body').on('click', '#show-user', function () {
var user_id = $(this).data('id');
$.get('router/'+user_id, function (data) {

$('#ssapid').html(data.sapid);
$('#shostname').html(data.hostname);
$('#sloopback').html(data.loopback);
$('#smac').html(data.mac);
$('#stype').html(data.type);

})
$('#userCrudModal-show').html("User Details");
$('#crud-modal-show').modal('show');
});

/* Delete customer */

$('body').on('click', '#delete-user', function () {
var user_id = $(this).data("id");
var SITEURL = '{{URL::to('/')}}';

var token = $("meta[name='csrf-token']").attr("content");
confirm("Are You sure want to delete !");

$.ajax({
type: "DELETE",
url: SITEURL +"/router/"+user_id,

data: {
"id": user_id,
"_token": token,
},
success: function (data) {

//$('#msg').html('Customer entry deleted successfully');
//$("#customer_id_" + user_id).remove();
table.ajax.reload();
},
error: function (data) {
console.log('Error:', data);
}
});
});

});

</script>
</html>
