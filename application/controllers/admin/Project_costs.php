<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Project_costs extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		//load the model
		// $this->load->model("Complaints_model");
		$this->load->model("Tax_model");
		// $this->load->model("Product_categories_model");
		$this->load->model("Employees_model");
		$this->load->model("Exin_model");

		$this->load->model("Vendor_model");
		$this->load->model("Product_model");
		$this->load->model("Project_model");
		$this->load->model("Project_costs_model");
	}

	/*Function to set JSON output*/
	public function output($Return = array())
	{
		/*Set response header*/
		header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json; charset=UTF-8");
		/*Final JSON response*/
		exit(json_encode($Return));
	}

	public function index()
	{
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}
		$data['title'] = $this->lang->line('ms_cost_dashboard') . ' | ' . $this->Xin_model->site_title();
		$data['all_employees'] = $this->Xin_model->all_employees();
		$data['get_all_companies'] = $this->Xin_model->get_companies();
		$data['breadcrumbs'] = $this->lang->line('ms_cost_dashboard');
		$data['path_url'] = 'project_costs';
		$role_resources_ids = $this->Xin_model->user_role_resource();
		if (in_array('19', $role_resources_ids)) {
			if (!empty($session)) {
				$data['subview'] = $this->load->view("admin/project_costs/dashboard", $data, TRUE);
				$this->load->view('admin/layout/layout_main', $data); //page load
			} else {
				redirect('admin/');
			}
		} else {
			redirect('admin/dashboard');
		}
	}

	public function complaint_list()
	{

		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		if (!empty($session)) {
			$this->load->view("admin/complaints/complaint_list", $data);
		} else {
			redirect('admin/');
		}
		// Datatables Variables
		$draw = intval($this->input->get("draw"));
		$start = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));



		$role_resources_ids = $this->Xin_model->user_role_resource();
		$user_info = $this->Xin_model->read_user_info($session['user_id']);
		if ($user_info[0]->user_role_id == 1) {
			$complaint = $this->Complaints_model->get_complaints();
		} else {
			if (in_array('237', $role_resources_ids)) {
				$complaint = $this->Complaints_model->get_company_complaints($user_info[0]->company_id);
			} else {
				$complaint = $this->Complaints_model->get_employee_complaints($session['user_id']);
			}
		}
		$data = array();

		foreach ($complaint->result() as $r) {

			/*if(in_array('376',$role_resources_ids)) {
				 $aim = explode(',',$r->complaint_against);
				 foreach($aim as $dIds) {
					 if($session['user_id'] == $dIds) {	
						
						// get user > added by
						$user = $this->Xin_model->read_user_info($r->complaint_from);
						// user full name
						if(!is_null($user)){
							$complaint_from = $user[0]->first_name.' '.$user[0]->last_name;
						} else {
							$complaint_from = '--';	
						}
					
						if($r->complaint_against == '') {
							$ol = '--';
						} else {
							$ol = '<ol class="nl">';
							foreach(explode(',',$r->complaint_against) as $desig_id) {
								$_comp_name = $this->Xin_model->read_user_info($desig_id);
								if(!is_null($_comp_name)){
									$ol .= '<li>'.$_comp_name[0]->first_name.' '.$_comp_name[0]->last_name.'</li>';
								} else {
									$ol .= '';
								}
							 }
							 $ol .= '</ol>';
						}
						// get complaint date
						$complaint_date = $this->Xin_model->set_date_format($r->complaint_date);
					
						if(in_array('223',$role_resources_ids)) { //edit
							$edit = '<span data-toggle="tooltip" data-placement="top" title="'.$this->lang->line('xin_edit').'"><button type="button" class="btn icon-btn btn-sm btn-outline-secondary waves-effect waves-light"  data-toggle="modal" data-target=".edit-modal-data"  data-complaint_id="'. $r->complaint_id . '"><span class="fas fa-pencil-alt"></span></button></span>';
						} else {
							$edit = '';
						}
						if(in_array('224',$role_resources_ids)) { // delete
							$delete = '<span data-toggle="tooltip" data-placement="top" title="'.$this->lang->line('xin_delete').'"><button type="button" class="btn icon-btn btn-sm btn-outline-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="'. $r->complaint_id . '">
							<span class="fas fa-trash-restore"></span></button></span>';
						} else {
							$delete = '';
						}
						if(in_array('237',$role_resources_ids)) { //view
							$view = '<span data-toggle="tooltip" data-placement="top" title="'.$this->lang->line('xin_view').'"><button type="button" class="btn icon-btn btn-sm btn-outline-secondary waves-effect waves-light" data-toggle="modal" data-target=".view-modal-data" data-complaint_id="'. $r->complaint_id . '"><span class="fa fa-eye"></span></button></span>';
						} else {
							$view = '';
						}
						// get company
						$company = $this->Xin_model->read_company_info($r->company_id);
						if(!is_null($company)){
							$comp_name = $company[0]->name;
						} else {
							$comp_name = '--';	
						}
						// get status
						if($r->status==0): $status = $this->lang->line('xin_pending');
						elseif($r->status==1): $status = $this->lang->line('xin_accepted'); else: $status = $this->lang->line('xin_rejected'); endif;
						
						$icomplaint_from = $complaint_from.'<br><small class="text-muted"><i>'.$this->lang->line('xin_description').': '.$r->description.'<i></i></i></small><br><small class="text-muted"><i>'.$status.'<i></i></i></small>';
						
						
						$combhr = $edit.$view.$delete;
						$data[] = array(
							$combhr,
							$icomplaint_from,
							$ol,
							$comp_name,
							$r->title,
							$complaint_date
						);
					 }
				}
			} else {*/
			// get user > added by
			$user = $this->Xin_model->read_user_info($r->complaint_from);
			// user full name
			if (!is_null($user)) {
				$complaint_from = $user[0]->first_name . ' ' . $user[0]->last_name;
			} else {
				$complaint_from = '--';
			}

			if ($r->complaint_against == '') {
				$ol = '--';
			} else {
				$ol = '<ol class="nl">';
				foreach (explode(',', $r->complaint_against) as $desig_id) {
					$_comp_name = $this->Xin_model->read_user_info($desig_id);
					if (!is_null($_comp_name)) {
						$ol .= '<li>' . $_comp_name[0]->first_name . ' ' . $_comp_name[0]->last_name . '</li>';
					} else {
						$ol .= '';
					}
				}
				$ol .= '</ol>';
			}
			// get complaint date
			$complaint_date = $this->Xin_model->set_date_format($r->complaint_date);

			if (in_array('223', $role_resources_ids)) { //edit
				$edit = '<span data-toggle="tooltip" data-placement="top" data-state="primary" title="' . $this->lang->line('xin_edit') . '"><button type="button" class="btn icon-btn btn-sm btn-outline-secondary waves-effect waves-light"  data-toggle="modal" data-target=".edit-modal-data"  data-complaint_id="' . $r->complaint_id . '"><span class="fas fa-pencil-alt"></span></button></span>';
			} else {
				$edit = '';
			}
			if (in_array('224', $role_resources_ids)) { // delete
				$delete = '<span data-toggle="tooltip" data-placement="top" data-state="danger"" title="' . $this->lang->line('xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-outline-danger" waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . $r->complaint_id . '">
					<span class="fas fa-trash-restore"></span></button></span>';
			} else {
				$delete = '';
			}
			if (in_array('237', $role_resources_ids)) { //view
				$view = '<span data-toggle="tooltip" data-placement="top" data-state="primary" title="' . $this->lang->line('xin_view') . '"><button type="button" class="btn icon-btn btn-sm btn-outline-secondary waves-effect waves-light" data-toggle="modal" data-target="#modals-slide" data-complaint_id="' . $r->complaint_id . '"><span class="fa fa-eye"></span></button></span>';
			} else {
				$view = '';
			}
			// get company
			$company = $this->Xin_model->read_company_info($r->company_id);
			if (!is_null($company)) {
				$comp_name = $company[0]->name;
			} else {
				$comp_name = '--';
			}
			// get status
			if ($r->status == 0) : $status = '<span class="badge bg-red">' . $this->lang->line('xin_pending') . '</span>';
			elseif ($r->status == 1) : $status = '<span class="badge bg-green">' . $this->lang->line('xin_accepted') . '</span>';
			else : $status = '<span class="badge bg-red">' . $this->lang->line('xin_rejected') . '</span>';
			endif;
			// info
			$icomplaint_from = $complaint_from . '<br><small class="text-muted"><i>' . $this->lang->line('xin_description') . ': ' . $r->description . '<i></i></i></small><br><small class="text-muted"><i>' . $status . '<i></i></i></small>';
			$combhr = $edit . $view . $delete;
			$data[] = array(
				$combhr,
				$icomplaint_from,
				$ol,
				$comp_name,
				$r->title,
				$complaint_date
			);
			//}


		}

		$output = array(
			"draw" => $draw,
			"recordsTotal" => $complaint->num_rows(),
			"recordsFiltered" => $complaint->num_rows(),
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}

	public function read()
	{
		$data['title'] = $this->Xin_model->site_title();
		$id = $this->input->get('complaint_id');
		$result = $this->Complaints_model->read_complaint_information($id);
		$data = array(
			'complaint_id' => $result[0]->complaint_id,
			'company_id' => $result[0]->company_id,
			'complaint_from' => $result[0]->complaint_from,
			'title' => $result[0]->title,
			'complaint_date' => $result[0]->complaint_date,
			'complaint_against' => $result[0]->complaint_against,
			'description' => $result[0]->description,
			'status' => $result[0]->status,
			'attachment' => $result[0]->attachment,
			'all_employees' => $this->Xin_model->all_employees(),
			'get_all_companies' => $this->Xin_model->get_companies()
		);
		$session = $this->session->userdata('username');
		if (!empty($session)) {
			$this->load->view('admin/complaints/dialog_complaint', $data);
		} else {
			redirect('admin/');
		}
	}

	// Validate and update info in database
	public function update()
	{

		if ($this->input->post('edit_type') == 'complaint') {

			$id = $this->uri->segment(4);

			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = $this->security->get_csrf_hash();

			/* Server side PHP input validation */
			$description = $this->input->post('description');
			$qt_description = htmlspecialchars(addslashes($description), ENT_QUOTES);

			if ($this->input->post('title') === '') {
				$Return['error'] = $this->lang->line('xin_error_complaint_title');
			} else if ($this->input->post('complaint_date') === '') {
				$Return['error'] = $this->lang->line('xin_error_complaint_date');
			}

			if ($Return['error'] != '') {
				$this->output($Return);
			}

			$data = array(
				'title' => $this->input->post('title'),
				'description' => $qt_description,
				'complaint_date' => $this->input->post('complaint_date'),
				'status' => $this->input->post('status'),
			);

			$result = $this->Complaints_model->update_record($data, $id);

			if ($result == TRUE) {
				$Return['result'] = $this->lang->line('xin_success_complaint_updated');
			} else {
				$Return['error'] = $this->lang->line('xin_error_msg');
			}
			$this->output($Return);
			exit;
		}
	}

	public function delete()
	{
		/* Define return | here result is used to return user data and error for error message */
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$id = $this->uri->segment(4);
		$Return['csrf_hash'] = $this->security->get_csrf_hash();
		$result = $this->Complaints_model->delete_record($id);
		if (isset($id)) {
			$Return['result'] = $this->lang->line('xin_success_complaint_deleted');
		} else {
			$Return['error'] = $this->lang->line('xin_error_msg');
		}
		$this->output($Return);
	}



	// trans
	public function transactions()
	{
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}
		$data['title'] = $this->lang->line('ms_project_trans') . ' | ' . $this->Xin_model->site_title();
		$data['all_employees'] = $this->Xin_model->all_employees();
		$data['get_all_companies'] = $this->Xin_model->get_companies();
		$data['breadcrumbs'] = $this->lang->line('ms_project_trans');
		$data['path_url'] = 'project_costs';

		$data['all_taxes'] = $this->Tax_model->get_all_taxes();

		$role_resources_ids = $this->Xin_model->user_role_resource();
		if (in_array('19', $role_resources_ids)) {
			if (!empty($session)) {
				$data['subview'] = $this->load->view("admin/project_costs/transaction_list", $data, TRUE);
				$this->load->view('admin/layout/layout_main', $data); //page load
			} else {
				redirect('admin/');
			}
		} else {
			redirect('admin/dashboard');
		}
	}

	public function get_ajax_table_transactions()
	{

		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		if (!empty($session)) {
			$this->load->view("admin/quotes/quotes_list", $data);
		} else {
			redirect('admin/');
		}
		// Datatables Variables
		$draw = intval($this->input->get("draw"));
		$start = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));


		$quotes = $this->Project_costs_model->get_all();
		$role_resources_ids = $this->Xin_model->user_role_resource();
		$data = array();

		foreach ($quotes->result() as $i => $r) {

			$amount = $this->Xin_model->currency_sign($r->amount);

			// get project
			$project = $this->Project_model->read_project_information($r->project_id);
			if (!is_null($project)) {
				$project_name = '<a href="' . site_url() . 'admin/project/detail/' .  $project[0]->project_id . '">' .  $project[0]->title . '</a>';
			} else {
				$project_name = '--';
			}


			// get vendor
			$vendor = $this->Vendor_model->read_vendor_information($r->vendor_id);
			// var_dump($vendor);
			if (!is_null($vendor)) {
				$vendor_name = $vendor[0]->vendor_name . '<br><small>' . $vendor[0]->vendor_address . '</small>';
			} else {
				$vendor_name = '--';
			}

			// get category
			$category = $this->Xin_model->read_category_information($r->category_id);
			if (!is_null($category)) {
				$category_name = $category[0]->category_name;
			} else {
				$category_name = '--';
			}


			$date = '<i class="far fa-calendar-alt position-left"></i> ' . $this->Xin_model->set_date_format($r->invoice_date);
			// $quote_due_date = '<i class="far fa-calendar-alt position-left"></i> ' . $this->Xin_model->set_date_format($r->quote_due_date);

			//invoice_number
			$invoice_id = '';
			if (in_array('330', $role_resources_ids)) { //view
				$invoice_id = '<a href="' . site_url() . 'admin/project_costs/view/' . $r->project_cost_id . '/">' . $r->invoice_id . '</a>';
			} else {
				$invoice_id = $r->invoice_id;
			}
			if (in_array('328', $role_resources_ids)) { //edit
				$edit = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_edit') . '"><a href="' . site_url() . 'admin/quotes/edit/' . $r->project_cost_id . '/"><button type="button" class="btn icon-btn btn-sm btn-outline-secondary waves-effect waves-light"><span class="fas fa-pencil-alt"></span></button></a></span>';
			} else {
				$edit = '';
			}
			if (in_array('329', $role_resources_ids)) { // delete
				$delete = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-outline-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . $r->project_cost_id . '"><span class="fas fa-trash-restore"></span></button></span>';
			} else {
				$delete = '';
			}
			if (in_array('330', $role_resources_ids)) { //view
				$view = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_view') . '"><a href="' . site_url() . 'admin/quotes/view/' . $r->project_cost_id . '/"><button type="button" class="btn icon-btn btn-sm btn-outline-secondary waves-effect waves-light""><span class="fa fa-arrow-circle-right"></span></button></a></span>';
			} else {
				$view = '';
			}
			// if ($r->status == 0) {
			// 	$status = '<span class="label label-warning">' . $this->lang->line('xin_quoted_title') . '</span>';
			// } else {
			// 	$status = '<span class="label label-success">' . $this->lang->line('xin_quote_invoiced') . '</span>';
			// }
			$quote_convert_record = 1;
			if ($quote_convert_record < 1) {
				$combhr = $edit . $view . $delete;
			} else {
				$combhr = $view . $delete;
			}

			$data[] = array(
				// $i += 1,
				$combhr,
				$project_name,
				$invoice_id,
				$date,
				$vendor_name,
				$r->ref_code,
				$amount
			);
		}

		$output = array(
			"draw" => $draw,
			"recordsTotal" => $quotes->num_rows(),
			"recordsFiltered" => $quotes->num_rows(),
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}

	public function view($id)
	{
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}
		$data['title'] = $this->lang->line('ms_project_trans') . ' | ' . $this->Xin_model->site_title();

		$result = $this->Project_costs_model->read_info($id);
		$data['products'] = $this->Product_model->read_info(json_decode($result[0]->product_id));
		$data['breadcrumbs'] = $this->lang->line('ms_project_trans_detail');
		$data['path_url'] = 'project_costs';

		$amount = $this->Xin_model->currency_sign($result[0]->amount);

		// get project
		$project = $this->Project_model->read_project_information($result[0]->project_id);
		if (!is_null($project)) {
			$project_name = '<a href="' . site_url() . 'admin/project/detail/' .  $project[0]->project_id . '">' .  $project[0]->title . '</a>';
		} else {
			$project_name = '--';
		}

		// get vendor
		$vendor = $this->Vendor_model->read_vendor_information($result[0]->vendor_id);
		// var_dump($vendor);
		if (!is_null($vendor)) {
			$vendor_name = $vendor[0]->vendor_name . '<br><small>' . $vendor[0]->vendor_address . '</small>';
		} else {
			$vendor_name = '--';
		}

		// get category
		$category = $this->Xin_model->read_category_information($result[0]->category_id);
		if (!is_null($category)) {
			$category_name = $category[0]->category_name;
		} else {
			$category_name = '--';
		}

		$date = '<i class="far fa-calendar-alt position-left"></i> ' . $this->Xin_model->set_date_format($result[0]->due_date);

		$hasil = new stdClass();
		$hasil->id = $result[0]->project_cost_id;
		$hasil->project_name = $project_name;
		$hasil->invoice_id = $result[0]->invoice_id;
		$hasil->invoice_date = $date;
		$hasil->vendor_name = $vendor_name;
		$hasil->ref_code = $result[0]->ref_code;
		$hasil->category_name = $category_name;
		$hasil->amount = $amount;

		$data['result'] = $hasil;


		$data['products'] = $this->Product_model->read_info(json_decode($result[0]->project_cost_id));

		var_dump($data['products']);
		$role_resources_ids = $this->Xin_model->user_role_resource();
		if (in_array('19', $role_resources_ids)) {
			if (!empty($session)) {
				$data['subview'] = $this->load->view("admin/project_costs/transaction_detail", $data, TRUE);
				$this->load->view('admin/layout/layout_main', $data); //page load
			} else {
				redirect('admin/');
			}
		} else {
			redirect('admin/dashboard');
		}
	}

	public function get_ajax_table_transaction_detail($id)
	{
		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');

		if (empty($session)) {
			redirect('admin/');
		}

		// Datatables Variables
		$draw = intval($this->input->get("draw"));
		$start = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));


		$quotes = $this->Project_costs_model->get_all();
		$role_resources_ids = $this->Xin_model->user_role_resource();
		$data = array();

		foreach ($quotes->result() as $i => $r) {

			$amount = $this->Xin_model->currency_sign($r->amount);

			// get project
			$project = $this->Project_model->read_project_information($r->project_id);
			if (!is_null($project)) {
				$project_name = '<a href="' . site_url() . 'admin/project/detail/' .  $project[0]->project_id . '">' .  $project[0]->title . '</a>';
			} else {
				$project_name = '--';
			}


			// get vendor
			$vendor = $this->Vendor_model->read_vendor_information($r->vendor_id);
			// var_dump($vendor);
			if (!is_null($vendor)) {
				$vendor_name = $vendor[0]->vendor_name . '<br><small>' . $vendor[0]->vendor_address . '</small>';
			} else {
				$vendor_name = '--';
			}

			// get category
			$category = $this->Xin_model->read_category_information($r->category_id);
			if (!is_null($category)) {
				$category_name = $category[0]->category_name;
			} else {
				$category_name = '--';
			}


			$date = '<i class="far fa-calendar-alt position-left"></i> ' . $this->Xin_model->set_date_format($r->due_date);
			// $quote_due_date = '<i class="far fa-calendar-alt position-left"></i> ' . $this->Xin_model->set_date_format($r->quote_due_date);

			//invoice_number
			$invoice_id = '';
			if (in_array('330', $role_resources_ids)) { //view
				$invoice_id = '<a href="' . site_url() . 'admin/project_costs/view/' . $r->project_cost_id . '/">' . $r->invoice_id . '</a>';
			} else {
				$invoice_id = $r->invoice_id;
			}

			$data[] = array(
				$i += 1,
				$combhr,
				$project_name,
				$invoice_id,
				$date,
				$vendor_name,
				$r->ref_code,
				$category_name,
				$amount
			);
		}

		$output = array(
			"draw" => $draw,
			"recordsTotal" => $quotes->num_rows(),
			"recordsFiltered" => $quotes->num_rows(),
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}
}
