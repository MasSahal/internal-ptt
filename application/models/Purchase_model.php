<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Purchase_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}


	//! Role Purchase Requisition 
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

	public function read_pr_by_pr_number($id, $field = false)
	{
		if ($field) {
			$this->db->select($field);
		} else {
			$this->db->select("*");
		}
		$this->db->from("ms_purchase_requisitions");
		// dd($id);	
		return $this->db->where('pr_number', $id)->get()->row() ?? false;
	}

	public function get_last_pr_number()
	{
		return $this->db->select('pr_number')
			->from('ms_purchase_requisitions')
			->order_by('pr_number', 'desc')
			->limit(1)->get()->row();
	}

	public function insert_pr($data)
	{
		$this->db->insert('ms_purchase_requisitions', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function insert_items_pr($data)
	{
		$this->db->insert('ms_items_purchase_requisition', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function delete_pr($id)
	{
		// delete items
		$del_items = $this->db->where('pr_number', $id)->delete('ms_items_purchase_requisition');
		$del_pr = $this->db->where('pr_number', $id)->delete('ms_purchase_requisitions');

		if ($del_items && $del_pr) {
			return true;
		} else {
			return false;
		}
	}

	public function reject_pr($id)
	{
		$this->db->set('purchase_status', 0);
		$this->db->where('pr_number', $id);
		$this->db->update('ms_purchase_requisitions');
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}



	//! Role Pyrchase Orders
	public function get_all_po()
	{
		return $this->db->get("ms_purchase_orders");
	}

	public function get_last_po_number()
	{
		return $this->db->select('po_number')
			->from('ms_purchase_orders')
			->order_by('po_number', 'desc')
			->limit(1)->get()->row();
	}


	public function get_all_pd()
	{
		return $this->db->get("ms_purchase_deliveries");
	}

	public function get_all_pi()
	{
		return $this->db->get("ms_purchase_deliveries");
	}


	public function insert_po($data)
	{
		$this->db->insert('ms_purchase_orders', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function insert_pd($data)
	{
		$this->db->insert('ms_purchase_deliveries', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function insert_pi($data)
	{
		$this->db->insert('ms_purchase_invoices', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}
}
