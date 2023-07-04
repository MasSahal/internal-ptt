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
		$output = array('status' => 'success', 'data' => []);

		/*Set response header*/
		header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json; charset=UTF-8");

		/*Final JSON response*/
		exit(json_encode($Return));
	}

	public function get_taxs()
	{
		$taxs = $this->Tax_model->get_taxes()->result();
		$data = [];
		foreach ($taxs as $i => $tax) {
			if ($tax->type == "fixed") {
				$text = $this->Xin_model->currency_sign($tax->rate);
			} else {
				$text = $tax->rate . "%";
			}
			$taxs[$i]->name = $tax->name . " ($text)";

			// $for = new stdClass();
			// $for->tax_id = $tax->tax_id;
			// $for->name = $tax->name . " ($text)";
			// $for->rate = $tax->rate;
			// $for->type = $tax->type;

			// $data[] = $for;
		}
		echo $this->output($taxs);
		exit();
	}

	public function get_discounts()
	{
		$res = $this->Xin_model->get_all_discounts()->result();
		foreach ($res as $i => $r) {
			if ($r->discount_type == 0) {
				$text = $this->Xin_model->currency_sign($r->discount_value);
			} else {
				$text = $r->discount_value . "%";
			}
			$res[$i]->discount_name = $r->discount_name . " ($text)";
		}
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
	// public function get_products()
	// {
	// 	if ($this->input->get('query')) {
	// 		$query = $this->input->get('query');
	// 	} else {
	// 		$query = false;
	// 	}
	// 	$res = $this->Product_model->gel_all_product($query)->result();

	// 	$data = [];
	// 	foreach ($res as $key => $r) {
	// 		$data[] = array(
	// 			'id' => $r->product_id,
	// 			'label' => $r->product_name,
	// 			'price' => $r->price,
	// 			'product_id' => $r->product_id,
	// 			'sub_category_id' => $r->sub_category_id,
	// 			'product_number' => $r->product_number,
	// 			'uom_id' => $r->uom_id,
	// 		);
	// 	}
	// 	echo $this->output($data);
	// 	exit();
	// }

	public function find_vendor()
	{
		$query = $this->input->get('query');
		$res = $this->Vendor_model->find_vendor($query);
		$data = [];
		foreach ($res as $key => $r) {
			$data[] = array(
				'id' => $r->vendor_id,
				'text' => $r->vendor_name,
			);
		}
		echo $this->output($data);
		exit();
	}

	public function find_product()
	{
		$query = $this->input->get('query');
		$res = $this->Product_model->find_product($query);
		$data = [];
		foreach ($res as $key => $r) {
			$data[] = array(
				'id' => $r->product_id,
				'text' => $r->product_name,
				'number' => $r->product_number,
				'price' => $r->price,
			);
		}
		echo $this->output($data);
		exit();
	}

	public function find_product_by_id()
	{

		$query = $this->input->get('query');
		$res = $this->Product_model->find_product_by_id($query);
		echo $this->output($res);
		exit();
	}

	public function find_project()
	{
		$query = $this->input->get('query');
		$res = $this->Xin_model->find_project($query);
		$data = [];
		foreach ($res as $key => $r) {
			$data[] = array(
				'id' => $r->project_id,
				'text' => $r->title,
			);
		}
		echo $this->output($data);
		exit();
	}

	public function find_tax()
	{
		$query = $this->input->get('query');
		$res = $this->Xin_model->find_tax($query);
		$data = [];

		foreach ($res as $key => $r) {
			if ($r->type == "fixed") {
				$text = $this->Xin_model->currency_sign($r->rate);
			} else {
				$text = $r->rate . "%";
			}

			$data[] = array(
				'id' => $r->tax_id,
				'text' => $r->name . " ($text)",
				'rate' => $r->rate,
			);
		}
		echo $this->output($data);
		exit();
	}

	public function find_tax_by_id()
	{
		$query = $this->input->get('query');
		$res = $this->Xin_model->find_tax_by_id($query);
		echo $this->output($res);
		exit();
	}


	public function find_discount()
	{
		$query = $this->input->get('query');
		$res = $this->Xin_model->find_discount($query);
		$data = [];

		foreach ($res as $key => $r) {
			if ($r->discount_type == 0) {
				$text = $this->Xin_model->currency_sign($r->discount_value);
			} else {
				$text = $r->discount_value . "%";
			}

			$data[] = array(
				'id' => $r->discount_id,
				'text' => $r->discount_name . " ($text)",
			);
		}
		echo $this->output($data);
		exit();
	}

	public function find_discount_by_id()
	{
		$query = $this->input->get('query');
		$res = $this->Xin_model->find_discount_by_id($query);
		echo $this->output($res);
		exit();
	}
}
