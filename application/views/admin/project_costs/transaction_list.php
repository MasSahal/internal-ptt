<?php
/* User Roles view
*/
?>
<?php $session = $this->session->userdata('username'); ?>
<?php $get_animate = $this->Xin_model->get_content_animate(); ?>
<?php $role_resources_ids = $this->Xin_model->user_role_resource(); ?>
<?php $user_info = $this->Xin_model->read_user_info($session['user_id']); ?>
<?php $vresult = $this->Vendor_model->gel_all_vendor()->result(); ?>
<?php $presult = $this->Product_model->gel_all_product()->result(); ?>
<?php $presult = $this->Project_model->get_projects()->result(); ?>
<?php $system_setting = $this->Xin_model->read_setting_info(1); ?>

<?php
// reports to 
// reports to 
$reports_to = get_reports_team_data($session['user_id']); ?>
<div id="smartwizard-2" class="smartwizard-example sw-main sw-theme-default">
	<ul class="nav nav-tabs step-anchor">
		<?php if (in_array('486', $role_resources_ids) && $user_info[0]->user_role_id == 1) { ?>
			<li class="nav-item clickable"> <a href="<?php echo site_url('admin/project_costs/dashboard/'); ?>" data-link-data="<?php echo site_url('admin/cost/dashboard/'); ?>" class="mb-3 nav-link hrsale-link"> <span class="sw-done-icon ion ion-md-speedometer"></span> <span class="sw-icon ion ion-md-speedometer"></span> <?php echo $this->lang->line('ms_cost_dashboard'); ?>
					<div class="text-muted small"><?php echo $this->lang->line('ms_cost_dashboard'); ?></div>
				</a> </li>
		<?php } ?>
		<?php if (in_array('487', $role_resources_ids) || $reports_to > 0) { ?>
			<li class="nav-item clickable"> <a href="<?php echo site_url('admin/project_costs/transactions'); ?>" data-link-data="<?php echo site_url('admin/project_costs/transactions/'); ?>" class="mb-3 nav-link hrsale-link"> <span class="sw-done-icon fas fa-money-bill-wave"></span> <span class="sw-icon fas fa-money-bill-wave"></span> <?php echo $this->lang->line('ms_project_trans'); ?>
					<div class="text-muted small"><?php echo $this->lang->line('xin_set_up'); ?> <?php echo $this->lang->line('ms_project_trans'); ?></div>
				</a> </li>
		<?php } ?>
		<?php if (in_array('487', $role_resources_ids) || $reports_to > 0) { ?>
			<li class="nav-item clickable"> <a href="<?php echo site_url('admin/vendors/'); ?>" data-link-data="<?php echo site_url('admin/vendors/'); ?>" class="mb-3 nav-link hrsale-link"> <span class="sw-done-icon fas fa-user-friends"></span> <span class="sw-icon fas fa-user-friends"></span> <?php echo $this->lang->line('ms_vendors'); ?>
					<div class="text-muted small"><?php echo $this->lang->line('xin_set_up'); ?> <?php echo $this->lang->line('ms_vendors'); ?></div>
				</a> </li>
		<?php } ?>
		<?php if (in_array('487', $role_resources_ids) || $reports_to > 0) { ?>
			<li class="nav-item clickable"> <a href="<?php echo site_url('admin/products/'); ?>" data-link-data="<?php echo site_url('admin/products/'); ?>" class="mb-3 nav-link hrsale-link"> <span class="sw-done-icon fas fa-boxes"></span> <span class="sw-icon fas fa-boxes"></span> <?php echo $this->lang->line('ms_products'); ?>
					<div class="text-muted small"><?php echo $this->lang->line('xin_set_up'); ?> <?php echo $this->lang->line('ms_products'); ?></div>
				</a> </li>
		<?php } ?>
		<?php if (in_array('487', $role_resources_ids) || $reports_to > 0) { ?>
			<li class="nav-item clickable"> <a href="<?php echo site_url('admin/product_categories/'); ?>" data-link-data="<?php echo site_url('admin/product_categories'); ?>" class="mb-3 nav-link hrsale-link"> <span class="sw-done-icon fas fa-cogs"></span> <span class="sw-icon fas fa-cogs"></span> <?php echo $this->lang->line('ms_product_categories'); ?>
					<div class="text-muted small"><?php echo $this->lang->line('xin_set_up'); ?> <?php echo $this->lang->line('ms_product_categories'); ?></div>
				</a> </li>
		<?php } ?>
	</ul>
