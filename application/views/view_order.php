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
      #order_pros_data tr td{
        padding: 5px 0px 5px 5px;
      }
      #hidetable, #hiddenDetails{
      	visibility: hidden;
      }
      #tableContent label{
      	font-weight: normal !important;
      }

      @media print {
          body * {
            visibility: hidden;
          }
          /*#items, #product_table, #receiver_address *{
            visibility: visible;
          }*/
          #hidetable *{
            visibility: visible;
            top: 0;
            margin-top: 0px;

          }
          #hiddenDetails *{
            visibility: visible;
            top: 0;
            margin-top: 0px;

          }
        }
    </style>
  </head>
  <body class="layout layout-header-fixed layout-left-sidebar-fixed">
    <?php $this->load->view('includes/topbar'); ?>
    <div class="site-main">
      <?php $this->load->view('includes/sidebar'); ?>
      
      <div class="site-content">
        <div class="profile">
        	<div class="row" id="hiddenDetails">
        		<div class="col-md-8 col-sm-5" id="fancyAddress">
        		<img src="<?=base_url()?>assets/img/logoBottom.png" height="50px">	
        		</div>
        		<div>
        			<div class="col-md-4 col-sm-5">
        			<label style="margin-left: 30px;"><b>Order Date</b></label><span style="margin-left: 15px;"> <?=$orderStatus[0]->status_date?></span><br>
        			<label style="margin-left: 30px;"><b>Order Id</b></label><span style="margin-left: 30px;"> <?=$orderDetail->order_code?></span><br>
        		</div>
        		</div>
        	</div>
        	<div class="row" style="margin-top: 30px;">
        		
        	</div>
        	<div id="hidetable">
          	<table width="100%" border="3px;" bordercolor="#ddd">
          		<thead>
          			 <tr>
                        <th style="padding: 20px;" width="60%">From</th>
                        <th style="padding: 20px;">Ship To</th>
                      </tr>
          		</thead>
          		<tbody>
          			<tr>
                        <th style="padding: 20px;" id="tableContent">
		        			<label>FANCY POINT (PVT) LTD</label><br>
		        			<label>85,GALLE ROAD,</label><br>
		        			<label>WELLEWATTA,</label><br>
		        			<label>COLOMBO-06,</label><br>
		        			<label>SRI LANKA,</label><br>
		        			<label>Telephone: +94 (0)11 2599877</label><br>
		        			<label>Fax: +94 (0)11 2505710</label><br>
		        			<label>fancypoint123@gmail.com</label><br>
		        			<label>https://www.fancypoint.lk</label><br></th>
                        <th style="">
                        	<div class="panel-body p-info" style="margin-top: -77px;">
			                  <div class="pi-item">
			                    <div class="pii-icon">
			                      <i class="zmdi zmdi-face"></i>
			                    </div>
			                    <div class="pii-value"><?=$address->fname.' '.$address->lname?></div>
			                  </div>
			                  <div class="pi-item">
			                    <div class="pii-icon">
			                      <i class="zmdi zmdi-home"></i>
			                    </div>
			                    <div class="pii-value"><?=$address->address.', <br>'.$address->city_name.', '.$address->region_name.', <br>'.$address->nicename?></div>
			                  </div>
			                  <div class="pi-item">
			                    <div class="pii-icon">
			                      <i class="zmdi zmdi-phone"></i>
			                    </div>
			                    <div class="pii-value"><?=$address->phone?></div>
			                  </div>
			                  <div class="pi-item">
			                    <div class="pii-icon">
			                      <i class="zmdi zmdi-email"></i>
			                    </div>
			                    <div class="pii-value"><?=$address->order_email?></div>
			                  </div>
			                </div>
                        </th>
                      </tr>
          		</tbody>
          	</table>
          </div>
        	<div id="hidetable" style="margin-top: 10px;">
          	<table width="100%" class="table table-hover" >
          		<thead>
          			 <tr>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Total</th>
                      </tr>
          		</thead>
          		<tbody>
          			<?php foreach ($orderProducts as $row) {
                        $img = 'default.jpg';
                        $name = $row->name;
                        $code = $row->pro_code;
                        if ($row->photo_path!=null) {
                          $img = 'products/'.$row->photo_path.'-sma.jpg';
                        }
                        if ($row->sub_pro_id!=0) {
                          $name = $row->name;
                        }
                      ?>
          			<tr>
                        <td><?=$name?></td>
                        <td><?=$code?></td>
                        <td><?=$row->billed_unit_price?></td>
                        <td><?=$row->qty?></td>
                        <td><?=$row->billed_unit_price*$row->qty?></td>
                      </tr>
                     <?php } ?>
                     <?php 
	                    $order_total = ($orderDetail->cart_total+$orderDetail->del_charge)-$orderDetail->discount; 
	                    $balance_amount = $order_total-$orderDetail->paid_total;
	                  ?>
                     <tr>
                     	<td colspan="4" align="right"><b>Cart Total</b></td>
                     	<td><b><?=$orderDetail->cart_total?></b></td>
                     </tr>
                     <tr>
                     	<td colspan="4" align="right" style="border-top: 1px solid #fff;">Delivery Charge</td>
                     	<td><?=$orderDetail->del_charge?></td>
                     </tr>
                     <tr>
                     	<td colspan="4" align="right" style="border-top: 1px solid #fff;">Discount</td>
                     	<td><?=$orderDetail->discount?></td>
                     </tr>
                     <tr>
                     	<td colspan="4" align="right" style="border-top: 1px solid #fff;"><b>Order Total</b></td>
                     	<td><?=number_format((float)$order_total, 2, '.', '')?></td>
                     </tr>
                     <tr>
                     	<td colspan="4" align="right" style="border-top: 1px solid #fff;">Paid Total</td>
                     	<td><b><?=$orderDetail->paid_total?></b></td>
                     </tr>
                     <tr>
                     	<td colspan="4" align="right" style="border-top: 1px solid #fff;"><b>Balance</b></td>
                     	<td style=" border-bottom: 3px solid #000;"><b><?=number_format((float)$balance_amount, 2, '.', '')?></b></td>
                     </tr>
          		</tbody>
          	</table>
          </div>
          <div class="row gutter-sm" style="top: 10px; position: absolute;">
            <div class="col-md-4 col-sm-5" id="receiver_address">

              <?php if($profile&&$view_cust){?>
              <div class="p-about m-b-20" id="profile">
                <div class="pa-padding">
                  <div class="pa-avatar">
                    <?php 
                      $img = 'user_default.jpg';
                      if ($profile->photo_path!=null) {
                        $img = 'customers/'.$profile->photo_path.'-sma.jpg';
                      }
                    ?>
                    <img src="<?=base_url();?>photos/<?=$img?>" alt="" width="100" height="100">
                  </div>
                  <div class="pa-name"><?=$profile->fname.' '.$profile->lname?>
                    <?php if ($profile->email_verified==1) {?>
                    <i class="zmdi zmdi-check-circle text-success m-l-5"></i>
                    <?php } ?>
                  </div>
                  <div><?=$profile->address.' '.$profile->city_name.', '.$profile->region_name.', '.$profile->nicename?></div>
                  <div><?=$profile->email?></div>
                  <div><?=$profile->mobile?></div>
                </div>
              </div>
              <?php } ?>
              
              <?php if($rec_det){?>
              <div class="panel panel-default panel-table">
                <div class="panel-heading">
                  <?php if($editAddress){?>
                  <div class="panel-tools">
                    <a href="#orderDelAddressModal" data-toggle="modal" class="tools-icon">
                      <i class="zmdi zmdi-edit"></i>
                    </a>
                  </div>
                  <?php } ?>
                  <h3 class="panel-title">Receiver Details</h3>
                </div>
                <div class="panel-body p-info">
                  <div class="pi-item">
                    <div class="pii-icon">
                      <i class="zmdi zmdi-face"></i>
                    </div>
                    <div class="pii-value"><?=$address->fname.' '.$address->lname?></div>
                  </div>
                  <div class="pi-item">
                    <div class="pii-icon">
                      <i class="zmdi zmdi-home"></i>
                    </div>
                    <div class="pii-value"><?=$address->address.', '.$address->city_name.', '.$address->region_name.', '.$address->nicename?></div>
                  </div>
                  <div class="pi-item">
                    <div class="pii-icon">
                      <i class="zmdi zmdi-phone"></i>
                    </div>
                    <div class="pii-value"><?=$address->phone?></div>
                  </div>
                  <div class="pi-item">
                    <div class="pii-icon">
                      <i class="zmdi zmdi-email"></i>
                    </div>
                    <div class="pii-value"><?=$address->order_email?></div>
                  </div>
                </div>
              </div>
              <?php } ?>

              <div class="panel panel-default panel-table" id="items">
                <div class="panel-heading">
                  <?php if($editDelCharge||$editDisc||$editPaidAmount){?>
                  <div class="panel-tools">
                    <a href="#orderPaymentModal" data-toggle="modal" class="tools-icon">
                      <i class="zmdi zmdi-edit"></i>
                    </a>
                  </div>
                  <?php } ?>
                  <h3 class="panel-title">Order Payment Details</h3>
                </div>
                <table class="table table-hover">
                  <?php 
                    $order_total = ($orderDetail->cart_total+$orderDetail->del_charge)-$orderDetail->discount; 
                    $balance_amount = $order_total-$orderDetail->paid_total;
                  ?>
                  <tr><td>Cart Total</td><td><?=$orderDetail->cart_total?></td></tr>
                  <tr><td>Delivery Charge</td><td><?=$orderDetail->del_charge?></td></tr>
                  <tr><td>Discount</td><td><?=$orderDetail->discount?></td></tr>
                  <tr><td><b>Order Total</td></b><td><b><?=number_format((float)$order_total, 2, '.', '')?></b></td></b></tr>
                  <tr><td>Paid Total</td><td><?=$orderDetail->paid_total?></td></tr>
                  <tr><td>Balance</td><td><?=number_format((float)$balance_amount, 2, '.', '')?></td></tr>
                </table>
              </div>


              <div class="panel panel-default panel-table" id="status_of_payment">
                <div class="panel-heading">
                  <h3 class="panel-title">Order Status</h3>
                  <div class="panel-subtitle">What happend to this order</div>
                </div>
                <div class="panel-body">
                  <div class="widget-activity">
                    <?php foreach ($orderStatus as $row) {
                      /*$date1 = new DateTime();
                      $date2 = $date1->diff(new DateTime($row->status_date));
                      $days = '';
                      if ($date2->y!=0) {
                        $days =+ $date2->y.' years'."\n";
                      }
                      if ($date2->y!=0&&$date2->m==0) {
                        $days =+ $date2->m.' months'."\n";
                      }elseif ($date2->y==0&&$date2->m!=0) {
                        $days =+ $date2->m.' months'."\n";
                      }
                      if () {
                        # code...
                      }
                      echo $date2->d.' days'."\n";
                      echo $date2->h.' hours'."\n";
                      echo $date2->i.' minutes'."\n";
                      echo $date2->s.' seconds'."\n";*/
                      $badge = 'success';
                      $icon = '';
                      if ($row->os_id==1) {
                        $badge = 'warning';
                        $icon = 'help';
                      }elseif ($row->os_id==2) {
                        $badge = 'danger';
                        $icon = 'close-circle';
                      }elseif ($row->os_id==3) {
                        $icon = 'shopping-basket';
                      }elseif ($row->os_id==4) {
                        $icon = 'case-check';
                      }elseif ($row->os_id==5) {
                        $icon = 'truck';
                      }elseif ($row->os_id==6) {
                        $icon = 'grid-off';
                        $badge = 'danger';
                      }elseif ($row->os_id==7) {
                        $icon = 'home';
                      }elseif ($row->os_id==8) {
                        $icon = 'long-arrow-return';
                        $badge = 'danger';
                      }
                    ?>
                    <div class="wa-item">
                      <div class="wai-icon bg-<?=$badge?>">
                        <i class="zmdi zmdi-<?=$icon?>"></i>
                      </div>
                      <div class=""><span class="text-primary"><?=$row->status?></span></div>
                      <div class="wai-time"><?=$row->status_date?></div>

                    </div>
                    <?php } ?>

                  </div>
                  <?php if($order_status||$pay_status){ ?>
                  <button type="button" class="btn btn-primary btn-block" onclick="editStatus();">Change status</button>
                  <?php } ?>
                </div>
              </div>

            </div>
            <div class="col-md-8 col-sm-7">

              <div class="panel panel-default panel-table">
                <div class="panel-heading">
                  <h3 class="m-t-0 m-b-5">Ordered Products</h3>
                  <button type="button" class="btn btn-outline-warning btn-labeled" id="prnt" onclick="print()">Print
                  <span class="btn-label btn-label-right p-x-10">
                    <i class="zmdi zmdi-print"></i>
                  </span>
           		  </button>
                </div>
                <div class="panel-body">
                  <div class="table-responsive" id="product_table">
                    <table class="table">
                      <thead>
                        <tr>
                          <th></th>
                          <th>Name</th>
                          <th>Code</th>
                          <th>User</th>
                          <th>Price</th>
                          <th>Qty</th>
                          <th>Total</th>
                          <!-- <th></th> -->
                        </tr>
                      </thead>
                      <tbody id="order_pros_data">
                        <?php foreach ($orderProducts as $row) {
                          $img = 'default.jpg';
                          $name = $row->name;
                          $code = $row->pro_code;
                          if ($row->photo_path!=null) {
                            $img = 'products/'.$row->photo_path.'-sma.jpg';
                          }
                          if ($row->sub_pro_id!=0) {
                            $name = $row->name;
                          }
                        ?>
                        <tr>
                          <td><img class="img-rounded" src="<?=base_url();?>photos/<?=$img?>" alt="" height="32"></td>
                          <td><?=$name?></td>
                          <td><?=$code?></td>
                          <td><?=$row->fname.' '.$row->lname?></td>
                          <td><?=$row->billed_unit_price?></td>
                          <td><?=$row->qty?></td>
                          <td><?=$row->billed_unit_price*$row->qty?></td>
                          <!-- <td><button type="button" class="btn btn-primary btn-sm" title="Edit Order"><i class="zmdi zmdi-edit"></i></button>
                            <button type="button" class="btn btn-danger btn-sm" title="Edit Order"><i class="zmdi zmdi-delete"></i></button></td> -->
                        </tr>
                        <?php if(!empty($row->attributes)){?>
                        <tr>
                          <td colspan="9">
                            <?php foreach ($row->attributes as $row2){
                              if ($row2->type==4||$row2->type==3) {?>
                                <span class="attrcls"><?=$row2->attribute?> : <span class="colored" style="background: <?=$row2->value?>"></span></span>
                              <?php }else{ ?>
                                <span class="attrcls"><?=$row2->attribute?> : <span class="otherVal"><?=$row2->value?></span></span>
                            <?php } } ?>
                          </td>
                        </tr>
                        <?php }} ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>

      <?php $this->load->view('includes/footer'); ?>
      <?php if($editDelCharge||$editDisc||$editPaidAmount){?>
      <div id="orderPaymentModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm">
          <div class="modal-content">
            <div class="modal-header bg-primary">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">
                  <i class="zmdi zmdi-close"></i>
                </span>
              </button>
              <h4 class="modal-title" id="modal_assign_title">Edit Order Payment Details</h4>
            </div>

            <form data-toggle="validator" id="inputmasks">
              <div class="modal-body">
                <input type="hidden" name="order_id" id="order_id" value="<?=$orderDetail->order_id?>">

                <div class="form-group">
                  <label for="form-control-4" class="control-label">Cart Total</label>
                  <input type="text" placeholder="Cart Total" value="<?=$orderDetail->cart_total?>" name="oCartTotal" id="oCartTotal" class="form-control" disabled> 
                  <div class="help-block with-errors"></div>
                </div>
                <div class="form-group">
                  <label for="form-control-4" class="control-label">Delivery Charge </label>
                  <input type="text" placeholder="Delivery Charge" value="<?=$orderDetail->del_charge?>" name="oDelCharge" id="oDelCharge" class="form-control" data-inputmask="'alias': 'decimal', 'groupSeparator': ',', 'autoGroup': true, 'rightAlign': false, 'allowMinus': false, 'allowPlus': false" <?php if ($editDelCharge) {echo 'data-required-error="Delivery Charge is Required" required';}else{echo "disabled";}?>> 
                  <div class="help-block with-errors"></div>
                </div>
                <div class="form-group">
                  <label for="form-control-4" class="control-label">Discount</label>
                  <input type="text" placeholder="Discount" value="<?=$orderDetail->discount?>" name="oDiscount" id="oDiscount" class="form-control" data-inputmask="'alias': 'decimal', 'groupSeparator': ',', 'autoGroup': true, 'rightAlign': false, 'allowMinus': false, 'allowPlus': false" <?php if (!$editDisc) {echo "disabled";}?>> 
                  <div class="help-block with-errors"></div>
                </div>
                <hr>
                <div class="form-group">
                  <label for="form-control-4" class="control-label">Order Total</label>
                  <input type="text" placeholder="Order Total" value="<?=number_format((float)$order_total, 2, '.', '')?>" name="oOrderTotal" id="oOrderTotal" class="form-control" disabled> 
                  <div class="help-block with-errors"></div>
                </div>
                <div class="form-group">
                  <label for="form-control-4" class="control-label">Paid Total</label>
                  <input type="text" placeholder="Paid Total" value="<?=$orderDetail->paid_total?>" name="oPaidTotal" id="oPaidTotal" class="form-control" data-inputmask="'alias': 'decimal', 'groupSeparator': ',', 'autoGroup': true, 'rightAlign': false, 'allowMinus': false, 'allowPlus': false" <?php if ($editPaidAmount) {echo 'data-required-error="Paid Total is Required" required';}else{echo "disabled";}?>> 
                  <div class="help-block with-errors"></div>
                </div>
                <hr>
                <div class="form-group">
                  <label for="form-control-4" class="control-label">Balance</label>
                  <input type="text" placeholder="Balance" value="<?=number_format((float)$balance_amount, 2, '.', '')?>" name="oBalance" id="oBalance" class="form-control" disabled> 
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
      <?php } ?>

      <?php if($editAddress){?>
      <div id="orderDelAddressModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
          <div class="modal-content">
            <div class="modal-header bg-primary">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">
                  <i class="zmdi zmdi-close"></i>
                </span>
              </button>
              <h4 class="modal-title" id="modal_assign_title">Edit Delivery Details</h4>
            </div>

            <form data-toggle="validator" id="delAddressMask">
              <div class="modal-body">
                <input type="hidden" name="delorder_id" id="delorder_id" value="<?=$orderDetail->order_id?>">
                <input type="hidden" name="add_id" id="add_id" value="<?=$address->add_id?>">

                <?php if ($updateDelCharge) {?>
                <div class="form-group">
                  <label for="form-control-3" class="control-label">Update Delivery Charge</label>
                  <input type="checkbox" class="js-switch" name="change_del" id="change_del" value="1" data-size="small" data-color="#34a853" checked="checked">
                  <div class="help-block with-errors"></div>
                </div>
                <?php } ?>

                <div class="form-group">
                  <label for="form-control-3" class="control-label">First Name</label>
                  <input type="text" pattern="^[a-zA-Z. ]+$" value="<?=$address->fname?>" placeholder="First Name" id="fName" name="fName" class="form-control" data-minlength="3" data-pattern-error="Invalid First Name" data-error="Minimum of 3 characters" data-required-error="First Name is Required" required>
                  <div class="help-block with-errors"></div>
                </div>

                <div class="form-group">
                  <label for="form-control-3" class="control-label">Last Name</label>
                  <input type="text" pattern="^[a-zA-Z. ]+$" value="<?=$address->lname?>" placeholder="Last Name" id="lName" name="lName" class="form-control" data-minlength="3" data-pattern-error="Invalid Last Name" data-error="Minimum of 3 characters" data-required-error="Last Name is Required" required>
                  <div class="help-block with-errors"></div>
                </div>
                
                <div class="form-group">
                  <label for="form-control-3" class="control-label">Country</label>
                  <select class="form-control" data-plugin="select2" name="country" id="country" data-required-error="Country is Required" style="width: 100%;" onchange="getRegion();" required>
                    <option></option>
                    <?php foreach ($countries as $row) {
                      $sel = '';
                      if ($row->country_id==$address->country_id) {
                        $sel = 'selected="selected"';
                      }
                    ?>
                      <option value="<?=$row->country_id?>" <?=$sel?>><?=$row->nicename?></option>
                    <?php } ?>
                  </select>
                  <div class="help-block with-errors"></div>
                </div>

                <div class="form-group">
                  <label for="form-control-3" class="control-label">Region</label>
                  <select class="form-control" data-plugin="select2" name="region" id="region" data-required-error="Region is Required" style="width: 100%;" onchange="getCities();" required>
                    <option></option>
                  </select>
                  <div class="help-block with-errors"></div>
                </div>

                <div class="form-group">
                  <label for="form-control-4" class="control-label">Address</label>
                  <textarea name="address" id="address" class="form-control" rows="3" placeholder="Address" data-error="Address is required." required><?=$address->address?></textarea>
                  <div class="help-block with-errors"></div>
                </div>

                <div class="form-group">
                  <label for="form-control-3" class="control-label">City</label>
                  <select class="form-control" data-plugin="select2" name="city" id="city" data-required-error="City is Required" style="width: 100%;" required>
                    <option></option>
                    
                  </select>
                  <div class="help-block with-errors"></div>
                </div>

                <div class="form-group">
                  <label for="form-control-4" class="control-label">Mobile</label>
                  <input type="number" placeholder="Mobile number" name="mobile" id="mobile" value="<?=$address->phone?>" class="form-control" data-minlength="9" data-error="Mobile number is invalid" data-required-error="Mobile number is Required" required> 
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
      <?php } ?>

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
                <input type="hidden" name="sorder_id" id="sorder_id" value="<?=$orderDetail->order_id?>">
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
    <script src="<?=base_url()?>assets/js/forms-form-masks.js"></script>
    <script src="<?=base_url()?>assets/js/forms-plugins.js"></script>
    <script type="text/javascript">
      $( document ).ready(function() {
        getRegion(<?=$address->reg_id?>);
      });

      $('#delAddressMask').find(':input').each(function () {
        $(this).inputmask();
      });

      $("#country").select2({
        placeholder: "Select User Country"
      });

      $("#region").select2({
        placeholder: "Select User Region"
      });

      $("#city").select2({
        placeholder: "Select User City"
      });

      /*$('#change_del').change(function() {
        if($(this).is(":checked")) {
            $(this).val(0);
        }else{
          $(this).val(1);
        }
      });*/

      function getRegion(selectOpt='') {
        var country = $("#country").val();
        $.ajax({
          type: "POST",
          url: "<?=base_url()?>getRegion",
          data: 'country='+country,
          success: function(result) {
            var responsedata = $.parseJSON(result);
            $("#region").empty();
            $("#region").append("<option></option>");
            for (var i = 0; i < responsedata.length; i++) {
              if (responsedata[i].region_name!='') {
                $("#region").append($("<option></option>").attr("value",responsedata[i].reg_id).text(responsedata[i].region_name));
              }
            }
            if (selectOpt!='') {
                $("#region").val(selectOpt);
                getCities(<?=$address->city_id?>);
            }
            $("#region").select2({
              placeholder: "Select User Region"
            });
          },
          error: function(result) {
            alert('error');
          }
        });
      }

      function getCities(selectOpt='') {
        var region = $("#region").val();
        $.ajax({
          type: "POST",
          url: "<?=base_url()?>getCities",
          data: 'region='+region,
          success: function(result) {
            var responsedata = $.parseJSON(result);
            $("#city").empty();
            $("#city").append("<option></option>");
            for (var i = 0; i < responsedata.length; i++) {
              if (responsedata[i].city_name!='') {
                $("#city").append($("<option></option>").attr("value",responsedata[i].city_id).text(responsedata[i].city_name));
              }
            }
            if (selectOpt!='') {
              $("#city").val(selectOpt);
              <?php if(!(empty($user))){?>
              $('#inputmasks').validator('validate');
              <?php } ?>
            }
            $("#city").select2({
              placeholder: "Select User City"
            });
          },
          error: function(result) {
            alert('error');
          }
        });
      }

      function orderPayment() {
        var cartTotal = parseFloat($('#oCartTotal').val().replace(',', ''));
        var delCharge = parseFloat($('#oDelCharge').val().replace(',', ''));
        var discount = parseFloat($('#oDiscount').val().replace(',', ''));
        var paidTotal = parseFloat($('#oPaidTotal').val().replace(',', ''));

        var orderTotal = parseFloat((cartTotal+delCharge)-discount).toFixed(2);;
        var balance = parseFloat(orderTotal - paidTotal).toFixed(2);;

        $('#oOrderTotal').val(orderTotal);
        $('#oBalance').val(balance);
      }
      
      $('#inputmasks input').blur(function(){
          if( !$(this).val() ) {
            $(this).val(0);
          }
          orderPayment()
      });

      $('#inputmasks').validator().on('submit', function (e) {
        if (!(e.isDefaultPrevented())) {
          e.preventDefault();
          run_waitMe('#inputmasks');
          $.ajax({
            type: "POST",
            url: "<?=base_url()?>updateOrderPayment",
            data: $('#inputmasks').serialize(),
            success: function(result) {
              var responsedata = $.parseJSON(result);
              if(responsedata.status=='success'){
                toastr.success(responsedata.message)
                setTimeout(function(){
                  $("#orderPaymentModal").modal('hide');
                  location.reload();
                }, 300);
              }else{
                toastr.error(responsedata.message)
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

      $('#delAddressMask').validator().on('submit', function (e) {
        if (!(e.isDefaultPrevented())) {
          e.preventDefault();
          run_waitMe('#delAddressMask');
          $.ajax({
            type: "POST",
            url: "<?=base_url()?>updateOrderAddr",
            data: $('#delAddressMask').serialize(),
            success: function(result) {
              var responsedata = $.parseJSON(result);
              if(responsedata.status=='success'){
                toastr.success(responsedata.message)
                setTimeout(function(){
                  $("#orderDelAddressModal").modal('hide');
                  location.reload();
                }, 300);
              }else{
                toastr.error(responsedata.message)
              }
              $('#delAddressMask').waitMe('hide');
            },
            error: function(result) {
              $('#delAddressMask').waitMe('hide');
              toastr.error('Error :'+result)
            }
          });
        }
      });

      function editStatus() {
        var id = $("#sorder_id").val();
        $.ajax({
          type: "POST",
          url: "<?=base_url()?>getOrderStatus",
          data: 'order_id='+id,
          success: function(result) {
            var responsedata = $.parseJSON(result);
            if (responsedata.status=='error') {
              toastr.error(responsedata.message)
            }else{
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
                toastr.success(responsedata.message)
                setTimeout(function(){
                  $("#status_modal").modal('hide');
                  location.reload();
                }, 300);
              }else if(responsedata.status=='error'){
                toastr.error(responsedata.message)
              }else{
                toastr.error("Somthing went wrong :(")
              }
              $('#statusUpdateMask').waitMe('hide');
            },
            error: function(result) {
              $('#statusUpdateMask').waitMe('hide');
              toastr.error('Error :'+result)
            }
          }); 
        }
      });

      function PrintElem(elem)
		{
		    var mywindow = window.open('', 'PRINT', 'height=400,width=600');

		    mywindow.document.write('<html><head><title>' + document.title  + '</title>');
		    mywindow.document.write('</head><body >');
		    mywindow.document.write('<h1>' + document.title  + '</h1>');
		    mywindow.document.write(document.getElementById(elem).innerHTML);
		    mywindow.document.write('</body></html>');

		    mywindow.document.close(); // necessary for IE >= 10
		    mywindow.focus(); // necessary for IE >= 10*/

		    mywindow.print();
		    mywindow.close();

		    return true;
		}
    </script>
  </body>
</html>