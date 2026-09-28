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
                    <div class="panel-tools">
                            <?php if($add_student){?>
                        <button type="button" class="btn btn-outline-success btn-pill" title="Add Student"
                            onclick="location.href='<?=base_url();?>add-student'"><i class="zmdi zmdi-plus"></i></button>
                        <?php }?>
                    </div>
                    <h3 class="m-t-0 m-b-5">ניהול תלמידים</h3>
                </div>
                <div class="panel-body">
                    <h5>סנן תלמידים</h5>
                    <div class="row">
                        <div class="col-sm-3 col-md-3">
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
                        <div class="col-sm-3 col-md-3">
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
                        <div class="col-sm-3 col-md-3">
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
                        <div class="col-sm-3 col-md-2">
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
                        <div class="col-md-1">
                            <div class="form-group" style="padding-top:25px;">
                                <button type="button" class="btn btn-primary" title="ייצוא לאקסל"
                                    onclick="exportStudents()"><i class="zmdi zmdi-collection-download"></i> ייצוא</button>
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
            <!-- Modal: Award Medals -->
            <div class="modal fade" id="awardMedalModal" tabindex="-1" role="dialog" aria-labelledby="awardMedalModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form id="awardMedalForm" onsubmit="submitAwardMedal(event);">
                            <input type="hidden" id="award_student_id" name="student_id" value="">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="awardMedalModalLabel"><i class="zmdi zmdi-star text-warning"></i> הענקת מדליות לתלמיד</h4>
                            </div>
                            <div class="modal-body">
                                <h4 id="award_student_name" class="m-t-0 m-b-15 text-primary text-center"></h4>
                                <div class="modal-points-box" style="background-color:#f7f9fa; border:1px solid #e4e7ea; border-radius:6px; padding:12px 15px; margin-bottom:15px;">
                                    <div class="row text-center">
                                        <div class="col-xs-4">
                                            <div style="font-size:18px; font-weight:bold;" class="text-primary" id="award_earned_pts">0</div>
                                            <div style="font-size:12px; color:#777;">נקודות שנצברו</div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div style="font-size:18px; font-weight:bold;" class="text-danger" id="award_spent_pts">0</div>
                                            <div style="font-size:12px; color:#777;">מדליות שחולקו</div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div style="font-size:18px; font-weight:bold;" class="text-success" id="award_remain_pts">0</div>
                                            <div style="font-size:12px; color:#777;">יתרה זמינה</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="award_medals_count" class="control-label">מספר מדליות להענקה <span class="text-danger">*</span></label>
                                    <input type="number" step="0.5" min="0.5" class="form-control" id="award_medals_count" name="medals_count" required placeholder="הזן כמות מדליות">
                                    <small class="help-block text-muted">הערך ינוכה מיתרת הנקודות הזמינה של התלמיד.</small>
                                </div>

                                <div class="form-group">
                                    <label for="award_notes" class="control-label">הערות / סיבת הענקה</label>
                                    <textarea class="form-control" id="award_notes" name="notes" rows="3" placeholder="למשל: מדליית זהב על הצטיינות..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">ביטול</button>
                                <button type="submit" id="awardSubmitBtn" class="btn btn-warning"><i class="zmdi zmdi-star"></i> הענק מדליות</button>
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

            window._studentFilters = { class_id, city_id, teacher_id, subject_id };

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
                        <?php if($award_medal){ ?>
                            var earnedMed = parseFloat(row.points_earned_medalian || 0);
                            var spentMed  = parseFloat(row.points_spent_medalian || 0);
                            var remainMed = earnedMed - spentMed;
                            if (remainMed < 0) remainMed = 0;
                            var safeNameMed = $('<div>').text(row.name).html().replace(/'/g, "\\'");
                            actionBtns += `<button type="button" class="btn btn-outline-warning btn-pill m-r-5" title="הענק מדליה" onclick="openAwardMedalModal(${row.user_id}, '${safeNameMed}', ${earnedMed}, ${spentMed}, ${remainMed})"><i class="zmdi zmdi-star"></i></button>`;
                        <?php } ?>
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

        function exportStudents() {
            var f = window._studentFilters || {};
            var params = $.param(f);
            window.location.href = '<?=base_url()?>export-students?' + params;
        }

        function openAwardMedalModal(studentId, studentName, earned, spent, remain) {
            $('#award_student_id').val(studentId);
            $('#award_student_name').text(studentName);
            $('#award_earned_pts').text(earned);
            $('#award_spent_pts').text(spent);
            $('#award_remain_pts').text(remain);
            $('#award_medals_count').val('');
            $('#award_medals_count').attr('max', remain);
            $('#award_notes').val('');

            if (remain <= 0) {
                toastr.warning('לתלמיד זה אין יתרת נקודות זמינה להענקת מדליות.');
                return;
            }

            $('#awardMedalModal').modal('show');
            setTimeout(function() {
                $('#award_medals_count').focus();
            }, 500);
        }

        function submitAwardMedal(e) {
            e.preventDefault();
            var studentId = $('#award_student_id').val();
            var count = parseFloat($('#award_medals_count').val());
            var notes = $('#award_notes').val();
            var currentRemain = parseFloat($('#award_remain_pts').text());

            if (isNaN(count) || count <= 0) {
                toastr.error('נא להזין כמות מדליות חוקית הגדולה מ-0.');
                return;
            }

            if (count > currentRemain) {
                toastr.error('לא ניתן להעניק יותר מדליות מיתרת הנקודות הקיימת (' + currentRemain + ').');
                return;
            }

            $('#awardSubmitBtn').prop('disabled', true).html('<i class="zmdi zmdi-spinner zmdi-hc-spin"></i> מעבד...');

            $.ajax({
                type: "POST",
                url: "<?=base_url()?>give-medal",
                data: { student_id: studentId, medals_count: count, notes: notes },
                success: function(response) {
                    $('#awardSubmitBtn').prop('disabled', false).html('<i class="zmdi zmdi-star"></i> הענק מדליות');
                    try {
                        var res = (typeof response === 'object') ? response : JSON.parse(response);
                        if (res.status === 'success') {
                            toastr.success(res.message);
                            $('#awardMedalModal').modal('hide');
                            filterStudents();
                        } else {
                            toastr.error(res.message || 'שגיאה בהענקת מדליות.');
                        }
                    } catch(err) {
                        toastr.error('שגיאה בתגובת השרת.');
                    }
                },
                error: function() {
                    $('#awardSubmitBtn').prop('disabled', false).html('<i class="zmdi zmdi-star"></i> הענק מדליות');
                    toastr.error('שגיאה בתקשורת עם השרת.');
                }
            });
        }
    </script>
</body>

</html>