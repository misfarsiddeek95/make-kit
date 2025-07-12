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
        <?php if($assign_subject_list){ ?>
        <div class="site-content">
          <div class="panel panel-default panel-table">
            <div class="panel-heading">
              <div class="panel-tools"> 
                <?php if($assign_subject){ ?>
                <button type="button" class="btn btn-outline-success btn-pill"  data-toggle="modal" data-target="#otherModal3" title="Assign Institute Circle"  onclick="assignSubjects();"><i class="zmdi zmdi-plus"></i></button>
                <?php } ?>
              </div>
              <h3 class="m-t-0 m-b-5">Institute Circle Assigning Management</h3>
            </div>
            <div class="panel-body"> 
              <div class="table-responsive m-y-5"> 
                <table class="table table-hover" id="table-1">
                    <thead>
                        <tr>
                            <th>Institute</th>   
                            <th>Circle</th>  
                            <?php if($edit_assigned_subject || $delete_assigned_subject){ ?>
                            <th style="text-align:right;">Options</th>  
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody id="tbody_data">  
                        <?php foreach ($class_subjects as $row) { ?> 
                        <tr id="subjassgnrow<?=$row->class_id?>">
                            <td><?=$row->class_name?></td>  
                            <td><?php foreach($row->subjects as $subs){ ?><span class="label label-info subjectIds<?=$row->class_id?>" sub-id="<?=$subs->sub_id?>" style="margin-right: 3px;"><?=$subs->subject_name?></span><?php } ?></td>  
                            <?php if($edit_assigned_subject || $delete_assigned_subject){ ?>
                            <td align="right">
                                <?php if($edit_assigned_subject){ ?>
                                <button type="button" class="btn btn-outline-primary btn-pill m-r-5" onclick="editAssignedSubject('<?=$row->class_id?>');"><i class="zmdi zmdi-edit"></i></button>
                                <?php } if($delete_assigned_subject){?>
                                <button type="button" class="btn btn-outline-danger btn-pill m-r-5" onclick="deleteMe('<?=$row->class_id?>');"><i class="zmdi zmdi-delete"></i></button>
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
            <div class="modal-dialog">
                <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">
                                <i class="zmdi zmdi-close"></i>
                            </span>
                            </button>
                            <h4 class="modal-title" id="modal-title">Assign Circle</h4>
                        </div>

                        <form data-toggle="validator" id="inputmasks">
                            <div class="modal-body">
                                <input type="hidden" name="clsub_id" id="clsub_id" value="0">   
                                <div class="form-group"> 
                                    <label for="class_id" class="control-label">Institute</label>
                                    <select id="class_id" name="class_id" class="form-control" data-plugin="select2" style="width: 100%;" required data-required-error="Please select an institute." >
                                        <?php foreach ($all_classes as $row ) { ?>
                                        <option value="<?=$row->class_id?>"><?=$row->class_name?></option> 
                                        <?php } ?>
                                    </select>
                                    <div class="help-block with-errors"></div> 
                                </div>
                                <div class="row">
                                    <?php foreach ($all_subjects as $row ) { ?>
                                    <div class="col-sm-6 col-md-6">
                                        <div class="form-group subjectChecksBox" subject-id="<?=$row->sub_id?>"> 
                                            <label class="custom-control custom-control-primary custom-checkbox subjectChecks<?=$row->sub_id?>">
                                                <input class="custom-control-input subjectChecked" type="checkbox" id="subjectChecked<?=$row->sub_id?>" name="subject[]" value="<?=$row->sub_id?>">
                                                <span class="custom-control-indicator"></span>
                                                <span class="custom-control-label"><?=$row->subject_name?></span>
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
        
        function assignSubjects() {
            $('#modal-title').text('Assign Circle');
            $('#clsub_id').val(0); 
            $('#class_id').val(null);
            $('#class_id').select2({placeholder: "Select a Class",dropdownParent: $('#otherModal3')}); 
            $('.subjectChecksBox').each(function (index, value){
                var sub_id = $(this).attr('subject-id');
                $('.subjectChecks'+sub_id).removeClass('active');
                $("#subjectChecked"+sub_id).prop("checked", false);
            });
        } 

        $('#inputmasks').validator().on('submit', function (e) {
            const selectedClass = $('#class_id').find(":selected").val();
            if (!(e.isDefaultPrevented())) {
                e.preventDefault();
                if (selectedClass == undefined || selectedClass == '' || selectedClass == null) {
                    return toastr.error('Please select the class');
                }
                run_waitMe('#inputmasks');
                $.ajax({
                    type: "POST",
                    url: "<?=base_url()?>save-class-subjects",
                    data: $('#inputmasks').serialize(),
                    success: function(result) {
                        var responsedata = $.parseJSON(result);
                        if(responsedata.status=='success'){
                            if (responsedata.message =='save') {
                                toastr.success('Subjects successfully assigned to the class.')
                                setTimeout(function(){
                                    location.reload();
                                }, 1000); 
                            }else{
                                toastr.success('Subjects successfully updated to the class.')   
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

        function editAssignedSubject(clid) {  
            $('.custom-checkbox').removeClass('active');
            $(".subjectChecked").prop("checked", false);
            $('#modal-title').text('Update Assigned Circle'); 
            $('#clsub_id').val(1);
            $('#class_id').val(clid).trigger('change');  
            $('.subjectIds'+clid).each(function (index, value){ 
                var sub_id = $(this).attr('sub-id'); 
                $('.subjectChecks'+sub_id).addClass('active');
                $("#subjectChecked"+sub_id).prop('checked', true);
            });
            $('#class_id').select2({dropdownParent: $('#otherModal3')});
            $('#otherModal3').modal('show');
        }

        function deleteMe(id) {
            toastr.warning("<button type='button' id='confirmBtn' class='btn btn-danger btn-sm' style='width:40%;display:inline;margin:3px;'>Yes</button><button type='button' id='closeBtn' class='btn btn-default btn-sm' style='width:40%;display:inline;margin:3px;'>No</button>",'Do you want to delete this assigned institute circle?',{
                closeButton: true,
                allowHtml: true,
                onShown: function (toast) {
                    $("#confirmBtn").click(function(){
                    $.ajax({
                        type: "POST",
                        url: "<?=base_url()?>delete-class-subjects",
                        data: 'class_id='+id,
                        success: function(result) {
                        var responsedata = $.parseJSON(result);
                        if (responsedata.status=='success') {
                            var table = $('#table-1').DataTable();
                            table.row('#subjassgnrow'+id).remove().draw( false );
                            toastr.success(responsedata.message)
                        }else{ 
                            toastr.error(responsedata.message);
                        }
                        },
                        error: function(result) {
                            toastr.error("Somthing went wrong :(");
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