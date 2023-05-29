<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Products extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		//load the model
		$this->load->model("Product_model");
		$this->load->model("Xin_model");
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
		$data['title'] = $this->lang->line('ms_products') . ' | ' . $this->Xin_model->site_title();
		$data['breadcrumbs'] = $this->lang->line('ms_products');
		$data['all_countries'] = $this->Xin_model->get_countries();

		$data['path_url'] = 'products';
		$role_resources_ids = $this->Xin_model->user_role_resource();
		if (in_array('19', $role_resources_ids)) {
			if (!empty($session)) {
				$data['subview'] = $this->load->view("admin/products/product_list", $data, TRUE);
				$this->load->view('admin/layout/layout_main', $data); //page load
			} else {
				redirect('admin/');
			}
		} else {
			redirect('admin/dashboard');
		}
	}

	public function read()
	{
		// $id = $this->input->get('role_id');
		// $result = $this->Roles_model->read_role_information($id);
		// $data = array(
		// 	'role_id' => $result[0]->role_id,
		// 	'role_name' => $result[0]->role_name,
		// 	'role_access' => $result[0]->role_access,
		// 	'role_resources' => $result[0]->role_resources,
		// 	'get_all_companies' => $this->Xin_model->get_companies(),
		// );
		// $session = $this->session->userdata('username');
		// if (!empty($session)) {
		// 	$this->load->view('admin/roles/dialog_role', $data);
		// } else {
		// 	redirect('admin/');
		// }
	}

	public function ajax_get_products()
	{
		$output = '';
		if (isset($_POST["query"])) {

			$query = $this->input->post('query');

			$result = $this->Product_model->get_like_product($query)->result();

			$output = '<ul class="list-unstyled">';

			if (mysqli_num_rows($result) > 0) {
				foreach ($result as $r) {
					$output .= '<li>' . $r->product_name . '</li>';
				}
			} else {
				$output .= '<li>User Not Found</li>';
			}

			$output .= '</ul>';
		}

		echo json_encode($output);
		exit();
	}


	public function add_product()
	{
		if ($this->input->post('type') == 'products') {
			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = $this->security->get_csrf_hash();

			/* Server side PHP input validation */
			if ($this->input->post('product_number') === '') {
				$Return['error'] = $this->lang->line('ms_product_error_product_number');
			} else if ($this->input->post('product_name') === '') {
				$Return['error'] = $this->lang->line('ms_product_error_product_name');
			} else if ($this->input->post('uom_id') === '') {
				$Return['error'] = $this->lang->line('ms_product_error_uom_id');
			} else if ($this->input->post('category_id') === '') {
				$Return['error'] = $this->lang->line('ms_product_error_category_id');
			} else if ($this->input->post('price') === '') {
				$Return['error'] = $this->lang->line('ms_product_error_price');
			}

			if ($Return['error'] != '') {
				$this->output($Return);
			}

			$data = array(
				'product_number' => $this->input->post('product_number'),
				'product_name' => $this->input->post('product_name'),
				'uom_id' => $this->input->post('uom_id'),
				'category_id' => $this->input->post('category_id'),
				'price' => $this->input->post('price'),
				'product_desc' => $this->input->post('product_desc'),
				'created_at' => date('Y-m-d H:i:s')
			);

			$result = $this->Product_model->insert($data);
			if ($result == TRUE) {
				$Return['result'] = $this->lang->line('ms_product_success_added');
			} else {
				$Return['error'] = $this->lang->line('xin_error_msg');
			}
			$this->output($Return);
			exit;
		}
	}


	public function product_list()
	{

		//get data role akses
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


		$query = $this->Product_model->gel_all_product();

		$data = array();

		foreach ($query->result() as $r) {

			$price = $this->Xin_model->currency_sign($r->price);

			// get category
			$category = $this->Xin_model->read_product_category($r->product_id);
			if (!is_null($category)) {
				$category_name = $category[0]->category_name;
			} else {
				$category_name = '--';
			}

			// get uom
			$uom = $this->Xin_model->read_uom($r->uom_id);
			if (!is_null($uom)) {
				$uom_name = $uom[0]->uom_name;
			} else {
				$uom_name = '--';
			}

			if (in_array('217', $role_resources_ids)) { //edit
				$edit = '<span data-toggle="tooltip" data-placement="top" data-state="primary" title="' . $this->lang->line('xin_edit') . '"><button type="button" class="btn icon-btn btn-sm btn-outline-secondary waves-effect waves-light"  data-toggle="modal" data-target=".edit-modal-data"  data-product_id="' . $r->product_id . '"><span class="fas fa-pencil-alt"></span></button></span>';
			} else {
				$edit = '';
			}
			if (in_array('218', $role_resources_ids)) { // delete
				$delete = '<span data-toggle="tooltip" data-placement="top" data-state="danger" title="' . $this->lang->line('xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-outline-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . $r->product_id . '"><span class="fas fa-trash-restore"></span></button></span>';
			} else {
				$delete = '';
			}

			$combhr = $edit . $delete;

			$data[] = array(
				$combhr,
				$r->product_number,
				$r->product_name,
				$price,
				$uom_name,
				$category_name,
				$r->product_desc,
			);
		}
		$output = array(
			"draw" => $draw,
			"recordsTotal" => $query->num_rows(),
			"recordsFiltered" => $query->num_rows(),
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}

	public function delete_product()
	{
		/* Define return | here result is used to return user data and error for error message */
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();
		$id = $this->uri->segment(4);
		$this->Product_model->delete($id);
		if (isset($id)) {
			$Return['result'] = $this->lang->line('ms_product_success_deleted');
		} else {
			$Return['error'] = $this->lang->line('xin_error_msg');
		}
		$this->output($Return);
	}


	// category
	public function categories()
	{
		$session = $this->session->userdata('username');
		if (empty($session)) {
			redirect('admin/');
		}
		$data['title'] = $this->lang->line('ms_categories') . ' | ' . $this->Xin_model->site_title();
		$data['breadcrumbs'] = $this->lang->line('ms_categories');
		$data['all_countries'] = $this->Xin_model->get_countries();

		$data['path_url'] = 'categories';
		$role_resources_ids = $this->Xin_model->user_role_resource();
		if (in_array('19', $role_resources_ids)) {
			if (!empty($session)) {
				$data['subview'] = $this->load->view("admin/products/categories/category_list", $data, TRUE);
				$this->load->view('admin/layout/layout_main', $data); //page load
			} else {
				redirect('admin/');
			}
		} else {
			redirect('admin/dashboard');
		}
	}
}
