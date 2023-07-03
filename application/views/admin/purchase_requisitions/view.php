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
		<div class="card-header with-elements justify-content-end align-center">
			<span class="card-header-title mr-2 my-0">
				<a name="" id="" class="btn btn-sm btn-transparent pl-2" href="<?= base_url('admin/purchase_requisitions'); ?>" role="button"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>
			</span>
			<strong id="pr_id" data-id="<?= $record->pr_number; ?>"><?= purchase_stats($record->purchase_status); ?></strong>
			<div class="ml-md-auto">
				<?php if ($record->purchase_status == 1) {; ?>
					<!-- <a href="#" class="ml-2 btn btn-sm btn-danger" id="btnReject"> <span class="ion ion-md-remove-circle"></span> <?php echo $this->lang->line('ms_reject_purchase'); ?></a> -->
					<?php $attributes = array('name' => 'formReject', 'id' => 'formReject', 'autocomplete' => 'off', 'class' => 'm-b-1'); ?>
					<?php $hidden = array('purchase_requisitions' => 'UPDATE', '_token' => $record->pr_number); ?>
					<?php echo form_open('admin/purchase_requisitions/reject', $attributes, $hidden); ?>
					<a href="<?= base_url('admin/purchase_orders?id=' . $record->pr_number); ?>" class="btn btn-sm btn-primary"> <span class="ion ion-md-add"></span> <?php echo $this->lang->line('ms_create_purchase_orders'); ?></a>
					<a href="<?= base_url('admin/purchase_requisitions/print?id=' . $record->pr_number) ?>" class="ml-2 btn btn-sm btn-info"> <span class="ion ion-md-print"></span> <?php echo $this->lang->line('xin_print'); ?></a>
					<button type="submit" class="ml-2 btn btn-sm btn-danger" id="btnReject" onclick="return confirm('<?php echo $this->lang->line('ms_confirm_reject_purchase'); ?>')"> <span class="ion ion-md-remove-circle"></span> <?php echo $this->lang->line('ms_reject_purchase'); ?></button>
					<?php echo form_close(); ?>
				<?php } else { ?>
					<a href="<?= base_url('admin/purchase_requisitions/print/' . $record->pr_number) ?>" class="ml-2 btn btn-sm btn-info"> <span class="ion ion-md-print"></span> <?php echo $this->lang->line('xin_print'); ?></a>
				<?php }; ?>
			</div>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-12">
					<table class="table table-borderless">
						<tr>
							<th>
								<label><?php echo $this->lang->line('ms_purchase_number'); ?></label><br>
								<strong><?= $record->pr_number; ?></strong>
							</th>
							<th>
								<label><?php echo $this->lang->line('xin_p_priority'); ?></label><br>
								<strong><?= priority_stats($record->priority_status); ?></strong>
							</th>

						</tr>
						<tr>
							<th>
								<label><?php echo $this->lang->line('ms_purchase_issue_date'); ?></label><br>
								<strong><?= $this->Xin_model->set_date_format($record->issue_date); ?></strong>
							</th>
							<th>
								<label><?php echo $this->lang->line('ms_purchase_due_approval_date'); ?></label><br>
								<strong><?= $this->Xin_model->set_date_format($record->due_approval_date); ?></strong>
							</th>
						</tr>
						<tr>
							<th>
								<label><?php echo $this->lang->line('ms_purchase_ref_expedition_name'); ?></label><br>
								<strong><?= $record->expedition ?? "--"; ?></strong>
							</th>
						</tr>
					</table>
				</div>
			</div>
			<br>
			<div class="row">
				<div class="col-md-12 mb-3">
					<label class="h5 mb-3"><?php echo $this->lang->line('ms_purchase_items'); ?></label>
				</div>
				<div class="col-md-12">
					<div class="table-responsive">
						<table class="table table-striped table" id="ms_table_items">
							<thead class="thead-light">
								<tr>
									<th><?php echo $this->lang->line('xin_title_item'); ?></th>
									<th><?php echo $this->lang->line('xin_project'); ?></th>
									<th><?php echo $this->lang->line('ms_ref_title_unit_price'); ?></th>
									<th><?php echo $this->lang->line('xin_title_qty'); ?></th>
									<th style="min-width: 150px;"><?php echo $this->lang->line('xin_title_sub_total'); ?></th>
									<!-- <th class="text-center"><?php echo $this->lang->line('xin_action'); ?></th> -->
								</tr>
							</thead>
							<tbody id="formRow">
							</tbody>
							<tfoot>
								<tr>
									<td colspan="4" class="text-right"><strong><?php echo $this->lang->line('xin_amount'); ?></strong></td>
									<td>
										<strong class="text-danger"><?= $this->Xin_model->currency_sign($record->amount); ?></strong>
									</td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
			<div class="row m-b-1">
				<div class="col-md-12">

				</div>
			</div>
		</div>
	</div>

<?php } ?>