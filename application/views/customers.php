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
          <?php if($add_cust){?>
          <div class="panel-tools">
            <button type="button" class="btn btn-outline-primary m-w-120" onclick="location.href='<?=base_url();?>addCustomer'">Add Customer</button>
          </div>
          <?php }?>
          <h3 class="m-t-0 m-b-5">Manage Customers</h3>
        </div>
        <div class="panel-body">
          <div class="table-responsive m-y-5">
            <table class="table table-hover" id="table-1">
              <thead>
                <tr>
                  <th></th>
                  <th>Name</th>
                  <th>Mobile</th>
                  <th>Email</th>
                  <th>added_date</th>
                  <th>Status</th>
                  <?php if($edit_cust||$delete_cust){?><th></th><?php }?>
                </tr>
              </thead>
              <tbody id="tbody_data">
                <?php foreach ($customers as $row) { 
                  $img = 'default.jpg';
                  $status = '';
                  if ($row->photo_path!=null) {
                    $img = 'customers/'.$row->photo_path.'-sma.jpg';
                  }
                  if ($row->custStatus==0) {
                    $status = 'checked="checked"';
                  }
                ?>
                <tr id="custrow<?=$row->cust_id?>">
                  <td><img class="img-rounded" src="<?=base_url();?>photos/<?=$img?>" height="32"></td>
                  <td><?=$row->fname.' '.$row->lname;?></td>
                  <td><?=$row->mobile;?></td>
                  <td><?=$row->email;?></td>
                  <td><?=$row->added_date;?></td>
                  <td><input type="checkbox" class="js-switch" data-size="small" data-color="#34a853" <?=$status;?> <?php if ($changeStatus) {echo 'onchange="updateCustStatus('.$row->cust_id.');"';}else{echo "disabled";}?> ></td>
                  <?php if($edit_cust||$delete_cust){?>
                  <td><div class="btn-group">
                      <button type="button" class="btn btn-primary btn-pill btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="zmdi zmdi-more"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-right">
                        <?php if($edit_cust){?>
                        <li><a href="javascript:editCust(<?=$row->cust_id?>);">Edit</a></li>
                        <?php } if($delete_cust){?>
                        <li><a href="javascript:deleteCust(<?=$row->cust_id?>);">Delete</a></li>
                        <?php }?>
                      </ul>
                    </div></td><?php }?>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <!--offer mail model-->
      <div id="otherModal3" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
          <div class="modal-content">
            <div class="modal-header bg-primary">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">
                  <i class="zmdi zmdi-close"></i>
                </span>
              </button>
              <h4 class="modal-title" id="modal-title">Offer Mail</h4>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label for="form-control-2" class="control-label">Offer</label>
                <input type="text" class="form-control" id="offer" name="category" placeholder="Offer" data-required-error="Category is Required" required>
                <div class="help-block with-errors"></div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary" onclick="sendMail()">Send</button>
              <button type="button" data-dismiss="modal" class="btn btn-default">Close</button>
            </div>
          </div>
        </div>
      </div>
    </div>
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

    function updateCustStatus(id) {
      $.ajax({
        type: "POST",
        url: "<?=base_url()?>updateCustomerStatus",
        data: 'cust_id='+id,
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

    function deleteCust(id) {
      toastr.warning("<button type='button' id='confirmBtn' class='btn btn-danger btn-sm' style='width:40%;display:inline;margin:3px;'>Yes</button><button type='button' id='closeBtn' class='btn btn-default btn-sm' style='width:40%;display:inline;margin:3px;'>No</button>",'Do you want to delete this Customer?',{
          closeButton: true,
          allowHtml: true,
          onShown: function (toast) {
            $("#confirmBtn").click(function(){
              $.ajax({
                type: "POST",
                url: "<?=base_url()?>deleteCustomer",
                data: 'cust_id='+id,
                success: function(result) {
                    var responsedata = $.parseJSON(result);
                    if (responsedata.status=='success') {
                      var table = $('#table-1').DataTable();
                      table.row('#custrow'+id).remove().draw( false );
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

    function sendMail() {
      var offer = $("#offer").val();
      $.ajax({
        type: "POST",
        url: "<?=base_url()?>sendMail",
        data: 'msg='+offer,
        success: function(result) {
            var responsedata = $.parseJSON(result);
            if (responsedata.status=='success') {
              toastr.success(responsedata.message)
            }else{
              toastr.error(responsedata.message)
            }
            $("#otherModal3").modal('hide');
        },
        error: function(result) {
          toastr.error("Somthing went wrong :(")
          $("#otherModal3").modal('hide');
        }
      });
    }

    function editCust(id) {
      var form = document.createElement("form");
      form.setAttribute("method", "post");
      form.setAttribute("action", "<?=base_url()?>editCustomer");
      hiddenField = document.createElement("input");
      hiddenField.setAttribute("type", "hidden");
      hiddenField.setAttribute("name", "cust_id");
      hiddenField.setAttribute("value", id);
      form.appendChild(hiddenField);
      document.body.appendChild(form);
      form.submit();
    }

    function Mail(){
      $("#otherModal3").modal('show');
    }

    function getData(){
      $.ajax({
        type: "POST",
        url: "<?=base_url()?>/downloadData",
        data: "name='subscribe'",
        success: function(result){
          var responsedata = $.parseJSON(result);
          if (responsedata.status=='success') {
            toastr.success(responsedata.message)
          }else{
            toastr.error(responsedata.message)
          }
        },
        error: function(result){
          toastr.error("Somthing went wrong :(")
        }
      });
    }
  </script>
</body>
</html>