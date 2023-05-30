<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Project_costs_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function get_all()
	{
		return $this->db->get("ms_project_costs");
	}

	public function read_info($id)
	{

		$sql = 'SELECT * FROM ms_project_costs WHERE project_cost_id = ?';
		$binds = array($id);
		$query = $this->db->query($sql, $binds);

		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	}


	public function get_like_product($id)
	{

		$sql = "SELECT * FROM ms_products WHERE product_name LIKE %$id%";
		$query = $this->db->query($sql);
		return $query;
	}

	public function get_employee_travel($id)
	{

		$sql = 'SELECT * FROM xin_employee_travels WHERE employee_id = ?';
		$binds = array($id);
		$query = $this->db->query($sql, $binds);
		return $query;
	}
	// get company travel
	public function get_company_travel($company_id)
	{

		$sql = 'SELECT * FROM xin_employee_travels WHERE company_id = ?';
		$binds = array($company_id);
		$query = $this->db->query($sql, $binds);
		return $query;
	}

	// get all travel arrangement types
	public function travel_arrangement_types()
	{
		$query = $this->db->query("SELECT * from xin_travel_arrangement_type");
		return $query->result();
	}

	// Function to add record in table
	public function add($data)
	{
		$this->db->insert('xin_employee_travels', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	// Function to Delete selected record from table
	public function delete_record($id)
	{
		$this->db->where('travel_id', $id);
		$this->db->delete('xin_employee_travels');
	}

	// Function to update record in table
	public function update_record($data, $id)
	{
		$this->db->where('travel_id', $id);
		if ($this->db->update('xin_employee_travels', $data)) {
			return true;
		} else {
			return false;
		}
	}

	public function get_trans_last_month()
	{
		$sql = "SELECT SUM(amount) AS total_amount FROM ms_recently_products";
		$query = $this->db->query($sql);
		return $query->row()->total_amount;
	}


	public function get_trans_remaining_payment()
	{
		$sql = "SELECT SUM(prepayment) AS prepayment FROM ms_project_costs";
		$query = $this->db->query($sql);
		return $query->row()->prepayment;
	}

	public function get_trans_prepayment()
	{
		$sql = "SELECT SUM(prepayment) AS prepayment FROM ms_project_costs";
		$query = $this->db->query($sql);
		return $query->row()->prepayment;
	}
}
