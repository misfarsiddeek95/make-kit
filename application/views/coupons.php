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
            <?php if($addCoupons){?>
              <div class="panel-tools">
                <button type="button" class="btn btn-outline-primary m-w-120" onclick="addCoupon();">Add Coupon</button>
              </div>
            <?php }?>
            <h3 class="m-t-0 m-b-5">Coupons</h3>
          </div>
          <div class="panel-body">
            <div class="page-layouts">
              <div class="row">
                <div id="controllers">
                    <div class="col-lg-5 col-sm-3 col-xs-12 m-y-5">
                        <div class="input-group">
                            <input class="form-control" type="text" placeholder="Search for..." style="border-color: #1d87e4;" id="searchField">
                            <span class="input-group-btn">
                              <button class="btn btn-outline-primary" type="button" onclick="getCouponsByStatus();">Search</button>
                              <button class="btn btn-outline-primary" type="button" onclick="reset_fun_search();">Reset</button>
                            </span>
                        </div>
                    </div>

                    <div class="col-lg-2 col-sm-3 col-xs-12 m-y-5">
                      <div class="btn-group" data-toggle="buttons">
                        <label class="btn btn-outline-primary active">
                          <input type="radio" name="filterActive" class="filterActive" value="" autocomplete="off" checked="checked">Reset
                        </label>
                        <label class="btn btn-outline-primary">
                          <input type="radio" name="filterActive" class="filterActive" value="0" autocomplete="off">On
                        </label>
                        <label class="btn btn-outline-primary">
                          <input type="radio" name="filterActive" class="filterActive" value="1" autocomplete="off">Off
                        </label>
                      </div>
                    </div>

                    <div class="col-lg-2 col-sm-6 col-xs-12 m-y-5">
                        <div class="input-group date">
                          <input type="text" class="form-control" style="border-color: #1d87e4;" id="filterByFromDate" placeholder="Select From Date" onchange="getCouponsByStatus();">
                        </div>
                    </div>

                    <div class="col-lg-2 col-sm-6 col-xs-12 m-y-5">
                        <div class="input-group date">
                          <input type="text" class="form-control" style="border-color: #1d87e4;" id="filterByToDate" placeholder="Select To Date" onchange="getCouponsByStatus();">
                        </div>
                    </div>

                    <div class="col-lg-1 col-sm-3 col-xs-12 m-y-5">
                        <select id="limit_sel" class="custom-select" onchange="getCouponsByStatus();" style="border-color: #1d87e4;">
                            <option value="50" selected="selected">50</option>
                            <option value="100">100</option>
                            <option value="250" >250</option>
                            <option value="500">500</option>
                        </select>
                    </div>
                </div>
              </div>

            </div>
            <div class="table-responsive">
              <table class="table table-hover m-b-10">
                <thead>
                  <tr>
                    <th>Code</th>
                    <th>Amount</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Count</th>
                    <th>Added Date</th>
                    <th>Status</th>
                    <?php if($editCoupons&&false){?>
                    <th>Edit</th>
                    <?php } if($deleteCoupons){ ?>
                    <th>Delete</th>
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
      <?php if($addCoupons||$editCoupons){ ?>
      <div id="coupon_modal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm">
          <div class="modal-content">
            <div class="modal-header bg-primary">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">
                  <i class="zmdi zmdi-close"></i>
                </span>
              </button>
              <h4 class="modal-title" id="modal-title">Add Coupon</h4>
            </div>

            <form data-toggle="validator" id="inputmasks">

              <div class="modal-body">
                <input type="hidden" name="coupon_id" id="coupon_id" value="0">

                <div class="form-group">                  
                  <label for="form-control-2" class="control-label">Coupon for</label>
                  <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-outline-info coupRadio">
                      <input type="radio" name="coup_for" id="coup_brand" autocomplete="off" value="0" required>Brand
                    </label>
                    <label class="btn btn-outline-info coupRadio">
                      <input type="radio" name="coup_for" id="coup_cate" autocomplete="off" value="1" required> Category
                    </label>
                  </div>
                </div>
                <div class="form-group" id="coupon_for_id_category_div">                  
                  <label for="form-control-2" class="control-label">Coupon Category</label>                 
                    <select id="coupon_for_id_category" name="coupf[]" class="form-control select2-hidden-accessible" data-plugin="select2" multiple="multiple" data-placeholder="Select Categories" aria-hidden="true" style="width: 100%;">
                      <option></option>
                      <?php
                        function write_with_child($category) {
                            $arr = explode("|",$category->tree_path);
                            $depth = count($arr)-1;
                            $val_str = "";
                            $sel = '';
                            for ($i=0; $i <$depth ; $i++) { 
                              $val_str ="&#160;&#160;". $val_str;
                            }
                            $val_str = $val_str.$category->category;
                            if(!(empty($product))){
                              if ($product->cate_id==$category->cate_id) {
                                $sel = 'selected';
                              }
                            }
                            if (isset($category->sub_cat) && sizeof($category->sub_cat) > 0) {?>
                              <option value="<?=$category->cate_id?>" <?php if(empty($product)){echo "disabled";}?> <?=$sel?>><?=$val_str?></option>
                              <?php foreach ($category->sub_cat as $child_cat) { ?>
                                  <?php write_with_child($child_cat); ?>
                              <?php } ?>
                            <?php } else { ?>
                              <option value="<?=$category->cate_id?>" <?=$sel?>><?=$val_str?></option>
                            <?php
                            }
                        }

                        foreach ($categories as $cate) {
                            write_with_child($cate);
                        }?>

                    </select>
                </div>
                <div class="form-group" id="coupon_for_id_brand_div">                  
                  <label for="form-control-2" class="control-label">Coupon Brand</label>                 
                    <select id="coupon_for_id_brand" name="coupf[]" class="form-control select2-hidden-accessible" data-plugin="select2" multiple="multiple" data-placeholder="Select Brands" aria-hidden="true" style="width: 100%;">
                      <option></option>
                      <?php foreach ($brands as $row) {  ?>
                      <option value="<?=$row->brand_id?>"><?=$row->brand?></option>
                      <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                  <label for="form-control-2" class="control-label">Coupon code</label>
                  <input type="text" pattern="^[0-9a-zA-Z-_]{0,6}$" data-pattern-error="Maximum 6 (a to z or 0 to 9) allows" class="form-control" id="coupon_code" name="coupon_code" placeholder="Coupon code" style="text-transform:uppercase" >
                  <div class="help-block with-errors"></div>
                </div>

                <div class="form-group">
                  <label for="form-control-4" class="control-label">Amount</label>
                  <div class="input-group">
                    <input type="text" placeholder="Amount" value="" name="coupAmount" id="coupAmount" class="form-control" data-inputmask="'alias': 'decimal', 'groupSeparator': ',', 'autoGroup': true, 'rightAlign': false, 'allowMinus': false, 'allowPlus': false" data-required-error="Amount is Required" required> 
                    <span class="input-group-addon"><input type="checkbox" value="1" name="coupon_type"> %</span>
                  </div>
                  <div class="help-block with-errors"></div>
                </div>

                <div class="form-group">
                  <label for="form-control-4" class="control-label">Start Date</label>
                  <input id="form-control-1" class="form-control" type="text" value="" data-inputmask="'alias': 'yyyy-mm-dd'" name="valid_from" id="valid_from" data-required-error="Start Date is Required" required>
                  <div class="help-block with-errors"></div>
                </div>

                <div class="form-group">
                  <label for="form-control-4" class="control-label">End Date</label>
                  <input id="form-control-1" class="form-control" type="text" value="" data-inputmask="'alias': 'yyyy-mm-dd'" name="valid_to" id="valid_to" data-required-error="Start Date is Required" required>
                  <div class="help-block with-errors"></div>
                </div>

                <div class="form-group">
                  <label for="form-control-4" class="control-label">Count</label>
                  <div class="input-group">
                    <input type="number" placeholder="Count" value="0" name="coupCount" id="coupCount" class="form-control" data-required-error="Count is Required" required> 
                    <span class="input-group-addon"><input type="checkbox" value="1" name="count_type"> Unlimited</span>
                  </div>
                  <div class="help-block with-errors"></div>
                </div>

                <?php if($couponsStatus){?>
                <div class="form-group">                  
                  <label for="form-control-2" class="control-label">Status</label>
                  <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-outline-primary statusRadio">
                      <input type="radio" name="coup_status" id="status_on" autocomplete="off" value="0" required> On
                    </label>
                    <label class="btn btn-outline-primary statusRadio">
                      <input type="radio" name="coup_status" id="status_off" autocomplete="off" value="1" required> Off
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
      <?php } ?>

    </div>
    <?php $this->load->view('includes/javascripts'); ?>
    <script src="<?=base_url()?>assets/js/forms-form-masks.js"></script>
    <script src="<?=base_url()?>assets/js/forms-plugins.js"></script>
    <script src="<?=base_url()?>assets/js/bootstrap-datepicker.js"></script>
    
    <script type="text/javascript">
      $( document ).ready(function() {
        $('#filterByFromDate,#filterByToDate').datepicker({
          format: 'yyyy-mm-dd'
        });
        getCoupons();

        $('#coupon_for_id_brand_div').hide();
        $('#coupon_for_id_category_div').hide();

      });

      $('.coupRadio input[name="coup_for"]').on('change',function() { 
        if (this.value == 0) {
          $('#coupon_for_id_brand_div').show();
          $('#coupon_for_id_category_div').hide();
        }else if(this.value == 1){
          $('#coupon_for_id_category_div').show();
          $('#coupon_for_id_brand_div').hide();
        }else{
          $('#coupon_for_id_brand_div').hide();
          $('#coupon_for_id_category_div').hide();
        }
      })


      function getCoupons() {
        var status = '';
        if ($(".filterActive").is(":checked")) {
          status = $('input[name=filterActive]:checked').val();
        }
        var limits = parseInt($('#limit_sel').val());
        var offset = parseInt($('#offset_field').val());
        $.ajax({
          type: "POST",
          url: "<?=base_url()?>getCoupons",
          data: 'search='+$('#searchField').val()+'&status='+status+'&fdate='+$('#filterByFromDate').val()+'&tdate='+$('#filterByToDate').val()+'&limit='+limits+'&offset='+offset,
          success: function(result) {
              var responsedata = $.parseJSON(result);
              $('#tbody_data,#pagination_ul').empty();
              var tbody = '';
              if (responsedata.rowcount==0) {
                $('#tbody_data').append('<tr><td colspan="10" class="text-center">No Results</td></tr>');
              }else{
                for (var i = 0; i < responsedata.coupons.length; i++) {
                  var coupon_type = 'LKR';
                  var status = '';
                  if (responsedata.coupons[i]['coupon_type']==1) {
                    coupon_type = '%';
                  }
                  <?php if($couponsStatus){?>
                  if (responsedata.coupons[i]['status']==0) {
                    status = 'onchange="updateCouponsStatus('+responsedata.coupons[i]['cp_id']+');" checked="checked"';
                  }else{
                    status = 'onchange="updateCouponsStatus('+responsedata.coupons[i]['cp_id']+');"';
                  }
                  <?php }else{ ?>
                    if (responsedata.coupons[i]['status']==0) {
                      status = 'onchange="updateCouponsStatus('+responsedata.coupons[i]['cp_id']+');" checked="checked"';
                    }else{
                      status = 'disabled="disabled"';
                    }
                  <?php } ?>
                  tbody +='<tr id="coupRow'+responsedata.coupons[i]['cp_id']+'"><td>'+responsedata.coupons[i]['coupon_code']+'</td>'+
                          '<td>'+responsedata.coupons[i]['coupon_amount']+' '+coupon_type+'</td>'+
                          '<td>'+responsedata.coupons[i]['valid_from']+'</td>'+
                          '<td>'+responsedata.coupons[i]['valid_to']+'</td>'+
                          '<td>'+responsedata.coupons[i]['coupon_count']+'</td>'+
                          '<td>'+responsedata.coupons[i]['create_date']+'</td>'+
                          '<td><input type="checkbox" class="js-switch" data-size="small" data-color="#34a853" '+status+'></td>'+
                          <?php if($editCoupons&&false){?>
                          '<td style="padding:0;margin:0;"><button type="button" class="btn btn-primary btn-sm" title="Edit Order Status" onclick="editCoupon('+responsedata.coupons[i]['cp_id']+');"><i class="zmdi zmdi-edit"></i></button></td>'+
                          <?php } if($deleteCoupons){?>
                          '<td style="padding:0;margin:0;"><button type="button" class="btn btn-danger btn-sm" title="Delete Order" onclick="deleteCoupons('+responsedata.coupons[i]['cp_id']+');"><i class="zmdi zmdi-delete"></i></button></td>'+
                          <?php } ?>
                          '</tr>';
              }
              $('#tbody_data').append(tbody);
              $('.js-switch').each(function () {
                new Switchery($(this)[0], $(this).data());
              });

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

      function addCoupon() {
        $('#modal-title').text('Add Coupon');
        $("#coupon_id").val(0);
        $('#coupon_for_id').select2({
          dropdownParent: $('#coupon_modal')
        });

        $('.coupRadio').removeClass('active');
        $('#coup_brand,#coup_cate').prop('checked',false);
        $('#coupon_for_id_brand_div').hide();
        $('#coupon_for_id_category_div').hide();

        $("#coupon_modal").modal('show');
      }

      function editCoupon(id) {
        var category = $("#coupRow"+id).find("td:eq(2)").text();
        $('#modal-title').text('Update Coupon');
        $("#coupon_id").val(id);
        $("#coupon_modal").modal('show');
      }

      function set_offset(value) {
        $('#offset_field').val(value);
        getCoupons();
      }
      function getCouponsByStatus() {
        $('#offset_field').val(0);
        getCoupons();
      }

      function reset_fun_search() {
        $('#searchField').val('');
        $('#offset_field').val(0);
        getCoupons();
      }

      $('input[type=radio][name=filterActive]').change(function() {
        getCouponsByStatus();
      });

      function updateCouponsStatus(id) {
        $.ajax({
          type: "POST",
          url: "<?=base_url()?>updateCouponsStatus",
          data: 'coupon_id='+id,
          success: function(result) {
            var responsedata = $.parseJSON(result);
            if (responsedata.status=='success') {
              toastr.success(responsedata.message)
            }else{
              getCouponsByStatus();
              toastr.error(responsedata.message)
            }
          },
          error: function(result) {
            toastr.error("Somthing went wrong :(")
          }
        });
      }

      $('#inputmasks').validator().on('submit', function (e) {
        if (!(e.isDefaultPrevented())) {
          e.preventDefault();
          run_waitMe('#inputmasks');
          $.ajax({
            type: "POST",
            url: "<?=base_url()?>addCoupon",
            data: $('#inputmasks').serialize(),
            success: function(result) {
              var responsedata = $.parseJSON(result);
              if(responsedata.status=='success'){
                document.getElementById('inputmasks').reset(); 
                $('#inputmasks').find("input").val("");
                $('#inputmasks').validator('destroy').validator();
                toastr.success(responsedata.message)
                $("#coupon_modal").modal('hide');
                getCoupons();
              }else if(responsedata.status=='error'){
                toastr.error(responsedata.message)
              }else{
                toastr.error("Somthing went wrong :(")
              }
              $('#inputmasks').waitMe('hide');
            },
            error: function(result) {
              $('#inputmasks').waitMe('hide');
              toastr.error('Error :'+result)
            }
          }); 
        }
      });

      function deleteCoupons(id) {
        toastr.warning("<button type='button' id='confirmBtn' class='btn btn-danger btn-sm' style='width:40%;display:inline;margin:3px;'>Yes</button><button type='button' id='closeBtn' class='btn btn-default btn-sm' style='width:40%;display:inline;margin:3px;'>No</button>",'Do you want to delete this coupon?',{
            closeButton: true,
            allowHtml: true,
            onShown: function (toast) {
              $("#confirmBtn").click(function(){
                $.ajax({
                  type: "POST",
                  url: "<?=base_url()?>deleteCoupons",
                  data: 'coupon_id='+id,
                  success: function(result) {
                      var responsedata = $.parseJSON(result);
                      if (responsedata.status=='success') {
                        getCouponsByStatus();
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