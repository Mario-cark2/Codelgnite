<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">  
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?=$title?></title>
<meta name="keywords" content="" />
<meta name="description" content="" />

<link rel="stylesheet" type="text/css" href="<?php echo base_url()?>assets/template/style.css" />
</head>
<body>
    <div id="container">
<div id="header">
    <h1>Tienda Virtual con CodeIgniter</h1>
</div><!-- End Header -->
<div id="menu">
    <ul>
        <li><?php anchor('table','Home');?></li>  
        <li><a href="#">Portafolio</a></li>
        <li><a href="#">Nosotros</a></li>
        <li><a href="#">Contactenos</a></li>
        <?php if($total_items=$this->cart->total_items()):?> <li><?=anchor('products/cart', 'Carrito - '.$total_items);?></li> <?php endif; ?>
    </ul>
</div><!-- End Menu -->
<div id="content">
 