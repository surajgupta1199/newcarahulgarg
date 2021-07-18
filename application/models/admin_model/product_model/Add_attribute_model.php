<?php
class Add_attribute_model extends CI_Model{
public $column_order = array(null, 'attribute_id','title','status'); //set column field database for datatable orderable
	public $column_search = array('title'); //set column field database for datatable searchable 

	

	
	public function _get_datatables_query()
	{
		$this->db->select('attribute_id,title,status,date_format(created_at, "%d %b %Y %r") as created_at')
				->from('attribute_master');
			$i = 0;	
		foreach ($this->column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				
				if($i===0) // first loop
				{
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->db->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column_search) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else
		{
			$order = array('cat_id' => 'asc');
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}


		public function get_cat_datatables()
	{
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}
	public function count_all()
	{
		$this->db->from('categorise_master');
		return $this->db->count_all_results();
	}

	public function count_filtered()
	{
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function check_assigned_attr($attr_id){
		return $this->db->select('*')
						->where('attr_type_id',$attr_id)
						->get('attribute_value_master')->result_array();
	}
	

}


 ?>