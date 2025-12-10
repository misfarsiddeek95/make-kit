<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php $this->load->view('includes/head'); ?>
    <style>
        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }
        .panel-group .panel{
            border: 1px dashed #288140;
        }
    </style>
</head>

<body class="layout layout-header-fixed layout-left-sidebar-fixed">
    <?php $this->load->view('includes/topbar'); ?>
    <div class="site-main">
        <?php $this->load->view('includes/sidebar'); ?>
        <?php if($assign_subject_list){ ?>
        <div class="site-content">
            <div class="panel panel-default panel-table">
                <div class="panel-heading">
                    <div class="panel-tools">
                        <?php if($assign_subject){ ?>
                        <button type="button" class="btn btn-outline-success btn-pill" data-toggle="modal"
                            data-target="#otherModal3" title="Assign" onclick="assignSubjects();"><i
                                class="zmdi zmdi-plus"></i></button>
                        <?php } ?>
                    </div>
                    <h3 class="m-t-0 m-b-5">ניהול הקצאת מדריך וחוג</h3>
                </div>
                <div class="panel-body">

                    <div class="panel-group" id="accordionOne" role="tablist" aria-multiselectable="true">
                        <?php 
                            foreach ($all_assigned_subjects as $subj) {  
                                $tabid = url_title($subj->subject_name, '-', true);
                        ?>
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab">
                                <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#accordionOne"
                                        href="#<?=$tabid?>" aria-expanded="false" class="collapsed">
                                        <i class="zmdi zmdi-chevron-down"></i>
                                        <?=$subj->subject_name?>
                                    </a>
                                </h4>
                            </div>
                            <div id="<?=$tabid?>" class="panel-collapse collapse" role="tabpanel">
                                <div class="panel-body">
                                    <div class="table-responsive m-y-5">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>כיתה</th>
                                                    <th>מורה</th>
                                                    <th>חוג</th>
                                                    <th>סוג חוג</th>
                                                    <?php if($edit_assigned_subject || $delete_assigned_subject){ ?>
                                                    <th style="text-align:right;">אפשרויות</th>
                                                    <?php } ?>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody_data">
                                                <?php 
                                                    $i = 1;
                                                    foreach ($subj->assigned_teachers as $row ) { 
                                                        $sub_type = '';
                                                        $cls = '';
                                                        if ($row->subject_type==0) {
                                                            $sub_type = 'תיאוריה';
                                                            $cls = 'primary';
                                                        }elseif ($row->subject_type==1) {
                                                            $sub_type = 'מעשי';
                                                            $cls = 'dark';
                                                        }else {
                                                            $sub_type = 'שניהם';
                                                            $cls = 'success';
                                                        }
                                                ?>
                                                <tr id="subjassgnrow<?=$row->sa_id?>">
                                                    <th><?=$i?></th>
                                                    <td cls-id="<?=$row->class_id?>"><?=$row->class_name?></td>
                                                    <td tr-id="<?=$row->user_id?>"><span class="label label-success"
                                                            style="cursor: pointer;"
                                                            onclick="location.href='<?=base_url()?>teacher-detail/<?=$row->user_id?>'"><?=$row->fname.' '.$row->lname?></span>
                                                    </td>
                                                    <td sub-id="<?=$row->sub_id?>"><?=$row->subject_name?></td>
                                                    <td><span class="label label-outline-<?=$cls?>"><?=$sub_type?></span></td>
                                                    <?php if($edit_assigned_subject || $delete_assigned_subject){ ?>
                                                    <td align="right">
                                                        <?php if($edit_assigned_subject){ ?>
                                                        <button type="button" class="btn btn-outline-primary btn-pill m-r-5"
                                                            onclick="editAssignedSubject('<?=$row->sa_id?>');"><i
                                                                class="zmdi zmdi-edit"></i></button>
                                                        <?php } if($delete_assigned_subject){?>
                                                        <button type="button" class="btn btn-outline-danger btn-pill m-r-5"
                                                            onclick="deleteAssignedSubject('<?=$row->sa_id?>');"><i
                                                                class="zmdi zmdi-delete"></i></button>
                                                        <?php } ?>
                                                    </td>
                                                    <?php } ?>
                                                </tr>
                                                <?php $i++;} ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div> 
                </div>
            </div>
            <div id="otherModal3" class="modal fade" tabindex="-1" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">
                                    <i class="zmdi zmdi-close"></i>
                                </span>
                            </button>
                            <h4 class="modal-title" id="modal-title">הקצה מדריך</h4>
                        </div>

                        <form data-toggle="validator" id="inputmasks">
                            <div class="modal-body">
                                <input type="hidden" name="sa_id" id="sa_id" value="0">
                                <div class="row">
                                    <div class="col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <label for="class_id" class="control-label">מוסד</label>
                                            <select id="class_id" name="class_id" class="form-control"
                                                data-plugin="select2" style="width: 100%;"
                                                data-placeholder="בחר מוסד" required data-required-error="בחר מוסד" style="width:100;" data-allow-clear="true">
                                                <option></option>
                                                <?php foreach ($all_classes as $row ) { ?>
                                                <option value="<?=$row->class_id?>"><?=$row->class_name?></option>
                                                <?php } ?>
                                            </select>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <label for="subject_id" class="control-label">חוג</label>
                                            <select id="subject_id" name="subject_id" class="form-control"
                                                data-plugin="select2" style="width: 100%;"
                                                data-placeholder="בחר חוג" required data-required-error="בחר חוג" style="width:100;" data-allow-clear="true">
                                                <option></option>
                                            </select>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <label for="teacher_id" class="control-label">מדריך</label>
                                            <select id="teacher_id" name="teacher_id" class="form-control"
                                                data-plugin="select2" style="width: 100%;"
                                                data-placeholder="בחר מדריך" required data-required-error="בחר מדריך" style="width:100;" data-allow-clear="true">
                                                <option></option>
                                            </select>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">שליחה</button>
                                <button type="button" data-dismiss="modal" class="btn btn-default">סגור</button>
                            </div>
                        </form>
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


        function assignSubjects() {
            $('#modal-title').text('הקצה חוג');
            $('#sa_id').val(0);
            $('#class_id').val(null);
            $('#class_id').select2({
                placeholder: "בחר כיתה",
                dropdownParent: $('#otherModal3')
            });
            
            $('#teacher_id').val(null);
            $('#teacher_id').select2({
                placeholder: "בחר מורה",
                dropdownParent: $('#otherModal3')
            });
            $('#subject_id').val(null);
            $('#subject_id').select2({
                placeholder: "בחר חוג",
                dropdownParent: $('#otherModal3')
            });
        }

        $('#class_id').on('change', function () {
            $('#section_id,#subject_id,#teacher_id').empty();
            var sa_id = $('#sa_id').val();
            $.ajax({
                type: "POST",
                url: "<?=base_url()?>get-sections-and-subjects",
                data: 'class_id=' + this.value,
                success: function (result) {
                    var responsedata = $.parseJSON(result);

                    var options = '<option></option>';
                    for (let i = 0; i < responsedata.subjects.length; i++) {
                        options += '<option value=' + responsedata.subjects[i].sub_id + '>' +
                            responsedata.subjects[i].subject_name + '</option>';
                    }
                    $('#subject_id').append(options);

                    var options = '<option></option>';
                    for (let i = 0; i < responsedata.teachers.length; i++) {
                        options += '<option value=' + responsedata.teachers[i].user_id + '>' +
                            responsedata.teachers[i].name + '</option>';
                    }
                    $('#teacher_id').append(options);

                    if (sa_id != 0) {
                        var teacher_id = $('#subjassgnrow' + sa_id).find('td:eq(1)').attr('tr-id');
                        $('#teacher_id').val(teacher_id).trigger('change');
                    }

                    if (sa_id != 0) {
                        var subject_id = $('#subjassgnrow' + sa_id).find('td:eq(2)').attr('sub-id');
                        $('#subject_id').val(subject_id).trigger('change');
                    }
                },
                error: function (result) {
                    toastr.error('Error :' + result)
                }
            });
        });

        $('#inputmasks').validator().on('submit', function (e) {
            if (!(e.isDefaultPrevented())) {
                e.preventDefault();
                run_waitMe('#inputmasks');
                $.ajax({
                    type: "POST",
                    url: "<?=base_url()?>saveAssignedSubject",
                    data: $('#inputmasks').serialize(),
                    success: function (result) {
                        var responsedata = $.parseJSON(result);
                        if (responsedata.status == 'success') {
                            if (responsedata.message == 'save') {
                                toastr.success('חוג ומדריך הוקצו בהצלחה.')
                                setTimeout(function () {
                                    location.reload();
                                }, 1000);
                            } else {
                                toastr.success(
                                    'חוג ומדריך עודכנו בהצלחה.')
                                setTimeout(function () {
                                    location.reload();
                                }, 1000);
                            }
                        } else {
                            toastr.error(responsedata.message)
                        }
                    },
                    error: function (result) {
                        $('#inputmasks').waitMe('hide');
                        toastr.error('Error :' + result)
                    }
                });
                $('#inputmasks').waitMe('hide');
            }
        });

        function editAssignedSubject(id) {
            $('#modal-title').text('עדכן חוג משוייך');
            var class_id = $('#subjassgnrow' + id).find('td:eq(0)').attr('cls-id');
            var teacher_id = $('#subjassgnrow' + id).find('td:eq(1)').attr('tr-id');
            var subject_id = $('#subjassgnrow' + id).find('td:eq(2)').attr('sub-id');


            $('#sa_id').val(id);
            $('#class_id').val(class_id).trigger('change');
            $('#teacher_id').val(teacher_id).trigger('change');
            $('#subject_id').val(subject_id).trigger('change');
            $('#class_id,#teacher_id,#subject_id').select2({
                dropdownParent: $('#otherModal3')
            });
            $('#otherModal3').modal('show');
        }

        function deleteAssignedSubject(id) {
            toastr.warning("<button type='button' id='confirmBtn' class='btn btn-danger btn-sm' style='width:40%;display:inline;margin:3px;'>כן</button><button type='button' id='closeBtn' class='btn btn-default btn-sm' style='width:40%;display:inline;margin:3px;'>לא</button>",'האם ברצונך למחוק רשומה משוייכת זו?',{
                closeButton: true,
                allowHtml: true,
                onShown: function (toast) {
                $("#confirmBtn").click(function(){
                    $.ajax({
                        type: "POST",
                        url: "<?=base_url()?>delete-assigned-subject",
                        data: 'sa_id=' + id,
                        success: function(result) {
                            var responsedata = $.parseJSON(result);
                            if (responsedata.status=='success') {
                                $('#subjassgnrow' + id).remove();
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
    </script>
</body>

</html>