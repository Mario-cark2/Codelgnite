<?php

    class Products_Model extends CI_Model {

    public function get_products($pagination,$segment){

    $this->db->order_by('marca', 'asc');
    $this->db->limit($pagination,$segment);
    $query = $this->db->get('productos')->result();

    return $query;

    }

    function get_product($id) {

    $query = $this->db->get_where('productos', array('id' => $id))->result();

    $result = $query[0];

    if ($result->valores) {
    $result->valores = explode(',',$result->valores);
    }

    return $result;
    }
  }