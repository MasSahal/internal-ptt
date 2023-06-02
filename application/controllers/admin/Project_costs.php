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


		// dd(array_keys($this->Project_costs_model->get_latest_month_trans_vendor()->result()));

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

	public function create_transaction()
	{
		// if ($this->input->post('type') == 'transaction') {
		/* Define return | here result is used to return user data and error for error message */
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();

		/* Server side PHP input validation */
		if ($this->input->post('vendor_name') === '') {
			$Return['error'] = $this->lang->line('ms_error_vendor_name_field');
		} else if ($this->input->post('vendor_name') === '') {
			$Return['error'] = $this->lang->line('ms_error_vendor_name_field');
		} else if ($this->input->post('vendor_contact') === '') {
			$Return['error'] = $this->lang->line('ms_error_vendor_contact_field');
		} else if ($this->input->post('vendor_address') === '') {
			$Return['error'] = $this->lang->line('ms_error_vendor_address_field');
		}


		$data_project_cost = array(
			'user_id' 				=> 0,
			'invoice_id' 			=> "PTT" . time(),
			'invoice_number'		=> $this->input->post('invoice_number'),
			'invoice_date' 			=> $this->input->post('invoice_date') ?? date("Y-m-d"),
			'vendor_id' 			=> $this->input->post('vendor'),
			'status' 				=> $this->input->post('status'),
			'prepayment' 			=> $this->input->post('prepayment') ?? 0,
			'discount_type' 		=> $this->input->post('discount_type'),
			'discount' 				=> $this->input->post('discount_amount'),
			'tax_total' 			=> $this->input->post('ftax_total') ?? 0,
			'amount' 				=> $this->input->post('fgrand_total') ?? 0,
			'ref_code' 				=> $this->input->post('ref_code'),
			'created_at' 			=> date('Y-m-d h:i:s')
		);

		$insert_project_cost = $this->Project_costs_model->insert($data_project_cost);

		if ($insert_project_cost) {
			//
			$data_insert = [];

			for ($i = 0; $i < count($this->input->post('item_name')); $i++) {
				$product_id 	= $this->input->post('product_id');
				// $category_id 	= $this->input->post('category_id');
				$product_name 	= $this->input->post('item_name');
				// $product_number = $this->input->post('product_number');
				$tax_type 		= $this->input->post('tax_type');
				$tax_rate_item	= $this->input->post('tax_rate_item');
				// $uom_id 		= $this->input->post('uom_id');
				$project_id		= $this->input->post('project_id');
				$qty			= $this->input->post('qty');
				$price			= $this->input->post('price');
				$sub_total_item	= $this->input->post('sub-total-item');

				$product_data = $this->Product_model->read_info($product_id[$i]);
				if (!is_null($product_data)) {
					$data_insert[] = [
						'product_id' 		=> $product_data[0]->product_id,
						'project_cost_id'	=> $insert_project_cost,
						'category_id' 		=> $product_data[0]->category_id,
						'product_name' 		=> $product_name[$i],
						'product_number' 	=> $product_data[0]->product_number,
						'uom_id' 			=> $product_data[0]->uom_id,
						'project_id' 		=> $project_id[$i],
						'tax_id' 			=> $tax_type[$i],
						'tax_rate' 			=> $tax_rate_item[$i],
						'qty' 				=> $qty[$i],
						'price' 			=> $price[$i],
						'amount' 			=> $sub_total_item[$i],
					];
				} else {
					$data_insert[] = [
						'product_id' 		=> 0,
						'category_id' 		=> 0,
						'project_cost_id'	=> $insert_project_cost,
						'product_name' 		=> $product_name[$i],
						'product_number' 	=> 0,
						'uom_id' 			=> 0,
						'project_id' 		=> $project_id[$i],
						'tax_id' 			=> $tax_type[$i],
						'tax_rate' 			=> $tax_rate_item[$i],
						'qty' 				=> $qty[$i],
						'price' 			=> $price[$i],
						'amount' 			=> $sub_total_item[$i],
					];
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

	public function get_last_month_trans_vendor()
	{

		/* Define return | here result is used to return user data and error for error message */
		$Return = array('chart_data' => '', 'c_name' => '', 'd_rows' => '', 'c_color' => '');
		$c_name = array();
		$d_rows = array();
		$c_color = array('#975df3', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b');
		$someArray = array();
		$j = 0;
		// dd($this->Project_costs_model->get_latest_month_trans_vendor()->result());
		foreach ($this->Project_costs_model->get_latest_month_trans_vendor()->result() as $r) {

			$condition = "vendor_id =" . "'" . $r->vendor_id . "'";
			$this->db->select('*');
			$this->db->from('ms_vendors');
			$this->db->where($condition);
			$query = $this->db->get();
			$check  = $query->row();

			// check if department available
			if ($query->num_rows() > 0) {
				$row = $query->num_rows();
				$d_rows[] = $row;
				$c_name[] = htmlspecialchars_decode($check->vendor_name);

				$someArray[] = array(
					'label'   => htmlspecialchars_decode($check->vendor_name),
					'value' => $r->amount,
					'bgcolor' => $c_color[$j]
				);
				$j++;
			}
		}
		$Return['c_name'] = $c_name;
		$Return['d_rows'] = $d_rows;
		$Return['chart_data'] = $someArray;
		$this->output($Return);
		exit;
	}


	public function get_last_month_trans()
	{

		/* Define return | here result is used to return user data and error for error message */
		$Return = array('chart_data' => '', 'c_name' => '', 'd_rows' => '', 'c_color' => '');
		$c_name = array();
		$d_rows = array();
		$c_color = ['#46be8a', '#f96868', '#00c0ef', '#66456e', '#c674ad', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#975df3', '#61a3ca', '#975df3', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#605ca8', '#d81b60', '#001f3f',  '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b',  '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b'];
		$someArray = array();

		$type = $this->input->get('type');
		$result = $this->Project_costs_model->get_latest_month_trans();
		// // dd($result);
		foreach ($result->result() as $j => $r) {
			$row = $result->num_rows();
			$d_rows[] = $row;
			$c_name[] = htmlspecialchars_decode($r->title);

			$someArray[] = array(
				'label'   => htmlspecialchars_decode($r->title),
				'value' => $r->total,
				'format_value' => $this->Xin_model->currency_sign($r->total),
				'bgcolor' => $c_color[$j]
			);
		}
		$Return['c_name'] = $c_name;
		$Return['d_rows'] = $d_rows;
		$Return['chart_data'] = $someArray;
		$this->output($Return);
		exit;


		// $color = array('#3c8dbc', '#f39c12', '#605ca8', '#66456e', '#39cccc', '#3c8dbc', '#006400', '#b26fc2', '#a98852', '#d81b60', '#001f3f', '#dd4b39', '#a98852', '#b26fc2', '#66456e', '#c674ad', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#3c8dbc', '#f39c12', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#b26fc2', '#66456e');
		// $color2 = array('#3c8dbc', '#006400', '#b26fc2', '#a98852', '#d81b60',  '#39cccc', '#a98852', '#b26fc2', '#66456e', '#001f3f', '#dd4b39', '#3c8dbc', '#f39c12', '#975df3', '#61a3ca', '#6bddbd', '#6bdd74', '#95b655', '#668b20', '#bea034', '#605ca8', '#66456e', '#c674ad',  '#d3733b', '#46be8a', '#f96868', '#00c0ef', '#605ca8', '#d81b60', '#001f3f', '#39cccc', '#3c8dbc', '#006400', '#dd4b39', '#a98852', '#3c8dbc', '#f39c12', '#b26fc2', '#66456e');

		// $data_vendor = array();
		// $data_jumlah_transaksi = array();
		// $res_2 = $this->Project_costs_model->get_latest_month_trans_vendor()->result();
		// foreach ($res_2 as $r) {
		// 	$data_vendor[] = $r->vendor_name;
		// 	$data_jumlah_transaksi[] = $r->total;
		// }

		// $data = array(
		// 	'title' => $data_vendor,
		// 	'value' => $data_jumlah_transaksi,
		// 	'bgcolor' => array_slice($color, 0, count($data_vendor)),
		// );

		// $this->output($data);
		// exit;
	}


	// trans
	public function transactions()
	{
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}
		$data['title'] = $this->lang->line('ms_project_trans') . ' | ' . $this->Xin_model->site_title();
		// $data['all_employees'] = $this->Xin_model->all_employees();
		// $data['get_all_companies'] = $this->Xin_model->get_companies();
		$data['projects'] = $this->Project_model->get_projects_name()->result();
		$data['breadcrumbs'] = $this->lang->line('ms_project_trans');
		$data['path_url'] = 'project_costs';

		$data['all_taxes'] = $this->Tax_model->get_all_taxes();

		// dd($data);
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

			// get vendor
			$vendor = $this->Vendor_model->read_vendor_information($r->vendor_id);
			if (!is_null($vendor)) {
				$vendor_name = $vendor[0]->vendor_name . '<br><small>' . $vendor[0]->vendor_address . '</small>';
			} else {
				$vendor_name = '--';
			}

			// recently product
			$rp = $this->Xin_model->read_id_category_recently_product($r->project_cost_id);
			// dd($rp);
			$category_res = $this->Project_costs_model->get_recently_category_name($rp);
			if (!is_null($category_res)) {


				$category_name = "<ul style='padding-left:0'>";
				$total_count = 0;

				foreach ($category_res as $cr) {
					$total_count += $cr->total_count;
				}

				foreach ($category_res as $cr) {
					$category_name .= '<li><small>' . $cr->total_count / $total_count * 100 . '%' . ' - ' . $cr->category_name  . '</small></li>';
				}
				$category_name .= "</ul>";
			} else {
				$category_name = '--';
			}

			// dd($category_name);
			// dd($category_name);
			$date = '<i class="far fa-calendar-alt position-left"></i> ' . $this->Xin_model->set_date_format($r->invoice_date);
			// $quote_due_date = '<i class="far fa-calendar-alt position-left"></i> ' . $this->Xin_model->set_date_format($r->quote_due_date);

			//invoice_number
			$invoice_id = '';
			if (in_array('330', $role_resources_ids)) { //view
				$invoice_id = '<a href="' . site_url() . 'admin/project_costs/view/' . $r->project_cost_id . '/">' . $r->invoice_id . '</a>';
			} else {
				$invoice_id = $r->invoice_id;
			}

			if (in_array('329', $role_resources_ids)) { //edit
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
				$view = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('xin_view') . '"><a href="' . site_url() . 'admin/project_costs/view/' . $r->project_cost_id . '/"><button type="button" class="btn icon-btn btn-sm btn-outline-secondary waves-effect waves-light""><span class="fa fa-arrow-circle-right"></span></button></a></span>';
			} else {
				$view = '';
			}

			if ($r->status == 0) {
				$status = '<span class="badge badge-danger">' . $this->lang->line('ms_status_pending') . '</span>';
			} else if ($r->status == 1) {
				$status = '<span class="badge badge-warning mb-1">' . $this->lang->line('ms_status_prepayment') . '</span><br><small><strong>' . $this->lang->line('ms_prepayment') . ':</strong> ' . $this->Xin_model->currency_sign($r->prepayment) . '</small>';
			} else {
				$status = '<span class="badge badge-success">' . $this->lang->line('ms_status_paid') . '</span>';
			}

			$combhr = $edit . $view . $delete;

			$data[] = array(
				// $i += 1,
				$combhr,
				$invoice_id,
				$date,
				$vendor_name,
				$status,
				$category_name,
				$r->ref_code,
				$amount
			);
		}

		// dd($data);
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
		// $data['products'] = $this->Xin_model->read_recently_product(json_decode($result[0]->product_id));
		$data['breadcrumbs'] = $this->lang->line('ms_project_trans_detail');
		$data['path_url'] = 'project_costs';

		$amount = $this->Xin_model->currency_sign($result[0]->amount);

		$project_name = '--';

		// get vendor
		$vendor = $this->Vendor_model->read_vendor_information($result[0]->vendor_id);
		// var_dump($vendor);
		if (!is_null($vendor)) {
			$vendor_name = $vendor[0]->vendor_name . '<br><small>' . $vendor[0]->vendor_address . '</small>';
		} else {
			$vendor_name = '--';
		}

		$date = '<i class="far fa-calendar-alt position-left"></i> ' . $this->Xin_model->set_date_format($result[0]->invoice_date);

		if ($result[0]->status == 0) {
			$status = '<span class="badge badge-danger">' . $this->lang->line('ms_status_pending') . '</span>';
		} else if ($result[0]->status == 1) {
			$status = '<span class="badge badge-warning mb-1">' . $this->lang->line('ms_status_prepayment') . '</span>';
		} else {
			$status = '<span class="badge badge-success">' . $this->lang->line('ms_status_paid') . '</span>';
		}
		$category_res = $this->Project_costs_model->get_recently_category_name($result[0]->project_cost_id);
		// dd($category_res);
		// dd($result);
		$hasil = new stdClass();
		$hasil->id = $result[0]->project_cost_id;
		$hasil->project_name = $project_name;
		$hasil->invoice_id = $result[0]->invoice_id;
		$hasil->invoice_number = $result[0]->invoice_number;
		$hasil->invoice_date = $date;
		$hasil->vendor_name = $vendor_name;
		$hasil->ref_code = $result[0]->ref_code;
		$hasil->category_name = "--";
		$hasil->amount = $amount;
		$hasil->status_payment = $status;
		$hasil->status_payment_code = $result[0]->status;
		$hasil->prepayment = $result[0]->prepayment;
		$hasil->tax_total = $result[0]->tax_total;
		$hasil->discount = $result[0]->discount;


		$data['result'] = $hasil;

		$record = [];
		$total = 0;
		$result2 = $this->Xin_model->read_recently_products($result[0]->project_cost_id);
		// dd($result2->result());

		// dd($result2->result());
		foreach ($result2->result() as $r) {

			$kategori = $this->Xin_model->read_product_category($r->category_id);
			if ($kategori) {
				$category_name = $kategori[0]->category_name;
			} else {
				$category_name = "--";
			}

			$proyek = $this->Project_model->read_project_information($r->project_id);
			if ($proyek) {
				$project_title = '<a href="' . site_url() . 'admin/project/detail/' . $proyek[0]->project_id . '">' . $proyek[0]->title . '</a>';
			} else {
				$project_title = "--";
			}

			$unit = $this->Xin_model->read_uom($r->uom_id);
			if ($unit == true) {
				$uom_name = $unit[0]->uom_name;
			} else {
				$uom_name = "--";
			}

			$d_project = $this->Project_model->read_project_information($r->project_id);
			// dd($d_project);
			if ($d_project) {
				$project_name = $d_project[0]->title;
			} else {
				$project_name = "--";
			}

			$res = new stdClass;
			$res->project_title = $project_title;
			$res->category_id = $category_name;
			$res->product_name = $r->product_name;
			$res->product_number = $r->product_number;
			$res->uom_id = $uom_name;
			$res->project_name = $project_name;
			$res->qty = $r->qty;
			$res->price = $this->Xin_model->currency_sign($r->price);
			$res->tax_rate = $r->tax_rate;
			$res->amount = $r->amount;
			array_push($record, $res);
			// $record[] = $res;
			$total = $total  + ($r->qty * $r->amount);
		}

		// var_dump($data['products']);

		$data['record'] = $record;
		$data['total'] = $total;
		$data['total_diskon'] = $result[0]->discount;
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

	public function dashboard()
	{
		return $this->index();
	}

	public function delete_transaction()
	{
		if ($this->input->post('type') == 'delete') {
			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			// $id = $this->uri->segment(4);
			$id = $this->input->post("_token");
			$Return['csrf_hash'] = $this->security->get_csrf_hash();
			$del_product = $this->Xin_model->delete_recently_product_record($id);

			$del_record = $this->Project_costs_model->delete_record($id);
			if ($del_product && $del_record) {
				$Return['result'] = $this->lang->line('ms_trans_deleted');
			} else {
				$Return['error'] = $this->lang->line('xin_error_msg');
			}
			$this->output($Return);
		}
	}
}
