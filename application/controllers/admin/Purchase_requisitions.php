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
		$this->load->model("Purchase_items_model");
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
		// if ($this->input->is_ajax_request()) {
		if (true) {
			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = $this->security->get_csrf_hash();

			/* Server side PHP input validation */
			if ($this->input->post('priority_status') === '') {
				$Return['error'] = $this->lang->line('ms_error_priority_status_field');
			} else if ($this->input->post('purpose') === '') {
				$Return['error'] = $this->lang->line('ms_error_purpose_field');
			}


			if (is_null($this->input->post('row_item_name'))) {
				$Return['error'] = $this->lang->line('ms_error_item_empty_data');
			}

			if ($Return['error'] != '') {
				$this->output($Return);
				exit();
			}

			$user_id = $this->session->userdata()['username']['user_id'] ?? 0;
			$department_id = $this->session->userdata()['username']['department_id'] ?? 0;

			// dd($this->input->post());
			$pr_number = $this->input->post('pr_number');
			$data_pr = array(
				'pr_number'				=> $pr_number,
				'purchase_number'		=> $pr_number, //maybe deleted
				'added_by' 				=> $user_id,
				'department_id'			=> $department_id,
				'issue_date'			=> $this->input->post('issue_date'),
				'due_approval_date'		=> $this->input->post('due_approval_date'),
				'purpose' 				=> $this->input->post('purpose'),
				'priority_status'		=> $this->input->post('priority_status'),
				'purchase_status' 		=> 1,
				'attachment'			=> $this->input->post('attachment'),
				'amount'				=> array_sum($this->input->post('row_amount')),
			);

			$insert_pr = $this->Purchase_model->insert_pr($data_pr);

			if ($insert_pr) {
				//
				$this->insert_items_pr($pr_number, true);
			} else {
				$Return['error'] = $this->lang->line('xin_error_msg');
				$this->output($Return);
			}
		}
	}

	public function view()
	{
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}

		$id = $this->uri->segment(4);
		$record = $this->Purchase_model->read_pr_by_pr_number($id);
		$data['title'] = $this->lang->line('ms_purchase_requisitions') . ' | ' . $this->Xin_model->site_title();
		$data['breadcrumbs'] = $this->lang->line('ms_detail') . $this->lang->line('ms_purchase_requisitions') . " " . $record->pr_number;
		$data['record'] = $record;
		$data['path_url'] = 'purchase_requisition';
		// dd($data);
		$role_resources_ids = $this->Xin_model->user_role_resource();
		if (in_array('121', $role_resources_ids) and !is_null($record)) {
			$data['subview'] = $this->load->view("admin/purchase_requisitions/view", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('admin/dashboard');
		}
	}

	public function insert_items_pr($pr_number, $batch = false)
	{
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();

		if ($batch) {
			$item_insert = [];

			for ($i = 0; $i < count($this->input->post('row_ref_item')); $i++) {

				$ref_item 			= $this->input->post('row_ref_item');
				$item_name 			= $this->input->post('row_item_name');
				$project 			= $this->input->post('row_project_id');
				$qty 				= $this->input->post('row_qty');
				$ref_price			= $this->input->post('row_ref_price');
				$amount				= $this->input->post('row_amount');

				$item_insert[] = [
					'item_name' 		=> $item_name[$i],
					'ref_item' 			=> $ref_item[$i],
					'pr_number' 		=> $pr_number,
					'project_id'		=> $project[$i],
					'quantity'			=> $qty[$i],
					'ref_price'			=> $ref_price[$i],
					'amount' 			=> $amount[$i],
				];
			}
			$result = $this->Purchase_items_model->insert_items_pr($item_insert, true);
		} else {
			$item_insert = [
				'item_name' 		=> $this->input->post('row_item_name'),
				'pr_number' 		=> $pr_number,
				'project_id'		=> $this->input->post('row_project_id'),
				'quantity'			=> $this->input->post('row_qty'),
				'ref_price'			=> $this->input->post('row_ref_amount'),
				'amount' 			=> $this->input->post('row_amount'),
			];
			$result = $this->Purchase_items_model->insert_items_pr($item_insert);
		}

		if ($result) {
			$Return['result'] = $this->lang->line('ms_trans_added');
		} else {
			$Return['error'] = $this->lang->line('xin_error_msg');
		}

		$this->output($Return);
		exit;
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
				$edit = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_edit') . '"><button type="button" class="btn icon-btn btn-sm btn-outline-secondary waves-effect waves-light" data-toggle="modal" data-target=".edit-modal-data" data-field_id="' . $r->pr_number . '" data-field_type="purchase_requisitions"><span class="fas fa-pencil-alt"></span></button></span>';
			} else {
				$edit = '';
			}
			if (in_array('493', $role_resources_ids)) { // delete
				$delete = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-outline-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . $r->pr_number . '" data-token_type="purchase_requisitions"><span class="fas fa-trash-restore"></span></button></span>';
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
				$full_name, //added by
				$department,
				$this->Xin_model->set_date_format($r->issue_date),
				// $this->Xin_model->set_date_time_format($r->due_approval_date),
				$r->purpose ?? "--",
				priority_stats($r->priority_status),
				purchase_stats($r->purchase_status),
				"<strong>" . $this->Xin_model->currency_sign($r->amount) . "</strong>"
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

	public function get_ajax_table_items()
	{
		$role_resources_ids = $this->Xin_model->user_role_resource();

		$data['title'] = $this->Xin_model->site_title();
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}
		$id = $this->uri->segment(4);

		// Datatables Variables
		$draw = intval($this->input->get("draw"));
		$start = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));


		$records = $this->Purchase_items_model->read_items_pr_by_pr_number($id);
		// dd($records);
		$data = array();

		if (!is_null($records)) {

			foreach ($records->result() as $r) {

				$project = $this->Xin_model->get_field('xin_projects', ['title', 'project_id'], 'project_id', $r->project_id)->row();

				// dd($project);
				$data[] = array(
					$r->item_name,
					"<a href='" . site_url() . "admin/project/detail/" . $project->project_id . "' class='m-0 p-0'>" . $project->title . "</a>" ?? "--",
					$this->Xin_model->currency_sign($r->ref_price),
					$r->quantity,
					$this->Xin_model->currency_sign($r->amount),
				);
				// dd($this->Xin_model->currency_sign($r->amount));
			}
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

	public function delete()
	{
		$role_resources_ids = $this->Xin_model->user_role_resource();

		/* Define return | here result is used to return user data and error for error message */
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();
		if (in_array($this->input->post('is_ajax'), $role_resources_ids)) {
			$id = $this->input->post('_token');
			$result = $this->Purchase_model->delete_pr($id);
			if (isset($id)) {
				$Return['result'] = $this->lang->line('ms_success_pr_deleted');
			} else {
				$Return['error'] = $this->lang->line('xin_error_msg');
			}
			$this->output($Return);
		}
	}

	public function reject()
	{
		$role_resources_ids = $this->Xin_model->user_role_resource();

		/* Define return | here result is used to return user data and error for error message */
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();

		// if (in_array($this->input->post('is_ajax'), $role_resources_ids)) {
		$id = $this->input->post('_token');
		$result = $this->Purchase_model->reject_pr($id);
		if ($result) {
			$res_data = $this->Xin_model->get_field('ms_purchase_requisitions', 'purchase_status', 'pr_number', $id)->row();
			$Return['result'] = $this->lang->line('ms_success_pr_deleted');
			$Return['status'] = purchase_stats($res_data->purchase_status);
		} else {
			$Return['error'] = $this->lang->line('xin_error_msg');
		}
		// } else {
		// 	$Return['error'] = $this->lang->line('xin_error_msg');
		// }

		$this->output($Return);
	}
}
