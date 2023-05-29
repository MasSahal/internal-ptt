<?php
/* Employees view
*/
?>
<?php $session = $this->session->userdata('username'); ?>
<?php $get_animate = $this->Xin_model->get_content_animate(); ?>
<?php $role_resources_ids = $this->Xin_model->user_role_resource(); ?>
<?php $user_info = $this->Xin_model->read_user_info($session['user_id']); ?>
<?php $system = $this->Xin_model->read_setting_info(1); ?>
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
<?php if (in_array('13', $role_resources_ids) || in_array('36', $role_resources_ids) || in_array('14', $role_resources_ids) || in_array('46', $role_resources_ids)) { ?>
	<div class="row">
		<?php if (in_array('13', $role_resources_ids)) { ?>
			<div class="col-sm-6 col-xl-3">
				<div class="card mb-4">
					<div class="card-body">
						<div class="d-flex align-items-center">
							<div class="ion ion-ios-contacts display-4 text-success"></div>
							<div class="ml-3">
								<div class="text-muted small"><?php echo $this->lang->line('dashboard_employees'); ?></div>
								<div class="text-large"><?php echo $this->Employees_model->get_total_employees(); ?></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php  } ?>
		<?php if (in_array('36', $role_resources_ids)) { ?>
			<div class="col-sm-6 col-xl-3">
				<div class="card mb-4">
					<div class="card-body">
						<div class="d-flex align-items-center">
							<div class="ion ion-ios-calculator display-4 text-info"></div>
							<div class="ml-3">
								<div class="text-muted small"><?php echo $this->lang->line('dashboard_total_salaries'); ?></div>
								<div class="text-large"><?php echo $this->Xin_model->currency_sign(total_salaries_paid()); ?></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php  } ?>
		<?php if (in_array('14', $role_resources_ids)) { ?>
			<div class="col-sm-6 col-xl-3">
				<div class="card mb-4">
					<div class="card-body">
						<div class="d-flex align-items-center">
							<div class="ion ion-ios-trophy display-4 text-danger"></div>
							<div class="ml-3">
								<div class="text-muted small"><?php echo $this->lang->line('left_awards'); ?></div>
								<div class="text-large"><?php echo $this->Exin_model->total_employee_awards_dash(); ?></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php  } ?>
		<?php if (in_array('46', $role_resources_ids)) { ?>
			<div class="col-sm-6 col-xl-3">
				<div class="card mb-4">
					<div class="card-body">
						<div class="d-flex align-items-center">
							<div class="ion ion-md-calendar display-4 text-warning"></div>
							<div class="ml-3">
								<div class="text-muted small"><?php echo $this->lang->line('xin_leave_request'); ?></div>
								<div class="text-large"><?php echo employee_request_leaves(); ?></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php  } ?>
	</div>
<?php  } ?>
<?php if (in_array('7', $role_resources_ids) || $user_info[0]->user_role_id == 1) { ?>
	<div class="row">
		<?php if (in_array('7', $role_resources_ids)) { ?>
			<div class="col-xl-6 col-md-6 align-items-strdetch">
				<!-- Daily progress chart -->
				<div class="card mb-4">
					<h6 class="card-header with-elements border-0 pr-0 pb-0">
						<div class="card-header-title"><?php echo $this->lang->line('left_office_shifts'); ?></div>
					</h6>
					<div class="row">
						<div class="col-md-6">
							<div class="overflow-scrolls py-4 px-3" style="overflow:auto; height:200px;">
								<div class="table-responsive">
									<table class="table mb-0 table-dashboard">
										<tbody>
											<?php $c_color = array('#647c8a', '#2196f3', '#02bc77', '#d3733b', '#673AB7', '#66456e', '#b26fc2', '#a98852', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e'); ?>
											<?php $j = 0;
											foreach (hrsale_office_shift() as $hr_office_shift) { ?>
												<?php
												$condition = "office_shift_id =" . "'" . $hr_office_shift->office_shift_id . "'";
												$this->db->select('*');
												$this->db->from('xin_employees');
												$this->db->where($condition);
												$query = $this->db->get();
												$r_row = $query->num_rows();
												?>
												<tr>
													<td style="vertical-align: inherit;">
														<div style="width:4px;border:5px solid <?php echo $c_color[$j]; ?>;"></div>
													</td>
													<td><?php echo htmlspecialchars_decode($hr_office_shift->shift_name); ?> (<?php echo $r_row; ?>)</td>
												</tr>
											<?php $j++;
											} ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div style="height:120px;">
								<canvas id="hrsale_office_shifts" style="display: block; height: 150px; width:300px;"></canvas>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php  } ?>
		<?php if ($user_info[0]->user_role_id == 1) { ?>
			<div class="col-xl-6 col-md-6 align-items-strdetch">

				<!-- Daily progress chart -->
				<div class="card mb-4">
					<h6 class="card-header with-elements border-0 pr-0 pb-0">
						<div class="card-header-title"><?php echo $this->lang->line('xin_roles'); ?></div>
					</h6>
					<div class="row">
						<div class="col-md-6">
							<div class="overflow-scrolls py-4 px-3" style="overflow:auto; height:200px;">
								<div class="table-responsive">
									<table class="table mb-0 table-dashboard">
										<tbody>
											<?php $c_color = array('#66456e', '#b26fc2', '#a98852', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e'); ?>
											<?php $j = 0;
											foreach (hrsale_roles() as $hr_roles) { ?>
												<?php
												$condition = "user_role_id =" . "'" . $hr_roles->role_id . "'";
												$this->db->select('*');
												$this->db->from('xin_employees');
												$this->db->where($condition);
												$query = $this->db->get();
												$r_row = $query->num_rows();
												?>
												<tr>
													<td style="vertical-align: inherit;">
														<div style="width:4px;border:5px solid <?php echo $c_color[$j]; ?>;"></div>
													</td>
													<td><?php echo htmlspecialchars_decode($hr_roles->role_name); ?> (<?php echo $r_row; ?>)</td>
												</tr>
											<?php $j++;
											} ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div style="height:120px;">
								<canvas id="hrsale_roles" style="display: block; height: 150px; width:300px;"></canvas>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php  } ?>
	</div>
<?php  } ?>