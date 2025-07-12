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
        <?php if($subject_list){ ?>
        <div class="site-content">
          <div class="panel panel-default panel-table">
            <div class="panel-heading">
              <div class="panel-tools"> 
                <?php if($add_subject){ ?>
                <button type="button" class="btn btn-outline-success btn-pill"  data-toggle="modal" data-target="#otherModal3" title="Add Subject"  onclick="adSubject();"><i class="zmdi zmdi-plus"></i></button>
                <?php } ?>
              </div>
              <h3 class="m-t-0 m-b-5">Circle Management</h3>
            </div>
            <div class="panel-body"> 
              <div class="table-responsive m-y-5"> 
                <table class="table table-hover" id="table-1">
                    <thead>
                        <tr>
                            <th>Circle Name</th> 
                            <th>Circle Code</th> 
                            <th>Circle Type</th> 
                            <?php if($edit_subject || $delete_subject){ ?>
                            <th style="text-align:right;">Options</th>  
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody id="tbody_data">  
                        <?php 
                            foreach ($all_subjects as $row) { 
                                $sub_type = '';
                                $cls = '';
                                if ($row->subject_type==0) {
                                    $sub_type = 'Theroy';
                                    $cls = 'primary';
                                }elseif ($row->subject_type==1) {
                                    $sub_type = 'Practical';
                                    $cls = 'info';
                                }else {
                                    $sub_type = 'Both';
                                    $cls = 'success';
                                }
                        ?>
                        <tr id="sbjrow<?=$row->sub_id?>">
                            <td><?=$row->subject_name?></td>  
                            <td><?=$row->subject_code?></td>
                            <td sbj-type="<?=$row->subject_type?>"><span class="label label-outline-<?=$cls?>"><?=$sub_type?></span></td>  
                            <?php if($edit_subject || $delete_subject){ ?>
                            <td align="right">
                                <?php if($edit_subject){ ?>
                                <button type="button" class="btn btn-outline-primary btn-pill m-r-5" onclick="editSubject('<?=$row->sub_id?>');"><i class="zmdi zmdi-edit"></i></button>
                                <?php } if($delete_subject){ ?>
                                <button type="button" class="btn btn-outline-danger btn-pill m-r-5" onclick="deleteSubject('<?=$row->sub_id?>');"><i class="zmdi zmdi-delete"></i></button>
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
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">
                                <i class="zmdi zmdi-close"></i>
                            </span>
                            </button>
                            <h4 class="modal-title" id="modal-title">Subject</h4>
                        </div>

                        <form data-toggle="validator" id="inputmasks">
                            <div class="modal-body">
                                <input type="hidden" name="subject_id" id="subject_id" value="0"> 
                                <div class="form-group">
                                    <label for="form-control-2" class="control-label">Circle Name</label>
                                    <input type="text" class="form-control" id="subject_name" name="subject_name" placeholder="Circle Name" data-required-error="Subject Name is Required" required autocomplete="off">
                                    <div class="help-block with-errors"></div>
                                </div>
                                <div class="form-group">
                                    <label for="form-control-2" class="control-label">Circle Code</label>
                                    <input type="text" class="form-control" id="subject_code" name="subject_code" placeholder="Circle Code" autocomplete="off">
                                    <div class="help-block with-errors"></div>
                                </div>
                                <div class="form-group">
                                    <label for="subject_type" class="control-label">Circle Type</label>
                                    <div class="btn-group" data-toggle="buttons">
                                        <label class="btn btn-outline-primary subjTypeRadio">
                                            <input type="radio" name="subject_type" id="theory" autocomplete="off" value="0" checked="checked"> Theory
                                        </label>
                                        <label class="btn btn-outline-info subjTypeRadio">
                                            <input type="radio" name="subject_type" id="practical" autocomplete="off" value="1"> Practical
                                        </label>
                                        <label class="btn btn-outline-success subjTypeRadio">
                                            <input type="radio" name="subject_type" id="both" autocomplete="off" value="2"> Both
                                        </label>
                                    </div>
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
    <script type="text/javascript">
        $('#table-1').DataTable();

        $('.table-responsive').on('show.bs.dropdown', function () {
            $('.table-responsive').css( "overflow", "inherit" );
        });

        $('.table-responsive').on('hide.bs.dropdown', function () {
            $('.table-responsive').css( "overflow", "auto" );
        });
    
        function adSubject() {
            $('#modal-title').text('Add Circle');
            $('#subject_id').val(0);
            $('#subject_name').val('');
            $('#subject_code').val(''); 
            $('.subjTypeRadio').removeClass('active');
            $("#theory,#practical,#both").prop("checked", false);
        }

        $('#inputmasks').validator().on('submit', function (e) {
            if (!(e.isDefaultPrevented())) {
                e.preventDefault();
                run_waitMe('#inputmasks');
                $.ajax({
                    type: "POST",
                    url: "<?=base_url()?>saveSubject",
                    data: $('#inputmasks').serialize(),
                    success: function(result) {
                        var responsedata = $.parseJSON(result);
                        if(responsedata.status=='success'){
                            if (responsedata.message =='save') {
                                toastr.success('Subject added successfully.')
                                setTimeout(function(){
                                    location.reload();
                                }, 1000); 
                            }else{
                                toastr.success('Subject updated successfully.')   
                                setTimeout(function(){
                                    location.reload();
                                }, 1000); 
                            } 
                        }else{
                            toastr.error("Somthing went wrong :(")
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

        function editSubject(id) {  
            $('#modal-title').text('Update Circle');
            var subject_name = $('#sbjrow'+id).find('td:eq(0)').text();  
            var subject_code = $('#sbjrow'+id).find('td:eq(1)').text();  
            var sub_type = $('#sbjrow'+id).find('td:eq(2)').attr('sbj-type');  

            $('#subject_id').val(id);
            $('#subject_name').val(subject_name);
            $('#subject_code').val(subject_code); 
            
            $('.subjTypeRadio').removeClass('active');
            $("#theory,#practical,#both").prop("checked", false);

            if (sub_type==0) {
                $("#theory").prop("checked", true);
                $("#theory").parent('.subjTypeRadio').addClass('active');
            }else if(sub_type==1){
                $("#practical").prop("checked", true);
                $("#practical").parent('.subjTypeRadio').addClass('active');
            }else{
                $("#both").prop("checked", true);
                $("#both").parent('.subjTypeRadio').addClass('active');
            }

            $('#otherModal3').modal('show');
        }

        function deleteSubject(id) {
            toastr.warning("<button type='button' id='confirmBtn' class='btn btn-danger btn-sm' style='width:40%;display:inline;margin:3px;'>Yes</button><button type='button' id='closeBtn' class='btn btn-default btn-sm' style='width:40%;display:inline;margin:3px;'>No</button>",'Do you want to delete this Subject?',{
                closeButton: true,
                allowHtml: true,
                onShown: function (toast) {
                    $("#confirmBtn").click(function(){
                    $.ajax({
                        type: "POST",
                        url: "<?=base_url()?>delete-subject",
                        data: 'sub_id='+id,
                        success: function(result) {
                        var responsedata = $.parseJSON(result);
                        if (responsedata.status=='success') {
                            var table = $('#table-1').DataTable();
                            table.row('#sbjrow'+id).remove().draw( false );
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