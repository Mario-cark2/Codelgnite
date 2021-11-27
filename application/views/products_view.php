<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8" />
    <title><?php echo $title;  ?></title>
</head>  
<body>
    <h1 align="center">Registros de la Bd con Codeigniter</h1>
<h3 align="center">
<?php  foreach($results as $result) {
              
      echo $result->marca." ";
      echo $result->procesador." ";
     echo $result->disco_duro."<br>";
   
}
 ?>  
 </h3>
</body>
</html>