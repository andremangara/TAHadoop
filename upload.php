<?php
	include 'connect_db.php';
	$target_dir = "uploads/";
	$fileName = basename($_FILES["fileToUpload"]["name"]);
	$filePath = "/opt/lampp/htdocs/TugasAkhir/uploads/".$fileName;
	$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
	$uploadOk = 1;
	$fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
	$fileSize = $_FILES["fileToUpload"]["size"] / 1000000;
	// Check if image file is a actual image or fake image
	if(isset($_POST["submit"])) {
	   $uploadOk = 1;
	}
	// // Check if file already exists
	 if (file_exists($target_file)) {
	     //echo "Sorry, file already exists.";
	     $uploadOk = 0;
			 $error_flag = 1;
	 }
	// Check file size
	if ($_FILES["fileToUpload"]["size"] > 50000000000) {
	    //echo "Sorry, your file is too large.";
	    $uploadOk = 0;
			$error_flag = 2;
	}
	// Allow certain file formats
	if($fileType != "xml" && $fileType != "txt") {
	    //echo "Sorry, only xml and txt files are allowed.";
	    $uploadOk = 0;
			$error_flag = 3;
	}
	// if everything is ok, try to upload file
	    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file) && $uploadOk == 1) {
	    	$query = "INSERT INTO tb_data (nama_data,ukuran_data, path_data, tipe) VALUES ('$fileName','$fileSize', '$filePath', 'i')";
	    	if(mysqli_query($conn, $query)){
	    		//echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
		        shell_exec('/usr/local/hadoop/bin/hadoop fs -put /opt/lampp/htdocs/TugasAkhir/'.$target_file.' /Wordcount/inputan');
				$notice = urlencode(
									'<div class="alert alert-success alert-dismissible fade in">
									<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
									<strong>File berhasil diunggah!</strong>
									</div>');
		        header('Location: test2.php?notice='.$notice);
	    	}
	    } else {
				switch ($error_flag) {
			    case 1:
					$notice = urlencode(
											'<div class="alert alert-danger alert-dismissible fade in">
												<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
												<strong>Nama file sudah terdaftar di database!</strong> Disarankan untuk mengganti nama file.
											</div>');
			        break;
			    case 2:
					$notice = urlencode(
											'<div class="alert alert-danger alert-dismissible fade in">
												<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
												<strong>Ukuran file melebihi batas maksimal!</strong> Maksimal ukuran file yang bisa diunggah adalah 50GB.
											</div>');
			        break;
			    case 3:
					$notice = urlencode(
											'<div class="alert alert-danger alert-dismissible fade in">
												<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
												<strong>Ekstensi file tidak sesuai ketentuan!</strong> Ekstensi file yang diterima adalah (*.xml) atau (*.txt).
											</div>');
			        break;
			    default:
					$notice = urlencode(
											'<div class="alert alert-danger alert-dismissible fade in">
												<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
												<strong>Data gagal diunggah!</strong>
											</div>');
				}
	        header('Location: test2.php?notice='.$notice);
	    }
?>
