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
<?php if (in_array('474', $role_resources_ids)) { ?>

	<style>
		/* Chrome, Safari, Edge, Opera */
		input[type="number"][data-type="currency"]::-webkit-outer-spin-button,
		input[type="number"][data-type="currency"]::-webkit-inner-spin-button {
			-webkit-appearance: none;
			margin: 0;
			text-align: right;
		}

		/* Firefox */
		input[type="number"][data-type="currency"] {
			-moz-appearance: textfield;
			text-align: right;
		}
	</style>
	<div class="card mb-4 <?php echo $get_animate; ?>">
		<div id="accordion">
			<div class="card-header with-elements"> <span class="card-header-title mr-2"><strong><?php echo $this->lang->line('xin_add_new'); ?></strong> <?php echo $this->lang->line('ms_purchase_requisitions'); ?></span>
				<div class="card-header-elements ml-md-auto"> <a class="text-dark collapsed" data-toggle="collapse" href="#add_role_form" aria-expanded="false">
						<button type="button" class="btn btn-xs btn-primary"> <span class="ion ion-md-add"></span> <?php echo $this->lang->line('xin_add_new'); ?></button>
					</a> </div>
			</div>
			<div id="add_role_form" class="collapse show add-form <?php echo $get_animate; ?>" data-parent="#accordion" style="">
				<div class="card-body">
					<div class="row m-b-1">
						<div class="col-md-12">
							<?php $attributes = array('name' => 'purchase_requisitions', 'id' => 'purchase_requisitions', 'autocomplete' => 'off', 'class' => 'm-b-1 add', 'enctype' => 'multipart/form-data'); ?>
							<?php $hidden = array('purchase_requisitions' => 'INSERT',); ?>
							<?php echo form_open('admin/purchase_requisitions/insert', $attributes, $hidden); ?>
							<input type="hidden" class="items-sub-total" name="items_sub_total" value="0" />
							<input type="hidden" class="items-tax-total" name="items_tax_total" value="0" />
							<div class="form-body">
								<div class="row mb-3">
									<div class="col-md-6">
										<div class="form-group">
											<label for="ms_vendor" class="control-label"><?php echo $this->lang->line('ms_vendor_name'); ?></label>
											<select class="form-control" name="vendor" data-plugin="select_vendor" data-placeholder="<?php echo $this->lang->line('ms_vendor_name'); ?>">
											</select>
											<input id="pr_number" name="pr_number" type="hidden" value="<?= $pr_number; ?>">

										</div>
										<!-- <option value=""></option>
										<?php foreach ($vresult as $vendor) { ?>
											<option value="<?php echo $vendor->vendor_id ?>"> <?php echo $vendor->vendor_name ?></option>
										<?php } ?> -->
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="po_number"><?php echo $this->lang->line('ms_purchase_faktur_number'); ?></label>
											<input class="form-control" placeholder="<?php echo $this->lang->line('ms_purchase_faktur_number'); ?>" id="po_number" name="po_number" type="text" value="<?= $pr_number; ?>" readonly required>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="faktur_number"><?php echo $this->lang->line('ms_purchase_faktur_number'); ?></label>
											<input class="form-control" placeholder="<?php echo $this->lang->line('ms_purchase_faktur_number'); ?>" id="faktur_number" name="faktur_number" type="text" required>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="warehouse_assign"><?php echo $this->lang->line('ms_warehouse_assign'); ?></label>
											<input class="form-control" placeholder="<?php echo $this->lang->line('ms_warehouse_assign'); ?>" id="warehouse_assign" name="warehouse_assign" type="text" required>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="transaction_date"><?php echo $this->lang->line('ms_purchase_date'); ?></label>
											<input class="form-control" placeholder="<?php echo $this->lang->line('ms_purchase_date'); ?>" id="transaction_date" name="transaction_date" type="date" required>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="due_date"><?php echo $this->lang->line('xin_invoice_due_date'); ?></label>
											<input class="form-control" placeholder="<?php echo $this->lang->line('xin_invoice_due_date'); ?>" id="due_date" name=" due_date" type="date" required>
										</div>
									</div>
									<div class=" col-md-4">
										<div class="form-group">
											<label for="select_due_date" class="control-label"><?php echo $this->lang->line('xin_select'); ?> <?php echo $this->lang->line('xin_invoice_due_date'); ?></label>
											<select class="form-control" name="select_due_date" id="select_due_date" data-plugin="select_hrm" data-placeholder="<?php echo $this->lang->line('xin_invoice_due_date'); ?>">
												<option value="0" selected><?= $this->lang->line('ms_custom'); ?></option>
												<option value="1" data-type="days">1 <?= $this->lang->line('xin_day'); ?></option>
												<option value="3" data-type="days">3 <?= $this->lang->line('xin_day'); ?></option>
												<option value="7" data-type="days">7 <?= $this->lang->line('xin_day'); ?></option>
												<option value="10" data-type="days">10 <?= $this->lang->line('xin_day'); ?></option>
												<option value="15" data-type="days">15 <?= $this->lang->line('xin_day'); ?></option>
												<option value="20" data-type="days">20 <?= $this->lang->line('xin_day'); ?></option>
												<option value="1" data-type="months">1 <?= $this->lang->line('xin_month'); ?></option>
												<option value="3" data-type="months">3 <?= $this->lang->line('xin_month'); ?></option>
												<option value="6" data-type="months">6 <?= $this->lang->line('xin_month'); ?></option>
												<option value="9" data-type="months">9 <?= $this->lang->line('xin_month'); ?></option>
												<option value="1" data-type="years">1 <?= $this->lang->line('xin_year'); ?></option>
												<option value="2" data-type="years">2 <?= $this->lang->line('xin_year'); ?></option>
											</select>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<h5><?php echo $this->lang->line('ms_purchase_shipping_information'); ?></h5>
										<hr>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="expedition_name"><?php echo $this->lang->line('ms_purchase_ref_expedition_name'); ?></label>
											<input class="form-control" placeholder="<?php echo $this->lang->line('ms_purchase_ref_expedition_name'); ?>" id="expedition_name" name="expedition_name" type="text">
										</div>
									</div>
								</div>

								<br>
								<div class="row">
									<div class="col-md-12">
										<label class="h5" required><?php echo $this->lang->line('ms_purchase_items'); ?></label>
										<hr>
									</div>
									<div class="col-md-12">
										<div class="table-responsive">
											<table class="table table-striped table-hover table-cell-input" id="item_product">
												<thead class="thead-light">
													<tr>
														<th style="min-width:100px"><?php echo $this->lang->line('xin_title_item'); ?></th>
														<th><?php echo $this->lang->line('xin_project'); ?></th>
														<th><?php echo $this->lang->line('xin_title_taxes'); ?></th>
														<th><?php echo $this->lang->line('xin_discount'); ?></th>
														<th style="width:100px"><?php echo $this->lang->line('xin_title_qty'); ?></th>
														<th style="min-width:100px"><?php echo $this->lang->line('ms_ref_title_unit_price'); ?></th>
														<th style="min-width:150px"><?php echo $this->lang->line('xin_title_sub_total'); ?></th>
														<th class="text-center"><?php echo $this->lang->line('xin_action'); ?></th>
													</tr>
												</thead>
												<tbody id="formRow">
												</tbody>
												<tfoot>
													<tr>
														<td>
															<button type="button" data-repeater-create="" class="btn btn-success" id="add-invoice-item" onclick="addRow()"> <i class="fa fa-plus"></i> <?php echo $this->lang->line('xin_title_add_item'); ?></button>
														</td>
													</tr>
												</tfoot>

											</table>
										</div>
									</div>
								</div>
								<hr>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="purpose"><?php echo $this->lang->line('ms_purpose'); ?></label>
											<textarea class="form-control" placeholder="<?php echo $this->lang->line('ms_purpose'); ?>" name="purpose" rows="3"></textarea>
										</div>
										<div class="form-group">
											<label for="pr_attachment"><?php echo $this->lang->line('xin_attachment'); ?>s</label>
											<input type="file" class="form-control" name="pr_attachment" id="pr_attachment">
										</div>
									</div>
									<div class="col-md-6">
										<table class="table table-borderless">
											<tr>
												<td class="text-right"><strong><?php echo $this->lang->line('ms_delivery_fee'); ?></strong></td>
												<td class="text-right">
													<input type="number" min="0" class="form-control delivery_fee" data-type="currency" data-inputmask="'alias': 'datetime'" name="delivery_fee" value="0" id="delivery_fee">
													<!-- <strong id="ref_delivery_fee_show" class="currency">0</strong> -->
												</td>
											</tr>
											<tr>
												<td class="text-right"><strong><?php echo $this->lang->line('xin_amount'); ?></strong></td>
												<td class="text-right">
													<input type="hidden" name="amount" value="0" id="amount">
													<strong id="amount_show" class="currency">0</strong>
												</td>
											</tr>
											<tr>
												<td></td>
												<td>
													<button type="submit" class="btn btn-primary btn-block"> <i class="far fa-check-square"></i> <?php echo $this->lang->line('xin_save'); ?> </button>
												</td>
											</tr>
										</table>
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

<?php } ?>

<div class="card <?php echo $get_animate; ?>">
	<div class="card-header with-elements"> <span class="card-header-title mr-2"><strong><?php echo $this->lang->line('xin_list_all'); ?></strong> <?php echo $this->lang->line('ms_purchase_requisitions'); ?></span>
	</div>
	<div class="card-body">
		<div class="box-datatable table-responsive">
			<table class="datatables-demo table table-striped" id="xin_table_purchase_requisitions">
				<thead>
					<tr>
						<th><?php echo $this->lang->line('xin_action'); ?></th>
						<th><?php echo $this->lang->line('ms_purchase_number'); ?></th>
						<th><?php echo $this->lang->line('xin_employee_name'); ?></th>
						<th><?php echo $this->lang->line('xin_departments'); ?></th>
						<th><?php echo $this->lang->line('ms_date'); ?></th>
						<th><?php echo $this->lang->line('ms_purpose'); ?></th>
						<th><?php echo $this->lang->line('xin_p_priority'); ?></th>
						<th><?php echo $this->lang->line('ms_purchase_status'); ?></th>
						<th style="min-width:100px"><?php echo $this->lang->line('xin_amount'); ?></th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
</div>