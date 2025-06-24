<!DOCTYPE html>
<html lang="en">
  <head>
    <?php $this->load->view('includes/head'); ?>
    <style type="text/css">
      #tbody_data{
        text-align: center;
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
            <!-- <?php if($add_order){?>
              <div class="panel-tools">
                <button type="button" class="btn btn-outline-primary m-w-120">Add Order</button>
              </div>
            <?php }?> -->
            <h3 class="m-t-0 m-b-5">Orders</h3>
          </div>
          <div class="panel-body">
            <div class="page-layouts">
              <div class="row">
                <div id="controllers">
                    <div class="col-lg-3 col-sm-3 col-xs-12 m-y-5">
                        <div class="input-group">
                            <input class="form-control" type="text" placeholder="Search for..." style="border-color: #1d87e4;" id="searchField">
                            <span class="input-group-btn">
                              <button class="btn btn-outline-primary" type="button" onclick="getOrdersByStatus();">Search</button>
                              <button class="btn btn-outline-primary" type="button" onclick="reset_fun_search();">Reset</button>
                            </span>
                        </div>
                    </div>

                    <div class="col-lg-2 col-sm-3 col-xs-12 m-y-5">
                      <select id="order_status" class="custom-select" style="border-color: #1d87e4;" onchange="getOrdersByStatus();">
                        <option value="" selected="selected">-- Filter By Order Status --</option>
                        <?php foreach ($order_statuses as $row) {?>
                        <option value="<?=$row->os_id?>"><?=$row->status?></option>
                        <?php } ?>
                      </select>
                    </div>

                    <div class="col-lg-2 col-sm-3 col-xs-12 m-y-5">
                      <select id="payment_status" class="custom-select" style="border-color: #1d87e4;" <?php if ($all_orders) { echo 'onchange="getOrdersByStatus();"';}else{echo "disabled";}?>>
                        <option value="" selected="selected">-- Filter By Payment Status --</option>
                        <?php foreach ($payment_status as $key => $value) {?>
                        <option value="<?=$key?>"><?=$value?></option>
                        <?php } ?>
                      </select>
                    </div>

                    <div class="col-lg-4 col-sm-6 col-xs-12 m-y-5">
                        <div class="input-group date">
                          <input type="text" class="form-control" style="width:50%;border-color: #1d87e4;" id="filterByFromDate" placeholder="Select From Date">
                          <input type="text" class="form-control" style="width:50%;border-color: #1d87e4;" id="filterByToDate" placeholder="Select To Date">
                          <span class="input-group-btn">
                            <button class="btn btn-outline-primary" type="button" onclick="getOrdersByStatus();">Filter</button>
                            <button class="btn btn-outline-primary" type="button" onclick="reset_fun_dates();">Reset</button>
                          </span>
                        </div>
                    </div>

                    <div class="col-lg-1 col-sm-3 col-xs-12 m-y-5">
                        <select id="limit_sel" class="custom-select" onchange="getOrdersByStatus();" style="border-color: #1d87e4;">
                            <option value="10" selected="selected">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
              </div>

            </div>
            <div class="table-responsive">
              <table class="table table-hover m-b-10" id="orderTbl">
                <thead>
                  <tr>
                    <th></th>
                    <th>Code</th>
                    <th>Cart Total</th>
                    <th>Del. Charge</th>
                    <th>Discount</th>
                    <th>Order Total</th>
                    <th>Paid Total</th>
                    <th>Balance</th>
                    <th>Pay. Method</th>
                    <th>Pay. Status</th>
                    <th>Order Status</th>
                    <th>Site</th>
                    <th>Date</th>
                    <?php if($view_order){?>
                    <th></th>
                    <?php } if($order_status||$pay_status){ ?>
                    <th></th>
                    <?php } if($delete_order){ ?>
                    <th></th>
                    <?php } ?>
                  </tr>
                </thead>
                <tbody id="tbody_data">
                  
                </tbody>
              </table>
            </div>
          </div>

          <nav>
            <ul class="pagination pagination-rounded m-l-10" id="pagination_ul">
            </ul>
          </nav>
        </div>
        <input type="hidden" id="offset_field" value="0">
        
      </div>

      <?php $this->load->view('includes/footer'); ?>

      <?php if($order_status||$pay_status){ ?>
      <div id="status_modal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm">
          <div class="modal-content">
            <div class="modal-header bg-primary">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">
                  <i class="zmdi zmdi-close"></i>
                </span>
              </button>
              <h4 class="modal-title" id="modal_assign_title">Update Status</h4>
            </div>

            <form data-toggle="validator" id="statusUpdateMask">

              <div class="modal-body">
                <input type="hidden" name="sorder_id" id="sorder_id" value="0">
                <?php if($pay_status){ ?>
                <div class="form-group">
                  <label for="form-control-2" class="control-label">Payment Status</label>
                  <select id="spayment_status" name="spayment_status" class="custom-select" required="required">
                    <option selected="selected" disabled="disabled">-- Select Payment Status --</option>
                    <?php foreach ($payment_status as $key => $value) {?>
                    <option value="<?=$key?>"><?=$value?></option>
                    <?php } ?>
                  </select>
                  <div class="help-block with-errors"></div>
                </div>
                <?php } if($order_status){ ?>
                <div class="form-group">
                  <label for="form-control-2" class="control-label">Order Status</label>
                  <select id="sorder_status" name="sorder_status" class="custom-select" required="required">
                    <option selected="selected" disabled="disabled">-- Select Order Status --</option>
                    <?php foreach ($order_statuses as $row) {?>
                    <option value="<?=$row->os_id?>"><?=$row->status?></option>
                    <?php } ?>
                  </select>
                  <div class="help-block with-errors"></div>
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
      <?php } ?>

    </div>
    <?php $this->load->view('includes/javascripts'); ?>
    <script src="<?=base_url()?>assets/js/bootstrap-datepicker.js"></script>
    <script src="<?=base_url()?>assets/js/forms-plugins.js"></script>
    
    <script type="text/javascript">
      $( document ).ready(function() {
        $('#filterByFromDate,#filterByToDate').datepicker({
          format: 'yyyy-mm-dd'
        });
        getOrders();
      });


      function getOrders() {
        var limits = parseInt($('#limit_sel').val());
        var offset = parseInt($('#offset_field').val());
        $.ajax({
          type: "POST",
          url: "<?=base_url()?>getOrders",
          data: 'search='+$('#searchField').val()+'&ostatus='+$('#order_status').val()+'&pstatus='+$('#payment_status').val()+'&fdate='+$('#filterByFromDate').val()+'&tdate='+$('#filterByToDate').val()+'&limit='+limits+'&offset='+offset,
          success: function(result) {
              var responsedata = $.parseJSON(result);
              $('#tbody_data,#pagination_ul').empty();
              var tbody = '';
              if (responsedata.rowcount==0) {

                $('#tbody_data').append('<tr><td colspan="16" class="text-center">No Results</td></tr>');
              }else{
                var myArray = {'2':'Success' , '0':'Pending' , '-1':'Canceled' , '-2':'Failed' , '-3':'Chargedback'};
                for (var i = 0; i < responsedata.orders.length; i++) {
                  var paymethod = 'Total - Online';
                  var payment_status = responsedata.orders[i]['payment_status'];
                  var order_status = responsedata.orders[i]['os_id'];
                  var icon = 'circle';
                  var circle = 'danger';
                  if (responsedata.orders[i]['payment_method']==2) {
                    paymethod = 'COD - Online';
                  }else if(responsedata.orders[i]['payment_method']==3){
                    paymethod = 'Total - Bank';
                  }else if(responsedata.orders[i]['payment_method']==4){
                    paymethod = 'COD - Bank';
                  }
                  var cart_total = parseFloat(responsedata.orders[i]['cart_total']);
                  var del_charge = parseFloat(responsedata.orders[i]['del_charge']);
                  var discount = parseFloat(responsedata.orders[i]['discount']);
                  var paid_total = parseFloat(responsedata.orders[i]['paid_total']);
                  var order_total = (cart_total+del_charge)-discount;
                  var balance = order_total-paid_total;

                  if (order_status==1) {icon = 'help';
                  }else if(order_status==2){icon = 'close-circle';
                  }else if(order_status==3){icon = 'shopping-basket';
                  }else if(order_status==4){icon = 'case-check';
                  }else if(order_status==5){icon = 'truck';
                  }else if(order_status==6){icon = 'grid-off';
                  }else if(order_status==7){icon = 'home';
                  }else if(order_status==8){icon = 'long-arrow-return';}

                  if (payment_status==2) {circle = 'success';}else if(payment_status==0){circle = 'warning';}
                  tbody +='<tr><td><i class="zmdi zmdi-'+icon+' zmdi-hc-lg text-'+circle+'"></i></td>'+
                          '<td>'+responsedata.orders[i]['order_code']+'</td>'+
                          '<td>'+Math.round(cart_total*100)/100+'</td>'+
                          '<td>'+Math.round(del_charge*100)/100+'</td>'+
                          '<td>'+Math.round(discount*100)/100+'</td>'+
                          '<td>'+Math.round(order_total*100)/100+'</td>'+
                          '<td>'+Math.round(paid_total*100)/100+'</td>'+
                          '<td>'+Math.round(balance*100)/100+'</td>'+
                          '<td>'+paymethod+'</td>'+
                          '<td>'+myArray[payment_status]+'</td>'+
                          '<td>'+responsedata.orders[i]['status']+'</td>'+
                          '<td>'+responsedata.orders[i]['ws_name']+'</td>'+
                          '<td>'+responsedata.orders[i]['order_date']+'</td>'+
                          <?php if($view_order){?>
                          '<td style="padding:0;margin:0;"><button type="button" class="btn btn-primary btn-sm" title="View Order" onclick="viewProduct('+responsedata.orders[i]['order_id']+');"><i class="zmdi zmdi-eye"></i></button></td>'+
                          <?php } if($order_status||$pay_status){?>
                          '<td style="padding:0;margin:0;"><button type="button" class="btn btn-primary btn-sm" title="Edit Order Status" onclick="editStatus('+responsedata.orders[i]['order_id']+');"><i class="zmdi zmdi-edit"></i></button></td>'+
                          <?php } if($delete_order){?>
                          '<td style="padding:0;margin:0;"><button type="button" class="btn btn-danger btn-sm" title="Delete Order" onclick="deleteOrder('+responsedata.orders[i]['order_id']+');"><i class="zmdi zmdi-delete"></i></button></td>'+
                          <?php } ?>
                          '</tr>';
              }
              $('#tbody_data').append(tbody);

              var pagination_str = "";
              var row_count = parseInt(responsedata.rowcount);
              var pages = Math.ceil(row_count/limits);
              var j=1;
              if (1<pages) {
                if (0<=(offset-limits)) {
                    pagination_str+='<li><a aria-label="Previous" onclick="set_offset('+(offset-limits)+')"><span aria-hidden="true">&laquo;</span></a></li>';
                }else{
                    pagination_str+='<li class="disabled"><a aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
                }
                
                if(1<((offset/limits)+1)){
                    pagination_str+='<li><a href="javascript:set_offset('+0+')">'+1+'</a></li>';
                }

                if(((offset/limits)+1)>3){
                    pagination_str+='<li><a>...</a></li>';
                }

                if(((offset/limits)+1)>2){
                    pagination_str+='<li><a href="javascript:set_offset('+(offset-limits)+')">'+(offset/limits)+'</a></li>';
                }

                pagination_str+='<li class="active"><a>'+((offset/limits)+1)+'</a></li>';

                if(((offset/limits)+1)<(pages-1)){
                    pagination_str+='<li><a href="javascript:set_offset('+(offset+limits)+')">'+((offset/limits)+2)+'</a></li>';
                }

                if(((offset/limits)+1)<(pages-2)){
                    pagination_str+='<li><a>...</a></li>';
                }

                if(pages>((offset/limits)+1)){
                    pagination_str+='<li><a href="javascript:set_offset('+((pages-1)*limits)+')">'+pages+'</a></li>';
                }

                if ((offset+limits)<(pages*limits)) {
                    pagination_str+='<li><a aria-label="Next" onclick="set_offset('+(offset+limits)+')"><span aria-hidden="true">&raquo;</span></a></li>';
                }else{
                    pagination_str+='<li class="disabled"><a aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
                }
                $('#pagination_ul').append(pagination_str);
              }
            }
          },
          error: function(result) {
            toastr.error("Somthing went wrong :(")
          }
        });
      }

      function set_offset(value) {
        $('#offset_field').val(value);
        getOrders();
      }
      function getOrdersByStatus() {
        $('#offset_field').val(0);
        getOrders();
      }

      function reset_fun_search() {
        $('#searchField').val('');
        $('#offset_field').val(0);
        getOrders();
      }
      function reset_fun_dates() {
        $('#filterByFromDate,#filterByToDate').val('');
        $('#offset_field').val(0);
        getOrders();
      }

      function editStatus(id) {
        $.ajax({
          type: "POST",
          url: "<?=base_url()?>getOrderStatus",
          data: 'order_id='+id,
          success: function(result) {
            var responsedata = $.parseJSON(result);
            if (responsedata.status=='error') {
              toastr.error(responsedata.message)
            }else{
              $("#sorder_id").val(id);
              $("#spayment_status").val(responsedata.payment_status);
              $("#sorder_status").val(responsedata.os_id);
              $("#status_modal").modal('show');
            }
          },
          error: function(result) {
            toastr.error("Somthing went wrong :(")
          }
        });
      }

      $('#statusUpdateMask').validator().on('submit', function (e) {
        if (!(e.isDefaultPrevented())) {
          e.preventDefault();
          run_waitMe('#statusUpdateMask');
          $.ajax({
            type: "POST",
            url: "<?=base_url()?>updateOrderStatus",
            data: $('#statusUpdateMask').serialize(),
            success: function(result) {
              var responsedata = $.parseJSON(result);
              if(responsedata.status=='success'){
                getOrders();
                toastr.success(responsedata.message)
              }else if(responsedata.status=='error'){
                toastr.error(responsedata.message)
              }else{
                toastr.error("Somthing went wrong :(")
              }
              $("#status_modal").modal('hide');
              $('#statusUpdateMask').waitMe('hide');
            },
            error: function(result) {
              $('#statusUpdateMask').waitMe('hide');
              toastr.error('Error :'+result)
            }
          }); 
        }
      });

      function deleteOrder(id) {
        toastr.warning("<button type='button' id='confirmBtn' class='btn btn-danger btn-sm' style='width:40%;display:inline;margin:3px;'>Yes</button><button type='button' id='closeBtn' class='btn btn-default btn-sm' style='width:40%;display:inline;margin:3px;'>No</button>",'Do you want to delete this order?',{
            closeButton: true,
            allowHtml: true,
            onShown: function (toast) {
              $("#confirmBtn").click(function(){
                $.ajax({
                  type: "POST",
                  url: "<?=base_url()?>delete_order",
                  data: 'order_id='+id,
                  success: function(result) {
                      var responsedata = $.parseJSON(result);
                      if (responsedata.status=='success') {
                        getOrders();
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

      function viewProduct(id) {
        var form = document.createElement("form");
        form.setAttribute("method", "post");
        form.setAttribute("action", "<?=base_url()?>view_order");

        hiddenField = document.createElement("input");
        hiddenField.setAttribute("type", "hidden");
        hiddenField.setAttribute("name", "order_id");
        hiddenField.setAttribute("value", id);
        form.appendChild(hiddenField);

        document.body.appendChild(form);
        form.submit();
      }
    </script>
  </body>
</html>