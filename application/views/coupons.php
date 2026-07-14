<!DOCTYPE html>
<html lang="en">
  <head>
    <?php $this->load->view('includes/head'); ?>
    <style type="text/css">
    .dz-preview.dz-processing.dz-image-preview.dz-complete{
      display: table;
      margin: 0 auto;
      background-color: transparent;
      width: 100%;
    }
    #myDropzone{
      background-color: transparent;
      display: table;
      margin: 0 auto;
      padding: 5px;
    }
    .dz-default.dz-message{
      padding: 0;
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
            <?php if($addCoupons){?>
              <div class="panel-tools">
                <button type="button" class="btn btn-outline-primary m-w-120" onclick="addCoupon();">הוסף קופון</button>
              </div>
            <?php }?>
            <h3 class="m-t-0 m-b-5">קופונים</h3>
          </div>
          <div class="panel-body">
            <div class="page-layouts">
              <div class="row">
                <div id="controllers">
                    <div class="col-lg-5 col-sm-3 col-xs-12 m-y-5">
                        <div class="input-group">
                            <input class="form-control" type="text" placeholder="חפש..." style="border-color: #1d87e4;" id="searchField">
                            <span class="input-group-btn">
                              <button class="btn btn-outline-primary" type="button" onclick="getCouponsByStatus();">חפש</button>
                              <button class="btn btn-outline-primary" type="button" onclick="reset_fun_search();">איפוס</button>
                            </span>
                        </div>
                    </div>

                    <div class="col-lg-2 col-sm-3 col-xs-12 m-y-5">
                      <div class="btn-group" data-toggle="buttons">
                        <label class="btn btn-outline-primary active">
                          <input type="radio" name="filterActive" class="filterActive" value="" autocomplete="off" checked="checked">איפוס
                        </label>
                        <label class="btn btn-outline-primary">
                          <input type="radio" name="filterActive" class="filterActive" value="0" autocomplete="off">פעיל
                        </label>
                        <label class="btn btn-outline-primary">
                          <input type="radio" name="filterActive" class="filterActive" value="1" autocomplete="off">כבוי
                        </label>
                      </div>
                    </div>

                    <div class="col-lg-2 col-sm-6 col-xs-12 m-y-5">
                        <div class="input-group date">
                          <input type="text" class="form-control" style="border-color: #1d87e4;" id="filterByFromDate" placeholder="בחר מתאריך" onchange="getCouponsByStatus();">
                        </div>
                    </div>

                    <div class="col-lg-2 col-sm-6 col-xs-12 m-y-5">
                        <div class="input-group date">
                          <input type="text" class="form-control" style="border-color: #1d87e4;" id="filterByToDate" placeholder="בחר עד תאריך" onchange="getCouponsByStatus();">
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
                    <th></th>
                    <th>קוד</th>
                    <th>סכום</th>
                    <th>מתאריך</th>
                    <th>עד תאריך</th>
                    <th>ספירה</th>
                    <th>תאריך הוספה</th>
                    <th>סטטוס</th>
                    <?php if($editCoupons){?>
                    <th>עריכה</th>
                    <?php } if($deleteCoupons){ ?>
                    <th>מחיקה</th>
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
              <h4 class="modal-title" id="modal-title">הוסף קופון</h4>
            </div>

            <div id="couponModalCont">
              <form action="<?=base_url()?>uploadCouponImage" class="dropzone" id="myDropzone">
                <div class="dz-default dz-message">
                  <span class="text-muted">גרור תמונת קופון לכאן</span>
                </div>
              </form>

              <form data-toggle="validator" id="inputmasks">

              <div class="modal-body">
                <input type="hidden" name="coupon_id" id="coupon_id" value="0">

                <div class="form-group">                  
                  <label for="form-control-2" class="control-label">קופון עבור</label>
                  <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-outline-info coupRadio">
                      <input type="radio" name="coup_for" id="coup_brand" autocomplete="off" value="0" required>מותג
                    </label>
                    <label class="btn btn-outline-info coupRadio">
                      <input type="radio" name="coup_for" id="coup_cate" autocomplete="off" value="1" required> קטגוריה
                    </label>
                  </div>
                </div>
                <div class="form-group" id="coupon_for_id_category_div">                  
                  <label for="form-control-2" class="control-label">קטגוריית קופון</label>                 
                    <select id="coupon_for_id_category" name="coupf[]" class="form-control select2-hidden-accessible" data-plugin="select2" multiple="multiple" data-placeholder="בחר קטגוריות" aria-hidden="true" style="width: 100%;">
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
                  <label for="form-control-2" class="control-label">מותג קופון</label>                 
                    <select id="coupon_for_id_brand" name="coupf[]" class="form-control select2-hidden-accessible" data-plugin="select2" multiple="multiple" data-placeholder="בחר מותגים" aria-hidden="true" style="width: 100%;">
                      <option></option>
                      <?php foreach ($brands as $row) {  ?>
                      <option value="<?=$row->brand_id?>"><?=$row->brand?></option>
                      <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                  <label for="form-control-2" class="control-label">קוד קופון</label>
                  <input type="text" pattern="^[0-9a-zA-Z-_]{0,6}$" data-pattern-error="מקסימום 6 (א-ת a-z או 0-9) מותר" class="form-control" id="coupon_code" name="coupon_code" placeholder="קוד קופון" style="text-transform:uppercase" >
                  <div class="help-block with-errors"></div>
                </div>

                <div class="form-group">
                  <label for="form-control-4" class="control-label">סכום</label>
                  <div class="input-group">
                    <input type="text" placeholder="סכום" value="" name="coupAmount" id="coupAmount" class="form-control" data-inputmask="'alias': 'decimal', 'groupSeparator': ',', 'autoGroup': true, 'rightAlign': false, 'allowMinus': false, 'allowPlus': false" data-required-error="סכום נדרש" required> 
                    <span class="input-group-addon"><input type="checkbox" value="1" name="coupon_type"> %</span>
                  </div>
                  <div class="help-block with-errors"></div>
                </div>

                <div class="form-group">
                  <label for="form-control-4" class="control-label">תאריך התחלה</label>
                  <input class="form-control" type="text" value="" data-inputmask="'alias': 'yyyy-mm-dd'" name="valid_from" id="valid_from" data-required-error="תאריך התחלה נדרש" required>
                  <div class="help-block with-errors"></div>
                </div>

                <div class="form-group">
                  <label for="form-control-4" class="control-label">תאריך סיום</label>
                  <input class="form-control" type="text" value="" data-inputmask="'alias': 'yyyy-mm-dd'" name="valid_to" id="valid_to" data-required-error="תאריך סיום נדרש" required>
                  <div class="help-block with-errors"></div>
                </div>

                <div class="form-group">
                  <label for="form-control-4" class="control-label">ספירה</label>
                  <div class="input-group">
                    <input type="number" placeholder="ספירה" value="0" name="coupCount" id="coupCount" class="form-control" data-required-error="ספירה נדרשת" required> 
                    <span class="input-group-addon"><input type="checkbox" value="1" name="count_type"> אין הגבלה</span>
                  </div>
                  <div class="help-block with-errors"></div>
                </div>

                <?php if($couponsStatus){?>
                <div class="form-group">                  
                  <label for="form-control-2" class="control-label">סטטוס</label>
                  <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-outline-primary statusRadio">
                      <input type="radio" name="coup_status" id="status_on" autocomplete="off" value="0" required> פעיל
                    </label>
                    <label class="btn btn-outline-primary statusRadio">
                      <input type="radio" name="coup_status" id="status_off" autocomplete="off" value="1" required> כבוי
                    </label>
                  </div>
                </div>
                <?php } ?>

              </div>

              <div class="modal-footer">
                <button type="submit" class="btn btn-primary">שליחה</button>
                <button type="button" data-dismiss="modal" class="btn btn-default">סגור</button>
              </div>
            </form>
            </div>
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
      var pendingCouponIds = '';
      var imageRemoved = false;

      $( document ).ready(function() {
        $('#filterByFromDate,#filterByToDate').datepicker({
          format: 'yyyy-mm-dd'
        });
        $('#valid_from,#valid_to').datepicker({
          format: 'yyyy-mm-dd',
          autoclose: true
        });
        getCoupons();

        $('#coupon_for_id_brand_div').hide();
        $('#coupon_for_id_category_div').hide();

      });

      $("#myDropzone").dropzone({
        acceptedFiles: 'image/*',
        autoProcessQueue: false,
        uploadMultiple: false,
        addRemoveLinks: true,
        maxFiles: 1,
        url: "<?=base_url()?>uploadCouponImage",
        sending: function(file, xhr, formData) {
          formData.append('coupon_ids', pendingCouponIds);
        },
        removedfile: function(file) {
          imageRemoved = true;
          var _ref;
          if (file.previewElement) {
            if ((_ref = file.previewElement) != null) {
              _ref.parentNode.removeChild(file.previewElement);
            }
          }
        },
        success : function(file, response){
          var responsedata = $.parseJSON(response);
          if (responsedata.status=='success') {
            toastr.success(responsedata.message);
          } else {
            toastr.error(responsedata.message);
          }
          document.getElementById('inputmasks').reset();
          $('#inputmasks').find("input").val("");
          $('#inputmasks').validator('destroy').validator();
          $("#coupon_modal").modal('hide');
          getCoupons();
          $('#couponModalCont').waitMe('hide');
        }
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
                $('#tbody_data').append('<tr><td colspan="10" class="text-center">אין תוצאות</td></tr>');
              }else{
                for (var i = 0; i < responsedata.coupons.length; i++) {
                  var coupon_type = '₪';
                  var status = '';
                  if (responsedata.coupons[i]['coupon_type']==1) {
                    coupon_type = '%';
                  }
                  var imgSrc = '<?=base_url()?>photos/default.jpg';
                  if (responsedata.coupons[i]['photo_path'] != null && responsedata.coupons[i]['photo_path'] != '') {
                    imgSrc = '<?=base_url()?>photos/coupons/'+responsedata.coupons[i]['photo_path']+'-org.'+responsedata.coupons[i]['extension'];
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
                  tbody +='<tr id="coupRow'+responsedata.coupons[i]['cp_id']+'"><td><img class="img-rounded" src="'+imgSrc+'" height="32"></td>'+
                          '<td>'+responsedata.coupons[i]['coupon_code']+'</td>'+
                          '<td>'+responsedata.coupons[i]['coupon_amount']+' '+coupon_type+'</td>'+
                          '<td>'+responsedata.coupons[i]['valid_from']+'</td>'+
                          '<td>'+responsedata.coupons[i]['valid_to']+'</td>'+
                          '<td>'+responsedata.coupons[i]['coupon_count']+'</td>'+
                          '<td>'+responsedata.coupons[i]['create_date']+'</td>'+
                          '<td><input type="checkbox" class="js-switch" data-size="small" data-color="#34a853" '+status+'></td>'+
                          <?php if($editCoupons){?>
                          '<td style="padding:0;margin:0;"><button type="button" class="btn btn-primary btn-sm" title="Edit Coupon" onclick="editCoupon('+responsedata.coupons[i]['cp_id']+',\''+responsedata.coupons[i]['coupon_code']+'\',\''+responsedata.coupons[i]['coupon_amount']+'\',\''+responsedata.coupons[i]['valid_from']+'\',\''+responsedata.coupons[i]['valid_to']+'\',\''+responsedata.coupons[i]['coupon_count']+'\',\''+responsedata.coupons[i]['coupon_type']+'\',\''+responsedata.coupons[i]['count_type']+'\',\''+responsedata.coupons[i]['coupon_for']+'\',\''+responsedata.coupons[i]['coupon_for_id']+'\',\''+responsedata.coupons[i]['status']+'\',\''+imgSrc+'\');"><i class="zmdi zmdi-edit"></i></button></td>'+
                          <?php } if($deleteCoupons){?>
                          '<td style="padding:0;margin:0;"><button type="button" class="btn btn-danger btn-sm" title="Delete Coupon" onclick="deleteCoupons('+responsedata.coupons[i]['cp_id']+');"><i class="zmdi zmdi-delete"></i></button></td>'+
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
                    pagination_str+='<li><a href="javascript:set_offset('+(offset-limits)+')">'+( offset/limits)+'</a></li>';
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
            toastr.error("משהו השתבש :(")
          }
        });
      }

      function addCoupon() {
        $('#modal-title').text('הוסף קופון');
        $("#coupon_id").val(0);
        pendingCouponIds = '';
        imageRemoved = false;
        $('#coupon_for_id_brand,#coupon_for_id_category').select2({
          dropdownParent: $('#coupon_modal')
        });

        $('.coupRadio').removeClass('active');
        $('#coup_brand,#coup_cate').prop('checked',false);
        $('#coupon_for_id_brand_div').hide();
        $('#coupon_for_id_category_div').hide();

        var myDropzone = Dropzone.forElement(".dropzone");
        myDropzone.removeAllFiles();

        $("#coupon_modal").modal('show');
      }

      function editCoupon(id, code, amount, valid_from, valid_to, coupon_count, coupon_type, count_type, coupon_for, coupon_for_id, status, imgSrc) {
        $('#modal-title').text('עדכן קופון');
        $("#coupon_id").val(id);
        pendingCouponIds = id.toString();
        imageRemoved = false;
        $("#coupon_code").val(code);
        $("#coupAmount").val(amount);
        $('#valid_from').datepicker('setDate', valid_from);
        $('#valid_to').datepicker('setDate', valid_to);
        $("#coupCount").val(coupon_count);
        
        if (coupon_type == '1') {
          $('input[name="coupon_type"]').prop('checked', true);
        } else {
          $('input[name="coupon_type"]').prop('checked', false);
        }
        
        if (count_type == '1') {
          $('input[name="count_type"]').prop('checked', true);
        } else {
          $('input[name="count_type"]').prop('checked', false);
        }

        $('#coupon_for_id_brand').select2({ dropdownParent: $('#coupon_modal') });
        $('#coupon_for_id_category').select2({ dropdownParent: $('#coupon_modal') });
        $('#coupon_for_id_brand').val(null).trigger('change');
        $('#coupon_for_id_category').val(null).trigger('change');

        if (coupon_for == '0') {
          $('#coup_brand').prop('checked', true).parent().addClass('active');
          $('#coup_cate').prop('checked', false).parent().removeClass('active');
          $('#coupon_for_id_brand_div').show();
          $('#coupon_for_id_category_div').hide();
          if (coupon_for_id) {
            $('#coupon_for_id_brand').val(coupon_for_id.split(',')).trigger('change');
          }
        } else if (coupon_for == '1') {
          $('#coup_cate').prop('checked', true).parent().addClass('active');
          $('#coup_brand').prop('checked', false).parent().removeClass('active');
          $('#coupon_for_id_category_div').show();
          $('#coupon_for_id_brand_div').hide();
          if (coupon_for_id) {
            $('#coupon_for_id_category').val(coupon_for_id.split(',')).trigger('change');
          }
        } else {
          $('#coupon_for_id_brand_div').hide();
          $('#coupon_for_id_category_div').hide();
        }

        if (status == '0') {
          $('#status_on').prop('checked', true).parent().addClass('active');
          $('#status_off').prop('checked', false).parent().removeClass('active');
        } else {
          $('#status_off').prop('checked', true).parent().addClass('active');
          $('#status_on').prop('checked', false).parent().removeClass('active');
        }

        var myDropzone = Dropzone.forElement(".dropzone");
        myDropzone.removeAllFiles();
        
        if (imgSrc && imgSrc.indexOf('default.jpg') === -1) {
          var mockFile = { name: code, size: 20 };
          myDropzone.options.addedfile.call(myDropzone, mockFile);
          myDropzone.options.thumbnail.call(myDropzone, mockFile, imgSrc);
          myDropzone.files.push( mockFile );
        }

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
            toastr.error("משהו השתבש :(")
          }
        });
      }

      $('#inputmasks').validator().on('submit', function (e) {
        if (!(e.isDefaultPrevented())) {
          e.preventDefault();
          run_waitMe('#couponModalCont');
          var myDropzone = Dropzone.forElement(".dropzone");

          $.ajax({
            type: "POST",
            url: "<?=base_url()?>addCoupon",
            data: $('#inputmasks').serialize(),
            success: function(result) {
              var responsedata = $.parseJSON(result);
              if(responsedata.status=='success'){
                if (myDropzone.getQueuedFiles().length > 0) {
                  pendingCouponIds = responsedata.coupon_ids.join(',');
                  myDropzone.processQueue();
                } else if (imageRemoved && responsedata.coupon_ids && responsedata.coupon_ids.length > 0) {
                  $.ajax({
                    type: "POST",
                    url: "<?=base_url()?>deleteCouponPhoto",
                    data: 'coupon_ids=' + responsedata.coupon_ids.join(','),
                    complete: function() {
                      document.getElementById('inputmasks').reset();
                      $('#inputmasks').find("input").val("");
                      $('#inputmasks').validator('destroy').validator();
                      toastr.success(responsedata.message);
                      $("#coupon_modal").modal('hide');
                      getCoupons();
                      $('#couponModalCont').waitMe('hide');
                    }
                  });
                } else {
                  document.getElementById('inputmasks').reset(); 
                  $('#inputmasks').find("input").val("");
                  $('#inputmasks').validator('destroy').validator();
                  toastr.success(responsedata.message)
                  $("#coupon_modal").modal('hide');
                  getCoupons();
                  $('#couponModalCont').waitMe('hide');
                }
              }else if(responsedata.status=='error'){
                toastr.error(responsedata.message)
                $('#couponModalCont').waitMe('hide');
              }else{
                toastr.error("משהו השתבש :(")
                $('#couponModalCont').waitMe('hide');
              }
            },
            error: function(result) {
              $('#couponModalCont').waitMe('hide');
              toastr.error('Error :'+result)
            }
          }); 
        }
      });

      function deleteCoupons(id) {
        toastr.warning("<button type='button' id='confirmBtn' class='btn btn-danger btn-sm' style='width:40%;display:inline;margin:3px;'>כן</button><button type='button' id='closeBtn' class='btn btn-default btn-sm' style='width:40%;display:inline;margin:3px;'>לא</button>",'האם ברצונך למחוק קופון זה?',{
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
                    toastr.error("משהו השתבש :(")
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