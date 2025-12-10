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
                    <h3 class="m-t-0 m-b-5">ניהול תלמידים</h3>
                </div>
                <div class="panel-body">
                    <h5>סנן תלמידים</h5>
                    <div class="row">
                        <div class="col-sm-4 col-md-3">
                            <div class="form-group">
                                <label for="class_id" class="control-label">מוסד</label>
                                <select id="class_id" name="class_id" class="form-control" data-plugin="select2" style="width: 100%;" onchange="filterStudents();">
                                    <option></option>
                                    <?php foreach ($loadInstitutes as $row ) {?>
                                    <option value="<?=$row->class_id?>"><?=$row->class_name?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-3">
                            <div class="form-group">
                                <label for="subject_id" class="control-label">חוג</label>
                                <select id="subject_id" name="subject_id" class="form-control" data-placeholder="בחר חוג" data-allow-clear="true" style="width: 100%;" data-plugin="select2" onchange="filterStudents();">
                                    <option></option>
                                    <?php foreach ($loadSubjects as $row ) {?>
                                    <option value="<?=$row->sub_id?>"><?=$row->subject_name?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-3">
                            <div class="form-group">
                                <label for="teacher_id" class="control-label">מדריך</label>
                                <select id="teacher_id" name="teacher_id" class="form-control" data-placeholder="בחר מדריך" data-allow-clear="true" style="width: 100%;" data-plugin="select2" onchange="filterStudents();">
                                    <option></option>
                                    <?php foreach ($loadInstructors as $row ) {?>
                                    <option value="<?=$row->teacher_id?>"><?=$row->teacher_name?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-3">
                            <div class="form-group">
                                <label for="city_id" class="control-label">עיר</label>
                                <select id="city_id" name="city_id" class="form-control" data-placeholder="בחר עיר" data-allow-clear="true" style="width: 100%;" data-plugin="select2" onchange="filterStudents();">
                                    <option></option>
                                    <?php foreach ($loadCities as $row ) {?>
                                    <option value="<?=$row->city_id?>"><?=$row->city_name?> [ <?=$row->city_name_hebrew?> ]</option>
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
                                    <th style="width:20%">שם</th>
                                    <th>מס' תפקיד</th>
                                    <th>מוסד</th>
                                    <th>מגדר</th>
                                    <th>עיר</th>
                                    <th>שם הורה</th>
                                    <th>טלפון הורה</th>
                                    <th>דוא"ל הורה</th>
                                    <th>סטטוס</th>
                                    <?php if($edit_student || $delete_student){ ?>
                                    <th style="text-align:right;width:10%">אפשרויות</th>
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
        $(document).ready(function(){
            // Initialize DataTable only once
            if (!$.fn.DataTable.isDataTable('#table-1')) {
                $('#table-1').DataTable();
            }

            $('.table-responsive').on('show.bs.dropdown', function () {
                $('.table-responsive').css("overflow", "inherit");
            });

            $('.table-responsive').on('hide.bs.dropdown', function () {
                $('.table-responsive').css("overflow", "auto");
            });
            filterStudents();
        });

        $("#class_id").select2({
            placeholder: "בחר מוסד",
            allowClear: true
        });

        function filterStudents() {
            var class_id = $('#class_id').val();
            var city_id = $('#city_id').val();
            var teacher_id = $('#teacher_id').val();
            var subject_id = $('#subject_id').val();

            $.ajax({
                type: "POST",
                url: "<?=base_url()?>filter-students",
                data: { class_id, city_id, teacher_id, subject_id },
                success: function (result) {
                    var resp = $.parseJSON(result);
                    var table = $('#table-1').DataTable();

                    table.clear(); // Remove existing rows

                    for (let i = 0; i < resp.length; i++) {
                        let row = resp[i];

                        let gender = 'לא נמסר';
                        let typecls = 'danger';
                        if (row.gender == 0) { gender = 'נקבה'; typecls = 'primary'; }
                        else if (row.gender == 1) { gender = 'זכר'; typecls = 'success'; }
                        else if (row.gender == 2) { gender = 'אחר'; typecls = 'warning'; }

                        let img = row.photo_path ? 'students/' + row.photo_path + '-thu.' + row.extension : 'user_default.jpg';

                        let status = row.status == 1 ? 'checked="checked"' : '';
                        const status_change = '<?=$changeStatus?>';
                        const myId = '<?=$this->session->userdata['staff_logged_in']['user_id']?>';
                        const myGroup = '<?=$this->session->userdata['staff_logged_in']['group_id']?>';

                        let status_action = (status_change == 1 && (myGroup != row.user_type || myId != row.user_id)) ? `onchange="updateUserStatus(${row.user_id})"` : 'disabled';

                        let actionBtns = '';
                        <?php if($edit_student){ ?>
                            actionBtns += `<button type="button" class="btn btn-outline-primary btn-pill m-r-5" onclick="editUser(${row.user_id})"><i class="zmdi zmdi-edit"></i></button>`;
                        <?php } ?>
                        <?php if($delete_student){ ?>
                            actionBtns += `<button type="button" class="btn btn-outline-danger btn-pill m-r-5" onclick="deleteMe(${row.user_id})"><i class="zmdi zmdi-delete"></i></button>`;
                        <?php } ?>

                        table.row.add([
                            `<img class="img-rounded" src="<?=base_url()?>photos/${img}" height="32">`,
                            row.name,
                            row.role_number ?? '',
                            row.class_name,
                            `<span class="label label-outline-${typecls}">${gender}</span>`,
                            `${row.city_name} [ ${row.city_name_hebrew} ]`,
                            row.parent_name,
                            row.parent_phone,
                            row.parent_email,
                            `<label class="switch switch-success m-t-10">
                                <input type="checkbox" class="s-input" ${status} ${status_action}>
                                <span class="s-content">
                                    <span class="s-track"></span>
                                    <span class="s-handle"></span>
                                </span>
                            </label>`,
                            actionBtns
                        ]).node().id = 'rowId' + row.user_id;
                    }

                    table.draw();
                },
                error: function (result) {
                    toastr.error('Error :' + result);
                }
            });
        }


        function updateUserStatus(id) {
            $.ajax({
                type: "POST",
                url: "<?=base_url()?>update-student-status",
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
                    toastr.error("משהו השתבש :(")
                }
            });
        }

        function deleteMe(id) {
            toastr.warning("<button type='button' id='confirmBtn' class='btn btn-danger btn-sm' style='width:40%;display:inline;margin:3px;'>כן</button><button type='button' id='closeBtn' class='btn btn-default btn-sm' style='width:40%;display:inline;margin:3px;'>לא</button>",'האם ברצונך למחוק תלמיד זה?',{
                closeButton: true,
                allowHtml: true,
                onShown: function (toast) {
                $("#confirmBtn").click(function(){
                    $.ajax({
                        type: "POST",
                        url: "<?=base_url()?>delete-student",
                        data: 'user_id=' + id,
                        success: function(result) {
                            var responsedata = $.parseJSON(result);
                            if (responsedata.status=='success') {
                                var table = $('#table-1').DataTable();
                                table.row('#rowId'+id).remove().draw( false );
                                toastr.success(responsedata.message)
                            }else{
                                toastr.error(responsedata.message)
                            }
                        },
                        error: function(result) {
                            toastr.error("משהו השתבש :(")
                        }
                    });
                });
                $("#closeBtn").click(function(){
                    toastr.clear()
                });
                }
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
    </script>
</body>

</html>