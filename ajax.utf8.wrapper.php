<?php 
	if(isset($_GET['data']) && $_GET['data']!=""){
	  	if (file_exists($_GET['data'])){
		  	header('content-type: application/json; charset: utf-8;');
		  	echo file_get_contents($_GET['data']);
	  	}else{
	  		echo "File does not exist";
	  	}
	}else{
		echo "Empty data GET parameter";
	}	  
?>
