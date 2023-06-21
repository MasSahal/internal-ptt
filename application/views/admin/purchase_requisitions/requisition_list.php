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
									<?php if ($user_info[0]->user_role_id == 10) { ?>
										<div class="col-md-6">
											<div class="form-group" id="employee_ajax">
												<label for="employee" required><?php echo $this->lang->line('xin_project_manager'); ?></label>
												<select name="assigned_to[]" class="form-control" data-plugin="select_hrm" data-placeholder="<?php echo $this->lang->line('xin_project_manager'); ?>" required>
													<option value=""></option>
												</select>
											</div>
										</div>
									<?php }; ?>
									<div class="col-md-6">
										<div class="form-group">
											<label for="pr_number" required><?php echo $this->lang->line('ms_purchase_number'); ?></label>
											<input class="form-control" placeholder="<?php echo $this->lang->line('ms_purchase_number'); ?>" id="pr_number" name=" pr_number" type="text" value="<?= $pr_number; ?>" readonly>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="priority_status" required><?php echo $this->lang->line('xin_p_priority'); ?></label>
											<select name="priority_status" id="priority_status" class="form-control" data-plugin="select_hrm" data-placeholder="<?php echo $this->lang->line('xin_p_priority'); ?>" required>
												<!-- <option value="1"><?php echo $this->lang->line('xin_highest'); ?></option> -->
												<option value="1"><?php echo $this->lang->line('xin_high'); ?></option>
												<option value="2"><?php echo $this->lang->line('xin_normal'); ?></option>
												<option value="3"><?php echo $this->lang->line('xin_low'); ?></option>
											</select>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="issue_date" required><?php echo $this->lang->line('ms_purchase_issue_date'); ?></label>
											<input class="form-control" placeholder="<?php echo $this->lang->line('ms_purchase_issue_date'); ?>" id="issue_date" name=" issue_date" type="date" required>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="due_approval_date" required><?php echo $this->lang->line('ms_purchase_due_approval_date'); ?></label>
											<input class="form-control" placeholder="<?php echo $this->lang->line('ms_purchase_due_approval_date'); ?>" id="due_approval_date" name=" due_approval_date" type="date" required>
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
											<label for="expedition_name"><?php echo $this->lang->line('ms_purchase_expedition_name'); ?></label>
											<input class="form-control" placeholder="<?php echo $this->lang->line('ms_purchase_expedition_name'); ?>" id="expedition_name" name="expedition_name" type="text">
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
														<th style="max-width:30px"><?php echo $this->lang->line('xin_title_qty_hrs'); ?></th>
														<th style="min-width:100px"><?php echo $this->lang->line('ms_ref_title_unit_price'); ?></th>
														<th style="min-width:100px"><?php echo $this->lang->line('xin_title_sub_total'); ?></th>
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
											<label for="purporse"><?php echo $this->lang->line('ms_purporse'); ?></label>
											<textarea class="form-control" placeholder="<?php echo $this->lang->line('ms_purporse'); ?>" name="ref_code" rows="3"></textarea>
										</div>
										<div class="form-group">
											<label for="pr_attachment"><?php echo $this->lang->line('xin_attachment'); ?>s</label>
											<input type="file" class="form-control" name="pr_attachment" id="pr_attachment">
										</div>
									</div>
									<div class="col-md-6">
										<table class="table table-borderless">
											<tr>
												<td class="text-right"><strong><?php echo $this->lang->line('ms_ref_delivery_fee'); ?></strong></td>
												<td class="text-center">
													<input type="hidden" name="ref_delivery_fee" value="0" id="ref_delivery_fee">
													<strong id="ref_delivery_fee_show" class="currency">0</strong>
												</td>
											</tr>
											<tr>
												<td class="text-right"><strong><?php echo $this->lang->line('xin_amount'); ?></strong></td>
												<td class="text-center">
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
								<div class=" row">
									<div class="col-md-12">

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
						<th><?php echo $this->lang->line('ms_purporse'); ?></th>
						<th><?php echo $this->lang->line('xin_p_priority'); ?></th>
						<th><?php echo $this->lang->line('ms_purchase_status'); ?></th>
						<th style="min-width:100px"><?php echo $this->lang->line('xin_amount'); ?></th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
</div>