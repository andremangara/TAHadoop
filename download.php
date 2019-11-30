<?php
	include 'connect_db.php';
	$id_data = $_POST['id_data'];
	$query2 = mysqli_query($conn, "SELECT nama_data FROM tb_data WHERE id_data = '$id_data'");
	$notice = urlencode(
						'<div class="alert alert-success alert-dismissible fade in">
							<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							<strong>File berhasil diunduh!</strong><br />
						</div>');
	while($pilihan_data = mysqli_fetch_assoc($query2)){
	    $nama_data = $pilihan_data['nama_data'];
	    mkdir('/opt/lampp/htdocs/TugasAkhir/downloads/'.$nama_data.'');
	    putenv("JAVA_HOME=/usr/lib/jvm/java-7-openjdk-i386");
        //putenv("PATH=/usr/local/pig/pig-0.17.0/bin");
        //putenv("PIG_CLASSPATH=/usr/local/hadoop/etc/hadoop");
        $output = shell_exec('/usr/local/hadoop/bin/hadoop fs -copyToLocal /Wordcount/'.$nama_data.'/part-r-00000 /opt/lampp/htdocs/TugasAkhir/downloads/'.$nama_data.'');
        echo "<pre>$output</pre>";
        header('Location: test2.php?notice='.$notice);
    }
?>
