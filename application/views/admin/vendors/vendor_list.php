<?php
/* User Roles view
*/
?>
<?php $session = $this->session->userdata('username'); ?>
<?php $get_animate = $this->Xin_model->get_content_animate(); ?>
<?php $role_resources_ids = $this->Xin_model->user_role_resource(); ?>
<?php $user_info = $this->Xin_model->read_user_info($session['user_id']); ?>
<?php

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
		<div class="card-header with-elements"> <span class="card-header-title mr-2"><strong><?php echo $this->lang->line('xin_add_new'); ?></strong> <?php echo $this->lang->line('ms_vendors'); ?></span>
			<div class="card-header-elements ml-md-auto"> <a class="text-dark collapsed" data-toggle="collapse" href="#add_role_form" aria-expanded="false">
					<button type="button" class="btn btn-xs btn-primary"> <span class="ion ion-md-add"></span> <?php echo $this->lang->line('xin_add_new'); ?></button>
				</a> </div>
		</div>
		<div id="add_role_form" class="collapse add-form <?php echo $get_animate; ?>" data-parent="#accordion" style="">
			<div class="card-body">
				<div class="row m-b-1">
					<div class="col-md-12">
						<?php $attributes = array('name' => 'vendors', 'id' => 'vendors', 'autocomplete' => 'off', 'class' => 'm-b-1 add'); ?>
						<?php $hidden = array('vendors' => 'UPDATE'); ?>
						<?php echo form_open('admin/vendors/create', $attributes, $hidden); ?>
						<div class="form-body">
							<div class="form-group">
								<label class="form-label"><?php echo $this->lang->line('ms_vendor_name'); ?></label>
								<input type="text" class="form-control" name="vendor_name" placeholder="<?php echo $this->lang->line('ms_vendor_name'); ?>">
							</div>
							<div class="form-group">
								<label class="form-label"><?php echo $this->lang->line('ms_vendor_contact'); ?></label>
								<input type="text" class="form-control" name="vendor_contact" placeholder="<?php echo $this->lang->line('ms_vendor_contact'); ?>">
							</div>
							<div class="form-group">
								<label class="form-label"><?php echo $this->lang->line('ms_vendor_address'); ?></label>
								<textarea class="form-control" name="vendor_address" placeholder="<?php echo $this->lang->line('ms_vendor_address'); ?>"></textarea>
							</div>
							<div class="form-group">
								<div class="row">
									<div class="col-md-4">
										<!-- <label class="form-label"><?php echo $this->lang->line('xin_city'); ?></label> -->
										<input class="form-control" placeholder="<?php echo $this->lang->line('xin_city'); ?>" name="city" type="text">
									</div>
									<div class="col-md-4">
										<!-- <label class="form-label"><?php echo $this->lang->line('xin_state'); ?></label> -->
										<input class="form-control" placeholder="<?php echo $this->lang->line('xin_state'); ?>" name="state" type="text">
									</div>
									<div class="col-md-4">
										<!-- <label class="form-label"><?php echo $this->lang->line('xin_zipcode'); ?></label> -->
										<input class="form-control" placeholder="<?php echo $this->lang->line('xin_zipcode'); ?>" name="zipcode" type="text">
									</div>
								</div>
							</div>
							<div class="form-group">
								<!-- <label class="form-label"><?php echo $this->lang->line('xin_country'); ?></label> -->
								<select class="form-control" name="country" data-plugin="select_hrm" data-placeholder="<?php echo $this->lang->line('xin_country'); ?>">
									<option value=""><?php echo $this->lang->line('xin_select_one'); ?></option>
									<?php foreach ($all_countries as $country) { ?>
										<option value="<?php echo $country->country_id; ?>"> <?php echo $country->country_name; ?></option>
									<?php } ?>
								</select>
							</div>

							<div class="form-actions box-footer">
								<button type="submit" class="btn btn-primary"> <i class="far fa-check-square"></i> <?php echo $this->lang->line('xin_save'); ?> </button>
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
	<div class="card-header with-elements"> <span class="card-header-title mr-2"><strong><?php echo $this->lang->line('xin_list_all'); ?></strong> <?php echo $this->lang->line('ms_vendors'); ?></span>
		<div class="card-header-elements ml-md-auto"> <a href="<?php echo site_url('admin/reports/'); ?>" class="text-dark collapsed">
				<button type="button" class="btn btn-xs btn-primary"> <span class="fas fa-chart-bar"></span> <?php echo $this->lang->line('xin_report'); ?></button>
			</a> </div>
	</div>
	<div class="card-body">
		<div class="box-datatable table-responsive">
			<table class="datatables-demo table table-striped table-bordered" id="xin_table_vendors">
				<thead>
					<tr>
						<th><?php echo $this->lang->line('xin_action'); ?></th>
						<th><?php echo $this->lang->line('xin_name'); ?></th>
						<th><?php echo $this->lang->line('ms_vendor_contact'); ?></th>
						<th><?php echo $this->lang->line('xin_address'); ?></th>
						<th><?php echo $this->lang->line('xin_address'); ?></th>
						<th><?php echo $this->lang->line('xin_country'); ?></th>
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