</div>

<hr class="border-light m-0 mb-3">
<div class="card mb-4 <?php echo $get_animate; ?>">
	<div id="accordion">
		<div class="card-header with-elements"> <span class="card-header-title mr-2"><strong><?php echo $this->lang->line('xin_add_new'); ?></strong> <?php echo $this->lang->line('ms_project_trans'); ?></span>
			<div class="card-header-elements ml-md-auto"> <a class="text-dark collapsed" data-toggle="collapse" href="#add_role_form" aria-expanded="false">
					<button type="button" class="btn btn-xs btn-primary"> <span class="ion ion-md-add"></span> <?php echo $this->lang->line('xin_add_new'); ?></button>
				</a> </div>
		</div>
		<div id="add_role_form" class="collapse add-form <?php echo $get_animate; ?>" data-parent="#accordion" style="">
			<div class="card-body">
				<div class="row m-b-1">
					<div class="col-md-12">
						<?php $attributes = array('name' => 'transaction', 'id' => 'transaction', 'autocomplete' => 'off', 'class' => 'm-b-1 add'); ?>
						<?php $hidden = array('transaction' => 'UPDATE'); ?>
						<?php echo form_open('admin/project_costs/create_transaction', $attributes, $hidden); ?>
						<input type="hidden" class="items-sub-total" name="items_sub_total" value="0" />
						<input type="hidden" class="items-tax-total" name="items_tax_total" value="0" />
						<div class="form-body">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="ms_vendor" class="control-label"><?php echo $this->lang->line('ms_vendor_name'); ?></label>
										<select class="form-control" name="vendor" data-plugin="select_hrm" data-placeholder="<?php echo $this->lang->line('ms_vendor_name'); ?>">
											<option value=""></option>
											<?php foreach ($vresult as $vendor) { ?>
												<option value="<?php echo $vendor->vendor_id ?>"> <?php echo $vendor->vendor_name ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="invoice_date"><?php echo $this->lang->line('xin_invoice_number'); ?></label>
										<input class="form-control" placeholder="<?php echo $this->lang->line('xin_invoice_number'); ?>" name="invoice_number" type="text">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="ms_vendor" class="control-label"><?php echo $this->lang->line('ms_status'); ?></label>
										<select class="form-control" name="status" data-plugin="select_hrm" data-placeholder="<?php echo $this->lang->line('ms_status'); ?>" onchange="getPrepayment(this)">
											<option value="0"><?= $this->lang->line('ms_status_pending') ?></option>
											<option value="1"><?= $this->lang->line('ms_status_prepayment') ?></option>
											<option value="2"><?= $this->lang->line('ms_status_paid') ?></option>
										</select>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="invoice_date"><?php echo $this->lang->line('xin_invoice_date'); ?></label>
										<input class="form-control date" placeholder="<?php echo $this->lang->line('xin_invoice_date'); ?>" readonly="readonly" name="invoice_date" type="text" value="">
									</div>
								</div>
								<div class="col-md-6" id="ms_prepayment">
									<!--  -->
								</div>
							</div>

							<div class="row">
								<div class="col-md-12">
									<div class="form-group overflow-hidden1">
										<div class="col-xs-12">
											<button type="button" data-repeater-create="" class="btn btn-primary" id="add-invoice-item" onclick="addRow()"> <i class="fa fa-plus"></i> <?php echo $this->lang->line('xin_title_add_item'); ?></button>
										</div>
									</div>
									<hr>
									<div class="table-responsive">
										<table class="datatables-demo table table-striped" id="item_product">
											<thead class="thead-light">
												<tr>
													<!-- <th>No</th> -->
													<th style="min-width:200px">Item</th>
													<th>Jenis Pajak</th>
													<th>Tarif Pajak</th>
													<th>Qty/Hr</th>
													<th>Price</th>
													<th>Subtotal</th>
													<th class="text-center">Action</th>
												</tr>
											</thead>
											<tbody id="formRow">
												<!-- <tr>
													<td><input type="text" class="form-control form-control-sm item_name" name="item_name[]" id="item_name" placeholder="Item Name"></td>
													<td>
														<select class="form-control form-control-sm tax_type" name="tax_type[]" id="tax_type">
															<option tax-type="fixed" tax-rate="0" value="1"> No Tax (Rp. 0)</option>
															<option tax-type="fixed" tax-rate="2" value="2"> IVU (Rp. 2)</option>
															<option tax-type="percentage" tax-rate="5" value="3"> VAT (5%)</option>
														</select>
													</td>
													<td><input type="number" readonly="readonly" class="form-control form-control-sm tax-rate-item" name="tax_rate_item[]" value="0"></td>
													<td><input type="hidden" name="qty[]" value="1"><input type="text" class="form-control form-control-sm qty" name="qty[]" id="qty" value="1"></td>
													<td><input type="number" name="price[]" class="form-control form-control-sm price" value="0" id="price"></td>
													<td><input type="number" class="form-control form-control-sm amount_item" readonly="readonly" name="amount_item[]" value="0"></td>
													<td style="text-align:center"><button type="button" class="btn icon-btn btn-xs btn-danger waves-effect waves-light remove-item" data-repeater-delete="" onclick="removeRow(row-0)"> <span class="fa fa-trash"></span></button></td>
												</tr> -->
											</tbody>
											<tfoot>
												<tr>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td style="border-bottom:1px solid #dddddd; text-align:left"><strong><?php echo $this->lang->line('xin_discount_type'); ?></strong></td>
													<td style="border-bottom:1px solid #dddddd; text-align:center"><strong><?php echo $this->lang->line('xin_discount'); ?></strong></td>
													<td colspan="2" style="border-bottom:1px solid #dddddd; text-align:left"><strong><?php echo $this->lang->line('xin_discount_amount'); ?></strong></td>
												</tr>
												<tr>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td>
														<div class="form-group">
															<select name="discount_type" class="form-control form-control-sm discount_type">
																<option value="1"> <?php echo $this->lang->line('xin_flat'); ?></option>
																<option value="2"> <?php echo $this->lang->line('xin_percent'); ?></option>
															</select>
														</div>
													</td>
													<td align="right">
														<div class="form-group">
															<input style="text-align:right" type="text" name="discount_figure" class="form-control form-control-sm discount_figure" value="0" data-valid-num="required">
														</div>
													</td>
													<td align="right" colspan="2">
														<div class="form-group">
															<input type="text" style="text-align:right" readonly="" name="discount_amount" value="0" class="discount_amount form-control form-control-sm">
														</div>
													</td>
												</tr>
												<tr>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td style="text-align:right"><?php echo $this->lang->line('xin_amount'); ?></td>
													<td align="right" colspan="2">
														<div class="form-group">
															<input type="text" style="text-align:right" readonly="" name="amount" value="0" class="total form-control form-control-sm">
														</div>
													</td>
												</tr>
											</tfoot>
										</table>
										<div class="row">
											<div class="col-md-7 col-sm-12 text-xs-center text-md-left">&nbsp; </div>
											<div class="col-md-5 col-sm-12">
												<div class="table-responsive">
													<?php
													$ar_sc = explode('- ', $system_setting[0]->default_currency_symbol);
													$sc_show = $ar_sc[1];
													?>
													<table class="table">
														<tbody>
															<tr>
																<td><?php echo $this->lang->line('xin_title_sub_total2'); ?></td>
																<td class="text-xs-right"><?php echo $sc_show; ?> <span class="sub_total">0</span></td>
															</tr>
															<tr>
																<td><?php echo $this->lang->line('xin_title_tax_c'); ?></td>
																<td class="text-xs-right"><?php echo $sc_show; ?> <span class="tax_total">0</span></td>
															</tr>
															<tr>
																<td colspan="2" style="border-bottom:1px solid #dddddd; padding:0px !important; text-align:left">
																	<table class="table table-bordered">
																		<tbody>
																			<tr>
																				<td width="30%" style="border-bottom:1px solid #dddddd; text-align:left"><strong><?php echo $this->lang->line('xin_discount_type'); ?></strong></td>
																				<td style="border-bottom:1px solid #dddddd; text-align:center"><strong><?php echo $this->lang->line('xin_discount'); ?></strong></td>
																				<td style="border-bottom:1px solid #dddddd; text-align:left"><strong><?php echo $this->lang->line('xin_discount_amount'); ?></strong></td>
																			</tr>
																			<tr>
																				<td>
																					<div class="form-group">
																						<select name="discount_type" class="form-control discount_type">
																							<option value="1"> <?php echo $this->lang->line('xin_flat'); ?></option>
																							<option value="2"> <?php echo $this->lang->line('xin_percent'); ?></option>
																						</select>
																					</div>
																				</td>
																				<td align="right">
																					<div class="form-group">
																						<input style="text-align:right" type="text" name="discount_figure" class="form-control discount_figure" value="0" data-valid-num="required">
																					</div>
																				</td>
																				<td align="right">
																					<div class="form-group">
																						<input type="text" style="text-align:right" readonly="" name="discount_amount" value="0" class="discount_amount form-control">
																					</div>
																				</td>
																			</tr>
																		</tbody>
																	</table>
																</td>
															</tr>
															<input type="hidden" class="fgrand_total" name="fgrand_total" value="0" />
															<tr>
																<td><?php echo $this->lang->line('xin_grand_total'); ?></td>
																<td class="text-xs-right"><?php echo $sc_show; ?> <span class="grand_total">0</span></td>
															</tr>
														</tbody>

													</table>
												</div>
											</div>
										</div>
									</div>
								</div>

							</div>
							<hr>
							<div class="row">
								<div class="col-md-12">
									<div class="form-actions box-footer float-right">
										<button type="submit" class="btn btn-primary"> <i class="far fa-check-square"></i> <?php echo $this->lang->line('xin_save'); ?> </button>
									</div>
								</div>
							</div>
						</div>
						<?php echo form_close(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="card <?php echo $get_animate; ?>">
	<div class="card-header with-elements"> <span class="card-header-title mr-2"><strong><?php echo $this->lang->line('xin_list_all'); ?></strong> <?php echo $this->lang->line('ms_project_trans'); ?></span>
	</div>
	<div class="card-body">
		<div class="box-datatable table-responsive">
			<table class="datatables-demo table table-striped" id="xin_table_project_costs">
				<thead>
					<tr>
						<th><?php echo $this->lang->line('xin_action'); ?></th>
						<th><?php echo $this->lang->line('ms_invoice_number'); ?></th>
						<th><?php echo $this->lang->line('ms_invoice_date'); ?></th>
						<th><?php echo $this->lang->line('ms_vendors'); ?></th>
						<th><?php echo $this->lang->line('ms_status'); ?></th>
						<th><?php echo $this->lang->line('ms_reference'); ?></th>
						<th><?php echo $this->lang->line('xin_amount'); ?></th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
</div>
<style type="text/css">
	.k-in {
		display: none !important;
	}
</style>

<script>
	function getPrepayment(e) {
		var html = '<div class="form-group"><label for="ms_prepayment"> <?php echo $this->lang->line('ms_prepayment'); ?></label><input class="form-control" placeholder="<?php echo $this->lang->line('ms_prepayment'); ?>" name="ms_prepayment" type="number" value = "0"></div>';
		var value = e.value;
		if (value == 1) {
			$('#ms_prepayment').html(html);
		} else {
			$('#ms_prepayment').html('');
		}
	}
</script>