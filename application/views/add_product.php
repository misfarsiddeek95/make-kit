<!DOCTYPE html>
<html lang="en">
	<head>
		<?php $this->load->view('includes/head'); ?>
		<link rel="stylesheet" href="<?=base_url()?>assets/spectrum.css">

		<style type="text/css">
			.attribName{
				margin-left: auto;
			    margin-right: auto;
			    width: 97px;
			    height: 77px;
			    text-align: center;
			}

			.scrollbar {
            width: 100%;
            height: 112px;
            overflow: auto;
	        }
	        .force-overflow {
	            min-height: 142px;
	            padding-right: 5px;
	        }
	        .scrl::-webkit-scrollbar-track
	        {
	            -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.1);
	            background-color: #F5F5F5;
	            border-radius: 10px;
	        }

	        .scrl::-webkit-scrollbar
	        {
	            width: 7px;
	            background-color: #f7941e;
	        }

	        .scrl::-webkit-scrollbar-thumb
	        {
	            border-radius: 20px;
	            background-color: #FFF;
	            background-image: -webkit-linear-gradient(top, #3e6e78 0%,#23236c 44%,#020024 100%);
	        }

			fieldset {
				border: 1px solid #c0c0c0;
				margin: 0 2px;
				padding: 0.35em 2.625em 0.75em;
			}

			legend {
				display: block;
				width: unset;
				padding: 0;
				margin-bottom: 10px;
				font-size: 19.5px;
				line-height: inherit;
				color: #333333;
				border: 0;
				border-bottom: unset;
			}
		</style>
	</head>
	<body class="layout layout-header-fixed layout-left-sidebar-fixed" <?php if(!(empty($product))){echo 'onload="loadattr('.$product->cate_id.');"';} ?>>
		<?php $this->load->view('includes/topbar'); ?>
		<div class="site-main">
			<?php $this->load->view('includes/sidebar'); ?>
			
			<div class="site-content">

				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="m-y-0"><?=$type?> מוצר</h3>
					</div>
					<div class="panel-body">

						<div class="row">
							<div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
								<form data-toggle="validator" id="inputmasks">

									<input type="hidden" name="pro_id" id="pro_id" value="<?php if(!(empty($product))){echo($product->pro_id);}else{echo(0);} ?>" />

									<div class="form-group">
										<label for="currency_type" class="control-label">שיטת מטבע מוצר</label>
										<select class="form-control" data-plugin="select2" name="credit_type_id" id="currency_type" data-placeholder="שיטת מטבע מוצר" data-allow-clear="true" required data-required-error="שיטת מטבע מוצר נדרשת." style="width: 100%;">
											<option></option>
											<?php foreach ($credit_types as $row) { ?>
											<option value="<?=$row->id?>"><?=$row->value.' מוצר'?></option>
											<?php } ?>
										</select>
										<div class="help-block with-errors"></div>
									</div>

									<div class="form-group hidden" id="elgible-points">
										<label for="minimum-eligiblity" class="control-label">נקודות זכאות לרכישת מוצר זה</label>
										<input type="text" placeholder="נקודות זכאות" value="<?php if(!(empty($product))){echo($product->minimum_eligiblity_value);}else{echo(0);} ?>" name="minimum_eligiblity_value" id="minimum_eligiblity_value" class="form-control" pattern="^[0-9]+$" data-pattern-error="הקלד מספרים שלמים בלבד"> 
										<div class="help-block with-errors"></div>
									</div>

									<?php if(!(empty($Users))&&$assign_pro==0){ ?>
									<div class="form-group">
										<label for="form-control-3" class="control-label">הקצה משתמש</label>
										<select class="form-control" data-plugin="select2" name="user_id" id="user_id" style="width: 100%;">
											<option></option>
											<?php foreach ($Users as $row) { 
												$sel ='';
												if(!(empty($product))){
													if ($product->user_id==$row->user_id) {
														$sel = 'selected';
													}
												} ?>
											<option value="<?=$row->user_id?>" title="<?=$row->username?>" <?=$sel?>><?=$row->fname.' '.$row->lname?></option>
											<?php } ?>
										</select>
										<div class="help-block with-errors"></div>
									</div>
									<?php  } ?>

									<div class="form-group">
										<label for="form-control-3" class="control-label">קטגוריות</label>
										<select class="form-control" data-plugin="select2" data-placeholder="בחר קטגוריה" name="proCate" id="proCate" data-required-error="קטגוריה נדרשת" required style="width: 100%;">
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
										<div class="help-block with-errors"></div>
									</div>

									<div class="form-group">
										<label for="form-control-3" class="control-label">קוד מוצר</label>
										<input type="text" pattern="^([a-zA-Z0-9_-]){2,25}$" value="<?php if(!(empty($product))){echo($product->pro_code);} ?>" placeholder="קוד מוצר" id="proCode" name="proCode" class="form-control" data-remote="<?=base_url()?><?php if(empty($product)){echo 'checkfields';}else{echo 'checkDBfieldOpt';}?>?data=pro_code&input=proCode&table=products" data-remote-error="קוד מוצר כבר קיים, נסה אחר" data-pattern-error="קוד מוצר לא חוקי">
										<div class="help-block with-errors"></div>
									</div>

									<div class="form-group">
										<label for="form-control-3" class="control-label">שם מוצר</label>
										<input type="text" pattern="^[a-zA-Z 0-9 .,&+-/']*$" value="<?php if(!(empty($product))){echo($product->name);} ?>" placeholder="שם" id="proName" name="proName" class="form-control" data-minlength="3" data-pattern-error="שם לא חוקי" data-error="מינימום 3 תווים" data-required-error="שם נדרש" required>
										<div class="help-block with-errors"></div>
									</div>

									<div class="form-group">
										<label for="form-control-4" class="control-label">מחיר</label>
										<input type="text" placeholder="מחיר" value="<?php if(!(empty($product))){echo($product->price);}else{echo(0);} ?>" name="proPrice" id="proPrice" class="form-control" data-inputmask="'alias': 'decimal', 'groupSeparator': ',', 'autoGroup': true, 'rightAlign': false, 'allowMinus': false, 'allowPlus': false" data-required-error="מחיר נדרש" required> 
										<div class="help-block with-errors"></div>
									</div>

									<div class="form-group">
										<label for="form-control-4" class="control-label">מחיר(POI)</label>
										<input type="text" placeholder="מחיר POI" value="<?php if(!(empty($product))){echo($product->price_poi);}else{echo(0);} ?>" name="proPOIPrice" id="proPOIPrice" class="form-control" data-inputmask="'alias': 'decimal', 'groupSeparator': ',', 'autoGroup': true, 'rightAlign': false, 'allowMinus': false, 'allowPlus': false"> 
										<div class="help-block with-errors"></div>
									</div>

									<div class="form-group">
										<label for="form-control-4" class="control-label">כמות</label>
										<input type="number" placeholder="כמות" value="<?php if(!(empty($product))){echo($product->quantity);}else{echo(6);} ?>" name="proQty" id="proQty" class="form-control" data-minlength="1" data-error="כמות לא חוקית" data-required-error="כמות נדרשת" required> 
										<div class="help-block with-errors"></div>
									</div>

									<div class="form-group">
										<label for="form-control-4" class="control-label">משקל (גרם)</label>
										<input type="number" placeholder="משקל" value="<?php if(!(empty($product))){echo($product->weight);}else{echo(1);} ?>" name="proWeight" id="proWeight" class="form-control" data-minlength="1" data-error="משקל לא חוקי" data-required-error="משקל נדרש" required> 
										<div class="help-block with-errors"></div>
									</div>

										<div class="form-group">
											<label for="form-control-4" class="control-label">ברקוד</label>
											<input type="text" placeholder="ברקוד" value="<?php if(!(empty($product))){echo($product->barcode);}?>" name="barcode" id="pro_Barcode" class="form-control" data-error="ברקוד לא חוקי"> 
											<div class="help-block with-errors"></div>
										</div>

									<div class="form-group">
										<label for="form-control-2" class="control-label">מותג</label>
										<select class="form-control" data-plugin="select2" name="brand_name" id="brand_name" <?php if(!(empty($product))){echo "attr_brand_id='".$product->brand_id."'";}?> data-placeholder="בחר מותג" data-required-error="מותג נדרש" required style="width: 100%;">
											<option></option>
										</select>
										<div class="help-block with-errors"></div>
									</div>

									<div id="attrDiv">
										
									</div>
									
									<?php if(!(empty($attributes))){
										foreach ($attributes as $row) {
											$selType = 'data-required-error="'.$row->attribute.' is Required" required';
											$attrName = 'attribute['.$row->attr_id.']';
											if ($row->type==1) {
												$selType = 'multiple="multiple" data-required-error="שדה זה נדרש" required';
												$attrName = 'multiAttr['.$row->attr_id.'][]';
												
											}elseif ($row->type==2) {
												$selType = 'multiple="multiple"';
												$attrName = 'multiAttr['.$row->attr_id.'][]';
											}
									?>
									<div class="form-group">
										<label for="form-control-3" class="control-label"><?=$row->attribute?></label>
										<select class="form-control selectcls clss<?=$row->attr_id?>" data-plugin="select2" name="<?=$attrName?>" data-placeholder="בחר <?=$row->attribute?>" <?=$selType?> style="width: 100%;">
											<option></option>
											<?php 
												if(!(empty($attributes))){
													foreach ($attribute_val as $atrval) {
														if ($row->attr_id==$atrval->attr_id) {
											?>
											<option value="<?=$atrval->av_id?>" title="<?=$atrval->description?>"><?=$atrval->value?></option>
											<?php } } }?>
										</select>
										<div class="help-block with-errors"></div>
									</div>
									<?php  } } ?>

									<?php if($add_other_cate){?>
									<div class="form-group">
										<label for="form-control-2" class="control-label">קטגוריות אחרות</label>
										<select class="form-control" data-plugin="select2" name="other_cates[]" id="other_cates" multiple="multiple" data-placeholder="קטגוריות אחרות" style="width: 100%;">
											<option></option>
											<?php
												function write_with_child1($category) {
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
													write_with_child1($cate);
											}?>
										</select>
										<div class="help-block with-errors"></div>
									</div>
									<?php } ?>

									<?php if($seo_det){?>
									<div class="form-group">
										<label for="form-control-3" class="control-label">כותרת SEO</label>
										<input type="text" value="<?php if(!(empty($product))){echo($product->seo_title);} ?>" placeholder="כותרת SEO" id="seoTitle" name="seoTitle" class="form-control">
										<div class="help-block with-errors"></div>
									</div>

									<div class="form-group">
										<label for="form-control-3" class="control-label">מילות מפתח SEO</label>
										<input type="text" pattern="^[a-zA-Z,]+$" value="<?php if(!(empty($product))){echo($product->seo_keyword);} ?>" placeholder="מילות מפתח SEO" id="seoKeywords" name="seoKeywords" class="form-control">
										<div class="help-block with-errors"></div>
									</div>

									<div class="form-group">
										<label for="form-control-3" class="control-label">תיאור SEO</label>
										<input type="text" value="<?php if(!(empty($product))){echo($product->seo_description);} ?>" placeholder="תיאור SEO" id="seoDescription" name="seoDescription" class="form-control">
										<div class="help-block with-errors"></div>
									</div>
									<?php } ?>

									 <?php if($seo_url){?>
									<div class="form-group">
										<label for="form-control-3" class="control-label">כתובת URL של SEO</label>
										<input id="form-control-7" class="form-control" type="text" data-inputmask="'alias': 'url'" value="<?php if(!(empty($product))){echo($product->seo_url);} ?>" placeholder="כתובת URL של SEO" id="seoUrl" name="seoUrl">
										<div class="help-block with-errors"></div>
									</div>
									<?php } ?>

									<div class="form-group">
									<label for="form-control-3" class="control-label">תיאור קצר</label>
									<textarea id="proShortDescription" name="proShortDescription" data-plugin="autosize" class="form-control" placeholder="תיאור קצר" style="resize: none; height: 54px; overflow: hidden; overflow-wrap: break-word;"><?php if(!(empty($product))){echo($product->short_description);}?></textarea>
									</div>

									<div class="form-group">
										<label for="form-control-3" class="control-label">תיאור מוצר</label>
										<textarea id="proDescription" name="proDescription" class="form-control" ><?php if(!(empty($product))){echo($product->description);}?></textarea>
									</div>

									<div class="form-group">
										<label for="form-control-3" class="control-label">מרכיבים</label>
										<textarea id="proIngredients" name="proIngredients" class="form-control" ><?php if(!(empty($product))){echo($product->ingredients);}?></textarea>
									</div>

									<div class="form-group">
										<label for="form-control-3" class="control-label">איך להשתמש</label>
										<textarea id="proUse" name="proUse" class="form-control" ><?php if(!(empty($product))){echo($product->how_to_use);}?></textarea>
									</div>
									
									<!-- Discount Section -->
									<fieldset class="m-b-20">
                                        <legend>
                                            <h5>שיעורי הנחה</h5>
                                        </legend>
                                        <div class="row">
                                            <div class="col-sm-6 col-md-12">
                                                <div class="table-flip-scroll">
                                                    <table class="ar-table table table-borderless">
                                                        <thead>
                                                            <tr>
                                                                <th style="text-align: center;">שיעור הנחה</th>
                                                                <th style="text-align: center;">ספירת פריטים מינימלית</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="as_t_body">
                                                            <tr class="scorerrow project_assignee_tr">
                                                                <td>
                                                                    <div class="form-group">
                                                                        <select class="form-control dscrates first_dropdown" data-plugin="select2" name="dscrates[]" data-placeholder="בחר שיעור הנחה למוצר" data-allow-clear="true" style="width: 100%;">
                                                                            <option></option>
                                                                            <?php
                                                                                foreach ($discount_rates as $row) { 
																					$dscRate = $row->discount_type == 1 ? number_format($row->discount_value + 0).'%' : $curr.''.number_format($row->discount_value + 0); 
                                                                            ?>
                                                                                <option value="<?=$row->id?>" ><?=$dscRate?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                        <div class="help-block with-errors"></div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <input type="text" pattern="^[0-9.]+$" value="" placeholder="ספירת פריטים" name="item_count[]" class="form-control item_count" data-pattern-error="פורמט לא חוקי" autocomplete="off" style="width: 100%;">
                                                                        <div class="help-block with-errors"></div>
                                                                    </div>
                                                                </td>
                                                                <td style="text-align: center;">
                                                                    <button type="button" class="label label-primary project-assignee-add-btn m-l-10 m-b-10 add-field" onclick="add_fields(this,'project_assignee_tr','project-assignee-add-btn','project-assignee-remove-btn');">
                                                                        <i class="zmdi zmdi-plus"></i>
                                                                    </button>
                                                                    <button type="button" class="label label-danger project-assignee-remove-btn m-b-10" style="display:none;" onclick="remove_fields(this,'project_assignee_tr','project-assignee-add-btn','project-assignee-remove-btn');">
                                                                        <i class="zmdi zmdi-minus"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>

									<button type="submit" class="btn btn-primary btn-block" id="submitBtn">שלח</button>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>

			<?php $this->load->view('includes/footer'); ?>

			<div class="modal-content animated bounceInUp bottomModal">
				<div class="modal-body">
					<div class="row d-flex justify-content-center align-items-center">
							<p class="ptag">מוצר נוסף בהצלחה. האם ברצונך להוסיף תמונות למוצר זה?</p>
							<div class="btnInside">
								<input type="hidden" id="saved_pro_id" value="0">
								<a type="button" class="btn btn-primary m-w-120" onclick="addProductImg();">המשך</a>
								<a type="button" class="btn btn-outline-primary m-w-120" data-dismiss="modal" onclick="pageRefresh();">לא, תודה</a>
							</div>
					</div>
				</div>
			</div>

		</div>


		<!-- my edit -->
		<div id="attrValModal" class="modal fade" tabindex="-1" role="dialog">

				<div class="modal-dialog modal-sm">

					<div class="modal-content">

						<div class="modal-header bg-primary">

							<button type="button" class="close" data-dismiss="modal" aria-label="Close">

								<span aria-hidden="true">

									<i class="zmdi zmdi-close"></i>

								</span>

							</button>

							<h4 class="modal-title" id="modal-val-title">ערך תכונה</h4>

						</div>



						<form data-toggle="validator" id="attrValMasks">

							<div class="modal-body">

								<input type="hidden" name="val_attr_id" id="val_attr_id" value="0">

								<input type="hidden" name="attr_val_id" id="attr_val_id" value="0">

								<div class="form-group">

									<label for="form-control-2" class="control-label">ערך</label>

									<input type='text' data-pattern-error="ערך לא חוקי" class="form-control colorPick" id="attrVal" name="attrVal" placeholder="ערך" data-required-error="ערך נדרש" required />

									<div class="help-block with-errors"></div>

								</div>

								<div class="form-group">

									<label for="form-control-2" class="control-label">תיאור</label>

									<input type="text" class="form-control" id="attrValDesc" name="attrValDesc" placeholder="תיאור">

									<div class="help-block with-errors"></div>

								</div>
							</div>

							<div class="modal-footer">

								<button type="submit" class="btn btn-primary">שלח</button>

								<button type="button" data-dismiss="modal" class="btn btn-default">סגור</button>

							</div>

						</form>

					</div>

				</div>

			</div>


		<?php $this->load->view('includes/javascripts'); ?>
		<script src="<?=base_url()?>assets/js/forms-form-masks.js"></script>
		<script src="<?=base_url()?>assets/js/forms-plugins.js"></script>
		<script src="<?=base_url()?>assets/js/ckeditor.js"></script>
		<script src="<?=base_url()?>assets/spectrum.js"></script>
		<script type="text/javascript">
			// initialize ckeditor
			let proDescription, proIngredients, proUse;
			ClassicEditor
			.create( document.querySelector( '#proDescription' ) )
			.then( newEditor => {
				proDescription = newEditor;
			} )
			.catch( error => {
				console.error( error );
			} );

			ClassicEditor
			.create( document.querySelector( '#proIngredients' ) )
			.then( newEditor => {
				proIngredients = newEditor;
			} )
			.catch( error => {
				console.error( error );
			} );

			ClassicEditor
			.create( document.querySelector( '#proUse' ) )
			.then( newEditor => {
				proUse = newEditor;
			} )
			.catch( error => {
				console.error( error );
			} );

		</script>
		<script type="text/javascript">

			$(document).ready(function () {
				$("#proCate,#brand_name.selectcls,#visibleSites").select2();

				$( ".colorPick" ).blur(function() {
					$(this).spectrum("set", $("#attrVal").val());
				});

				$("#user_id").select2({
					placeholder: "בחר משתמש",
					allowClear: true
				});

				<?php if(!empty($product)) { ?>
					$('#currency_type').val('<?=$product->credit_type_id?>').trigger('change');


					<?php if(!empty($product->discounts)) { ?>
						<?php foreach ($product->discounts as $k => $dsc) { ?>
							$('.project_assignee_tr:last').find('.add-field').click();
							$('.project_assignee_tr:last').find('.first_dropdown').val('<?=$dsc->discount_id?>').trigger('change');
							$('.project_assignee_tr:last').find('.item_count').val('<?=$dsc->min_item_count?>');
						<?php } ?>
						$('.project_assignee_tr:first').remove();
					<?php } ?>
				<?php } ?>
			});
			
			function checkedFun() {
				$('#inputmasks').find('input[type=checkbox]').each(function() {
						var name = $(this).attr('name');
						if (1 <= $('input[name="' + name + '"]:checked').length){
							$("input[type=checkbox]").prop('required',false);
							$("#inputmasks").validator('update');
							$("#inputmasks").validator('validate');
						}
				});
			}

			$("#proCate").change(function() {
				run_waitMe('#inputmasks');
				var cate_id = $(this).val();
				var pro_id = $('#pro_id').val();
				$('#proDescription').html(proDescription.getData());
				$('#proIngredients').html(proIngredients.getData());
				$('#proUse').html(proUse.getData());
				$.ajax({
					type: "POST",
					url: "<?=base_url()?>getAttr",
					data: 'cate_id='+cate_id+'&pro_id='+pro_id,
					success: function(result) {
						var responsedata = $.parseJSON(result);
						var attr = "";
						$("#brand_name,#attrDiv").empty();
						$("#brand_name").append("<option></option>");
						if(responsedata.brands != undefined){
							for (var i = 0; i < responsedata.brands.length; i++) {
								$("#brand_name").append($("<option></option>").attr("value",responsedata.brands[i]['brand_id']).text(responsedata.brands[i]['brand']));
							}
						}
						if ($('#brand_name').is("[attr_brand_id]")) {
							var brand_id = $('#brand_name').attr('attr_brand_id');
							$('#brand_name').val(brand_id).trigger('change');
						}

						if(responsedata.attributes != undefined){
							for (var i = 0; i < responsedata.attributes.length; i++) {
								attr+='<div class="form-group">'+
											'<label for="form-control-3" class="control-label">'+responsedata.attributes[i].attribute+'</label>';

								if (responsedata.attributes[i].type==3) {
									attr+='<ul class="pro_color_sel scrl" style="overflow-y: scroll;max-height: 260px;margin-bottom:5px;">';
									for (var j = 0; j < responsedata.attribute_val.length; j++) {
										if (responsedata.attribute_val[j].attr_id==responsedata.attributes[i].attr_id) {
											attr+='<li class="attribName"><input type="checkbox" class="multiAttrColor clss'+responsedata.attributes[i].attr_id+'" onclick="checkedFun();" name="multiAttr['+responsedata.attributes[i].attr_id+'][]" id="'+responsedata.attribute_val[j].av_id+'" value="'+responsedata.attribute_val[j].av_id+'" required/>'+
														'<label for="'+responsedata.attribute_val[j].av_id+'" style="background-color:'+responsedata.attribute_val[j].value+';" title="'+responsedata.attribute_val[j].description+'"></label><br>'+
														responsedata.attribute_val[j].description+'</li>';
										}
									}

									//my edit
									attr+='</ul><button type="button" class="btn btn-outline-info" data-toggle="modal" onclick="addAttrVal('+responsedata.attributes[i].attr_id+');">Add more color</button>';
								}else if(responsedata.attributes[i].type==4){
									attr+='<ul class="pro_color_sel scrl" style="overflow-y: scroll;max-height: 260px;">';
									for (var j = 0; j < responsedata.attribute_val.length; j++) {
										if (responsedata.attribute_val[j].attr_id==responsedata.attributes[i].attr_id) {
											attr+='<li class="attribName"><input type="radio" class="clss'+responsedata.attributes[i].attr_id+'" name="attribute['+responsedata.attributes[i].attr_id+']" id="'+responsedata.attribute_val[j].av_id+'" value="'+responsedata.attribute_val[j].av_id+'" required/>'+
														'<label for="'+responsedata.attribute_val[j].av_id+'" style="background-color:'+responsedata.attribute_val[j].value+';" title="'+responsedata.attribute_val[j].description+'"></label><br>'+
														responsedata.attribute_val[j].description+'</li>';
										}
									}
									attr+='</ul>';
								}else{
									var selType = 'data-required-error="'+responsedata.attributes[i].attribute+' is Required" required';
									var attrName = 'attribute['+responsedata.attributes[i].attr_id+']';
									var selCls = 'selectcls';
									if (responsedata.attributes[i].type==1) {
										selCls = 'selectcls multisel';
										selType = 'multiple="multiple" data-required-error="שדה זה נדרש" required';
										attrName = 'multiAttr['+responsedata.attributes[i].attr_id+'][]';
									}else if (responsedata.attributes[i].type==2){
										selType = 'multiple="multiple"';
										attrName = 'multiAttr['+responsedata.attributes[i].attr_id+'][]';
									}
									attr+='<select class="form-control selectcls clss'+responsedata.attributes[i].attr_id+'" data-placeholder="בחר '+responsedata.attributes[i].attribute+'" name="'+attrName+'" id="attribute['+i+']" '+selType+' style="width: 100%;"><option></option>';
									for (var j = 0; j < responsedata.attribute_val.length; j++) {
										if (responsedata.attribute_val[j].attr_id==responsedata.attributes[i].attr_id) {
											attr+='<option value="'+responsedata.attribute_val[j].av_id+'" title="'+responsedata.attribute_val[j].description+'">'+responsedata.attribute_val[j].value+'</option>';
										}
									}
									attr+='</select>';
								}
								attr+='<div class="help-block with-errors"></div></div>';
							}
						}
						$("#attrDiv").append(attr);
						$('#brand_name,.selectcls').select2();

						if(responsedata.pro_attribute_val != undefined){
							if (0<responsedata.pro_attribute_val.length) {
								var attrArray = [];
								for (var i = 0; i < responsedata.pro_attribute_val.length; i++) {
									if (responsedata.pro_attribute_val[i].type==0) {
										$('.clss'+responsedata.pro_attribute_val[i].attr_id).val(responsedata.pro_attribute_val[i].av_id).trigger('change');
									}else if((responsedata.pro_attribute_val[i].type)==3||(responsedata.pro_attribute_val[i].type)==4){
										$(".clss"+responsedata.pro_attribute_val[i].attr_id+"[value=" + responsedata.pro_attribute_val[i].av_id + "]").prop('checked', true);
									}else if((responsedata.pro_attribute_val[i].type)==1||(responsedata.pro_attribute_val[i].type)==2){
										attrArray.push([responsedata.pro_attribute_val[i].av_id]);
										$('.clss'+responsedata.pro_attribute_val[i].attr_id).val(attrArray).trigger('change');
									}
								}
								checkedFun();
							}
						}
						if(responsedata.pro_cate_val != undefined){
							if (0<responsedata.pro_cate_val.length) {
								var cateArray = [];
								for (var i = 0; i < responsedata.pro_cate_val.length; i++) {
									cateArray.push([responsedata.pro_cate_val[i].cate_id]);
									$('#other_cates').val(cateArray).trigger('change');
								}
							}
						}
						$('#inputmasks').validator('update');
						$('#inputmasks').waitMe('hide');
					},
					error: function(result) {
						alert('error');
						$('#inputmasks').waitMe('hide');
					}
				});
			});

			$('#inputmasks').validator().on('submit', function (e) {
				if (!(e.isDefaultPrevented())) {
					e.preventDefault();
					run_waitMe('#inputmasks');
					if (validate_checkbox()) {
						$('#proDescription').html(proDescription.getData());
						$('#proIngredients').html(proIngredients.getData());
						$('#proUse').html(proUse.getData());
						$.ajax({
							type: "POST",
							url: "<?=base_url()?>saveProducts",
							data: $('#inputmasks').serialize(),
							success: function(result) {
								var responsedata = $.parseJSON(result);
								if(responsedata.status=='success'){
									if (responsedata.message=='update') {
										toastr.success("מוצר עודכן בהצלחה.")
										setTimeout(function(){
											window.location = "<?=base_url()?>products/view_products";
										}, 500);
									}else{
										$('#saved_pro_id').val(responsedata.id);
										document.getElementById('inputmasks').reset(); 
										$('#inputmasks').find("input").val("");
										$('#inputmasks').find("textarea").val("");
										$('#proCate').val('').trigger('change');
										$('#inputmasks').validator('destroy').validator();
										$('.bottomModal').modal('show');
										setTimeout(function(){
											$(".bottomModal").modal('hide');
											location.reload();
										}, 15000);
									}
								}else if(responsedata.status=='error'){
									toastr.error(responsedata.message)
								}else{
									toastr.error("Somthing went wrong :(")
								}
								$('#inputmasks').waitMe('hide');
							},
							error: function(result) {
								$('#inputmasks').waitMe('hide');
								toastr.error('Error :'+result)
							}
					});
				}else{
					$('#inputmasks').waitMe('hide');
				}
			}});

			function validate_checkbox() {
				var status = false;
				if ($('#inputmasks').find('input[type=checkbox]').length) {
						$('#inputmasks').find('input[type=checkbox]').each(function() {
							var name = $(this).attr('name');
							if (1 <= $('input[name="' + name + '"]:checked').length){
								status = true;
							}
					});
				}else{
					status = true;
				}
				return status;
			}

			/*function checkMultiSelect() {
				$('.multisel').each(function() {
					alert($(this).select2('data').id)
					alert($(this).select2('data').text)
				});
				$('#inputmasks').waitMe('hide');
				return false;
			}*/

			function addProductImg() {
				var id = $('#saved_pro_id').val();

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

			// my edit
			function addAttrVal(attrId) {
				$('#modal-val-title').text('Add Attribute Value');
				$('#attrVal, #attrValDesc').val("");
				$("#val_attr_id").val(attrId);
				$("#attr_val_id").val(0);
				$('#attrVal').attr('pattern','^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$');
				$(".colorPick").spectrum({
					preferredFormat: "hex",
					showInput: true,
					allowEmpty:true,
					replacerClassName: 'myClass'
				});
				$(".colorPick").show();
				$('#attrValModal').modal('show');
			}

			$('#attrValMasks').validator().on('submit', function (e) {
				if (!(e.isDefaultPrevented())) {
					e.preventDefault();
					run_waitMe('#attrValMasks');
					var id = $("#val_attr_id").val();

					$.ajax({
					type: "POST",
					url: "<?=base_url()?>addAttributeVal_prod",
					data: $('#attrValMasks').serialize(),
					success: function(result) {
						var responsedata = $.parseJSON(result);
						if(responsedata.status=='success'){
						toastr.success(responsedata.message)
						var attr ='<li class="attribName"><input type="checkbox" class="multiAttrColor clss'+responsedata.inserted_val.attr_id+'" onclick="checkedFun();" name="multiAttr['+responsedata.inserted_val.attr_id+'][]" id="'+responsedata.				inserted_val.av_id+'" value="'+responsedata.inserted_val.av_id+'" required/>'+
									'<label for="'+responsedata.inserted_val.av_id+'" style="background-color:'+responsedata.inserted_val.value+';" title="'+responsedata.inserted_val.description+'"></label><br>'+responsedata.inserted_val.description+'</li>';
						$('.pro_color_sel').append(attr);

						}else if(responsedata.status=='error'){
						toastr.error(responsedata.message)
						}else{
						toastr.error("Somthing went wrong :(")
						}
						$("#attrValModal").modal('hide');
						$('#attrValMasks').waitMe('hide');
					},
					error: function(result) {
						$('#attrValMasks').waitMe('hide');
						toastr.error('Error :'+result)
					}
				});
				}
			});
				
			function loadattr(val) {
				$('#proCate').val(val).trigger('change');
			}
			function pageRefresh() {
				setTimeout(function(){
					location.reload();
				}, 100);	
			}

			$('#currency_type').on('change', function () {
				const isNotNormalCurrency = this.value == 2 || this.value == 3; // 2 : Makekit | 3: Medalian
				const eligibleField = $('#elgible-points');
				if(isNotNormalCurrency) {
					eligibleField.removeClass('hidden');
					eligibleField.find('input').attr({
						'required': 'required',
						'data-required-error': 'Eligible point is required.'
					});
				} else {
					eligibleField.addClass('hidden');
					eligibleField.find('input').removeAttr('required data-required-error');
				}
			});

			add_fields = function (elms,dvclass,adclass,rmclass) {
				$('.'+dvclass+':last').find('.second_dropdown').select2('destroy');
				$('.'+dvclass+':last').find('.first_dropdown').select2('destroy');
				var ele = $(elms).closest('.'+dvclass).clone(true);
				$(elms).closest('.'+dvclass).after(ele);   
				$(elms).closest('.'+dvclass).find('.'+rmclass).css({"display":"inline-block"});
				$(elms).css('display','none');
				$('.'+dvclass).last().find('.'+rmclass).css({"display":"inline-block"});
				var numItems = $('.'+dvclass).length;
				$('.second_dropdown,.first_dropdown').select2();
				$('.'+dvclass).last().find('.'+rmclass).css({"display":"inline-block"});
			}

			remove_fields = function(elms,dvclass,adclass,rmclass) {
				$(elms).closest('.'+dvclass).remove();
				$('.'+dvclass).last().find('.'+adclass).css({"display":"inline-block"});
				$('.'+dvclass).first().find('.'+rmclass).css({"display":"none"});
				var numItems = $('.'+dvclass).length;
			}
			
		</script>
	</body>
</html>