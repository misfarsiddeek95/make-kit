<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>  
    <div class="site-left-sidebar">
        <div class="sidebar-backdrop"></div>
        <div class="custom-scrollbar">
          <ul class="sidebar-menu">
            <?php 
                foreach ($tree as $item){
            ?>
              <li <?php if($item->is_folder==1){echo 'class="with-sub"';}?>>
                  <a <?php if($item->is_folder==0){echo 'href="'.base_url($item->url.'/'.DEFAULT_METHOD.'/'.$item->optid).'"';}else{echo 'href=""';}?> <?php if($item->is_folder==1){echo '" '.'aria-haspopup="true"';}?>>
                    <span class="menu-icon">
                      <i class="<?=$item->icon_class?>"></i>
                    </span>
                    <span class="menu-text"><?=$item->description?></span>
                  </a>
                  <?php if($item->is_folder==1 && sizeof($item->children)>0){?>
                      <ul class="sidebar-submenu collapse">
                          <li class="menu-subtitle"><?=$item->description?></li>
                          <?php foreach ($item->children as $child){ ?>
                          <li><a href="<?= base_url($child->url.'/'.DEFAULT_METHOD.'/'.$child->optid)?>"><?=$child->description?></a></li>
                          <?php } ?>
                      </ul>
                  <?php } ?>
              </li>
            <?php } ?>
          </ul>
        </div>
      </div>