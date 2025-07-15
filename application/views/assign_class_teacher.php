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
            -moz-appearance:textfield; 
        } 
    </style>
  </head>

  <body class="layout layout-header-fixed layout-left-sidebar-fixed">
    <?php $this->load->view('includes/topbar'); ?>
    <div class="site-main">
      <?php $this->load->view('includes/sidebar'); ?>
        <?php if($class_tr_list){ ?>
        <div class="site-content">
          <div class="panel panel-default panel-table">
            <div class="panel-heading">
              <div class="panel-tools"> 
                <?php if($add_class_tr){ ?>
                <button type="button" class="btn btn-outline-success btn-pill"  data-toggle="modal" data-target="#otherModal3" title="Add"  onclick="addClassTeacher();"><i class="zmdi zmdi-plus"></i></button>
                <?php } ?>
              </div>
              <h3 class="m-t-0 m-b-5">Institute's Instructor Management</h3>
            </div>   
            <div class="panel-body"> 
              <div class="table-responsive m-y-5"> 
                <table class="table table-hover" >
                    <thead>
                        <tr>
                            <th>Class Name</th> 
                            <th>Teacher Name</th> 
                            <?php if($edit_class_tr || $delete_class_tr){ ?>
                            <th style="text-align:right;">Options</th>  
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody id="tbody_data">  
                        <?php foreach ($assigned_teachers as $row) { ?>
                        <tr id="classtrrow<?=$row->clsec_id?>">
                            <td cls-id="<?=$row->class_id?>"><?=$row->class_name?></td>  
                            <td><?php foreach($row->clstrs as $cltr){ ?> <span class="label label-success classTeachrIds<?=$row->clsec_id?>" style="cursor: pointer;" tr-id="<?=$cltr->user_id?>" onclick="location.href='<?=base_url()?>teacher-detail/<?=$cltr->user_id?>'"><?=$cltr->fname.' '.$cltr->lname?></span><?php } ?></td>  
                            <?php if($edit_class_tr || $delete_class_tr){ ?>
                            <td align="right">
                                <?php if($edit_class_tr){ ?>
                                <button type="button" class="btn btn-outline-primary btn-pill m-r-5" onclick="editClassTeacher('<?=$row->clsec_id?>');"><i class="zmdi zmdi-edit"></i></button>
                                <?php } if($delete_class_tr){?>
                                <button type="button" class="btn btn-outline-danger btn-pill m-r-5" onclick="deleteAssignedTeachers('<?=$row->clsec_id?>');"><i class="zmdi zmdi-delete"></i></button>
                                <?php } ?>
                            </td>
                            <?php } ?>
                        </tr>  
                        <?php } ?>
                    </tbody>
                </table>
              </div>
            </div> 
          </div>
          <div id="otherModal3" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">
                                <i class="zmdi zmdi-close"></i>
                            </span>
                            </button>
                            <h4 class="modal-title" id="modal-title">Assign Instructor</h4>
                        </div>

                        <form data-toggle="validator" id="inputmasks">
                            <div class="modal-body">
                                <input type="hidden" name="tc_id" id="tc_id" value="0">  
                                <div class="form-group">
                                    <label for="class_id" class="control-label">Class</label>
                                    <select id="class_id" name="class_id" class="form-control" data-plugin="select2" style="width: 100%;" required data-required-error="Please select a class.">
                                        <?php foreach ($all_classes as $row ) { ?>
                                        <option value="<?=$row->class_id?>"><?=$row->class_name?></option> 
                                        <?php } ?>
                                    </select> 
                                    <div class="help-block with-errors"></div> 
                                </div>
                                <div class="row">
                                    <?php foreach ($teachers as $row ) { ?>
                                    <div class="col-sm-4 col-md-4">.
                                        <div class="form-group teacherChecksBox" teacher-id="<?=$row->user_id?>"> 
                                            <label class="custom-control custom-control-primary custom-checkbox teacherChecks<?=$row->user_id?>">
                                                <input class="custom-control-input teacherChecked" type="checkbox" id="teacherChecks<?=$row->user_id?>" name="teachers[]" value="<?=$row->user_id?>">
                                                <span class="custom-control-indicator"></span>
                                                <span class="custom-control-label"><?=$row->fname.' '.$row->lname?></span>
                                            </label>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                                
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <button type="button" data-dismiss="modal" class="btn btn-default">Close</button>
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
            $('.table-responsive').css( "overflow", "inherit" );
        });

        $('.table-responsive').on('hide.bs.dropdown', function () {
            $('.table-responsive').css( "overflow", "auto" );
        });
        
        function addClassTeacher() {
            $('#modal-title').text('Add Class Teacher');
            $('#tc_id').val(0); 
            $('#class_id').val(null).trigger('change');
            $('#class_id').select2({placeholder: "Select a Class",dropdownParent: $('#otherModal3')});

            $('.teacherChecksBox').each(function (index, value){
                var teacher_id = $(this).attr('teacher-id');
                $('.teacherChecks'+teacher_id).removeClass('active');
                $("#teacherChecks"+teacher_id).prop("checked", false);
            });
        }

        $('#inputmasks').validator().on('submit', function (e) {
            if (!(e.isDefaultPrevented())) {
                e.preventDefault();
                run_waitMe('#inputmasks');
                $.ajax({
                    type: "POST",
                    url: "<?=base_url()?>saveAssignedTeacher",
                    data: $('#inputmasks').serialize(),
                    success: function(result) {
                        var responsedata = $.parseJSON(result);
                        if(responsedata.status=='success'){
                            if (responsedata.message =='save') {
                                toastr.success('Class teacher assigned successfully.')
                                setTimeout(function(){
                                    location.reload();
                                }, 1000); 
                            }else{
                                toastr.success('Class teacher assigning updated successfully.')   
                                setTimeout(function(){
                                    location.reload();
                                }, 1000); 
                            } 
                        }else{
                            toastr.error(responsedata.message)
                        }
                    },
                    error: function(result) {
                        $('#inputmasks').waitMe('hide');
                        toastr.error('Error :'+result)
                    }
                });
                $('#inputmasks').waitMe('hide');
            }
        });

        function editClassTeacher(id) { 
            $('.custom-checkbox').removeClass('active');
            $(".teacherChecked").prop("checked", false);
            $('#modal-title').text('Update Class Teachers');
            var class_id = $('#classtrrow'+id).find('td:eq(0)').attr('cls-id');  
            $('.classTeachrIds'+id).each(function (index, value){ 
                var teacher_id = $(this).attr('tr-id'); 
                $('.teacherChecks'+teacher_id).addClass('active');
                $("#teacherChecks"+teacher_id).prop('checked', true);
            });
            
            $('#tc_id').val(id);
            $('#class_id').val(class_id).trigger('change'); 
            $('#class_id').select2({dropdownParent: $('#otherModal3')});
            $('#otherModal3').modal('show');
        }

        function deleteAssignedTeachers(id) {
            toastr.warning("<button type='button' id='confirmBtn' class='btn btn-danger btn-sm' style='width:40%;display:inline;margin:3px;'>Yes</button><button type='button' id='closeBtn' class='btn btn-default btn-sm' style='width:40%;display:inline;margin:3px;'>No</button>",'Do you want to delete this assinged record?',{
                closeButton: true,
                allowHtml: true,
                onShown: function (toast) {
                $("#confirmBtn").click(function(){
                    $.ajax({
                        type: "POST",
                        url: "<?=base_url()?>delete-class-teacher",
                        data: 'clsec_id='+id,
                        success: function(result) {
                            var responsedata = $.parseJSON(result);
                            if (responsedata.status=='success') {
                                $('#classtrrow'+id).remove();
                                toastr.success(responsedata.message)
                            }else{
                                toastr.error(responsedata.message)
                            }
                        },
                        error: function(result) {
                            toastr.error("Somthing went wrong :(")
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