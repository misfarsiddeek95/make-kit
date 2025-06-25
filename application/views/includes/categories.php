
                  <!-- <?php 
                    $mainCate = array();
                    $subCate = array();
                    foreach ($categories as $row) {
                      if ($row->parent_id==0) {
                        if (!(in_array($row->category, $mainCate))) {
                          $mainCate[$row->cate_id] = $row->category;
                        }
                      }else if (array_key_exists($row->parent_id, $mainCate)) {
                        $subCate[$row->parent_id][$row->cate_id] = $row->category;
                      }
                    }

                    print_r($mainCate);
                    echo("<br>");
                    print_r($subCate);
                    echo("<br>");

                    foreach ($mainCate as $key => $value) {
                      echo $key.'-'.$value;
                      if (array_key_exists($key, $subCate)) {
                         foreach ($subCate[$key] as $key1 => $value1) {
                          echo $key1.'-'.$value1;
                        }
                      }
                    }
                  ?> -->