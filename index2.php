<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>AdminLTE 2 | Dashboard</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <!-- jvectormap -->
  <link rel="stylesheet" href="bower_components/jvectormap/jquery-jvectormap.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
  <?php require_once('header.php'); include 'connect_db.php'; ?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Dashboard
        <small>Version 2.0</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dashboard</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <!-- Alert space -->
      <div class="row">
        <div class="col-md-7">
          <?php
          if( !empty($_GET['notice']) ){
            echo $_GET['notice'];
          }
          ?>
        </div>
      </div>
      <!-- Main row -->
      <div class="row">
        <!-- Left col -->
        <section class="col-lg-7 connectedSortable">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Tombol Hadoop</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <div class="btn-group">
                  <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-wrench"></i></button>
                  <ul class="dropdown-menu" role="menu">
                    <li><a href="#">Action</a></li>
                    <li><a href="#">Another action</a></li>
                    <li><a href="#">Something else here</a></li>
                    <li class="divider"></li>
                    <li><a href="#">Separated link</a></li>
                  </ul>
                </div>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="row">
                <div class="col-md-7">
                    <button type="button" class="btn btn-default margin" onclick="location='test2.php'">List File</button>
                    <button type="button" class="btn btn-default margin" onclick="location='test3.php'">Input File</button>
                    <button type="button" class="btn btn-default margin" onclick="location='test4.php'">Delete File</button>
                    <button type="button" class="btn btn-default margin" onclick="location='test5.php'">Mapreduce</button>
                </div>
                <div class="col-md-7">
                  <form action="download.php" method="post" enctype="multipart/form-data">
                      Select file to be downloaded:
                      <select name="id_data" required>
                        <?php
                          $query = mysqli_query($conn, "SELECT * FROM tb_data WHERE tipe = 'o' ");
                          if(mysqli_num_rows($query) > 0){
                            while($pilihan = mysqli_fetch_assoc($query)){
                              $id_data = $pilihan['id_data'];
                              echo '<option value="'.$id_data.'">'.$pilihan["nama_data"].'</option>';
                            }
                          }
                        ?>
                      </select>
                      <input type="submit"  value="Download File" name="submit">
                  </form>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
              <div class="row">
                <div class="col-md-7">
                  <table id="example1" class="table table-bordered table-hover" border="1">
                  <thead>
                  <tr>
                    <th><center>No</center></th>
                    <th><center>Nama File</center></th>
                    <th><center>Ukuran File</center></th>
                    <th><center>Path</center></th>
                    <th><center>Delete</center></th>
                  </tr>
                  </thead>
                    <tbody>
      								<?php
      									$db = new mysqli($servername, $username, $password, $database);
      									if ($db->connect_errno){
      									die ("Could not connect to the database: <br />". $db->connect_error);
      									}

      									// Assign the query
      									$query = "SELECT * FROM tb_data WHERE tipe = 'i' ";
      									// Execute the query
      									$result = $db->query( $query );
      									if (!$result){
      										die ("Could not query the database: <br />". $db->error);
      									}
      									$i=1;
      									// Fetch and display the results
      									while ($row = $result->fetch_object()){
      										echo '<tr>';
      											echo '<td>'.$i.'</td>';
      											echo '<td>'.$row->nama_data.'</td>';
                            echo '<td>'.$row->ukuran_data.' mb</td>';
      											echo '<td>'.$row->path_data.'</td>';
      											echo "<td><a href='delete_data.php?id=".$row->id_data."' onclick='return checkDelete()' class='btn btn-block btn-lg btn-xs bg-red' role='button'><i class='fa fa-trash'/></a></td>";
      										echo '</tr>';
      										$i = $i+1;
      									}
      									$db->close();
      								?>
      							</tbody>
                  </table>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
            </div>
            <!-- ./box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
      </section>
      <!-- /.left row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <?php require_once('footer.php'); ?>
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->
<!-- Javascript Pop-up Delete Message -->
<script language="JavaScript" type="text/javascript">
  function checkDelete(){
    return confirm('Hapus data?');
  }
</script>
<!-- jQuery 3 -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- FastClick -->
<script src="bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- Sparkline -->
<script src="bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
<!-- jvectormap  -->
<script src="plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- SlimScroll -->
<script src="bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- ChartJS -->
<script src="bower_components/Chart.js/Chart.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="dist/js/pages/dashboard2.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
</body>
</html>
