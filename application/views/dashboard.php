<!DOCTYPE html>
<html lang="en">
  <head>
    <?php $this->load->view('includes/head'); ?>
    <style>
      .br-8 {
        border-radius: 8px;
      }
    </style>
  </head>
  <body class="layout layout-header-fixed layout-left-sidebar-fixed">
    <?php $this->load->view('includes/topbar'); ?>
    <div class="site-main">
      <?php $this->load->view('includes/sidebar'); ?>
      
      <div class="site-content">
        <div class="row">
          <div class="col-md-4 col-sm-4">
            <div class="widget widget-tile-2 bg-info m-b-30 br-8">
              <div class="wt-content p-a-20 p-b-50">
                <div class="wt-title">Total Success Orders</div>
                <div class="wt-number"><?=$totalorders?></div>
                <div class="wt-text"><?=$newordercount?> order(s)  on <?=date('F')?></div>
              </div>
              <div class="wt-icon">
                <i class="zmdi zmdi-shopping-basket"></i>
              </div>
              <div class="wt-chart">
                <span id="peity-chart-3"><?php echo implode(', ', array_map(function ($object) { return $object->ordercount; }, $priviousOrders)); ?></span>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-sm-4">
            <div class="widget widget-tile-2 bg-warning m-b-30 br-8">
              <div class="wt-content p-a-20 p-b-50">
                <div class="wt-title">Total Product(s)</div>
                <div class="wt-number"><?=$totalprolist?></div>
                <div class="wt-text"><?=$productcount?> items added on <?=date('F')?></div>
              </div>
              <div class="wt-icon">
                <i class="zmdi zmdi-view-module"></i>
              </div>
              <div class="wt-chart">
                <span id="peity-chart-2"><?php echo implode(', ', array_map(function ($object) { return $object->procount; }, $procountlist)); ?></span>
              </div>
            </div>
          </div>          
          <div class="col-md-4 col-sm-4">
            <div class="widget widget-tile-2 bg-primary m-b-30 br-8">
              <div class="wt-content p-a-20 p-b-50">
                <div class="wt-title">Total user(s)
                  <span class="t-caret text-success">
                    <i class="zmdi zmdi-caret-up"></i>
                  </span>
                </div>
                <div class="wt-number"><?=$totalusercount?></div>
                <div class="wt-text"><?=$usercount?> signup(s) on <?=date('F')?> <!-- | Updated: <?=date('h:i a', time())?> --></div>
              </div>
              <div class="wt-icon">
                <i class="zmdi zmdi-accounts"></i>
              </div>
              <div class="wt-chart">
                <span id="peity-chart-1"><?php echo implode(', ', array_map(function ($object) { return $object->custlist; }, $totaluserlist)); ?></span>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <a href="<?=base_url()?>/products/view_Outproducts/9">
          <div class="col-md-4 col-sm-4">
            <div class="widget widget-tile-2 bg-danger m-b-30 br-8">
              <div class="wt-content p-a-20 p-b-50">
                <div class="wt-title">Out of Stocks</div>
                <div class="wt-number"><?=$outOfStocks?></div>
                <div class="wt-text">click here to check product quantity</div>
              </div>
              <div class="wt-icon">
                <i class="zmdi zmdi-square-down"></i>
              </div>
              <div class="wt-chart">
               
              </div>
            </div>
          </div>
          </a>  
        </div>
       
        </div>
      </div>

      <?php $this->load->view('includes/footer'); ?>
    </div>
    <?php $this->load->view('includes/javascripts'); ?>
    <script src="<?=base_url()?>assets/js/dashboard-2.js"></script>
    
  </body>
</html>