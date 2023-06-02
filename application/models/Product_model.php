<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Product_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function gel_all_product()
	{
		return $this->db->get("ms_products");
	}

	public function get_like_product($id)
	{

		$sql = "SELECT * FROM ms_products WHERE product_name LIKE %$id%";
		$query = $this->db->query($sql);
		return $query;
	}

	public function read_info($id)
	{

		$sql = 'SELECT * FROM ms_products WHERE product_id = ?';
		$binds = array($id);
		$query = $this->db->query($sql, $binds);

		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	}

	// Function to add record in table
	public function insert($data)
	{
		$this->db->insert('ms_products', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	// Function to Delete selected record from table
	public function delete($id)
	{
		$this->db->where('product_id', $id);
		$this->db->delete('ms_products');
	}

	// Function to update record in table
	public function update($data, $id)
	{
		$this->db->where('product_id', $id);
		if ($this->db->update('ms_products', $data)) {
			return true;
		} else {
			return false;
		}
	}

	public function searchProduct($searchTerm)
	{
		// Query untuk mencari produk berdasarkan product_name atau product_number
		$this->db->select('product_id, product_name, product_number, uom_id, category_id');
		$this->db->like('product_name', $searchTerm);
		$this->db->or_like('product_number', $searchTerm);
		$result = $this->db->get('ms_products');
		return $result->result();
	}
}
