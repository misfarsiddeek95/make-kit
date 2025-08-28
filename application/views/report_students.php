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
        <div class="site-content">
            <div class="panel panel-default panel-table">
                <div class="panel-heading">
                    <h3 class="m-t-0 m-b-5">Student Report</h3>
                </div>
                <div class="panel-body">
                    <h5>FILTER STUDENTS</h5>
                    <div class="row">
                        <div class="col-sm-4 col-md-3">
                            <div class="form-group">
                                <label for="class_id" class="control-label">Institute</label>
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
                                <label for="subject_id" class="control-label">Circle</label>
                                <select id="subject_id" name="subject_id" class="form-control" data-placeholder="Select a circle" data-allow-clear="true" style="width: 100%;" data-plugin="select2" onchange="filterStudents();">
                                    <option></option>
                                    <?php foreach ($loadSubjects as $row ) {?>
                                    <option value="<?=$row->sub_id?>"><?=$row->subject_name?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-3">
                            <div class="form-group">
                                <label for="teacher_id" class="control-label">Instructor</label>
                                <select id="teacher_id" name="teacher_id" class="form-control" data-placeholder="Select an instructor" data-allow-clear="true" style="width: 100%;" data-plugin="select2" onchange="filterStudents();">
                                    <option></option>
                                    <?php foreach ($loadInstructors as $row ) {?>
                                    <option value="<?=$row->teacher_id?>"><?=$row->teacher_name?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-3">
                            <div class="form-group">
                                <label for="city_id" class="control-label">City</label>
                                <select id="city_id" name="city_id" class="form-control" data-placeholder="Select a city" data-allow-clear="true" style="width: 100%;" data-plugin="select2" onchange="filterStudents();">
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
                                    <!-- <th></th> -->
                                    <th>Name</th>
                                    <th>Institute</th>
                                    <th>Circle</th>
                                    <th>Instructor</th>
                                    <th style="width:15%;">City</th>
                                    <th class="text-center">MakeKit Points</th>
                                    <th>Medalian Points</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_data"></tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
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
            placeholder: "Select a institute",
            allowClear: true
        });

        function filterStudents() {
            var class_id = $('#class_id').val();
            var city_id = $('#city_id').val();
            var teacher_id = $('#teacher_id').val();
            var subject_id = $('#subject_id').val();

            $.ajax({
                type: "POST",
                url: "<?=base_url()?>report-filter-students",
                data: { class_id, city_id, teacher_id, subject_id },
                success: function (result) {
                    var resp = $.parseJSON(result);
                    var table = $('#table-1').DataTable();

                    table.clear(); // Remove existing rows

                    for (let i = 0; i < resp.length; i++) {
                        const row = resp[i];

                        let img = row.photo_path ? 'students/' + row.photo_path + '-thu.' + row.extension : 'user_default.jpg';

                        table.row.add([
                            // `<img class="img-rounded" src="<?=base_url()?>photos/${img}" height="32">`,
                            row.name,
                            row.class_name,
                            row.subject_name,
                            `<span style="font-weight: bold;">${row.instructor_name}</span>`,
                            `${row.city_name} - ${row.city_name_hebrew}`,
                            `
                                <div style="text-align:center; font-size:12px; line-height:1.4;">
                                    <div>
                                        <small><b>Earned</b>: ${parseFloat(row.points_earned + 0)}</small>
                                        <small><b>Spent</b>: ${parseFloat(row.points_spent + 0)}</small>
                                    </div>
                                    <hr style="margin:4px auto; width:60%; border:0; border-top:1px solid #ccc;">
                                    <div><span class="text-muted"><b>Balance</b>: ${row.points_earned - row.points_spent}</span></div>
                                </div>
                            `,
                            `
                                <span style="font-weight: bold;">Total Points: ${parseFloat(row.points_earned_medalian + 0)}</span>
                            `
                        ]).node().id = 'rowId' + row.user_id;
                    }

                    table.draw();
                },
                error: function (result) {
                    toastr.error('Error :' + result);
                }
            });
        }
    </script>
</body>

</html>