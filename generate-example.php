<?php 
include 'init.php';
if(!$my_access->logged()) {
   $_SESSION['error']=_('You need to be logged');
   $_SESSION['redir']=$_SERVER['REQUEST_URI'];
   header('Location: ./login.php');
   exit(0);
}


	   $mtime = microtime();
	   $mtime = explode(" ",$mtime);
	   $mtime = $mtime[1] + $mtime[0];
	   $starttime = $mtime;

	$scriptPath='/home/hector/TIMEE-1.0';
	
	$weburl=$_SERVER['SERVER_NAME'];
	$requri=$_SERVER['REQUEST_URI'];
	$system_version= substr($scriptPath, strrpos($scriptPath, '/') + 1);
	// ONLY FOR SERVER
	if(!eregi('local', $weburl)){
		$ssh2libraryPath='../../../../home/hllorens/web/ssh2.so';
		if(eregi('/beta',$requri)){$ssh2libraryPath='../'.$ssh2libraryPath;}
		$claucod='tqveqf{qukucec{'; // = '052245282';  //'26756443;5';  // use security encode to encode new pass
		$bridgeserver='altea.dlsi.ua.es';
		include '/home/hllorens/includes/security.php'; 
		if (!extension_loaded('ssh2')){ if (!dl($ssh2libraryPath)){ echo "Error: extension not loaded.";exit;}}
	}





	// CHECK WHETHER THE EXAMPLE EXISTS
	if(!isset($_GET['data']) || $_GET['data']=="" || !isset($_GET['lang']) || $_GET['lang']==""){
		header('location: index.php');
	}else{
		// Leave only the name
		$_GET['data']=str_replace(" ","_",trim($_GET['data']));
		if(strrpos($_GET['data'], '/')){
			$_GET['data']=substr($_GET['data'], strrpos($_GET['data'], '/') + 1);
		}
				
		$filename="data/user-generated/".$_GET['data'].'-'.$_GET['lang'].".txt.TAn";
		$filename1="data/history/".$_GET['data'].'-'.$_GET['lang'].".txt.TAn";
		$filename2="data/biographical/".$_GET['data'].'-'.$_GET['lang'].".txt.TAn";
		    if (file_exists($filename) || file_exists($filename1) || file_exists($filename2)){
			if(file_exists($filename)){
				header('Location: index.php?data=user-generated/'.$_GET['data'].'-'.$_GET['lang']);
				exit();
			}
			if(file_exists($filename1)){
				header('Location: index.php?data=history/'.$_GET['data'].'-'.$_GET['lang']);
				exit();
			}
			if(file_exists($filename2)){
				header('Location: index.php?data=biographical/'.$_GET['data'].'-'.$_GET['lang']);
				exit();
			}
		    }
    


?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
 <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Time-Surfer Example Generator</title>
 </head>
 <body>

    <h1>Time-Surfer Example Generator: <?=$_GET['data']?></h1>
	<p>Process launched...</p>

<?php


$fp = @fopen('http://'.$_GET['lang'].'.wikipedia.org/wiki/'.$_GET['data'], 'r'); // @suppresses all error messages .'-'.$_GET['lang']
//$fp2 = @fopen($_GET['data'], 'r'); // @suppresses all error messages .'-'.$_GET['lang']
if ($fp) {
      // connection was made to server at domain example.com
      if ($fp) fclose($fp);
//      if ($fp2) fclose($fp2);

      
$fileid='online_'.date("Y-m");       // for log info

$command='java -jar "'.$scriptPath.'/dist/TIMEE.jar" -a TAN \"'.str_replace("\\\"","'",str_replace("`","'",str_replace("\'","'",$_GET['data']))).'\" -ap approach=TIPSemB  -l '.$_GET['lang'];

if(!($con = ssh2_connect($bridgeserver, 22))){echo "fail: unable to establish connection\n";
}else {
	if(!ssh2_auth_password($con, "hllorens", decrypt($claucod))) {echo "fail: unable to authenticate\n";}
	else{
		if(!($stream = ssh2_exec($con,'ssh hector@hllorens "cd tempTIMEE;'.$command.'"' )) ){
			echo "fail: unable to execute command\n";
		} else{
			echo "Executed command: ".$command." <br />";
			echo '<textarea rows="15" cols="100">';
			stream_set_blocking( $stream, true );
			$resultFile=fopen('log/'.$fileid,a);
			fputs($resultFile,"\n-----------------------------------\n".date("Y-m-d--H.i.s")."\tIP=".get_ip()."\t".str_replace("\'","'",$_POST['itext'])."\n\n");
			while( $buf = fgets($stream) ){echo "".$buf.""; fputs($resultFile,$buf);}
			fclose($resultFile);
			fclose($stream);			
			echo '</textarea><br />';
		}
	}
}


if(!($con = ssh2_connect($bridgeserver, 22))){echo "fail: unable to establish connection\n";
}else {
	if(!ssh2_auth_password($con, "hllorens", decrypt($claucod))) {echo "fail: unable to authenticate\n";}
	else{
		if(!($stream = ssh2_exec($con,'ssh hector@hllorens "cd tempTIMEE;cat '.$_GET['data'].'-'.$_GET['lang'].'.txt.TAn"' )) ){
			echo "fail: unable to execute command\n";
		} else{
			stream_set_blocking( $stream, true );
			$resultFile2=fopen('data/user-generated/'.$_GET['data'].'-'.$_GET['lang'].'.txt.TAn',a);
			while( $buf = fgets($stream) ){fputs($resultFile2,$buf);}
			fclose($resultFile2);
			fclose($stream);
		}
	}
}


if(!($con = ssh2_connect($bridgeserver, 22))){echo "fail: unable to establish connection\n";
}else {
	if(!ssh2_auth_password($con, "hllorens", decrypt($claucod))) {echo "fail: unable to authenticate\n";}
	else{
		if(!($stream = ssh2_exec($con,'ssh hector@hllorens "cd tempTIMEE;cat '.$_GET['data'].'-'.$_GET['lang'].'.txt.nav.php"' )) ){
			echo "fail: unable to execute command\n";
		} else{
			stream_set_blocking( $stream, true );			
			$resultFile3=fopen('data/navphp/'.$_GET['data'].'-'.$_GET['lang'].'.txt.nav.php',a);
			while( $buf = fgets($stream) ){fputs($resultFile3,$buf);}
			fclose($resultFile3);
			fclose($stream);
		}
	}
}





	#$last_line = exec('tail -n 2 ../../results/result.txt | head -n 1 | sed "s/^[[:blank:]]*\(.*\)[[:blank:]]*\$/\1/ig"',$output, $retval);
	/*$last_line = exec('tail -n 1 log/'.$fileid.' | head -n 1 | sed "s/^[[:space:]]*\(.*\)[[:blank:]]*\$/\1/ig"',$output, $retval);		
	//echo 'Result: <b>'.$last_line.'</b><br />'; 
        echo 'Result: <b>'.$output[0].'</b><br />';*/
	   $mtime = microtime();
	   $mtime = explode(" ",$mtime);
	   $mtime = $mtime[1] + $mtime[0];
	   $endtime = $mtime;
	   $totaltime = ($endtime - $starttime);
	   echo " <br />This page was created in ".$totaltime." seconds";      
      
}else{
	echo "Error: There is no article for <b>".$_GET['data']."</b> in Wikipedia (".$_GET['lang'].").<br/> Make sure the spelling and the casing is correct.";
}      
?>

<a href="index.php">Return to main menu</a>
 </body>
</html>
<?php
}
?>

