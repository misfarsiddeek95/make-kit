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
                <span class="label label-success make-cursor" onclick="location.href='<?=base_url();?>add_page'" title="Add"><i class="zmdi zmdi-plus"></i></span>
              </div>
              <?php }?>
              <h3 class="m-t-0 m-b-5">Page Management</h3>
            </div>
            <div class="panel-body">
              <div class="panel-group" id="accordionOne" role="tablist" aria-multiselectable="true">
                <?php 
                  foreach ($pages as $child) { 
                    $tabid = str_replace(" ","-",strtolower($child->page_for));
                ?>
                <div class="panel panel-default">
                  <div class="panel-heading" role="tab">
                    <h4 class="panel-title">
                      <a role="button" data-toggle="collapse" data-parent="#accordionOne" class="collapsed" href="#<?=$tabid?>" aria-expanded="false">
                        <i class="zmdi zmdi-chevron-down"></i> <?=ucwords(strtolower($child->page_for))?>
                      </a>
                    </h4>
                  </div>
                  <div id="<?=$tabid?>" class="panel-collapse collapse" role="tabpanel">
                    <div class="panel-body">
                      <div class="table-responsive m-y-5">
                        <table class="table table-hover" id="table-1">
                          <thead>
                            <tr>
                              <th></th>
                              <th>Name</th>
                              <th>Page Type</th>
                              <th>Page Title</th>
                              <th>Headline</th>
                              <th>Second Title</th>
                              <th>create_date</th>
                              <?php if($edit_page || $add_photo){?>
                              <th style="text-align:right;">Options</th> 
                              <?php } ?>
                            </tr>
                          </thead>
                          <tbody id="tbody_data">
                            <?php foreach ($child->pages as $row) { 
                              $img = 'default.jpg';
                              if ($row->photo_path!=null) {
                                $img = 'pages/';
                                $img .= $row->extension == 'png' ? $row->photo_path.'-org.'.$row->extension : $row->photo_path.'-sma.jpg';
                              }
                              $pageType = $row->page_type;
                              $page_type = '';
                              switch ($pageType) {
                                case 0:
                                  $page_type = 'Main Page';
                                  break;
                                case 1:
                                  $page_type = 'Slider';
                                  break;
                                case 2:
                                  $page_type = 'Banner';
                                  break;
                                case 3:
                                  $page_type = 'Gallery';
                                  break;
                                default:
                                  $page_type = '';
                                  break;
                              }
                            ?>

                            <tr id="pagerow<?=$row->page_id?>">
                              <td><img class="img-rounded" src="<?=base_url();?>photos/<?=$img?>" height="32"></td>
                              <td><?=$row->name;?></td>
                              <td><span class="label label-dark"><?=$page_type?></span></td>
                              <td><?=$row->page_title;?></td>
                              <td><?=$row->headline;?></td>
                              <td><?=$row->second_title;?></td>
                              <td><?=$row->create_date;?></td>
                              <?php if($edit_page || $add_photo){?>
                              <td align="right">
                                <?php if($edit_page){?>
                                  <span class="label label-primary make-cursor" title="Edit" onclick="editMe('<?=$row->page_id?>');"><i class="zmdi zmdi-edit"></i></span>
                                <?php } if($add_photo){ ?>
                                  <span class="label label-info make-cursor" title="Upload Image" onclick="addPageImg('<?=$row->page_id?>');"><i class="zmdi zmdi-upload"></i></span>
                                <?php } ?>
                              </td>
                              <?php } ?>
                            </tr>
                            <?php } ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
                <?php } ?>
              </div>
            </div>
          </div>

        </div>
        <?php $this->load->view('includes/footer'); ?>
    </div>
    <?php $this->load->view('includes/javascripts'); ?>
    <script type="text/javascript">
      $('#table-1').DataTable();

      function editMe(id) {
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
    </script>
  </body>
</html>