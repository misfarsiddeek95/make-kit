<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php $this->load->view('includes/head'); ?>
    <style>
        .points-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 600;
        }
        .points-earned {
            background-color: #e8f4fd;
            color: #1d87e4;
            border: 1px solid #bce8f1;
        }
        .points-spent {
            background-color: #fef0f0;
            color: #f44336;
            border: 1px solid #fcd3d3;
        }
        .points-remain {
            background-color: #e8f8f5;
            color: #27ae60;
            border: 1px solid #c2eedd;
            font-size: 14px;
        }
        .modal-points-box {
            background-color: #f7f9fa;
            border: 1px solid #e4e7ea;
            border-radius: 6px;
            padding: 12px 15px;
            margin-bottom: 15px;
        }
        .modal-points-item {
            text-align: center;
        }
        .modal-points-val {
            font-size: 18px;
            font-weight: bold;
        }
        .modal-points-label {
            font-size: 12px;
            color: #777;
        }
    </style>
</head>

<body class="layout layout-header-fixed layout-left-sidebar-fixed">
    <?php $this->load->view('includes/topbar'); ?>
    <div class="site-main">
        <?php $this->load->view('includes/sidebar'); ?>
        <div class="site-content">
            <div class="panel panel-default panel-table">
                <div class="panel-heading">
                    <h3 class="m-t-0 m-b-5"><i class="zmdi zmdi-star text-warning"></i> חלוקת מדליות לתלמידים</h3>
                </div>
                <div class="panel-body">
                    <h5>סנן תלמידים</h5>
                    <div class="row">
                        <div class="col-sm-3 col-md-3">
                            <div class="form-group">
                                <label for="class_id" class="control-label">מוסד</label>
                                <select id="class_id" name="class_id" class="form-control" data-plugin="select2" style="width: 100%;" onchange="filterMedalStudents();">
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
                                <select id="subject_id" name="subject_id" class="form-control" data-placeholder="בחר חוג" data-allow-clear="true" style="width: 100%;" data-plugin="select2" onchange="filterMedalStudents();">
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
                                <select id="teacher_id" name="teacher_id" class="form-control" data-placeholder="בחר מדריך" data-allow-clear="true" style="width: 100%;" data-plugin="select2" onchange="filterMedalStudents();">
                                    <option></option>
                                    <?php foreach ($loadInstructors as $row ) {?>
                                    <option value="<?=$row->teacher_id?>"><?=$row->teacher_name?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3 col-md-3">
                            <div class="form-group">
                                <label for="city_id" class="control-label">עיר</label>
                                <select id="city_id" name="city_id" class="form-control" data-placeholder="בחר עיר" data-allow-clear="true" style="width: 100%;" data-plugin="select2" onchange="filterMedalStudents();">
                                    <option></option>
                                    <?php foreach ($loadCities as $row ) {?>
                                    <option value="<?=$row->city_id?>"><?=$row->city_name?> [ <?=$row->city_name_hebrew?> ]</option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive m-y-5">
                        <table class="table table-hover" id="medals-table">
                            <thead>
                                <tr>
                                    <th style="width:18%;">שם תלמיד</th>
                                    <th>מוסד</th>
                                    <th>חוג</th>
                                    <th>מדריך</th>
                                    <th>עיר</th>
                                    <th class="text-center" style="width:12%;">נקודות שנצברו</th>
                                    <th class="text-center" style="width:12%;">מדליות שחולקו</th>
                                    <th class="text-center" style="width:12%;">יתרת נקודות</th>
                                    <th style="text-align:right;width:15%;">פעולות</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_medals"></tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
        <?php $this->load->view('includes/footer'); ?>
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
                        <div class="modal-points-box">
                            <div class="row">
                                <div class="col-xs-4 modal-points-item">
                                    <div class="modal-points-val text-primary" id="award_earned_pts">0</div>
                                    <div class="modal-points-label">נקודות שנצברו</div>
                                </div>
                                <div class="col-xs-4 modal-points-item">
                                    <div class="modal-points-val text-danger" id="award_spent_pts">0</div>
                                    <div class="modal-points-label">מדליות שחולקו</div>
                                </div>
                                <div class="col-xs-4 modal-points-item">
                                    <div class="modal-points-val text-success" id="award_remain_pts">0</div>
                                    <div class="modal-points-label">יתרה זמינה</div>
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
                            <textarea class="form-control" id="award_notes" name="notes" rows="3" placeholder="למשל: מדליית זהב על הצטיינות במבחן מסכם..."></textarea>
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

    <!-- Modal: Medals History -->
    <div class="modal fade" id="medalsHistoryModal" tabindex="-1" role="dialog" aria-labelledby="medalsHistoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="medalsHistoryModalLabel"><i class="zmdi zmdi-time-restore text-info"></i> היסטוריית חלוקת מדליות</h4>
                </div>
                <div class="modal-body">
                    <h4 id="history_student_name" class="m-t-0 m-b-15 text-primary text-center"></h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="history-table">
                            <thead>
                                <tr>
                                    <th style="width:20%;">תאריך ושעה</th>
                                    <th style="width:15%;" class="text-center">כמות מדליות</th>
                                    <th style="width:25%;">הוענק על ידי</th>
                                    <th>הערות</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_history"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">סגור</button>
                </div>
            </div>
        </div>
    </div>

    <?php $this->load->view('includes/javascripts'); ?>
    <script src="<?=base_url()?>assets/js/forms-plugins.js"></script>
    <script type="text/javascript">
        var canAward = <?=$award_medal ? 1 : 0?>;
        var medalsDataTable = null;

        $(document).ready(function(){
            if (!$.fn.DataTable.isDataTable('#medals-table')) {
                medalsDataTable = $('#medals-table').DataTable({
                    "language": {
                        "emptyTable": "לא נמצאו נתונים",
                        "info": "מציג _START_ עד _END_ מתוך _TOTAL_ רשומות",
                        "infoEmpty": "מציג 0 עד 0 מתוך 0 רשומות",
                        "infoFiltered": "(מסונן מתוך _MAX_ רשומות)",
                        "lengthMenu": "הצג _MENU_ רשומות",
                        "loadingRecords": "טוען...",
                        "processing": "מעבד...",
                        "search": "חיפוש:",
                        "zeroRecords": "לא נמצאו רשומות מתאימות",
                        "paginate": {
                            "first": "ראשון",
                            "last": "אחרון",
                            "next": "הבא",
                            "previous": "הקודם"
                        }
                    }
                });
            } else {
                medalsDataTable = $('#medals-table').DataTable();
            }

            $("#class_id").select2({ placeholder: "בחר מוסד", allowClear: true });

            filterMedalStudents();
        });

        function filterMedalStudents() {
            var class_id = $('#class_id').val();
            var city_id = $('#city_id').val();
            var teacher_id = $('#teacher_id').val();
            var subject_id = $('#subject_id').val();

            $.ajax({
                type: "POST",
                url: "<?=base_url()?>filter-students",
                data: { class_id: class_id, city_id: city_id, teacher_id: teacher_id, subject_id: subject_id },
                success: function (result) {
                    var resp = $.parseJSON(result);
                    medalsDataTable.clear();

                    for (var i = 0; i < resp.length; i++) {
                        var row = resp[i];
                        var earned = parseFloat(row.points_earned_medalian || 0);
                        var spent = parseFloat(row.points_spent_medalian || 0);
                        var remain = earned - spent;
                        if (remain < 0) remain = 0;

                        var actionBtns = '';
                        if (canAward) {
                            var safeName = $('<div>').text(row.name).html().replace(/'/g, "\\'");
                            actionBtns += '<button type="button" class="btn btn-warning btn-sm btn-pill m-r-5" onclick="openAwardMedalModal(' + row.user_id + ', \'' + safeName + '\', ' + earned + ', ' + spent + ', ' + remain + ')" title="הענק מדליה"><i class="zmdi zmdi-star"></i> הענק</button>';
                        }
                        var safeNameHist = $('<div>').text(row.name).html().replace(/'/g, "\\'");
                        actionBtns += '<button type="button" class="btn btn-outline-info btn-sm btn-pill" onclick="openMedalsHistoryModal(' + row.user_id + ', \'' + safeNameHist + '\')" title="היסטוריית חלוקת מדליות"><i class="zmdi zmdi-time-restore"></i> היסטוריה</button>';

                        medalsDataTable.row.add([
                            '<b>' + row.name + '</b>' + (row.role_number ? '<br><small class="text-muted">' + row.role_number + '</small>' : ''),
                            row.class_name || '-',
                            row.subject_name || '-',
                            row.instructor_name ? '<b>' + row.instructor_name + '</b>' : '-',
                            (row.city_name || '') + (row.city_name_hebrew ? ' [' + row.city_name_hebrew + ']' : ''),
                            '<div class="text-center"><span class="points-badge points-earned" id="earned_val_' + row.user_id + '">' + earned + '</span></div>',
                            '<div class="text-center"><span class="points-badge points-spent" id="spent_val_' + row.user_id + '">' + spent + '</span></div>',
                            '<div class="text-center"><span class="points-badge points-remain" id="remain_val_' + row.user_id + '">' + remain + '</span></div>',
                            actionBtns
                        ]).node().id = 'medalRow_' + row.user_id;
                    }

                    medalsDataTable.draw();
                },
                error: function (result) {
                    toastr.error('שגיאה בטעינת נתונים: ' + result);
                }
            });
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

                            // Update live table badges
                            $('#spent_val_' + studentId).text(res.new_spent);
                            $('#remain_val_' + studentId).text(res.new_remaining);

                            // Refresh student filter row data cleanly
                            filterMedalStudents();
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

        function openMedalsHistoryModal(studentId, studentName) {
            $('#history_student_name').text(studentName);
            $('#tbody_history').html('<tr><td colspan="4" class="text-center"><i class="zmdi zmdi-spinner zmdi-hc-spin"></i> טוען היסטוריה...</td></tr>');
            $('#medalsHistoryModal').modal('show');

            $.ajax({
                type: "POST",
                url: "<?=base_url()?>medals-history",
                data: { student_id: studentId },
                success: function(response) {
                    try {
                        var res = (typeof response === 'object') ? response : JSON.parse(response);
                        if (res.status === 'success') {
                            var list = res.data;
                            if (!list || list.length === 0) {
                                $('#tbody_history').html('<tr><td colspan="4" class="text-center text-muted">לא נמצאו רשומות חלוקת מדליות עבור תלמיד זה.</td></tr>');
                                return;
                            }
                            var html = '';
                            for (var i = 0; i < list.length; i++) {
                                var item = list[i];
                                html += '<tr>' +
                                    '<td>' + (item.created_at || '-') + '</td>' +
                                    '<td class="text-center"><span class="label label-warning" style="font-size:13px;"><i class="zmdi zmdi-star"></i> ' + parseFloat(item.medals_count) + '</span></td>' +
                                    '<td>' + (item.given_by_name || '-') + '</td>' +
                                    '<td>' + (item.notes ? $('<div>').text(item.notes).html() : '<span class="text-muted">-</span>') + '</td>' +
                                '</tr>';
                            }
                            $('#tbody_history').html(html);
                        } else {
                            $('#tbody_history').html('<tr><td colspan="4" class="text-center text-danger">' + (res.message || 'שגיאה בטעינת היסטוריה') + '</td></tr>');
                        }
                    } catch(err) {
                        $('#tbody_history').html('<tr><td colspan="4" class="text-center text-danger">שגיאה בפענוח הנתונים.</td></tr>');
                    }
                },
                error: function() {
                    $('#tbody_history').html('<tr><td colspan="4" class="text-center text-danger">שגיאה בתקשורת עם השרת.</td></tr>');
                }
            });
        }
    </script>
</body>

</html>
