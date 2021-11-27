<?php

    class Products extends CI_Controller {

    function __construct() {

    	parent::__Construct();
    	$this ->load->model('Products_Model'); // Cargamos el Modelo
	$this->load->library(array('pagination', 'cart','form_validation','email','table')); // Cargamos las librerias
        $this->load->helper('text'); // Cargamos el helper
    }

    public function index(){

	 $pagination = 3;
    	 $config['base_url'] = base_url().'products/index';
	 $config['total_rows'] = $this->db->get('productos')->num_rows();
    	 $config['per_page'] = $pagination;
    	 $config['num_links'] = 20;
    	 $config['next_link'] = 'Siguiente »';
    	 $config['prev_link'] = '« Anterior';	
	
	 $this->pagination->initialize($config);

    	$data['title']='Computadoras portatiles';
    	$data['results']= $this->Products_Model->get_products($pagination,$this->uri->segment(3));

    	$this->load->view('front/header',$data);
	$this->load->view('front/content');
	$this->load->view('front/footer');

    }

    public function add() {

    $segment = $this -> input -> post('segment');
    $url = base_url() . 'products/index/' . $segment;

    $id = $this -> input -> post('id');
    $product = $this -> Products_Model -> get_product($id);
    $option = $this -> input -> post($product -> opcion);

    if ($product -> opcion) {// si el producto tiene opciones las colocamos en un arreglo

        foreach ($product->valores as $key => $values) {

            $value[] = $values;

        }

        $id_option = $id . $value[$option];
        // se crea una variable como identificador único
        $selected = $value[$option];
        // la opción seleccionada está en la posición $option
    }

    $row = '';

    if ($cart = $this -> cart -> contents()) {// verificamos si el carrito existe

        foreach ($cart as $item) {//foreach contenedor

            if ($item['id'] === $id && !$product -> valores) {

                $row = $item['rowid'] . "-" . ($item['qty'] + 1);
                break;
                // si se cumple la condición el foreach dejará de ejecutarse
            }

            if ($this -> cart -> has_options($item['rowid'])) {

                foreach ($this->cart->product_options($item['rowid']) as $key => $options) {// foreach interno

                    $cart_option = $item['id'] . $options;

                    if ($cart_option === $id_option) {

                        $row = $item['rowid'] . "-" . ($item['qty'] + 1);
                        break;
                    }
                } // fin del foreach interno

            } // fin del if que evalua si los productos insertados en el carrtito tienen opciones

        }// fin del foreach contenedor

    }// fin del if que evalua si el carrito existe

    /* la variable $row contiene el rowid y el qty de cada producto concatenados; si esta
     * variable no está vacia significa que se debe actualizar el producto */

    if ($row !== '') {

        $this -> update($row, $url);

    } else {
        $insert = array('id' => $id, 'qty' => 1, 'price' => $product -> precio, 'name' => convert_accented_characters($product -> marca) // para quitar los acentos
        );

        if ($selected !== Null) {

            $insert['options'] = array($product -> opcion => $selected);

        }

        $this -> cart -> insert($insert);
        redirect($url);
        // si en Windows da algún problema remplazarlo por: redirect($url, 'refresh');
    }

}// fin de la método  add

public function update($row, $url) {

    $row = explode('-', $row);
    $this -> cart -> update(array('rowid' => $row[0], 'qty' => $row[1]));

    redirect($url);

}

public function update_cart($row) {

    $row = explode('-', $row);
    $this -> cart -> update(array('rowid' => $row[0], 'qty' => $row[1]));

    redirect('products/cart');

}

public function remove($rowid) {

    $this -> cart -> update(array('rowid' => $rowid, 'qty' => 0));

    redirect('products/cart');

}

public function delete() {
    $message = 'El carrito se ha eliminado satisfactoriamente';
    $this -> cart -> destroy();
    $this -> cart($message);

}

public function cart($message = NULL) {

    $data['message'] = $message;
    $data['title'] = 'Carrito de Compras';

    $this -> load -> view('front/header', $data);
    $this -> load -> view('front/cart');
    $this -> load -> view('front/footer');

}
    

function checkout(){
       
      $data['title'] = 'Solicitar Pedido';
     
       $this->form_validation->set_rules('name', 'Nombre', 'required');
       $this->form_validation->set_rules('phone', 'Celular', 'required|numeric');
       $this->form_validation->set_rules('address', 'Ciudad y dirección', 'required');
       $this->form_validation->set_rules('email', 'Email', 'required|valid_email');  
     
       $this->form_validation->set_message('required', 'el campo %s es requerido');
       $this->form_validation->set_message('valid_email', 'El email no es válido');
        
           $this -> form_validation -> set_error_delimiters('<ul><li>', '</li></ul>');
      
      
    if ($this->form_validation->run() == FALSE)
        {
            $this->load->view('front/header', $data); 
            $this->load->view('front/checkout');
            $this->load->view('front/footer');
          
            }else{
                      
            $name = $this->input->post('name');
            $mobil = $this->input->post('phone');
            $email = $this->input->post('email');
                      
if($cart=$this->cart->contents()){
$this->table->set_heading('Portatil', 'Detalle','Cantidad','Precio', 'Total'); // la tabla que irá en el correo


    foreach($cart as $item){
        $selected = '';      
     if ($this->cart->has_options($item['rowid'])) {
            foreach ($this->cart->product_options($item['rowid']) as $option => $value) {
                                $selected = $option . ": <em>" . $value . "</em>";
                          }
                      }
             
              $price = ($item['price']*$item['qty']);
               $this->table->add_row($item['name'], $selected, $item['qty'], $price);
                                  
             } // fin del foreach
        
 $this->table->add_row('Total', '', '', '', $this->cart->format_number($this->cart->total()));
            
    $message = 'Señor(a): '.$name.br(1).'Celular: '.$mobil.br(1).'Email: '.$email.br(2).'Detalles del pedido';                                    
    $pedido = $message.$this->table->generate();  // concatenamos el mensaje con la tabla que contiene el pedido
          
    $config = array('protocol'=>'smtp',
                    'smtp_host'=>'ssl://smtp.googlemail.com',
                    'smtp_user'=>'tonouy5678@gmail.com', // colocamos nuestra propia cuenta de Gmail
                    'smtp_pass'=>'unodostres123', // contraseña de la cuenta Gmail                  
                    'smtp_port'=>'465',
                    'smtp_timeout'=>'6',
                    'mailtype'=>'html',                  
                    );
                  
   $this->email->initialize($config);
  
            // Datos para enviar el correo
            $this->email->from('admin-tienda@gmail.com', 'Tienda virtual');
            $this->email->to($email);
            $this->email->subject('Pedido de Computador(es) Portatil(es)');              
            $this->email->message($pedido);
            $this->email->send(); // envia el correo
          
            $data['title']='Detalles del Pedido';
            $data['pedido']=$pedido;
                   //  echo $this->email->print_debugger(); exit;                          
             $this->load->view('front/header', $data);
             $this->load->view('front/success');
             $this->load->view('front/footer');
            
            }
        } // fin del else
    } // fin del método


}