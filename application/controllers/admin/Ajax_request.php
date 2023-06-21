<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ajax_request extends MY_Controller
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

		// if (!$this->input->is_ajax_request()) {
		// 	$this->output([
		// 		'error' => 403
		// 	]);
		// 	exit('No direct script access allowed');
		// }
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

	public function get_taxs()
	{
		$taxs = $this->Tax_model->get_taxes()->result();
		echo $this->output($taxs);
		exit();
	}

	public function get_discounts()
	{
		$res = $this->Xin_model->get_all_discounts()->result();
		echo $this->output($res);
		exit();
	}

	public function get_projects()
	{
		$completed = $this->input->get('completed');
		$res = $this->Project_model->get_all_projects($completed);

		$data = [];
		foreach ($res as $key => $r) {
			$data[] = array(
				'id' => $r->project_id,
				'value' => $r->title,
			);
		}
		// dd($data);
		echo $this->output($data);
		exit();
	}

	public function get_products()
	{
		if ($this->input->get('query')) {
			$query = $this->input->get('query');
		} else {
			$query = false;
		}
		$res = $this->Product_model->gel_all_product($query)->result();

		$data = [];
		foreach ($res as $key => $r) {
			$data[] = array(
				'id' => $r->product_id,
				'label' => $r->product_name,
				'price' => $r->price,
				'product_id' => $r->product_id,
				'sub_category_id' => $r->sub_category_id,
				'product_number' => $r->product_number,
				'uom_id' => $r->uom_id,
			);
		}
		echo $this->output($data);
		exit();
	}
}
