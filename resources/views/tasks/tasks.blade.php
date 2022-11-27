
<!DOCTYPE html>
<html>
<head>
    <title>Task</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>       
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
<body>

    <div class="container" style="margin-bottom: 150px; margin-top:80px;">
        <a href="/home" class="btn btn-primary">Back to Dashboard</a>
        <br />
        <h3 align="center">Datatables Server Side Processing in Laravel</h3>
        <br />
        <div align="right">
            <button type="button" name="add" id="add_data" class="btn btn-success btn-sm">Add</button>
        </div>
        <br />
        <table id="task_table" class="table table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>Task</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>

    <div id="secondTaskModel" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" id="updat_form" name="updat_form">
                    <div class="modal-header">
                       <button type="button" class="close" data-dismiss="modal">&times;</button>
                       <h4 class="modal-title">Mark Your Task As Done</h4>
                    </div>
                    <div class="modal-body">
                        {{csrf_field()}}
                        <span id="update_form_output"></span>
                        <div class="form-group">
                            <label>Are You Sure You The Task Is Don?</label>
                            <input type="hidden" name="status" id="status" class="form-control" />
                        </div>
                    </div>
                    <div class="modal-footer">
                         <input type="hidden" name="task_id" id="task_id" value="" />
                        <input type="hidden" name="button_action" id="button_action" value="insert" />
                        <input type="submit" name="submit" id="action" value="Add" class="btn btn-info" />
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div id="taskModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" id="task_form" name="task_form">
                    <div class="modal-header">
                       <button type="button" class="close" data-dismiss="modal">&times;</button>
                       <h4 class="modal-title">Add Data</h4>
                    </div>
                    <div class="modal-body">
                        {{csrf_field()}}
                        <span id="form_output"></span>
                        <div class="form-group">
                            <input type="hidden" name="user_id" id="user_id" class="form-control" value="" />
                        </div>
                        <div class="form-group">
                            <label>Task</label>
                            <input type="text" name="task" id="task" class="form-control" />
                        </div>
                    </div>
                    <div class="modal-footer">
                         <input type="hidden" name="task_id" id="task_id" value="" />
                        <input type="hidden" name="button_action" id="button_action" value="insert" />
                        <input type="submit" name="submit" id="action" value="Add" class="btn btn-info" />
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    
<script type="text/javascript">
// ============================ Add task section ============================================
$(document).ready(function() {
     $('#task_table').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "{{ route('tasks.create') }}",
        "columns":[
            { "data": "task" },
            { "data": "status" },
            { "data": "action", orderable:false, searchable: false},
        ]
     });

    $('#add_data').click(function(){
        $('#taskModal').modal('show');
        $('#task_form')[0].reset();
        $('#form_output').html('');
        $('#button_action').val('insert');
        $('#action').val('Add');
    });

    $('#task_form').on('submit', function(event){
        event.preventDefault();
        var form_data = $(this).serialize();
        $.ajax({
            url:"{{ route('tasks.store') }}",
            method:"POST",
            data:form_data,
            dataType:"json",
            success:function(data)
            {
                if(data.error.length > 0)
                {
                    var error_html = '';
                    for(var count = 0; count < data.error.length; count++)
                    {
                        error_html += '<div class="alert alert-danger">'+data.error[count]+'</div>';
                    }
                    $('#form_output').html(error_html);
                }
                else
                {
                    $('#form_output').html(data.success);
                    $('#task_form')[0].reset();
                    $('#action').val('Add');
                    $('.modal-title').text('Add Data');
                    $('#button_action').val('insert');
                    $('#task_table').DataTable().ajax.reload();
                }
            }
        })
    });
   

});


// ============================ update section ============================================

$('#updat_form').on('submit', function(event){
        event.preventDefault();
        var form_data = $(this).serialize();
        $.ajax({
            url:"{{ route('tasks.store') }}",
            method:"POST",
            data:form_data,
            dataType:"json",
            success:function(data)
            {
                if(data.error.length > 0)
                {
                    var error_html = '';
                    for(var count = 0; count < data.error.length; count++)
                    {
                        error_html += '<div class="alert alert-danger">'+data.error[count]+'</div>';
                    }
                    $('#update_form_output').html(error_html);
                }
                else
                {
                    $('#update_form_output').html(data.success);
                    $('#updat_form')[0].reset();
                    $('#action').val('Check');
                    $('.modal-title').text('Add Data');
                    $('#button_action').val('insert');
                    $('#task_table').DataTable().ajax.reload();
                }
            }
        })
    });
   




$(document).ready(function() {

    $(document).on('click', '.edit', function(){
        var id = $(this).attr("id");
        $('#update_form_output').html('');
        $.ajax({
            url:"{{route('tasks.fetchdata')}}",
            method:'get',
            data:{id:id},
            dataType:'json',
            success:function(data)
            {
                $('#status').val("checked");
                $('#task_id').val(id);
                $('#secondTaskModel').modal('show');
                $('#action').val('Check');
                $('.modal-title').text('Mark Your Task As Done');
                $('#button_action').val('update');
            }
        })
    });
   

});
</script>
</body>
</html>
