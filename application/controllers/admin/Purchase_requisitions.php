<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Purchase_requisitions extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		//load the model
		$this->load->model("Tax_model");
		$this->load->model("Exin_model");

		$this->load->model("Vendor_model");
		$this->load->model("Product_model");
		$this->load->model("Project_model");
		$this->load->model("Department_model");
		$this->load->model("Purchase_model");
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

	public function pr_number()
	{
		$query = $this->Purchase_model->get_last_pr_number();

		if (!is_null($query)) {
			// Extract the numeric part of the invoice number
			$numericPart = intval(substr($query->pr_number, 3));

			// Increment the numeric part
			$nextNumericPart = $numericPart + 1;
		} else {
			$nextNumericPart = 1;
		}

		// Create the new invoice number with the prefix and padded numeric part
		return sprintf("PR-%05d", $nextNumericPart);
	}

	public function index()
	{
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}
		$data['title'] = $this->lang->line('ms_purchase_requisitions') . ' | ' . $this->Xin_model->site_title();
		$data['breadcrumbs'] = $this->lang->line('ms_purchase_requisitions');
		$data['path_url'] = 'purchase_requisition';
		$data['pr_number'] = $this->pr_number();
		$role_resources_ids = $this->Xin_model->user_role_resource();
		// dd($data);
		if (in_array('470', $role_resources_ids)) {
			if (!empty($session)) {
				$data['subview'] = $this->load->view("admin/purchase_requisitions/requisition_list", $data, TRUE);
				$this->load->view('admin/layout/layout_main', $data); //page load
			} else {
				redirect('admin/');
			}
		} else {
			redirect('admin/dashboard');
		}
	}

	public function insert()
	{
		// Get the input data
		$pr_number = $this->input->post('pr_number');
		$priority_status = $this->input->post('priority_status');
		$purpose = $this->input->post('purpose');
		$items = $this->input->post('items');

		if ($this->input->is_ajax_request()) {
			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = $this->security->get_csrf_hash();

			/* Server side PHP input validation */
			if ($this->input->post('priority_status') === '') {
				$Return['error'] = $this->lang->line('ms_error_priority_status_field');
			} else if ($this->input->post('purpose') === '') {
				$Return['error'] = $this->lang->line('ms_error_purpose_field');
			}


			if (is_null($this->input->post('item_name'))) {
				$Return['error'] = $this->lang->line('ms_error_item_empty_data');
			}

			if ($Return['error'] != '') {
				$this->output($Return);
				exit();
			}

			// dd($this->input->post());
			$data_project_cost = array(
				'pr_number'		=> $this->input->post('pr_number'),
				'purchase_number'		=> $this->input->post('purchase_number'),
				'added_by' 				=> 0,
				'department_id'			=> $this->input->post('department_id'),
				'issue_date'			=> $this->input->post('issue_date') ?? date("Y-m-d"),
				'due_approval_date'		=> $this->input->post('due_approval_date'),
				'purpose' 				=> $this->input->post('purpose'),
				'priority_status'		=> $this->input->post('priority_status'),
				'purchase_status' 		=> 0,
				'pr_attachment'			=> $this->input->post('fpr_attachment')
			);

			$insert_project_cost = $this->Project_costs_model->insert($data_project_cost);

			if ($insert_project_cost) {
				//
				$data_insert = [];

				for ($i = 0; $i < count($this->input->post('item_name')); $i++) {

					$product_id 		= $this->input->post('product_id');
					$sub_category_id	= $this->input->post('sub_category_id');
					$product_name 		= $this->input->post('item_name');
					$product_number 	= $this->input->post('product_number');
					$tax_type 			= $this->input->post('tax_type');
					$tax_rate_item		= $this->input->post('tax_rate_item');
					$discount_type		= $this->input->post('discount_type');
					$discount_rate_item	= $this->input->post('discount_rate_item');
					$uom_id 			= $this->input->post('uom_id');
					$project_id			= $this->input->post('project_id');
					$qty				= $this->input->post('qty');
					$price				= $this->input->post('price');
					$sub_total_item		= $this->input->post('sub-total-item');

					$data_insert[] = [
						'product_id' 		=> $product_id[$i],
						'project_cost_id'	=> $insert_project_cost,
						'sub_category_id'	=> $sub_category_id[$i],
						'product_name' 		=> $product_name[$i],
						'product_number' 	=> $product_number[$i],
						'uom_id' 			=> $uom_id[$i],
						'project_id' 		=> $project_id[$i],
						'tax_id' 			=> $tax_type[$i],
						'tax_rate' 			=> $tax_rate_item[$i],
						'discount_id'		=> $discount_type[$i],
						'discount_rate'		=> $discount_rate_item[$i],
						'qty' 				=>	$qty[$i],
						'price' 			=> $price[$i],
						'amount' 			=> $sub_total_item[$i],
					];


					// jika id produk = 0 -> berarti kosong
					if ($product_id[$i] == 0) {
						$this->Product_model->insert(
							[
								'sub_category_id'	=> 0,
								'product_name' 		=> $product_name[$i],
								'product_number' 	=> "KD" . time() . rand(100, 990),
								'uom_id' 			=> 0,
								'price' 			=> $price[$i],
							]
						);
					}
				}

				$result = $this->Xin_model->add_recently_products($data_insert, true);
				if ($result) {
					$Return['result'] = $this->lang->line('ms_trans_added');
				} else {
					$Return['error'] = $this->lang->line('xin_error_msg');
				}
				$this->output($Return);
				exit;
			} else {
				$Return['error'] = $this->lang->line('xin_error_msg');
				$this->output($Return);
			}
		}
	}

	public function view()
	{
		dd($this->Purchase_model->read_pr(1));
	}

	public function get_ajax_table()
	{
		$role_resources_ids = $this->Xin_model->user_role_resource();

		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}
		// Datatables Variables
		$draw = intval($this->input->get("draw"));
		$start = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));


		$records = $this->Purchase_model->get_all_pr();

		$data = array();

		foreach ($records->result() as $r) {

			$pr_number = '<a href="' . site_url() . 'admin/purchase_requisitions/view/' . $r->pr_number . '/">' . $r->pr_number . '</a>';

			if (in_array('492', $role_resources_ids)) { //edit
				$edit = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_edit') . '"><button type="button" class="btn icon-btn btn-sm btn-outline-secondary waves-effect waves-light" data-toggle="modal" data-target=".edit-modal-data" data-field_id="' . $r->pr_id . '" data-field_type="purchase_requisitions"><span class="fas fa-pencil-alt"></span></button></span>';
			} else {
				$edit = '';
			}
			if (in_array('493', $role_resources_ids)) { // delete
				$delete = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-outline-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . $r->pr_id . '" data-token_type="purchase_requisitions"><span class="fas fa-trash-restore"></span></button></span>';
			} else {
				$delete = '';
			}

			$user = $this->Xin_model->read_user_info($r->added_by);
			// user full name
			if (!is_null($user)) {
				$full_name = $user[0]->first_name . ' ' . $user[0]->last_name;
				$dep = $this->Department_model->read_department_information($user[0]->department_id);

				if (!is_null($dep)) {
					$department = $dep[0]->department_name;
				} else {
					$department = '--';
				}
			} else {
				$full_name = '--';
				$department = '--';
			}

			$combhr = $edit . $delete;

			$data[] = array(
				$combhr,
				$pr_number,
				$full_name,
				$department,
				$this->Xin_model->set_date_time_format($r->issue_date),
				$r->purpose,
				priority_stats($r->priority_status),
				purchase_stats($r->purchase_status),
				$this->Xin_model->currency_sign($r->purchase_amount),
				$this->Xin_model->set_date_time_format($r->due_approval_date),
			);
		}


		$output = array(
			"draw" => $draw,
			"recordsTotal" => $records->num_rows(),
			"recordsFiltered" => $records->num_rows(),
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}
}
