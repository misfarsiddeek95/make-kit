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
              <?php if($add_page){?>
              <div class="panel-tools">
                <button type="button" class="btn btn-outline-primary m-w-120" onclick="location.href='<?=base_url();?>add_page'">Add Page</button>
              </div>
              <?php }?>
              <h3 class="m-t-0 m-b-5">Page Management</h3>
            </div>
            <div class="panel-body"> 
              <div class="table-responsive m-y-5">
                <table class="table table-hover" id="table-1">
                  <thead>
                    <tr>
                      <th></th>
                      <th>Website</th>
                      <th>Name</th>
                      <th>Page Title</th>
                      <th>Headline</th>
                      <th>Second Title</th>
                      <th>create_date</th>
                      <?php if($edit_page){?>
                      <th>Edit</th>
                      <?php } if($add_photo){ ?>
                      <th>Upload Image</th>
                      <?php } ?>
                    </tr>
                  </thead>
                  <tbody id="tbody_data">
                    <?php foreach ($pages as $row) { 
                      $img = 'default.jpg';
                      if ($row->photo_path!=null) {
                        $img = 'pages/'.$row->photo_path.'-sma.jpg';
                      }

                      switch ($row->website) {
                        case '1':
                          $webtitle = 'Fancypoint';
                          break;
                        case '2':
                          $webtitle = 'Cosmoline';
                          break;
                        case '3':
                          $webtitle = 'UScosmo';
                          break;
                        default:
                          $webtitle = 'All';
                          break;
                      }
                    ?>

                    <tr id="pagerow<?=$row->page_id?>">
                      <td><img class="img-rounded" src="<?=base_url();?>photos/<?=$img?>" height="32"></td>
                      <td><?=$webtitle?></td>
                      <td><?=$row->name;?></td>
                      <td><?=$row->page_title;?></td>
                      <td><?=$row->headline;?></td>
                      <td><?=$row->second_title;?></td>
                      <td><?=$row->create_date;?></td>
                      <?php if($edit_page){?>
                      <td><button type="button" class="btn btn-outline-primary btn-sm" title="Edit Page" onclick="editPage(<?=$row->page_id?>);"><i class="zmdi zmdi-edit"></i></button></td>
                      <?php } if($add_photo){ ?>
                      <td><button type="button" class="btn btn-outline-primary btn-sm" title="Upload Image" onclick="addPageImg(<?=$row->page_id?>);"><i class="zmdi zmdi-upload"></i></button></td>
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
    </div>
    <?php $this->load->view('includes/javascripts'); ?>
    <script type="text/javascript">
      $('#table-1').DataTable();

      var dive = $('#table-1_wrapper').find('div.row:first');
      dive.find('div:nth-child(2)').removeClass('col-sm-6').addClass('col-sm-3')
      var html = '<div class="col-sm-3">'+
                    '<select class="form-control" id="siteFilter" onchange="filterSite()" style="width:100%;height:28px;">'+
                      '<option value="" disabled="" selected="">filter by site</option>'+
                      '<option value="All">All</option>'+
                      '<option value="Fancypoint">Fancypoint</option>'+
                      '<option value="Cosmoline">Cosmoline</option>'+
                      '<option value="UScosmo">UScosmo</option>'+
                      '<option value="">Clear</option>'+
                    '</select>'+
                  '</div>';
      dive.find('div:first').after(html)

      function editPage(id) {
        var form = document.createElement("form");
        form.setAttribute("method", "post");
        form.setAttribute("action", "<?=base_url()?>add_page");

        hiddenField = document.createElement("input");
        hiddenField.setAttribute("type", "hidden");
        hiddenField.setAttribute("name", "page_id");
        hiddenField.setAttribute("value", id);
        form.appendChild(hiddenField);

        document.body.appendChild(form);
        form.submit();
      }

      function addPageImg(id) {
        var form = document.createElement("form");
        form.setAttribute("method", "post");
        form.setAttribute("action", "<?=base_url()?>page_img_page");

        hiddenField = document.createElement("input");
        hiddenField.setAttribute("type", "hidden");
        hiddenField.setAttribute("name", "page_id");
        hiddenField.setAttribute("value", id);
        form.appendChild(hiddenField);

        document.body.appendChild(form);
        form.submit();
      }

      function filterSite(){
        var site = $('#siteFilter').val();
        $('.input-sm').val(site);
        var Table = $('#table-1').dataTable();
        Table.fnFilter(site);
      }
    </script>
  </body>
</html>