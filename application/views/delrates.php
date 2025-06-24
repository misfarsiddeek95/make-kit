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
              <?php if($add_rate){?>
              <div class="panel-tools">
                <button type="button" class="btn btn-outline-primary m-w-120" onclick="addRate();">Add Rate</button>
              </div>
              <?php }?>
              <h3 class="m-t-0 m-b-5">Delivery Rates</h3>
            </div>
            <div class="panel-body">
              <div class="page-layouts">
                <div class="row">
                  <div id="controllers">
                        <div class="col-lg-3 col-sm-3 col-xs-12 m-y-5">
                          <select class="form-control" data-plugin="select2" id="countryFilter" onchange="getRegion(this.value,'regionFilter');">
                          <option></option>
                          <?php foreach ($countries as $row) {?>
                            <option value="<?=$row->country_id?>"><?=$row->nicename?></option>
                          <?php } ?>
                        </select>
                        </div>
                        <div class="col-lg-3 col-sm-3 col-xs-12 m-y-5">
                            <select id="regionFilter" class="custom-select" data-plugin="select2" onchange="getCities(this.value,'cityFilter');" style="border-color: #1d87e4;">
                              <option></option>
                            </select>
                        </div>
                        <div class="col-lg-3 col-sm-3 col-xs-12 m-y-5">
                            <select id="cityFilter" class="custom-select" data-plugin="select2" style="border-color: #1d87e4;">
                              <option></option>
                            </select>
                        </div>
                        <div class="col-lg-1 col-sm-3 col-xs-12 m-y-5">
                            <button class="btn btn-primary" style="width: 100%;" onclick="getOrderByStatus();">Filter</button>
                        </div>
                        <div class="col-lg-2 col-sm-3 col-xs-12 m-y-5">
                            <select id="limit_sel" class="custom-select" onchange="getOrderByStatus();" style="border-color: #1d87e4;">
                                <option value="10" selected="selected">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>
                </div>
              </div>

              <div class="table-responsive m-y-5">
                <table class="table table-bordered">
                  <thead id="thead_data">
                    <tr>
                      <th>Country</th>
                      <th>All Of Country</th>
                      <th>Region</th>
                      <th>All Of Region</th>
                      <th>City</th>
                      <th>Initial Charge</th>
                      <th>Charge per KG</th>
                      <?php if($edit_rate){?>
                      <th></th>
                      <?php } if($delete_rate){?>
                      <th></th>
                      <?php }?>
                    </tr>
                  </thead>
                  <tbody id="tbody_data">
                    
                  </tbody>
                </table>
              </div>
            </div>
            <nav>
              <ul class="pagination pagination-rounded m-l-10" id="pagination_ul"></ul>
            </nav>
          </div>
          <input type="hidden" id="offset_field" value="0">

        </div>

        <div id="otherModal1" class="modal fade" role="dialog">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">
                    <i class="zmdi zmdi-close"></i>
                  </span>
                </button>
                <h4 class="modal-title" id="modalTitle">Add Rate</h4>
              </div>
              <div class="modal-body">
                <form  id="inputmasks" data-toggle="validator">
                  <input type="hidden" class="form-control" name="rateMId" id="rateMId" value="">

                  <div class="form-group col-md-8 hiddenDivClz" style="margin: 0px;padding: 0px;">
                    <label for="form-control-3" class="control-label">Country</label>
                    <select class="form-control" data-plugin="select2" name="fromCountry" id="fromCountry" onchange="getRegion(this.value,'fromRegion');" data-required-error="Country is Required" style="width: 100%;" required>
                      <option></option>
                      <?php foreach ($countries as $row) {?>
                        <option value="<?=$row->country_id?>"><?=$row->nicename?></option>
                      <?php } ?>
                    </select>
                  </div>

                  <div class="form-group col-md-4 hiddenDivClz">
                    <label for="form-control-3" class="control-label" style="display: block;">&nbsp;</label>
                    <label class="custom-control custom-control-primary custom-checkbox">
                      <input class="custom-control-input" type="checkbox" name="fromCountryAll" id="fromCountryAll" value="1">
                      <span class="custom-control-indicator"></span>
                      <span class="custom-control-label">All Of Country</span>
                    </label>
                  </div>

                  <div class="form-group col-md-8 hiddenDivClz" style="margin: 0px;padding: 0px;">
                    <label for="form-control-3" class="control-label">Region</label>
                    <select class="form-control" data-plugin="select2" name="fromRegion" id="fromRegion" onchange="getCities(this.value,'fromCity');" data-required-error="Region is Required" style="width: 100%;" required>  
                      <option></option>
                    </select>
                  </div>

                  <div class="form-group col-md-4 hiddenDivClz">
                    <label for="form-control-3" class="control-label" style="display: block;">&nbsp;</label>
                    <label class="custom-control custom-control-primary custom-checkbox">
                      <input class="custom-control-input" type="checkbox" name="fromRegionAll" id="fromRegionAll" value="1">
                      <span class="custom-control-indicator"></span>
                      <span class="custom-control-label">All Of Region</span>
                    </label>
                  </div>

                  <div class="form-group col-md-8 hiddenDivClz" style="margin: 0px;padding: 0px;">
                    <label for="form-control-3" class="control-label">City</label>
                    <select class="form-control" data-plugin="select2" name="fromCity" id="fromCity" style="width: 100%;">
                      <option></option>
                    </select>
                  </div>

                  <div class="form-group col-md-6" style="margin: 0px;padding: 0px;">
                    <label for="form-control-3" class="control-label">Initial Charge</label>
                    <input type="text" placeholder="Initial Charge" value="" name="initialCharge" id="initialCharge" class="form-control" data-inputmask="'alias': 'decimal', 'groupSeparator': ',', 'autoGroup': true, 'rightAlign': false, 'allowMinus': false, 'allowPlus': false" data-required-error="Initial Charge is Required" required> 
                  </div>

                  <div class="form-group col-md-6" style="margin: 0px;padding: 0px;">
                    <label for="form-control-3" class="control-label">Charge per KG</label>
                    <input type="text" placeholder="Charge per KG" value="" name="chargePerKG" id="chargePerKG" class="form-control" data-inputmask="'alias': 'decimal', 'groupSeparator': ',', 'autoGroup': true, 'rightAlign': false, 'allowMinus': false, 'allowPlus': false" data-required-error="Charge per KG is Required" required>
                  </div>

                  <div class="text-center">
                    <button type="submit" class="btn btn-primary" style="width: 50%;margin: 15px 0 0;">Add Rate</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <?php $this->load->view('includes/footer'); ?>
    </div>
    <?php $this->load->view('includes/javascripts'); ?>
    <script src="<?=base_url()?>assets/js/forms-form-masks.js"></script>
    <script src="<?=base_url()?>assets/js/forms-plugins.js"></script>
    <script type="text/javascript">
      $( document ).ready(function() {
        getDelChargeData();
      });

      $("#regionFilter").on("select2:unselecting", function(e) {
        $("#regionFilter,#cityFilter").empty();
      });
      $("#cityFilter").on("select2:unselecting", function(e) {
        $(this).empty();
      });

      $("#fromCountry,#countryFilter").select2({
        placeholder: "Select a Country",
        allowClear: true
      });

      $("#fromRegion,#regionFilter").select2({
        placeholder: "Select a Region",
        allowClear: true
      });

      $("#fromCity,#cityFilter").select2({
        placeholder: "Select a City",
        allowClear: true
      });

      function getRegion(val,id) {
        if (val!=''&&val!=null) {
          $.ajax({
            type: "POST",
            url: "<?=base_url()?>getRegion",
            data: 'country='+val,
            success: function(result) {
              var responsedata = $.parseJSON(result);
              $("#"+id).empty();
              $("#"+id).append("<option></option>");
              for (var i = 0; i < responsedata.length; i++) {
                if (responsedata[i].region_name!='') {
                  $("#"+id).append($("<option></option>").attr("value",responsedata[i].reg_id).text(responsedata[i].region_name));
                }
              }
              $("#"+id).select2({
                placeholder: "Select a Region",
                allowClear: true
              });
            },
            error: function(result) {
              alert('error');
            }
          });
        }
      }

      function getCities(val,id) {
        if (val!=''&&val!=null) {
          $.ajax({
            type: "POST",
            url: "<?=base_url()?>getCities",
            data: 'region='+val,
            success: function(result) {
              var responsedata = $.parseJSON(result);
              $("#"+id).empty();
              $("#"+id).append("<option></option>");
              for (var i = 0; i < responsedata.length; i++) {
                if (responsedata[i].city_name!='') {
                  $("#"+id).append($("<option></option>").attr("value",responsedata[i].city_id).text(responsedata[i].city_name));
                }
              }
              $("#"+id).select2({
                placeholder: "Select a City",
                allowClear: true
              });
            },
            error: function(result) {
              alert('error');
            }
          });
        }
      }

      $('#inputmasks').validator().on('submit', function (e) {
        if (!(e.isDefaultPrevented())) {
          e.preventDefault();
          run_waitMe('#inputmasks');
          $.ajax({
              type: "POST",
              url: "<?=base_url()?>addDelCharges",
              data: $('#inputmasks').serialize(),
              success: function(result) {
                  var responsedata = $.parseJSON(result);
                  if (responsedata.status=='success') {
                    $('#otherModal1').modal('hide');
                    document.getElementById('inputmasks').reset();
                    $("#fromCity,#fromRegion").empty();
                    $('#inputmasks').validator('destroy').validator();
                    getDelChargeData();
                    toastr.success(responsedata.message)
                  }else{
                    toastr.error(responsedata.message)
                  }
                  $('#inputmasks').waitMe('hide');
              },
              error: function(result) {
                  toastr.error("Somthing went wrong :(")
              }
          });
        }
      });

      function getDelChargeData() {
        var limits = parseInt($('#limit_sel').val());
        var offset = parseInt($('#offset_field').val());
        $.ajax({
          type: "POST",
          url: "<?=base_url()?>getDelCharges",
          data: 'country='+$('#countryFilter').val()+'&region='+$('#regionFilter').val()+'&city='+$('#cityFilter').val()+'&limit='+limits+'&offset='+offset,
          success: function(result) {
              var responsedata = $.parseJSON(result);
                $('#tbody_data,#pagination_ul').empty();
                var tbody=document.getElementById('tbody_data');
                if (responsedata.rowcount==0) {
                  $('#tbody_data').append('<tr><td colspan="9" class="text-center">No Results</td></tr>');
                }else{
                  for (var i = 0; i < responsedata.del_rates.length; i++) {
                    var tr=document.createElement("tr");
                    tr.id="del_rate_id_"+responsedata.del_rates[i]['charges_id'];
                    var region = '-';
                    var city = '-';
                    var AOC = 'No';
                    var AOR = 'No';
                    if (responsedata.del_rates[i]['state_id']!=0||responsedata.del_rates[i]['reg_id']!=null) {
                      region = responsedata.del_rates[i]['region_name'];
                    }
                    if (responsedata.del_rates[i]['city_id']!=0||responsedata.del_rates[i]['city_name']!=null) {
                      city = responsedata.del_rates[i]['city_name'];
                    }
                    if (responsedata.del_rates[i]['all_of_country']==1) {
                      AOC = 'Yes';
                    }
                    if (responsedata.del_rates[i]['all_of_state']==1) {
                      AOR = 'Yes';
                    }

                    var inside ='<td class="text-center">'+responsedata.del_rates[i]['nicename']+'</td>'+
                                '<td class="text-center">'+AOC+'</td>'+
                                '<td class="text-center">'+region+'</td>'+
                                '<td class="text-center">'+AOR+'</td>'+
                                '<td class="text-center">'+city+'</td>'+
                                '<td class="text-center">'+responsedata.del_rates[i]['initial_charge']+'</td>'+
                                '<td class="text-center">'+responsedata.del_rates[i]['charge_per_kg']+'</td>'+
                                <?php if($edit_rate){?>
                                '<td class="text-center"><button type="button" class="btn btn-outline-primary btn-xs" onclick="updateDelCharge('+responsedata.del_rates[i]['charges_id']+');"><i class="fa fa-pencil"></i></button></td>'+
                                <?php } if($delete_rate){?>
                                '<td class="text-center"><button type="button" class="btn btn-outline-danger btn-xs" onclick="deleteDelCharge('+responsedata.del_rates[i]['charges_id']+');"><i class="fa fa-trash"></i></button></td>'+
                                <?php }?>
                                '';
                            tr.innerHTML=inside;
                              tbody.appendChild(tr);
                  }

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

      function addRate() {
        $('.hiddenDivClz').show();
        $('#modalTitle').text('Add Rate');
        $('#rateMId').val('');
        document.getElementById('inputmasks').reset();
        $('#fromCountry').trigger('change');
        $('#fromRegion,#fromCity').attr('required', true);
        $("#fromRegion,#fromCity").prop('disabled', false);
        $("#fromCity,#fromRegion").empty();
        $('#inputmasks').validator('destroy').validator();
        $('#otherModal1').modal('show');
      }

      function updateDelCharge(id) {
        $('.hiddenDivClz').hide();
        $('#fromCountry,#fromRegion,#fromCity').attr('required', false);
        var initial_charge = $('#del_rate_id_'+id).find("td").eq(5).html();  
        var charge_per_kg = $('#del_rate_id_'+id).find("td").eq(6).html();
        $('#rateMId').val(id);
        $('#initialCharge').val(initial_charge);
        $('#chargePerKG').val(charge_per_kg);
        $('#modalTitle').text('Edit Rate');
        $('#otherModal1').modal('show');
      }

      $("#fromCountryAll").change(function() {
        var ischecked= $(this).is(':checked');
        if(!ischecked){
          $('#fromRegion,#fromCity').attr('required', true);
          $("#fromRegion,#fromCity").prop('disabled', false);
        }else{
          $('#fromRegion,#fromCity').attr('required', false);
          $("#fromRegion,#fromCity").prop('disabled', true);
        }
      });

      $("#fromRegionAll").change(function() {
        var ischecked= $(this).is(':checked');
        var isDisabled = $('#fromRegion').prop('disabled');
        if(ischecked||isDisabled){
          $('#fromCity').attr('required', false);
          $("#fromCity").prop('disabled', true);
        }else{
          $('#fromCity').attr('required', true);
          $("#fromCity").prop('disabled', false);
        }
      });

      function set_offset(value) {
        $('#offset_field').val(value);
        getDelChargeData();
      }
      function getOrderByStatus() {
        $('#offset_field').val(0);
        getDelChargeData();
      }

      function deleteDelCharge(id) {
        toastr.warning("<button type='button' id='confirmBtn' class='btn btn-danger btn-sm' style='width:40%;display:inline;margin:3px;'>Yes</button><button type='button' id='closeBtn' class='btn btn-default btn-sm' style='width:40%;display:inline;margin:3px;'>No</button>",'Do you want to delete this rate?',{
            closeButton: false,
            allowHtml: true,
            onShown: function (toast) {
              $("#confirmBtn").click(function(){
                $.ajax({
                  type: "POST",
                  url: "<?=base_url()?>deleteDelCharge",
                  data: 'rate_id='+id,
                  success: function(result) {
                      var responsedata = $.parseJSON(result);
                      if (responsedata.status=='success') {
                        getDelChargeData();
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