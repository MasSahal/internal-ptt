<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Purchase_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function get_all_pr()
	{
		return $this->db->get("ms_purchase_requisitions");
	}

	public function read_pr($id, $field = false)
	{
		if ($field) {
			$this->db->select($field);
		} else {
			$this->db->select("*");
		}
		$this->db->from("ms_purchase_requisitions");
		return $this->db->where('pr_id', $id)->get()->row();
	}

	public function get_last_pr_number()
	{
		return $this->db->select('pr_number')
			->from('ms_purchase_requisitions')
			->order_by('pr_number', 'desc')
			->limit(1)->get()->row();
	}



	public function get_all_po()
	{
		return $this->db->get("ms_purchase_orders");
	}

	public function get_all_pd()
	{
		return $this->db->get("ms_purchase_deliveries");
	}

	public function get_all_pi()
	{
		return $this->db->get("ms_purchase_deliveries");
	}
}
