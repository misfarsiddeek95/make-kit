<!DOCTYPE html>
<html lang="en">
  <head>
    <?php $this->load->view('includes/head'); ?>
    <style type="text/css">
      .attrcls{
        margin: 0 5px 0 0; 
      }
      .colored{
        width: 18px;
        height: 18px;
        margin:auto;
        display:inline-block;
        border: 1px solid #e6e6e6;
        vertical-align: middle;
        border-radius: 18px; 
      }
      .otherVal{
        width: 18px;
        height: 18px;
        border: 1px solid #e6e6e6;
        margin:auto;
        padding: 2px;
      }
      #tbody_data .form-group{
        margin: 0;
        padding: 0;
      }
      .tbImg{
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
      #imageShow {
        text-align: center;
        padding: 0!important;
      }

      #imageShow:before {
        content: '';
        display: inline-block;
        height: 100%;
        vertical-align: middle;
        margin-right: -4px;
      }

      #imageShow .modal-dialog {
        display: inline-block;
        text-align: left;
        vertical-align: middle;
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
            <div class="panel-tools">
              <button type="button" class="btn btn-outline-primary m-w-120" onclick="getAttrbutes();">Add Sub Product</button>
            </div>
            <h3 class="m-t-0 m-b-5">Edit Sub Products</h3>
          </div>
          <!-- <?=$product->name?> -->
          </pre>

          <div class="table-responsive">
            <form data-toggle="validator" id="inputmasks" name="subproductform" action="<?= base_url('update_sub_products')?>" method="post">
              <input type="hidden" name="proName" value="$product->name">
              <table class="table m-b-10">
                <thead>
                  <tr>
                    <th></th>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Price(POI)</th>
                    <th>Image</th>
                    <th>Delete</th>
                  </tr>
                </thead>
                <tbody id="tbody_data">
                  <?php 
                    $count = 0;
                    foreach ($subPros as $subPro) {
                      $img = 'default.jpg';
                      $func = '';
                      $clz = '';
                      if ($subPro->photo_path!=null) {
                        $img = 'products/'.$subPro->photo_path.'-sma.jpg';
                        $func = 'onclick="show_img(this.src);"';
                        $clz = 'tbImg';
                      }
                  ?>
                      <tr style="padding: 0;margin: 0;" id="subProGridtr<?=$count?>" class="subPro_tr<?=$subPro->sub_pro_id?>">
                      <td><img class="img-rounded <?=$clz?>" src="<?=base_url();?>photos/<?=$img?>" alt="<?=$subPro->photo_title;?>" height="32" <?=$func?>></td>
                      <td><div class="form-group">
                        <input type="hidden" value="<?=$subPro->sub_pro_id?>" id="subid[]" name="subproduct[<?=$count?>][subid]">
                        <input type="text" pattern="^[a-zA-Z 0-9 .&+-]*$" value="<?= $subPro->sub_name?>" placeholder="Name" id="subname[]" name="subproduct[<?=$count?>][subname]" class="form-control" data-minlength="3" data-pattern-error="Invalid Name" data-error="Minimum of 3 characters" data-required-error="Name is Required" required>
                        
                      </td>
                      <td><div class="form-group">
                        <input type="text" pattern="^([a-zA-Z0-9_-]){2,25}$" value="<?= $subPro->sub_pro_code?>" placeholder="Sub Product Code" id="proCode[]" name="subproduct[<?=$count?>][subproductCod]" class="form-control"  data-remote-error="Product Code already Exist, Try another" data-pattern-error="Invalid Product Code">
                        
                      </td>
                      <td><div class="form-group">
                        <input type="number" placeholder="Quantity" value="<?= $subPro->stock?>" name="subproduct[<?=$count?>][quantity]" id="proQty[]" class="form-control" data-minlength="1" data-error="Quantity is invalid" data-required-error="Quantity is Required" required> 
                        
                      </td>
                      <td><div class="form-group">
                        <input type="text" placeholder="Price" value="<?= $subPro->sub_price?>" name="subproduct[<?=$count?>][sub_price]" id="proPrice[]" class="form-control" data-inputmask="'alias': 'decimal', 'groupSeparator': ',', 'autoGroup': true, 'rightAlign': false, 'allowMinus': false, 'allowPlus': false" data-required-error="Price is Required" required>
                      </td>
                      <td><div class="form-group">
                        <input type="text" placeholder="POI price" value="<?= $subPro->sub_price_poi?>" name="subproduct[<?=$count?>][poi_price]" id="proPOIPrice[]" class="form-control" data-inputmask="'alias': 'decimal', 'groupSeparator': ',', 'autoGroup': true, 'rightAlign': false, 'allowMinus': false, 'allowPlus': false"> 
                        
                      </td>
                      <td><button type="button" class="btn btn-outline-primary btn-sm" title="Upload Image" onclick="addProductImg(<?=$subPro->sub_pro_id?>);"><i class="zmdi zmdi-upload"></i></button></td>
                      <td><button type="button" class="btn btn-outline-danger btn-sm" title="Delete" onclick="deleteSubPro(<?=$subPro->sub_pro_id?>);"><i class="zmdi zmdi-delete"></i></button></td>
                    </tr>
                    <tr>
                      <td colspan="8" id="subProSpecGridtr<?=$count?>" class="subPro_tr<?=$subPro->sub_pro_id?>">
                        <?php 
                          $c2 = 0;
                          foreach ($subPro->specs as $spec) {
                        ?>
                            
                            <?php
                              if ($spec->type==4||$spec->type==3) {
                            ?>
                                <!--<input type="hidden" name="subproduct[<?=$count?>][specs][<?= $c2?>][attrid]" value="<?= $spec->attr_id?>">-->
                                <input type="hidden" name="subproduct[<?=$count?>][specs][<?= $spec->attr_id?>]" value="<?=$spec->av_id?>">
                                <span class="attrcls" attr-id="<?= $spec->attr_id?>"><?=$spec->attribute?> : <span class="colored" style="background: <?=$spec->value?>" av-id="<?=$spec->av_id?>"></span></span>
                            <?php
                              }else{
                            ?>
                                <!--<input type="hidden" name="subproduct[<?=$count?>][specs][<?= $c2?>][attrid]" value="<?= $spec->attr_id?>">-->
                                <input type="hidden" name="subproduct[<?=$count?>][specs][<?= $spec->attr_id?>]" value="<?=$spec->av_id?>">
                                <span class="attrcls" attr-id="<?= $spec->attr_id?>"><?=$spec->attribute?> : <span class="otherVal" av-id="<?=$spec->av_id?>"><?=$spec->value?></span></span>
                            <?php
                              }
                            ?>
                            
                        <?php
                            $c2++;
                          }
                        ?>
                        <!--<span class="attrcls">Color : <span class="colored" style="background: #ff6699"></span></span>
                        <span class="attrcls">Size : <span class="otherVal">XL</span></span>-->
                        
                      </td>
                    </tr>
                  <?php
                      $count++;
                    }
                  ?>
                  <!-- <tr style="padding: 0;margin: 0;">
                    <td><img class="img-rounded" src="<?=base_url();?>photos/default.jpg" alt="" height="32"></td>
                    <td><div class="form-group">
                      <input type="text" pattern="^[a-zA-Z 0-9 .&+-]*$" value="" placeholder="Name" id="subname[]" name="subname[]" class="form-control" data-minlength="3" data-pattern-error="Invalid Name" data-error="Minimum of 3 characters" data-required-error="Name is Required" required>
                      
                    </td>
                    <td><div class="form-group">
                      <input type="text" pattern="^([a-zA-Z0-9_-]){2,25}$" value="" placeholder="Sub Product Code" id="proCode[]" name="proCode[]" class="form-control"  data-remote-error="Product Code already Exist, Try another" data-pattern-error="Invalid Product Code">
                      
                    </td>
                    <td><div class="form-group">
                      <input type="number" placeholder="Quantity" value="1" name="proQty[]" id="proQty[]" class="form-control" data-minlength="1" data-error="Quantity is invalid" data-required-error="Quantity is Required" required> 
                      
                    </td>
                    <td><div class="form-group">
                      <input type="text" placeholder="Price" value="0" name="proPrice[]" id="proPrice[]" class="form-control" data-inputmask="'alias': 'decimal', 'groupSeparator': ',', 'autoGroup': true, 'rightAlign': false, 'allowMinus': false, 'allowPlus': false" data-required-error="Price is Required" required>
                    </td>
                    <td><div class="form-group">
                      <input type="text" placeholder="POI price" value="0" name="proPOIPrice[]" id="proPOIPrice[]" class="form-control" data-inputmask="'alias': 'decimal', 'groupSeparator': ',', 'autoGroup': true, 'rightAlign': false, 'allowMinus': false, 'allowPlus': false"> 
                      
                    </td>
                    <td><button type="button" class="btn btn-outline-primary" title="Upload Image"><i class="zmdi zmdi-upload zmdi-hc-lg"></i></button></td>
                    <td><button type="button" class="btn btn-outline-danger" title="Upload Image"><i class="zmdi zmdi-delete zmdi-hc-lg"></i></button></td>
                  </tr>
                  <tr>
                    <td colspan="8">
                      <span class="attrcls">Color : <span class="colored" style="background: #ff6699"></span></span>
                      <span class="attrcls">Size : <span class="otherVal">XL</span></span>
                    </td>
                  </tr> -->
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="4"></td>
                    <td colspan="2"><button type="submit" class="btn btn-primary btn-block" id="submitBtn">Submit</button></td>
                    <td colspan="2"><button class="btn btn-default btn-block" onclick="location.href='<?=base_url()?>products/view_products'">Cancel</button></td>
                  </tr>
                </tfoot>
              </table>
            </form>
          </div>


        </div>
        
      </div>

      <?php $this->load->view('includes/footer'); ?>
      <input type="hidden" id="pro_name" value="<?=$product->name?>">
      <input type="hidden" id="pro_code" value="<?=$product->pro_code?>">
      <input type="hidden" id="pro_price" value="<?=$product->price?>">
      <input type="hidden" id="pro_price_poi" value="<?=$product->price_poi?>">
      <input type="hidden" id="pro_quantity" value="<?=$product->quantity?>">

      <div id="otherModal3" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm">
          <div class="modal-content">
            <div class="modal-header bg-primary">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">
                  <i class="zmdi zmdi-close"></i>
                </span>
              </button>
              <h4 class="modal-title" id="modal-title">Attributes</h4>
            </div>
            <input type="hidden" name="" id="getSelAtrNames" value="">
            <input type="hidden" name="" id="getSelAtrId" value="">
            <form data-toggle="validator" id="attrmasks">
              <div class="modal-body">
                <input type="hidden" name="pro_id" id="pro_id" value="<?=$pro_id?>">
                <div id="attrDiv">
                	
                </div>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Add</button>
                <button type="button" data-dismiss="modal" class="btn btn-default">Close</button>
              </div>
            </form>
          </div>
        </div>
      </div>

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
    <script src="<?=base_url()?>assets/js/forms-form-masks.js"></script>
    <script src="<?=base_url()?>assets/js/forms-plugins.js"></script>
    
    <script type="text/javascript">
      var rowCount = <?= $count?>;
      $('#attrmasks').find(':input').each(function () {
        $(this).inputmask();
      });

      var test="";

      function getAttrbutes() {
        var id = $("#pro_id").val();
        $.ajax({
          type: "POST",
          url: "<?=base_url()?>getProAttr",
          data: 'pro_id='+id,
          success: function(result) {
            var responsedata = $.parseJSON(result);
            var attr = "";
            $("#attrDiv").empty();
            for (var i = 0; i < responsedata.attributes.length; i++) {
              attr+='<div class="form-group attrClz" attr-id="'+responsedata.attributes[i].attr_id+'" attr-type="'+responsedata.attributes[i].type+'">'+
                    '<label for="form-control-3" class="control-label">'+responsedata.attributes[i].attribute+'</label>';
              if(responsedata.attributes[i].type==4||responsedata.attributes[i].type==3){
                test = responsedata.attributes[i].type;
                attr+='<ul class="pro_color_sel">';
                for (var j = 0; j < responsedata.attribute_val.length; j++) {
                  if (responsedata.attribute_val[j].attr_id==responsedata.attributes[i].attr_id) {
                    attr+='<li>'+
                          '<input type="hidden" class="" name="attribute['+responsedata.attributes[i].description+']" id="'+responsedata.attribute_val[j].description+'" value="'+responsedata.attribute_val[j].description+'" atv-val="'+responsedata.attribute_val[j].description+'" required/>'+
                          '<input type="radio" class="" name="attribute['+responsedata.attributes[i].attr_id+']" id="'+responsedata.attribute_val[j].av_id+'" value="'+responsedata.attribute_val[j].av_id+'" atv-val="'+responsedata.attribute_val[j].value+'" required/>'+
                          '<label for="'+responsedata.attribute_val[j].av_id+'" style="background-color:'+responsedata.attribute_val[j].value+';" title="'+responsedata.attribute_val[j].description+'"></label></li>';
                  }
                }
                attr+='</ul>';
              }else{
                var selType = 'data-required-error="'+responsedata.attributes[i].attribute+' is Required" required';
                var attrName = 'attribute['+responsedata.attributes[i].attr_id+']';
                attr+='<select class="form-control selectcls" data-plugin="select2" onchange="appendAttr()" multiple="multiple" data-placeholder="Select a '+responsedata.attributes[i].attribute+'" name="'+attrName+'" id="'+attrName+'" '+selType+' style="width:100%;"><option></option>';
                for (var j = 0; j < responsedata.attribute_val.length; j++) {
                  if (responsedata.attribute_val[j].attr_id==responsedata.attributes[i].attr_id) {
                    attr+='<option value="'+responsedata.attribute_val[j].av_id+'" title="'+responsedata.attribute_val[j].description+'">'+responsedata.attribute_val[j].value+'</option>';
                  }
                }
                attr+='</select>';
                test = test+responsedata.attributes[i].type;
              }
              attr+='<div class="help-block with-errors"></div></div>';
            }
            $("#attrDiv").append(attr);
            $('.selectcls').select2();
            $('#attrmasks').validator('destroy').validator();
            $('#otherModal3').modal('show');
          },
          error: function(result) {
            toastr.error("Somthing went wrong :(")
          }
        });
      }

      $('#attrmasks').validator().on('submit', function (e) {
        if (!(e.isDefaultPrevented())) {
          e.preventDefault();

          if ($('.attrClz').length) {
            var attr = '';
            var status = true;
            var pro_name = $("#pro_name").val();
            var pro_code = $("#pro_code").val();
            var pro_price = $("#pro_price").val();
            var pro_price_poi = $("#pro_price_poi").val();
            var pro_quantity = $("#pro_quantity").val();
            // if (type==4||type==3) {
              // alert(test);
              if(test == 4 || test == 3){
                var attr_name = $(this).find('input:checked').prev('input').val();
                var count = $(this).find('option:selected').text();
                var times = (count.match(/l/g) || []).length;

                // alert(times);
              }else if(test == 1 || test == 2){
                var attr_name = $(this).find('option:selected').text();
                var count = $(this).find('option:selected').text();
                var times = (count.match(/l/g) || []).length;

              }else{
                var attr_name = $(this).find('input:checked').prev('input').val();
                attr_name = attr_name + " " + $(this).find('option:selected').text();
                var count = $(this).find('option:selected').text();
                var times = (count.match(/l/g) || []).length;

              }

                var attrValArray = $("#getSelAtrNames").val().split(",");
                var attrNamArray = $("#getSelAtrId").val().split("l");
                // var strArray = a.split(",");
                // alert(attrNamArray[0]);

                	// alert("working");

              test = "";
              if (times<1) {
              	times = 1;
              }
            // }else{
            //   var attr_name = $(this).find('option:selected').text();
            // }
            var addArray = {subname:'', subproductCod:'', quantity:'', sub_price:'', poi_price: ''};

            for (var i = 0; i < times; i++) {
            
            
            attr += '<tr style="padding: 0;margin: 0;" id="subProGridtr'+rowCount+'" class="insertedSubPro_tr'+rowCount+'">'+
                    '<td><img class="img-rounded" src="<?=base_url();?>photos/default.jpg" alt="" height="32"></td>'+
                    '<td><div class="form-group">'+
                      '<input type="hidden" value="0" id="subid[]" name="subproduct['+rowCount+'][subid]">'+
                      '<input type="text" pattern="^[a-zA-Z 0-9 .&+-]*$" value="'+pro_name+" - "+attr_name+'" placeholder="Name" id="subname[]" name="subproduct['+rowCount+'][subname]" class="form-control" data-minlength="3" data-pattern-error="Invalid Name" data-error="Minimum of 3 characters" data-required-error="Name is Required" required>'+
                    '</td>'+
                    '<td><div class="form-group">'+
                      '<input type="text" pattern="^([a-zA-Z0-9_-]){2,25}$" value="'+pro_code+'" placeholder="Sub Product Code" id="proCode[]" name="subproduct['+rowCount+'][subproductCod]" class="form-control"  data-remote-error="Product Code already Exist, Try another" data-pattern-error="Invalid Product Code">'+
                    '</td>'+
                    '<td><div class="form-group">'+
                      '<input type="number" placeholder="Quantity" value="'+pro_quantity+'" name="subproduct['+rowCount+'][quantity]" id="proQty[]" class="form-control" data-minlength="1" data-error="Quantity is invalid" data-required-error="Quantity is Required" required>'+
                    '</td>'+
                    '<td><div class="form-group">'+
                      '<input type="text" placeholder="Price" value="'+pro_price+'" name="subproduct['+rowCount+'][sub_price]" id="proPrice[]" class="form-control prizeMask" data-required-error="Price is Required" required>'+
                    '</td>'+
                    '<td><div class="form-group">'+
                      '<input type="text" placeholder="POI price" value="'+pro_price_poi+'" name="subproduct['+rowCount+'][poi_price]" id="proPOIPrice[]" class="form-control prizeMask">'+
                    '</td>'+
                    '<td><button type="button" class="btn btn-outline-primary" title="Upload Image" disabled="disabled"><i class="zmdi zmdi-upload zmdi-hc-lg"></i></button></td>'+
                    '<td><button type="button" class="btn btn-outline-danger" title="Delete" onclick="deleteInsertedRow('+rowCount+');"><i class="zmdi zmdi-delete zmdi-hc-lg"></i></button></td></tr><tr class="insertedSubPro_tr'+rowCount+'" id="subProSpecGridtr'+rowCount+'"><td colspan="8">';
            $('.attrClz').each(function() {
              var attrid  = $(this).attr('attr-id');
              var attrr  = $(this).find('.control-label').text();
              var type  = $(this).attr('attr-type');
              if(addArray['specs'] == null || addArray['specs'] == undefined){
                addArray['specs'] = {};
              }
              
              attr+='<span class="attrcls" attr-id="'+attrid+'">'+attrr+' : ';
              if (type==4||type==3) {
                var atval = $(this).find('input:checked').val();
                var atvaltext = $(this).find('input:checked').attr('atv-val');
                attr+='<input type="hidden" name="subproduct['+rowCount+'][specs]['+attrid+']" value="'+atval+'">';
                attr+='<span class="colored" style="background: '+atvaltext+'" av-id="'+atval+'"></span>';
                addArray['specs'][attrid]=  atval;
              }else{
                var atval = $(this).find('option:selected').val();
                var atvaltext = $(this).find('option:selected').text();
                attr+='<input type="hidden" name="subproduct['+rowCount+'][specs]['+attrid+']" value="'+attrValArray[i]+'">';
                attr+='<span class="otherVal" av-id="'+attrValArray[i]+'">'+attrNamArray[i]+'</span>';
                addArray['specs'][attrid]=  atval;
              }
              attr+='</span>';
              if (atval==null||atval==''||atval=='undefined'||atvaltext==null||atvaltext==''||atvaltext=='undefined') {
                status = false;
                return false; 
              }
            });
            attr+='</td></tr>';
          	
          	// new count if there are more selected values in size
          	if (times>1) {
          		rowCount++
          	}

          	}
            if (status) {
              
              var pro_id = $("#pro_id").val();
              var data = $('.attrClz select,.attrClz input[type=radio]').serializeArray();
              data.push({name: "pro_id", value: pro_id});
              // alert(count);
              $.ajax({
                type: "POST",
                url: "<?=base_url()?>checkSubPro",
                data: data,
                success: function(result) {
                  var responsedata = $.parseJSON(result);
                  if(responsedata['error']){
                    toastr.error(responsedata['error'])
                  }else{
                    $('#tbody_data').append(attr);
                    $('.prizeMask').inputmask({'alias': 'decimal', 'groupSeparator': ',', 'autoGroup': true, 'rightAlign': false, 'allowMinus': false, 'allowPlus': false});
                    $('#inputmasks').validator('destroy').validator();
                    $('#otherModal3').modal('hide');
                    rowCount++;
                  }
                },
                error: function(result) {
                  toastr.error("Somthing went wrong :(")
                }
              });
            }
          }
        }
      });

      $( "#inputmasks" ).submit(function( event ) {
        event.preventDefault();
        var respArray=[];

        var xmlhttp;
        if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
          xmlhttp=new XMLHttpRequest();
        }else{// code for IE6, IE5
          xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        
        xmlhttp.onreadystatechange=function(){
            if (xmlhttp.readyState==4 && xmlhttp.status==200){
                  respArray = eval('('+xmlhttp.responseText+')');
                  var status = validateOnSubmit(respArray);
                  if (status){
                    xmlhttp;
                    if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
                      xmlhttp=new XMLHttpRequest();
                    }else{// code for IE6, IE5
                      xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
                    }

                    xmlhttp.onreadystatechange=function(){
                        if (xmlhttp.readyState==4 && xmlhttp.status==200){
                              responsedata = eval('('+xmlhttp.responseText+')');
                              if(responsedata['error']){
                                toastr.error(responsedata['error'])
                              }else{
                                setTimeout(function(){
                                  location.reload();
                                }, 500);
                                toastr.success(responsedata['success'])
                              }
                        }
                    }

                    xmlhttp.open("POST","<?= base_url('update_sub_products')?>",true);
                    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                    xmlhttp.send($("form").serialize());
                  }
            }
        }
        xmlhttp.open("POST","<?= base_url('convert-to-array')?>",true);
        xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xmlhttp.send($("form[name*='subproductform']").serialize());
  
        /*$.ajax({
                type: "POST",
                url: "<?=base_url()?>update_sub_products",
                data: {data: $('#inputmask').serialize()},
                success: function(result) {
                  var responsedata = $.parseJSON(result);
                  if(responsedata['error']){
                    toastr.error(responsedata['error'])
                  }else{
                  }
                },
                error: function(result) {
                  toastr.error("Somthing went wrong :(")
                }
              });*/
        //alert(JSON.stringify($('form').serialize());
        //alert(document.subproductform.subproduct);

        /*var str = decodeURIComponent($("form[name*='subproductform']").serialize());
        str = str.replace("&", ",");
        str = str.replace("=", ":");

        alert(decodeURIComponent($("form[name*='subproductform']").serialize()));*/
      });

      function validateOnSubmit(dataArray){
        for (var i = 0; i <dataArray.length; i++) {
          var source = dataArray[i];

          //console.log("1) "+JSON.stringify(source));
          if(source== null || source== undefined){
            //console.log("2) source null or undifined");
            continue;
          }

          for (var j= i+1; j <dataArray.length; j++) {
            var sub2 = dataArray[j];
            //console.log("3) "+JSON.stringify(sub2));
            if(sub2== null ||sub2== undefined){
              //console.log("4) sub2 null or undifined");
              continue;
            }

            var exact = true; 
            var attr_arr = Object.keys(source['specs']);
            //console.log("5) "+JSON.stringify(attr_arr));
            for(var k = 0; k <attr_arr.length; k++) {
                var key = attr_arr[k];
                //console.log("6) key"+key);
                //console.log("7) "+source['specs'][key]+" "+sub2['specs'][key]);
                if(source['specs'][key] != sub2['specs'][key]){
                  //console.log("8) exact = false");
                  exact = false;
                  break;
                }
            }
            if(exact){
              toastr.error("Same sub product exist.")
              return false;
            }
          }
        }
        return true;
      }

      function validateOnAdd(addArray,dataArray){
          var source = addArray;

          for (var j= 0; j <dataArray.length; j++) {
            var sub2 = dataArray[j];
            if(sub2== null ||sub2== undefined){
              continue;
            }

            var exact = true; 
            var attr_arr = Object.keys(source['specs']);
            for(var k = 0; k <dataArray.length; k++) {
                var key = attr_arr[k];
                if(source['specs'][key] != sub2['specs'][key]){
                  exact = false;
                  break;
                }
            }
            if(exact){
              return false;
            }
          }
        return true;
      }

      function deleteInsertedRow(id){
          toastr.warning("<button type='button' id='confirmBtn' class='btn btn-danger btn-sm' style='width:40%;display:inline;margin:3px;'>Yes</button><button type='button' id='closeBtn' class='btn btn-default btn-sm' style='width:40%;display:inline;margin:3px;'>No</button>",'Do you want to remove this sub product?',{
            closeButton: true,
            allowHtml: true,
            onShown: function (toast) {
              $("#confirmBtn").click(function(){
                $('.insertedSubPro_tr'+id).fadeTo("slow",0.7, function(){
                  $('.insertedSubPro_tr'+id).remove();
                });
              });
              $("#closeBtn").click(function(){
                toastr.clear()
              });
            }
        });
      }

      function deleteSubPro(id) {
        toastr.warning("<button type='button' id='confirmBtn' class='btn btn-danger btn-sm' style='width:40%;display:inline;margin:3px;'>Yes</button><button type='button' id='closeBtn' class='btn btn-default btn-sm' style='width:40%;display:inline;margin:3px;'>No</button>",'Do you want to delete this sub product?',{
            closeButton: true,
            allowHtml: true,
            onShown: function (toast) {
              $("#confirmBtn").click(function(){
                $.ajax({
                  type: "POST",
                  url: "<?=base_url()?>deleteSubProduct",
                  data: 'id='+id,
                  success: function(result) {
                      var responsedata = $.parseJSON(result);
                      if (responsedata.status=='success') {
                        $('.subPro_tr'+id).fadeTo("slow",0.7, function(){
                          $('.subPro_tr'+id).remove();
                        });
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

      function show_img(src) {
        run_waitMe('#imageShow');
        var res = src.replace("-sma.jpg", "-std.jpg");
        $("#largeImg").attr("src",res);
        $('#imageShow').waitMe('hide');
        $("#imageShow").modal('show');
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
        hiddenField1.setAttribute("value", 'sub_product');
        form.appendChild(hiddenField);
        form.appendChild(hiddenField1);

        document.body.appendChild(form);
        form.submit();
      }
      function appendAttr(){
      	var attrVal = $(".selectcls").val();
      	var attrNam = $(".selectcls option:selected").text().toLowerCase();
      	// attrNam += ',';
      	// alert(attrNam);
      	$("#getSelAtrNames").val(attrVal);
      	$("#getSelAtrId").val(attrNam);
      }
    </script>
  </body>
</html>