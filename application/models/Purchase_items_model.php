<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Purchase_items_model extends CI_Model
{
	public function insert_items_pr($data, $batch = false)
	{
		if ($batch) {
			$this->db->insert_batch('ms_items_purchase_requisition', $data);
		} else {
			$this->db->insert('ms_items_purchase_requisition', $data);
		}

		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function read_items_pr_by_pr_number($id)
	{
		return $this->db->select("*")->from("ms_items_purchase_requisition")->where('pr_number', $id)->get();
	}
}
