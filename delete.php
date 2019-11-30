<?php
	include 'connect_db.php';
	$id_data = $_GET['id_data'];
	$query = "DELETE FROM tb_data WHERE id_data = '$id_data' ";
	$query2 = mysqli_query($conn, "SELECT * FROM tb_data WHERE id_data = '$id_data'");
	while($pilihan_data = mysqli_fetch_assoc($query2)){
	    $nama_data = $pilihan_data['nama_data'];
	    $tipe = $pilihan_data['tipe'];
	    if($tipe == 'i'){
	    	$output = shell_exec('/usr/local/hadoop/bin/hadoop fs -rm /Wordcount/inputan/'.$nama_data.'');
	    	unlink('uploads/'.$nama_data);
	    	if(mysqli_query($conn, $query)){
				$notice = urlencode(
												'<div class="alert alert-success alert-dismissible">
												  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
													<strong>File berhasil dihapus!</strong>
												</div>');
		        header('Location: test2.php?notice='.$notice);			
		    }else{
				$notice = urlencode(
											'<div class="alert alert-danger alert-dismissible">
												<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
												<strong>File gagal dihapus!</strong>
											</div>');
				header('Location: test2.php?notice='.$notice);		
			}
	    }elseif($tipe == 'o'){
	    	$output = shell_exec('/usr/local/hadoop/bin/hadoop fs -rm -r /Wordcount/'.$nama_data.'');
			if(mysqli_query($conn, $query)){
				$notice = urlencode(
												'<div class="alert alert-success alert-dismissible">
												  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
													<strong>File berhasil dihapus!</strong>
												</div>');
		        header('Location: test2.php?notice='.$notice);
			}else{
				$notice = urlencode(
											'<div class="alert alert-danger alert-dismissible">
												<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
												<strong>File gagal dihapus!</strong>
											</div>');
				header('Location: test2.php?notice='.$notice); 
			}
	    }
        echo "<pre>$output</pre>";
    }
?>