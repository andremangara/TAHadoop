<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>HadoopTest | Dashboard</title>
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
  <!-- DataTables -->
  <link rel="stylesheet" href="bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
  <!-- Loader CSS -->
  <link rel="stylesheet" href="dist/css/loader.css">

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
<!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-sm">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Loading...</h4>
        </div>
        <div class="modal-body">
           <div class="loader"></div> 
        </div>
        <div class="modal-footer">
        </div>
      </div>
      
    </div>
  </div>
<div class="wrapper">
  <?php require_once('header.php'); include 'connect_db.php'; ?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Dashboard
        <small>HadoopTest</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dashboard</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-md-3">
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
              <h3 class="box-title">Tabel File</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
              
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="row">
                <div class="col-lg-12">
                  <div class="nav-tabs-custom">
                    <!-- Tabs within a box -->
                    <ul class="nav nav-tabs pull-right">
                      <li class="active"><a href="#input" data-toggle="tab">Input</a></li>
                      <li><a href="#output" data-toggle="tab">Output</a></li>
                    </ul>
                    <div class="tab-content no-padding">
                      <div class="chart tab-pane active" id="input">
                        <table id="example2" class="table table-bordered table-hover">
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
                              $query = "SELECT * FROM tb_data WHERE tipe='i' ORDER BY nama_data";
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
                                  echo "<td><a href='delete.php?id_data=".$row->id_data."' onclick='return checkDelete()' class='btn btn-block btn-lg btn-xs bg-red' role='button'><i class='fa fa-trash'/></a></td>";
                                echo '</tr>';
                                $i = $i+1;
                              }
                              $db->close();
                            ?>
                          </tbody>
                        </table>
                      </div>
                      <div class="chart tab-pane" id="output">
                        <form action="download.php" method="post" enctype="multipart/form-data">
                            Pilih file untuk diunduh:
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
                        <table id="example1" class="table table-bordered table-hover">
                          <thead>
                            <tr>
                              <th><center>No</center></th>
                              <th><center>Nama File</center></th>
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
                              $query = "SELECT * FROM tb_data WHERE tipe='o' ORDER BY id_data";
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
                                  echo '<td>'.$row->path_data.'</td>';
                                  echo "<td><a href='delete.php?id_data=".$row->id_data."' onclick='return checkDelete()' class='btn btn-block btn-lg btn-xs bg-red' role='button'><i class='fa fa-trash'/></a></td>";
                                echo '</tr>';
                                $i = $i+1;
                              }
                              $db->close();
                            ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
            </div>
            <!-- ./box-body -->
          </div>
          <!-- /.box -->
        </section>
          <!-- /.left col -->
          <!-- right col -->
          <section class="col-lg-5 connectedSortable">
            <div class="box">
              <div class="box-header with-border">
                <h3 class="box-title">Upload File</h3>
                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
              </div>
              <!-- /.box-header -->
              <!-- form start -->
              <form role="form" action="upload.php" class="loadingData" method="post" enctype="multipart/form-data">
                <div class="box-body">
                  <div class="form-group">
                    <label for="exampleInputFile">File input</label>
                    <input type="file" name="fileToUpload" id="exampleInputFile">
                    <p class="help-block">Ketentuan: Ukuran max 50GB & Ekstensi (.xml)/(.txt) .</p>
                  </div>
                </div>
                <!-- /.col -->
                <div class="box-footer">
                  <button type="submit" class="btn btn-primary" name="Submit">Submit</button>
                </div>
              </form>
            </div>

            <div class="box">
              <div class="box-header with-border">
                <h3 class="box-title">Mapreduce</h3>
                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
              </div>
            <!-- /.box-header -->
            <!-- form start -->
              <form role="form" action="mapreduce2.php" method="post" class="loadingData" enctype="multipart/form-data">
                <div class="box-body">
                  <div class="form-group">
                    <label>Pilih file untuk diproses:</label>
                    <select name="id_data" class="form-control" required>
                      <?php
                        $query = mysqli_query($conn, "SELECT * FROM tb_data WHERE tipe = 'i'");
                        if(mysqli_num_rows($query) > 0){
                          while($pilihan = mysqli_fetch_assoc($query)){
                            $id_data = $pilihan['id_data'];
                            echo '<option value="'.$id_data.'">'.$pilihan["nama_data"].'</option>';
                          }
                        }
                      ?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label>Output Directory</label>
                    <input name="output_directory" class="form-control" placeholder="Masukkan nama direktori untuk menyimpan hasil mapreduce" maxlength="50" required>
                  </div>
                  <div class="form-group">
                    <label>Tipe Mapreduce: </label>
                    <input type="radio" name="mapreduce_type" <?php if (isset($mapreduce_type) && $mapreduce_type=="text") echo "checked";?> value="text"> Text
                    <input type="radio" name="mapreduce_type" <?php if (isset($mapreduce_type) && $mapreduce_type=="xml") echo "checked";?> value="xml"> XML
                  </div>
                </div>
                <!-- ./box-body -->
                <div class="box-footer">
                  <button type="submit" class="btn btn-primary" name="Submit">Submit</button>
                </div>
              </form>
            </div>
            <!-- /.box -->
          </section>
          <!-- /.right col -->
        </div>
        <!-- /.col -->
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
<!-- DataTables -->
<script src="bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!-- ChartJS -->
<script src="bower_components/Chart.js/Chart.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="dist/js/pages/dashboard2.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
<!-- page script -->
<script>
  $(function () {
    $('#example1').DataTable({
      'paging'      : true,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : false
    })
    $('#example2').DataTable({
      'paging'      : true,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : false
    })
  })
</script>
<!-- Javascript Pop-up Delete Message -->
<script language="JavaScript" type="text/javascript">
  function checkDelete(){
    return confirm('Hapus data?');
  }
</script>
<script type="text/javascript">
  function show_slow_warning() {
    $("#myModal").modal();
  }

  $(document).ready(function() {
    $(".loadingData").submit(function(){
      show_slow_warning();
    });
  });
</script>
</body>
</html>
