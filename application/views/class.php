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
        <?php if($class_list){ ?>
        <div class="site-content">
          <div class="panel panel-default panel-table">
            <div class="panel-heading">
              <div class="panel-tools">
              <?php if($add_class){ ?>
                <button type="button" class="btn btn-outline-success btn-pill"  data-toggle="modal" data-target="#otherModal3" title="Add Class"  onclick="addClass();"><i class="zmdi zmdi-plus"></i></button>
              <?php } ?>
              </div>
              <h3 class="m-t-0 m-b-5">Circle Management</h3>
            </div>
            <div class="panel-body"> 
              <div class="table-responsive m-y-5"> 
                <table class="table table-hover" >
                    <thead>
                        <tr>
                            <th>Circle Name</th> 
                            <th>Circle in Numeric (If available)</th> 
                            <?php if($edit_class || $delete_class){ ?>
                            <th style="text-align:right;">Options</th>  
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody id="tbody_data">  
                        <?php foreach ($all_classes as $row) { ?>
                        <tr id="classrow<?=$row->class_id?>">
                            <td><?=$row->class_name?></td>  
                            <td><?=$row->class_numeric?></td>  
                            <?php if($edit_class || $delete_class){ ?>
                            <td align="right">
                                <?php if($edit_class){ ?>
                                <button type="button" class="btn btn-outline-primary btn-pill m-r-5" onclick="editClass('<?=$row->class_id?>');"><i class="zmdi zmdi-edit"></i></button>
                                <?php } if($delete_class){?>
                                <button type="button" class="btn btn-outline-danger btn-pill m-r-5" onclick="deleteClass('<?=$row->class_id?>');"><i class="zmdi zmdi-delete"></i></button>
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
                            <h4 class="modal-title" id="modal-title">Class</h4>
                        </div>

                        <form data-toggle="validator" id="inputmasks">
                            <div class="modal-body">
                                <input type="hidden" name="class_id" id="class_id" value="0"> 
                                <div class="form-group">
                                    <label for="form-control-2" class="control-label">Class Name</label>
                                    <input type="text" class="form-control" id="class_name" name="class_name" placeholder="Class Name" data-required-error="Class Name is Required" required autocomplete="off">
                                    <div class="help-block with-errors"></div>
                                </div>
                                <div class="form-group">
                                    <label for="form-control-2" class="control-label">Class Name in Numeric</label>
                                    <input type="number" pattern="^[0-9-.]+$" class="form-control" id="class_numeric" name="class_numeric" placeholder="Class Name in Numeric" data-pattern-error="Invalid Class Number" autocomplete="off">
                                    <div class="help-block with-errors"></div>
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
    
        function addClass() {
            $('#modal-title').text('Add Class');
            $('#class_id').val(0);
            $('#class_name').val('');
            $('#class_numeric').val('');
        }

        $('#inputmasks').validator().on('submit', function (e) {
            if (!(e.isDefaultPrevented())) {
                e.preventDefault();
                run_waitMe('#inputmasks');
                $.ajax({
                    type: "POST",
                    url: "<?=base_url()?>saveClass",
                    data: $('#inputmasks').serialize(),
                    success: function(result) {
                        var responsedata = $.parseJSON(result);
                        if(responsedata.status=='success'){
                            if (responsedata.message =='save') {
                                toastr.success('Class added successfully.')
                                setTimeout(function(){
                                    location.reload();
                                }, 1000); 
                            }else{
                                toastr.success('Class updated successfully.')   
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

        function editClass(id) { 
            $('#modal-title').text('Update Class');
            var class_name = $('#classrow'+id).find('td:eq(0)').text();  
            var class_numeric = $('#classrow'+id).find('td:eq(1)').text(); 

            $('#class_id').val(id);
            $('#class_name').val(class_name);
            $('#class_numeric').val(class_numeric);

            $('#otherModal3').modal('show');
        }

        function deleteClass(id) {
            toastr.warning("<button type='button' id='confirmBtn' class='btn btn-danger btn-sm' style='width:40%;display:inline;margin:3px;'>Yes</button><button type='button' id='closeBtn' class='btn btn-default btn-sm' style='width:40%;display:inline;margin:3px;'>No</button>",'Do you want to delete this Class?',{
                closeButton: true,
                allowHtml: true,
                onShown: function (toast) {
                    $("#confirmBtn").click(function(){
                    $.ajax({
                        type: "POST",
                        url: "<?=base_url()?>delete-class",
                        data: 'class_id='+id,
                        success: function(result) {
                        var responsedata = $.parseJSON(result);
                        if (responsedata.status=='success') {
                            $('#classrow'+id).remove(); 
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