<!DOCTYPE html>
<html lang="en">
  <head>
    <?php $this->load->view('includes/head'); ?>
    <style type="text/css">
      .compress,.tbImg{
        cursor: pointer;
      }
      #modalCloseBtn{
        display: block;
        color: #FFF;
        background: rgba(0, 0, 0, 0.4);
        width: 30px;
        height: 30px;
        top: 0;
        position: absolute;
        text-align: center;
        line-height: 30px;
        transition: opacity .35s ease;
      }
      .modal {
        text-align: center;
        padding: 0!important;
      }

      .modal:before {
        content: '';
        display: inline-block;
        height: 100%;
        vertical-align: middle;
        margin-right: -4px;
      }

      .modal-dialog {
        display: inline-block;
        text-align: left;
        vertical-align: middle;
      }

      .hypertextRemoveStyle{
        text-decoration: none; 
        color: inherit;
      }
      .lowqty, .lowqty .editinput{
        background-color: #fff769;
      }
      .highlighted, .highlighted .editinput{
        background-color: #ccefff;
      }
      .editinput{
        background-color: #fff;
        border: none;
      }
      .edited{
        border: 1px solid #d9d9d9;
      }
      .table-hover > tbody > tr:hover .editinput{
        background-color: #f5f5f5;
      }
      input::-webkit-outer-spin-button,
      input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
      }

      /* Firefox */
      input[type=number] {
        -moz-appearance: textfield;
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
            <h3 class="m-t-0 m-b-5">View Products</h3>
          </div>
          <div class="panel-body">
            <input type="hidden" id="type" value="<?=$type?>" name="">
            <div class="page-layouts">
              <div class="row">
                <div id="controllers">
                    <div class="col-lg-3 col-sm-3 col-xs-12 m-y-5">
                        <div class="input-group">
                            <input class="form-control" type="text" placeholder="Search for..." style="border-color: #1d87e4;" id="searchField">
                            <span class="input-group-btn">
                              <button class="btn btn-outline-primary" type="button" onclick="getProductsByStatus();">Search</button>
                              <button class="btn btn-outline-primary" type="button" onclick="reset_fun();">Reset</button>
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
                    <div class="col-lg-2 col-sm-3 col-xs-12 m-y-5">
                        <div class="input-group">
                          <select class="form-control" data-plugin="select2" name="proCate" id="proCate" onchange="getProductsByStatus();">
                            <option></option>
                            <?php
                              function write_with_child($category) {
                                  $arr = explode("|",$category->tree_path);
                                  $depth = count($arr)-1;
                                  $val_str = "";
                                  for ($i=0; $i <$depth ; $i++) { 
                                    $val_str ="&#160;&#160;". $val_str;
                                  }
                                  $val_str = $val_str.$category->category;
                                  if (isset($category->sub_cat) && sizeof($category->sub_cat) > 0) {
                                      ?>

                                      <option value="<?=$category->cate_id?>"><?=$val_str?></option>
                                              <?php foreach ($category->sub_cat as $child_cat) { ?>
                                                  <?php write_with_child($child_cat); ?>
                                              <?php } ?>
                                  <?php } else { ?>
                                      <option value="<?=$category->cate_id?>"><?=$val_str?></option>
                                      <?php
                                  }
                              }
                              
                              foreach ($categories as $cate) {
                                  write_with_child($cate);
                              }
                            ?>

                          </select>
                        </div>
                    </div>
                    <?php if($userAc==0){?>
                    <div class="col-lg-2 col-sm-3 col-xs-12 m-y-5">
                      <select id="allUsers" class="custom-select" style="border-color: #1d87e4;" onchange="getProductsByStatus();">
                        <option value="" selected="selected">-- User --</option>
                        <?php foreach ($users as $row) {?>
                          <option title="<?=$row->company_name?>" value="<?=$row->user_id?>"><?=$row->fname.' '.$row->lname?></option>
                        <?php } ?>
                      </select>
                    </div>
                    <?php } ?>
                    <div class="col-lg-2 col-sm-3 col-xs-12 m-y-5">
                      <select class="form-control" data-plugin="select2" name="proBrand" id="proBrand" onchange="getProductsByStatus();">
                          <option></option>
                          <?php foreach ($brands as $row) {?>
                            <option value="<?=$row->brand_id?>"><?=$row->brand?></option>
                          <?php } ?>

                      </select>
                    </div>
                    <div class="col-lg-1 col-sm-3 col-xs-12 m-y-5">
                        <select id="limit_sel" class="custom-select" onchange="getProductsByStatus();" style="border-color: #1d87e4;">
                            <option value="50">50</option>
                            <option value="100" selected="selected">100</option>
                            <option value="250">250</option>
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
                    <th>Name</th>
                    <th>Code</th>
                    <th>User</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Wight(g)</th>
                    <th>Barcode</th>
                    <th>Views</th>
                    <th>Sales</th>
                    <th>Status</th>
                    <th></th>
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
        <div id="imageShow" class="modal" tabindex="-1" role="dialog">
          <div class="modal-dialog modal-sm">
            <div class="modal-content animated bounceIn">
              <img src="" id="largeImg" style="width: 100%">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="modalCloseBtn">
                <span aria-hidden="true">
                  <i class="zmdi zmdi-close"></i>
                </span>
              </button>
            </div>
          </div>
        </div>

    </div>
    <?php $this->load->view('includes/javascripts'); ?>
    <script src="<?=base_url()?>assets/js/forms-plugins.js"></script>
    
    <script type="text/javascript">
      $( document ).ready(function() {
        getProducts();
      });

      $("#proCate").select2({
        placeholder: "Filter By Category",
        allowClear: true
      });
      $("#proBrand").select2({
        placeholder: "Filter By Brand",
        allowClear: true
      });

      $('.table-responsive').on('show.bs.dropdown', function () {
        $('.table-responsive').css( "overflow", "inherit" );
      });

      $('.table-responsive').on('hide.bs.dropdown', function () {
        $('.table-responsive').css( "overflow", "auto" );
      });


      function getProducts() {
        var type = $('#type').val();
        var status = '';
        if ($(".filterActive").is(":checked")) {
          status = $('input[name=filterActive]:checked').val();
        }
        var limits = parseInt($('#limit_sel').val());
        var offset = parseInt($('#offset_field').val());
        $.ajax({
          type: "POST",
          url: "<?=base_url()?>getProducts",
          data: 'search='+$('#searchField').val()+'&status='+status+'&user='+$('#allUsers').val()+'&category='+$('#proCate').val()+'&brand='+$('#proBrand').val()+'&limit='+limits+'&offset='+offset+'&type='+type,
          success: function(result) {
              var responsedata = $.parseJSON(result);
              //alert(responsedata.sql);
              $('#tbody_data,#pagination_ul').empty();
              var tbody = '';
              if (responsedata.rowcount==0) {
                $('#tbody_data').append('<tr><td colspan="12" class="text-center">No Results</td></tr>');
              }else{
                for (var i = 0; i < responsedata.products.length; i++) {
                  var status = '';
                  var img = 'default.jpg';
                  var func = '';
                  var clz = '';
                  var clzz = '';
                  var show_sub="style='display:none;'";
                  <?php if($onStatus==0){?>
                    if (responsedata.products[i]['pro_status']==0) {
                      status = 'onchange="updateProStatus('+responsedata.products[i]['pro_id']+');" checked="checked"';
                    }else{
                      status = 'onchange="updateProStatus('+responsedata.products[i]['pro_id']+');"';
                    }
                  <?php }else{ ?>
                    if (responsedata.products[i]['pro_status']==0) {
                      status = 'onchange="updateProStatus('+responsedata.products[i]['pro_id']+');" checked="checked"';
                    }else{
                      status = 'disabled="disabled"';
                    }
                  <?php } ?>
                  
                  if (responsedata.products[i]['photo_path']!=null) {
                    img = 'products/'+responsedata.products[i]['photo_path']+'-sma.'+responsedata.products[i].extension;
                    func = 'onclick="show_img(this.src);"';
                    clz = 'tbImg';
                  }
                  if (responsedata.products[i]['attr_count']!=0) {
                    show_sub="";
                  }

                  if(responsedata.products[i]['quantity'] <= 2){
                    clzz = 'class="lowqty"';
                  }else{
                    clzz = '';
                  }

                  var link_name = createHtmlName(responsedata.products[i]['name']);
                  var link_cate = createHtmlName(responsedata.products[i]['category']);

                  tbody+='<tr '+clzz+' tr-id="'+responsedata.products[i]['pro_id']+'" ondblclick="updateRow(this)"><td><img class="img-rounded '+clz+'" '+func+' src="<?=base_url();?>photos/'+img+'" alt="'+responsedata.products[i]['photo_title']+'" height="32"></td>'+
                  '<td><a href="javascript:void(0)" target="new" class="hypertextRemoveStyle">'+responsedata.products[i]['name']+'</a></td>'+
                  '<td>'+responsedata.products[i]['pro_code']+'</td>'+
                  '<td title="'+responsedata.products[i]['company_name']+'">'+responsedata.products[i]['fname']+' '+responsedata.products[i]['lname']+'</td>'+
                  '<td>'+responsedata.products[i]['category']+'</td>'+
                  '<td><input type="number" class="editinput" id="tr-price'+responsedata.products[i]['pro_id']+'" value="'+responsedata.products[i]['price']+'" disabled style="width:80px;"></td>'+
                  '<td><input type="number" class="editinput" id="tr-qty'+responsedata.products[i]['pro_id']+'" value="'+responsedata.products[i]['quantity']+'" disabled style="width:40px;"></td>'+
                  '<td>'+responsedata.products[i]['weight']+'</td>'+
                  '<td>'+responsedata.products[i]['barcode']+'</td>'+
                  '<td>'+responsedata.products[i]['view_count']+'</td>'+
                  '<td>'+responsedata.products[i]['sales_count']+'</td>'+
                  '<td><input type="checkbox" class="js-switch" data-size="small" data-color="#34a853" '+status+'></td>'+
                  '<td><div class="btn-group">'+
                  '<button type="button" class="btn btn-primary btn-pill btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'+
                      '<i class="zmdi zmdi-more"></i>'+
                    '</button>'+
                    '<ul class="dropdown-menu dropdown-menu-right">'+
                      '<li><a href="javascript:addProductImg('+responsedata.products[i]['pro_id']+');">Add Image</a></li>'+
                      <?php if($editAc==0){?>
                      '<li><a href="javascript:editProduct('+responsedata.products[i]['pro_id']+');">Edit</a></li>'+
                      <?php } ?><?php if($deleteAc==0){?>
                      '<li><a href="javascript:deleteProduct('+responsedata.products[i]['pro_id']+');">Delete</a></li>'+
                      <?php } ?><?php if($editSub==0){?>
                      '<li '+show_sub+'><a href="javascript:editSubProducts('+responsedata.products[i]['pro_id']+');">Edit Sub Products</a></li>'+
                      <?php } ?>
                      '</ul></div></td>';
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
      function show_img(src) {
        run_waitMe('#imageShow');
        var res = src.replace("-sma.jpg", "-std.jpg");
        $("#largeImg").attr("src",res);
        $('#imageShow').waitMe('hide');
        $("#imageShow").modal('show');
      }

      function updateRow(evt) {
        var trid = $(evt).attr('tr-id');
        var qty = parseInt($('#tr-qty' + trid).val());
        if ($('#tr-qty' + trid).hasClass('edited')) {
          // alert('a');
          $.ajax({
            type: "POST",
            url: "<?=base_url()?>updateProQtyPrice",
            data: 'pro_id='+trid+'&qty='+qty+'&price='+$('#tr-price' + trid).val(),
            success: function(result) {
              var responsedata = $.parseJSON(result);
              $(evt).removeClass('highlighted');
              if (result) {
                $(evt).effect("highlight", {color: "#ccffcc"}, 1500);
                if(qty <= 2){
                  $(evt).addClass('lowqty');
                }else{
                  $(evt).removeClass('lowqty');
                }
              }else{
                $(evt).effect("highlight", {color: "#ffb3b3"}, 1500);
              }
              
              $(evt).find('input').attr("disabled", true);
              $('input').removeClass("edited");
            },
            error: function(result) {
              toastr.error("Somthing went wrong :(")
            }
          });
        }else{
          $(evt).find('input').attr("disabled", false);
          $(evt).addClass('highlighted');
          // $(evt).find('input').attr("disabled", false);
          $(evt).find('input').toggleClass('edited');
        }


      }

      $('input[type=radio][name=filterActive]').change(function() {
        getProductsByStatus();
      });

      function set_offset(value) {
        $('#offset_field').val(value);
        getProducts();
      }
      function reset_fun() {
        $('#searchField').val('');
        $('#offset_field').val(0);
        getProducts();
      }
      function getProductsByStatus() {
        $('#offset_field').val(0);
        getProducts();
      }

      function updateProStatus(id) {
        $.ajax({
          type: "POST",
          url: "<?=base_url()?>updateProStatus",
          data: 'pro_id='+id,
          success: function(result) {
            var responsedata = $.parseJSON(result);
            getProducts();
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

      function deleteProduct(pid) {
        toastr.warning("<button type='button' id='confirmBtn' class='btn btn-danger btn-sm' style='width:40%;display:inline;margin:3px;'>Yes</button><button type='button' id='closeBtn' class='btn btn-default btn-sm' style='width:40%;display:inline;margin:3px;'>No</button>",'Do you want to delete this product?',{
            closeButton: true,
            allowHtml: true,
            onShown: function (toast) {
              $("#confirmBtn").click(function(){
                $.ajax({
                  type: "POST",
                  url: "<?=base_url()?>deleteProduct",
                  data: 'pro_id='+pid,
                  success: function(result) {
                      var responsedata = $.parseJSON(result);
                      if (responsedata.status=='success') {
                        getProducts();
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

      function addProductImg(id) {
        var form = document.createElement("form");
        form.setAttribute("method", "post");
        form.setAttribute("action", "<?=base_url()?>add_img_page");

        hiddenField = document.createElement("input");
        hiddenField.setAttribute("type", "hidden");
        hiddenField.setAttribute("name", "product_id");
        hiddenField.setAttribute("value", id);
        hiddenField1 = document.createElement("input");
        hiddenField1.setAttribute("type", "hidden");
        hiddenField1.setAttribute("name", "product_table");
        hiddenField1.setAttribute("value", 'products');
        form.appendChild(hiddenField);
        form.appendChild(hiddenField1);

        document.body.appendChild(form);
        form.submit();
      }

      function editProduct(id) {
        var form = document.createElement("form");
        form.setAttribute("method", "post");
        form.setAttribute("action", "<?=base_url()?>edit_product_page");

        hiddenField = document.createElement("input");
        hiddenField.setAttribute("type", "hidden");
        hiddenField.setAttribute("name", "product_id");
        hiddenField.setAttribute("value", id);
        form.appendChild(hiddenField);

        document.body.appendChild(form);
        form.submit();
      }

      function editSubProducts(id) {
        var form = document.createElement("form");
        form.setAttribute("method", "post");
        form.setAttribute("action", "<?=base_url()?>edit_sub_products");

        hiddenField = document.createElement("input");
        hiddenField.setAttribute("type", "hidden");
        hiddenField.setAttribute("name", "product_id");
        hiddenField.setAttribute("value", id);
        form.appendChild(hiddenField);

        document.body.appendChild(form);
        form.submit();
      }
      function createHtmlName(string){
            string = string.toLowerCase();
            string = string.replace(/[^a-zA-Z0-9_-]/g, '-');
            string = string.replace("(", "");
            string = string.replace(")", "");
            string = string.replace("---","-");
            string = string.replace("--","-");
            return string;
        }
    </script>
  </body>
</html>