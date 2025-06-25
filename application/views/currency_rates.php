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
              <?php if($addRate){?>
              <div class="panel-tools">
                <button type="button" class="btn btn-outline-primary m-w-120" data-toggle="modal" data-target="#otherModal3" onclick="addRates();">Add Rate</button>
              </div>
              <?php }?>
              <h3 class="m-t-0 m-b-5">Currency Rates</h3>
            </div>
            <div class="panel-body">
              <div class="table-responsive m-y-5">
                <table class="table table-hover" id="table-1">
                  <thead>
                    <tr>
                      <th>Country</th>
                      <th>ISO</th>
                      <th>Currency</th>
                      <th>Code</th>
                      <th>Symbol</th>
                      <th>Rate</th>
                      <th>Status</th>
                      <th>Default</th>
                      <?php if($editRate){?>
                      <th>Edit</th>
                      <?php } if($deleteRate){ ?>
                      <th>Delete</th>
                      <?php } ?>
                    </tr>
                  </thead>
                  <tbody id="tbody_data">
                    <?php foreach ($curRates as $row) {
                      $status = '';
                      $type = '';
                      if ($row->status==0) {
                        $status = 'checked="checked"';
                      }
                      if ($row->type==1) {
                        $type = 'checked="checked"';
                      }
                    ?>

                    <tr id="curRow<?=$row->cc_id?>">
                      <td country-id="<?=$row->country_id;?>"><?=$row->nicename;?></td>
                      <td><?=$row->iso;?></td>
                      <td currency-id="<?=$row->currency_id;?>"><?=$row->currency;?></td>
                      <td><?=$row->code;?></td>
                      <td><?=$row->symbol;?></td>
                      <td><?=$row->rate;?></td>
                      <td status="<?=$row->status;?>"><input type="checkbox" class="js-switch" data-size="small" data-color="#34a853" <?=$status;?> <?php if ($rateStatus) {echo 'onchange="updateCurStatus('.$row->cc_id.');"';}else{echo "disabled";}?> ></td>
                      <td type="<?=$row->type;?>"><input type="checkbox" class="js-switch" data-size="small" data-color="#34a853" <?=$type;?> <?php if ($rateType) {echo 'onchange="updateRateType('.$row->cc_id.');"';}else{echo "disabled";}?> ></td>
                      <?php if($editRate){?>
                      <td><button type="button" class="btn btn-outline-primary" onclick="editRates(<?=$row->cc_id;?>);">Edit</button></td>
                      <?php } if($deleteRate){ ?>
                      <td><button type="button" class="btn btn-outline-danger" onclick="deleteRates(<?=$row->cc_id;?>);">Remove</button></td>
                      <?php } ?>
                    </tr>
                    <?php } ?>

                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <?php $this->load->view('includes/footer'); ?>

      <div id="otherModal3" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
          <div class="modal-content">
            <div class="modal-header bg-primary">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">
                  <i class="zmdi zmdi-close"></i>
                </span>
              </button>
              <h4 class="modal-title" id="modal-title">Add Rate</h4>
            </div>
              <form data-toggle="validator" id="inputmasks">
                <div class="modal-body">
                  <input type="hidden" name="rate_id" id="rate_id" value="">
                  <div class="form-group">
                    <label for="form-control-3" class="control-label">Country</label>
                    <select class="form-control" data-plugin="select2" name="country" id="country" data-placeholder="Select a Country" data-required-error="Country is Required" style="width: 100%" required>
                      <option></option>
                      <?php foreach ($countries as $row) {?>
                        <option value="<?=$row->country_id?>"><?=$row->nicename?></option>
                      <?php } ?>
                    </select>
                    <div class="help-block with-errors"></div>
                  </div>
                  <div class="form-group">
                    <label for="form-control-3" class="control-label">Currency</label>
                    <select class="form-control" data-plugin="select2" name="currency" id="currency" data-placeholder="Select a Currency" data-required-error="Currency is Required" style="width: 100%" required>
                      <option></option>
                      <?php foreach ($currencies as $row) {?>
                        <option value="<?=$row->currency_id?>"><?=$row->currency.' - '.$row->code?></option>
                      <?php } ?>
                    </select>
                    <div class="help-block with-errors"></div>
                  </div>
                  <div class="form-group">
                    <label for="form-control-2" class="control-label">Rate</label>
                    <input type="text" class="form-control" id="rate" name="rate" data-inputmask="'alias': 'decimal', 'groupSeparator': ',', 'autoGroup': true, 'rightAlign': false, 'allowMinus': false, 'allowPlus': false" placeholder="Rate" data-required-error="Rate is Required" required>
                    <div class="help-block with-errors"></div>
                  </div>
                  <?php if($rateStatus){?>
                  <div class="form-group">                  
                    <label for="form-control-2" class="control-label">Status</label>
                    <div class="btn-group" data-toggle="buttons">
                      <label class="btn btn-outline-primary statusRadio">
                        <input type="radio" name="rate_status" id="status_on" autocomplete="off" value="0" required> On
                      </label>
                      <label class="btn btn-outline-primary statusRadio">
                        <input type="radio" name="rate_status" id="status_off" autocomplete="off" value="1" required> Off
                      </label>
                    </div>
                  </div>
                  <?php } ?>
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
    <?php $this->load->view('includes/javascripts'); ?>
    <script src="<?=base_url()?>assets/js/forms-form-masks.js"></script>
    <script src="<?=base_url()?>assets/js/forms-plugins.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type="text/javascript">

      $("#country,#currency").select2();

      $('#table-1').DataTable();

      $('#inputmasks').validator().on('submit', function (e) {
        if (!(e.isDefaultPrevented())) {
          e.preventDefault();
          run_waitMe('#inputmasks');
          $.ajax({
            type: "POST",
            url: "<?=base_url()?>saveCurRate",
            data: $('#inputmasks').serialize(),
            success: function(result) {
              var responsedata = $.parseJSON(result);
              if(responsedata.status=='success'){
                toastr.success(responsedata.message)
                setTimeout(function(){
                  location.reload();
                }, 500);
              }else if(responsedata.status=='error'){
                toastr.error(responsedata.message)
              }else{
                toastr.error("Somthing went wrong :(")
              }
              $("#otherModal3").modal('hide');
              $('#inputmasks').waitMe('hide');
            },
            error: function(result) {
              $('#inputmasks').waitMe('hide');
              toastr.error('Error :'+result)
            }
          });
        }
      });

      function addRates() {
        $('#modal-title').text('Add Rate');
        $('#rate').val("");
        $("#rate_id").val(0);
        $('#country').val('').trigger('change');
        $('#currency').val('').trigger('change');
        $('.statusRadio').removeClass('active');
        $("#status_on,#status_off").prop("checked", false);
      }

      function editRates(id) {
        $('#modal-title').text('Update Rate');
        $("#rate_id").val(id);
        var country = $("#curRow"+id).find("td:nth-child(1)").attr('country-id');
        var currency = $("#curRow"+id).find("td:nth-child(3)").attr('currency-id');
        var rate = $("#curRow"+id).find("td:nth-child(6)").text();
        var status = $("#curRow"+id).find("td:nth-child(7)").attr('status');
        $('#country').val(country).trigger('change');
        $('#currency').val(currency).trigger('change');
        $('#rate').val(rate);
        if (status==0) {
          $("#status_on").prop("checked", true);
          $("#status_on").parent('.statusRadio').addClass('active');
        }else{
          $("#status_off").prop("checked", true);
          $("#status_off").parent('.statusRadio').addClass('active');
        }
        $("#otherModal3").modal('show');
      }

      function deleteRates(id) {
        toastr.warning("<button type='button' id='confirmBtn' class='btn btn-danger btn-sm' style='width:40%;display:inline;margin:3px;'>Yes</button><button type='button' id='closeBtn' class='btn btn-default btn-sm' style='width:40%;display:inline;margin:3px;'>No</button>",'Do you want to delete this rate?',{
            closeButton: true,
            allowHtml: true,
            onShown: function (toast) {
              $("#confirmBtn").click(function(){
                $.ajax({
                  type: "POST",
                  url: "<?=base_url()?>deleteCurRate",
                  data: 'rate_id='+id,
                  success: function(result) {
                    var responsedata = $.parseJSON(result);
                    if (responsedata.status=='success') {
                      var table = $('#table-1').DataTable();
                      table.row('#curRow'+id).remove().draw( false );
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

      function updateCurStatus(id) {
        $.ajax({
          type: "POST",
          url: "<?=base_url()?>updateCurStatus",
          data: 'rate_id='+id,
          success: function(result) {
            var responsedata = $.parseJSON(result);
            if (responsedata.status=='success') {
              toastr.success(responsedata.message)
            }else{
              toastr.error(responsedata.message)
            }
          },
          error: function(result) {
            toastr.error("Somthing went wrong :(")
          }
        });
      }

      function updateRateType(id) {
        $.ajax({
          type: "POST",
          url: "<?=base_url()?>updateRateType",
          data: 'rate_id='+id,
          success: function(result) {
            var responsedata = $.parseJSON(result);
            setTimeout(function(){
              location.reload();
            }, 250);
            if (responsedata.status=='success') {
              toastr.success(responsedata.message)
            }else{
              toastr.error(responsedata.message)
            }
          },
          error: function(result) {
            toastr.error("Somthing went wrong :(")
          }
        });
      }
    </script>
  </body>
</html>