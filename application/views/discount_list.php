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
        <?php if($list){ ?>
        <div class="site-content">
          <div class="panel panel-default panel-table">
            <div class="panel-heading">
              <div class="panel-tools">
              <?php if($add){ ?>
                <button type="button" class="btn btn-outline-success btn-pill" title="Add"  onclick="addMe();"><i class="zmdi zmdi-plus"></i></button>
              <?php } ?>
              </div>
              <h3 class="m-t-0 m-b-5">Discount Management</h3>
            </div>
            <div class="panel-body"> 
              <div class="table-responsive m-y-5"> 
                <table class="table table-hover" >
                    <thead>
                        <tr>
                            <th>Discount Value</th> 
                            <?php if($edit || $delete){ ?>
                            <th style="text-align:right;">Options</th>  
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody id="tbody_data">  
                        <?php 
                            foreach ($discounts as $row) { 
                                $value = $row->discount_type == 1 ? number_format($row->discount_value + 0).'%' : $curr.''.number_format($row->discount_value + 0); 
                        ?>
                        <tr id="rowId<?=$row->id?>">
                            <td discount-type="<?=$row->discount_type?>" discount-value="<?=($row->discount_value + 0)?>"><?=$value?></td>  
                            <?php if($edit || $delete){ ?>
                            <td align="right">
                                <?php if($edit){ ?>
                                <button type="button" class="btn btn-outline-primary btn-pill m-r-5" onclick="editMe('<?=$row->id?>');"><i class="zmdi zmdi-edit"></i></button>
                                <?php } if($delete){?>
                                <button type="button" class="btn btn-outline-danger btn-pill m-r-5" onclick="deleteMe('<?=$row->id?>');"><i class="zmdi zmdi-delete"></i></button>
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
                            <h4 class="modal-title" id="modal-title">Discount</h4>
                        </div>

                        <form data-toggle="validator" id="inputmasks">
                            <div class="modal-body">
                                <input type="hidden" name="discount_id" id="discount_id" value="0"> 
                                <div class="form-group">
                                    <label for="discount-value" class="control-label">Discount Value</label>
                                    <input type="text" class="form-control" id="discount-value" name="discount_value" placeholder="Discount Value" data-required-error="Discount value is Required" required autocomplete="off" pattern="^[0-9]+$" data-pattern-error="Discount value only can be a number.">
                                    <div class="help-block with-errors"></div>
                                </div>
                                <div class="form-group">
                                    <label for="dsc_type" class="control-label">Discount Type</label>
                                    <div class="btn-group" data-toggle="buttons">
                                        <label class="btn btn-outline-success dsc_type">
                                            <input type="radio" name="dsc_type" id="percentage" autocomplete="off" value="1" requred> Percentage
                                        </label>
                                        <label class="btn btn-outline-danger dsc_type">
                                            <input type="radio" name="dsc_type" id="flat_amount" autocomplete="off" value="0" required> Flat Amount
                                        </label> 
                                    </div>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary" id="sub-btn">Submit</button>
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
    
        function addMe() {
            $('#modal-title').text('Add Discount');
            $('#discount_id').val(0);
            $('#discount_value').val('');
            $('.dsc_type').removeClass('active');
            $("#percentage,#flat_amount").prop("checked", false);
            $('#otherModal3').modal({backdrop: 'static', keyboard: false});
        }

        $('#inputmasks').validator().on('submit', function (e) {
            if (!(e.isDefaultPrevented())) {
                e.preventDefault();
                run_waitMe('#sub-btn');
                $.ajax({
                    type: "POST",
                    url: "<?=base_url()?>save-discount",
                    data: $('#inputmasks').serialize(),
                    success: function(result) {
                        const responsedata = $.parseJSON(result);
                        if(responsedata.status=='success'){
                            if (responsedata.message =='save') {
                                toastr.success('Discount rate added successfully.')
                                setTimeout(function(){
                                    location.reload();
                                }, 1000); 
                            }else{
                                toastr.success('Discount rate updated successfully.')   
                                setTimeout(function(){
                                    location.reload();
                                }, 1000); 
                            } 
                        }else{
                            toastr.error(responsedata.message)
                        }
                    },
                    error: function(result) {
                        $('#sub-btn').waitMe('hide');
                        toastr.error('Error :'+result)
                    }
                });
                $('#inputmasks').waitMe('hide');
            }
        });

        function editMe(id) { 
            $('#modal-title').text('Update Class');
            const value = $('#rowId'+id).find('td:eq(0)').attr('discount-value');
            const discountType = $('#rowId'+id).find('td:eq(0)').attr('discount-type');

            $('#discount_id').val(id);
            $('#discount-value').val(value);

            $('.dsc_type').removeClass('active');
            $("#percentage,#flat_amount").prop("checked", false);

            if (discountType == 1) {
                $("#percentage").parent('.dsc_type').addClass('active');
                $("#percentage").prop("checked", true);
            }else if(discountType == 0){
                $("#flat_amount").parent('.dsc_type').addClass('active');
                $("#flat_amount").prop("checked", true);
            }

            $('#otherModal3').modal({backdrop: 'static', keyboard: false});
        }

        function deleteMe(id) {
            toastr.warning("<button type='button' id='confirmBtn' class='btn btn-danger btn-sm' style='width:40%;display:inline;margin:3px;'>Yes</button><button type='button' id='closeBtn' class='btn btn-default btn-sm' style='width:40%;display:inline;margin:3px;'>No</button>",'Do you want to delete this discount value?',{
                closeButton: true,
                allowHtml: true,
                onShown: function (toast) {
                    $("#confirmBtn").click(function(){
                    $.ajax({
                        type: "POST",
                        url: "<?=base_url()?>delete-discount",
                        data: 'discount_id='+id,
                        success: function(result) {
                        const responsedata = $.parseJSON(result);
                        if (responsedata.status=='success') {
                            $('#rowId'+id).remove(); 
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