<?php	
	include 'connect_db.php';
	$output_directory = $_POST['output_directory'];
	$id_data = $_POST['id_data'];
	$tipe_mapreduce = $_POST['mapreduce_type'];
	$query2 = mysqli_query($conn, "SELECT nama_data FROM tb_data WHERE id_data = '$id_data'");
	while($nama_data = mysqli_fetch_assoc($query2)){
	    $nama_data = $nama_data['nama_data'];
	    $filepath = '/Wordcount/'.$output_directory;
	    putenv("JAVA_HOME=/usr/lib/jvm/java-7-openjdk-i386");
        //putenv("PATH=/usr/local/pig/pig-0.17.0/bin");
        //putenv("PIG_CLASSPATH=/usr/local/hadoop/etc/hadoop");
        if($tipe_mapreduce == 'txt'){
        	$time_pre = microtime(true);
        	$output = shell_exec('/usr/local/hadoop/bin/hadoop jar /home/hduser/wc.jar WordCount /Wordcount/inputan/'.$nama_data.' /Wordcount/'.$output_directory.'');
			$time_post = microtime(true);
			$exec_time = formatPeriod($time_post,$time_pre);	
        }elseif ($tipe_mapreduce == 'xml'){
        	$time_pre = microtime(true);
        	$output = shell_exec('/usr/local/hadoop/bin/hadoop jar /home/hduser/xd.jar XmlDriver /Wordcount/inputan/'.$nama_data.' /Wordcount/'.$output_directory.'');
			$time_post = microtime(true);
			$exec_time = formatPeriod($time_post,$time_pre);
        }
        if(mysqli_query($conn, "INSERT INTO tb_data (nama_data, path_data, waktu_ekse, tipe) VALUES ('$output_directory', '$filepath', '$exec_time', 'o')")){
        	$notice = urlencode(
						'<div class="alert alert-success alert-dismissible fade in">
							<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							<strong>File berhasil diolah!</strong><br />
							Waktu Eksekusi: '.$exec_time.'
						</div>');
        	header('Location: test2.php?notice='.$notice); 
        }else{
        	$notice = urlencode(
						'<div class="alert alert-danger alert-dismissible fade in">
						  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							<strong>File gagal diolah!</strong>
						</div>');
        	header('Location: test2.php?notice='.$notice); 
        }   
    }
    //Fungsi hitung waktu eksekusi
    function formatPeriod($endtime, $starttime){
		$duration = $endtime - $starttime;
		$hours = (int) ($duration / 60 / 60);
		$minutes = (int) ($duration / 60) - $hours * 60;
	    $seconds = (int) $duration - $hours * 60 * 60 - $minutes * 60;
	    return ($hours == 0 ? "00":$hours) . ":" . ($minutes == 0 ? "00":($minutes < 10? "0".$minutes:$minutes)) . ":" . ($seconds == 0 ? "00":($seconds < 10? "0".$seconds:$seconds));
	}
?>