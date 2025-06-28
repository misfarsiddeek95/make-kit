<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php $this->load->view('includes/head'); ?>
</head>

<body class="layout layout-header-fixed layout-left-sidebar-fixed">
    <?php $this->load->view('includes/topbar'); ?>
    <div class="site-main">
        <?php $this->load->view('includes/sidebar'); ?>
        <?php if($student_list){ ?>
        <div class="site-content">
            <div class="panel panel-default panel-table">
                <div class="panel-heading">
                    <?php if($add_student){?>
                    <div class="panel-tools">
                        <button type="button" class="btn btn-outline-success btn-pill" title="Add Student"
                            onclick="location.href='<?=base_url();?>add-student'"><i class="zmdi zmdi-plus"></i></button>
                    </div>
                    <?php }?>
                    <h3 class="m-t-0 m-b-5">Student Management</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-6 col-md-6">
                            <div class="form-group">
                                <label for="class_id" class="control-label">Class</label>
                                <select id="class_id" name="class_id" class="form-control" data-plugin="select2" onchange="filterStudents();">
                                    <option></option>
                                    <?php foreach ($loadclass as $row ) {?>
                                    <option value="<?=$row->class_id?>"><?=$row->class_name?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive m-y-5">
                        <table class="table table-hover" id="table-1">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                    <th>Roll Number</th>
                                    <th>Class</th>
                                    <th>Gender</th>
                                    <th>City</th>
                                    <th>Parent Name</th>
                                    <th>Parent Phone</th>
                                    <th>Parent Email</th>
                                    <th>Active Status</th>
                                    <?php if($edit_student || $delete_student){ ?>
                                    <th style="text-align:right;">Options</th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody id="tbody_data"></tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
        <?php } ?>
        <?php $this->load->view('includes/footer'); ?>
    </div>
    <?php $this->load->view('includes/javascripts'); ?>
    <script src="<?=base_url()?>assets/js/forms-plugins.js"></script>
    <script type="text/javascript">
        $('#table-1').DataTable();

        $('.table-responsive').on('show.bs.dropdown', function () {
            $('.table-responsive').css("overflow", "inherit");
        });

        $('.table-responsive').on('hide.bs.dropdown', function () {
            $('.table-responsive').css("overflow", "auto");
        });

        $("#class_id").select2({
            placeholder: "Select a class",
            allowClear: true
        });

        function filterStudents() {
            var class_id = $('#class_id option:selected').val();
            var sts = '';
            $.ajax({
                type: "POST",
                url: "<?=base_url()?>filter-students",
                data: 'class_id='+class_id+'&status='+sts,
                success: function(result){
                    var resp = $.parseJSON(result);
                    var html = '';
                    for (let i = 0; i < resp.length; i++) { 
                        var gender = '';
                        var typecls = '';

                        if (resp[i].gender==0) {
                            gender = 'Female';
                            typecls = 'primary';
                        } else if(resp[i].gender==1){
                            gender = 'Male';
                            typecls = 'success';
                        }else{
                            gender = 'Other';
                            typecls = 'warning';
                        }

                        var access_group = resp[i].group_desc;
                        var status = '';
                        if (resp[i].status == 1) {
                            status = 'checked="checked"';
                        }

                        var status_change = '<?=$changeStatus?>'; 
                        var status_action = '';
                        if (status_change == 1) {
                            status_action = 'onchange="updateUserStatus('+resp[i].user_id+');"';
                        }else{
                            status_action = 'disabled';
                        }
                        var base_url = '<?=base_url();?>'; 
                        var img = 'user_default.png';
                        if (resp[i].user_pic!=null && resp[i].user_pic!='') {
                            img = 'staff/'+resp[i].user_pic+'-thu.'+resp[i].extension;
                        }else{
                            img = 'user_default.png';
                        }

                        html+='<tr id="userrow'+resp[i].user_id+'">'+
                                '<td><img class="img-rounded" src="'+base_url+'photos/'+img+'" height="32"></td>'+
                                '<td style="width:14%;">'+resp[i].name+'</td>'+
                                '<td>'+resp[i].role_number+'</td>'+
                                '<td>'+resp[i].class_name+'</td>'+
                                '<td><span class="label label-outline-'+typecls+'">'+gender+'</span></td>'+
                                '<td>'+resp[i].city_name_hebrew+'</td>'+
                                '<td>'+resp[i].parent_name+'</td>'+
                                '<td>'+resp[i].parent_phone+'</td>'+
                                '<td>'+resp[i].parent_email+'</td>'+
                                '<td>'+
                                    '<label class="switch switch-success m-t-10">'+
                                        '<input type="checkbox" class="s-input" '+status+' '+status_action+'>'+
                                        '<span class="s-content">'+
                                            '<span class="s-track"></span>'+
                                            '<span class="s-handle"></span>'+
                                        '</span>'+
                                    '</label>'+
                                '</td>';
                                <?php if($edit_student || $delete_student){ ?>
                                    html+= '<td align="right" style="width:14%;">';
                                    <?php if($edit_student){ ?>
                                        html += '<button type="button" class="btn btn-outline-primary btn-pill m-r-5" onclick="editUser('+resp[i].user_id+');"><i class="zmdi zmdi-edit"></i></button>'; 
                                    <?php } if ($delete_student) { ?>
                                        html += '<button type="button" class="btn btn-outline-danger btn-pill m-r-5" onclick="deleteUser('+resp[i].user_id+');"><i class="zmdi zmdi-delete"></i></button>';
                                    <?php } ?>
                                    html += '</td>';
                                <?php } ?>
                            html += '</tr>';
                    }
                    $('#tbody_data').html(html);
                    /* var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
                    elems.forEach(function(htmls) { 
                        var init = new Switchery(htmls, { size: 'small' });
                    });  */
                },
                error: function(result) {
                    toastr.error('Error :'+result)
                }
            });
        }

        function updateUserStatus(id) {
            $.ajax({
                type: "POST",
                url: "<?=base_url()?>updateUserStatus",
                data: 'user_id=' + id,
                success: function (result) {
                    var responsedata = $.parseJSON(result);
                    if (responsedata.status == 'success') {
                        toastr.success(responsedata.message)
                    } else {
                        toastr.error(responsedata.message)
                    }
                },
                error: function (result) {
                    toastr.error("Somthing went wrong :(")
                }
            });
        }

        function deleteUser(id) {
            swal({
                    title: "Are you sure?",
                    text: "Your will not be able to recover this!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm: false
                },
                function () {
                    $.ajax({
                        type: "POST",
                        url: "<?=base_url()?>deleteUser",
                        data: 'user_id=' + id,
                        success: function (result) {
                            var responsedata = $.parseJSON(result);
                            if (responsedata.status == 'success') {
                                var table = $('#table-1').DataTable();
                                table.row('#userrow' + id).remove().draw(false);
                                swal("Done!", responsedata.message, "success")
                            } else {
                                swal("Sorry!", responsedata.message, "error");
                            }
                        },
                        error: function (result) {
                            swal("", "Somthing went wrong :(", "error");
                        }
                    });
                });
        }

        function editUser(id) {
            var form = document.createElement("form");
            form.setAttribute("method", "post");
            form.setAttribute("action", "<?=base_url()?>edit-student");

            hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", "user_id");
            hiddenField.setAttribute("value", id);
            form.appendChild(hiddenField);

            document.body.appendChild(form);
            form.submit();
        }

        const updateLoginAccess = (id, accessGroup) => {
            $.ajax({
            type: "POST",
            url: "<?=base_url()?>update-login-access",
            data: 'user_id='+id+"&accessGroup="+accessGroup,
            success: function(result) {
                var resp = $.parseJSON(result);
                if (resp.status=='success') {
                toastr.success(resp.message)
                }else{
                toastr.error(resp.message)
                }
            },
            error: function(result) {
                toastr.error("Somthing went wrong :(")
            }
            });
        }
    </script>
</body>

</html>