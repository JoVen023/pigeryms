<?php
session_start();
error_reporting(0); //if there is an error in the data it will throw an exception
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)//if the user successfully login he will be redirected to the index.php
	{	
header('location:index.php');
}
else{

if(isset($_POST['submit'])) //if the user click the submit button he will be
{
$brand=$_POST['brand'];
$sql="INSERT INTO  tblbrands(BrandName) VALUES(:brand)";
$query = $dbh->prepare($sql);
$query->bindParam(':brand',$brand,PDO::PARAM_STR);
$query->execute();
$lastInsertId = $dbh->lastInsertId();
if($lastInsertId)
{
$msg="Brand Created successfully";
}
else 
{
$error="Something went wrong. Please try again";
}

}
?>

<!doctype html>
<html lang="en">
<head>
	<title>Admin Create Breed</title>

	<!-- Font awesome -->
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<!-- Sandstone Bootstrap CSS -->
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<!-- Bootstrap Datatables -->
	<link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
	<!-- Bootstrap social button library -->
	<link rel="stylesheet" href="css/bootstrap-social.css">
	<!-- Bootstrap select -->
	<link rel="stylesheet" href="css/bootstrap-select.css">
	<!-- Bootstrap file input -->
	<link rel="stylesheet" href="css/fileinput.min.css">
	<!-- Awesome Bootstrap checkbox -->
	<link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
	<!-- Admin Stye -->
	<link rel="stylesheet" href="css/style.css">
  <style>
		.errorWrap {
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #dd3d36;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
.succWrap{
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #5cb85c;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
		</style>


</head>

<body>
	<?php include('includes/header.php');?>
	<div class="ts-main-content">
	<?php include('includes/leftbar.php');?>
		<div class="content-wrapper">
			<div class="container-fluid">

				<div class="row">
					<div class="col-md-12">					
						<h2 class="page-title">Create Breed</h2>
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default">
		            <div class="panel-heading"  style="background-color:  #8db86f; color: white;"> Add Breed</div>
									<div class="panel-body">
										<form method="post" class="form-horizontal" onSubmit="return valid();">										
  	        	  <?php if($error){?><div class="errorWrap"><strong>ERROR</strong>:<?php echo $error; ?> </div><?php } 
				      else if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo $msg; ?> </div><?php }?>

							<div class="card-body">    
             <div class="row">
              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                <label>Pig Breed</label>
                <input type="text" id="brand" name="brand" required="required"
                class="form-control form-control-sm rounded-0" />
              </div>
              <div class="col-lg-1 col-md-2 col-sm-2 col-xs-2">
                <label>&nbsp;</label>
                <br>
                <button type="submit" id="submit" name="submit" class="btn btn-primary btn-sm btn-flat btn-block">Save</button>
              </div>
            </div>        
        </div>
										</form>

									</div>
								</div>
							</div>
							
						</div>
						
					<div class="row">
					<div class="col-md-12">
						<h2 class="page-title"><center>Pig Breeds</center></h2>
						<div class="panel panel-default">
							<div class="panel-heading"   style="background-color:  #8db86f; color: white;">Listed  Breeds</div>
							<div class="panel-body">
								<table id="zctb" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
									<thead>
										<tr  style="background-color:  #778f66;  color:black;">
											<th><center>#</center></th>
											<th><center>Pig Breed</th></center>
											<th><center>Creation Date</th></center>
											<th><center>Updation date</th></center>									
											<th><center>Action</th></center>
										</tr>
									</thead>									
									<tbody>

<?php 
$sql = "SELECT * from  tblbrands ";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{				
	?>	
											<tr>
											<td><center><?php echo $cnt;?></td></center>
											<td><center><?php echo $result->BrandName;?></td></center>
											<td><center><?php echo $result->CreationDate;?></td></center>
											<td><center><?php echo $result->UpdationDate;?></td></center>
<td><center><a href="edit-brand.php?id=<?php echo $result->id;?>"><i class="fa fa-edit"style="color: #0097b2"></i></a>&nbsp;&nbsp;
<a href="manage-brands.php?del=<?php echo $result->id;?>" onclick="return confirm('Do you want to delete');"><i class="fa fa-close" style="color: #ff3131"></i></a></center></td>
										</tr>
										<?php $cnt=$cnt+1; }} ?>
										
									</tbody>
								</table>						
							</div>
						</div>
					</div>
				</div>						
			</div>
				<br>

<br>
		</div>
	</div>

	<!-- Loading Scripts -->
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap-select.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.dataTables.min.js"></script>
	<script src="js/dataTables.bootstrap.min.js"></script>
	<script src="js/Chart.min.js"></script>
	<script src="js/fileinput.js"></script>
	<script src="js/chartData.js"></script>
	<script src="js/main.js"></script>
</body>
</html>
<?php } ?>