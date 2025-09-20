<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
	{	
header('location:index.php');
}
else{
    $_SESSION['sidebarname'] = 'Sales';
	?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="style.css">
	<title>Sales</title>
	<!-- CSS -->
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="icon" type="image/x-icon" href="img/logos.jpeg">
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">

<!-- SCRIPTS -->

<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<!-- Then load Bootstrap and its dependencies -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS should be loaded after jQuery -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
</head>
<body class="<?= $_SESSION['dark_mode'] ? 'dark' : '' ?>">

	<!-- SIDEBAR -->
	<?php include('includes/sidebar.php');?>
	<!-- SIDEBAR -->
	<!-- CONTENT -->
	<section id="content">
		<!-- NAVBAR -->
		<?php include('includes/header.php');?>
		<!-- NAVBAR -->

		<!-- MAIN -->
		<main>
		<div class="head-title">
				<div class="left">
					<h1>Sales Records</h1>
				
				</div>
			</div>
        <div class="table-data">
				<div class="order">
				<div class="left">
					<h1>Pigs Sold</h1>
				</div>
				<div class="date-filter">
				
    <label for="startDate">Date Range Sorting: &nbsp;  Start Date:</label>
    <input type="date" id="startDate" name="startDate">

    <label for="endDate">&nbsp; &nbsp;  End Date:</label>
    <input type="date" id="endDate" name="endDate">

    <button id="filterButton"><i class='bx bx-filter-alt'></i></button>
</div>
					<table id="myTable">
						<thead>
							<tr>
                                <th>ID</th>
								<th>Customer Name</th>
								<th>Date Ordered</th>
                                <th>Date Delivered</th>
                                <th>Mode Of Payment</th>
                                <th>Total Amount</th>
								<th>Details</th>
							</tr>
						</thead>
						<tbody>
						<?php 
$sql ="SELECT tblusers.id, tblusers.FullName,tblusers.ContactNo, tblorders.id as order_id,tblorders.orderdate,tblorders.deliverydate, tblorders.orderstatus,tblorders.total_amount,tblorders.mop, tblorders.cust_id 
FROM tblusers 
JOIN tblorders ON tblusers.id = tblorders.cust_id WHERE tblorders.orderstatus = 'Completed' AND tblorders.piglets = 0 AND tblorders.cull = 0 ";
$query3 = $dbh->prepare($sql);
$query3->execute();
$results=$query3->fetchAll(PDO::FETCH_OBJ);

foreach($results as $result){
	$orderdate = new DateTime($result->orderdate);
	$formattedorderdate = $orderdate->format('F j, Y');

    $deliverdate = new DateTime($result->deliverydate);
	$formatteddeliverdate = $deliverdate->format('F j, Y');
    
?>
							<tr>
                            <td>
	<p><?php echo htmlentities($result->id); ?></p>
		</td>
	<td>
	<p><?php echo htmlentities($result->FullName); ?></p>
		</td>
	<td><?php echo htmlentities($formattedorderdate); ?></td>
    <td><?php echo htmlentities($formatteddeliverdate); ?></td>
	<td><?php echo htmlentities($result->mop); ?></td>
	<td><span>&#8369;</span><?php echo htmlentities(number_format($result->total_amount,2)); ?></td>
    <td>  <button type="button"  title="Click to view" data-bs-toggle="modal" data-bs-target="#confirmModal" data-orderid="<?php echo htmlentities($result->order_id); ?>"  
    class="openModalBtn" ><i class='bx bx-message-alt-add'></i></button>

	<!-- <button type="button"  title="Click to view" data-bs-toggle="modal" data-bs-target="#confirmModal" data-orderid="<?php echo htmlentities($result->order_id); ?>"  
    class="openModalBtn" ><i class='bx bx-printer'></i></button> -->

	<!-- <a href="printsalespigletdetails.php?id=<?php echo htmlentities($result->order_id); ?>" class="openModalBtn print" title="Print Piglets Details" target="_blank">
  -->

<a href="#"
	onclick ="window.open(
	'printsalespigletdetails.php?id=<?php echo htmlentities($result->order_id); ?>',
	'Print Reciept',
	'width=900,height=650,scrollbars=yes'
	);
	return false;"
	class="openModalBtn print" 
	><i class='bx bx-printer'></i></a>
</td>
	
</tr>

<?php 
} 
?>	
						</tbody>
					</table>



					
			

<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header custom-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Order Details</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
     
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
       
      </div>
    </div>
  </div>
                </div>
				</div>
</div>



<div class="table-data">
				<div class="order">
				<div class="left">
					<h1>Piglets Sold</h1>
				</div>
				<div class="date-filter">
				
    <label for="startDate">Date Range Sorting: &nbsp;  Start Date:</label>
    <input type="date" id="startDate" name="startDate">

    <label for="endDate">&nbsp; &nbsp;  End Date:</label>
    <input type="date" id="endDate" name="endDate">

    <button id="filterButton"><i class='bx bx-filter-alt'></i></button>
</div>
					<table id="myTable">
						<thead>
							<tr>
                                <th>ID</th>
								<th>Customer Name</th>
								<th>Date Ordered</th>
                                <th>Date Delivered</th>
                                <th>Mode Of Payment</th>
                                <th>Total Amount</th>
								<th>Details</th>
							</tr>
						</thead>
						<tbody>
						<?php 
$sql ="SELECT tblusers.id, tblusers.FullName, tblorders.id as order_id,tblorders.orderdate,tblorders.deliverydate, tblorders.orderstatus,tblorders.total_amount,tblorders.mop, tblorders.cust_id 
FROM tblusers 
JOIN tblorders ON tblusers.id = tblorders.cust_id WHERE tblorders.orderstatus = 'Completed' AND tblorders.piglets = 1";
$query3 = $dbh->prepare($sql);
$query3->execute();
$results=$query3->fetchAll(PDO::FETCH_OBJ);

foreach($results as $result){
	$orderdate = new DateTime($result->orderdate);
	$formattedorderdate = $orderdate->format('F j, Y');

    $deliverdate = new DateTime($result->deliverydate);
	$formatteddeliverdate = $deliverdate->format('F j, Y');
    
?>
							<tr>
                            <td>
	<p><?php echo htmlentities($result->id); ?></p>
		</td>
	<td>
	<p><?php echo htmlentities($result->FullName); ?></p>
		</td>
	<td><?php echo htmlentities($formattedorderdate); ?></td>
    <td><?php echo htmlentities($formatteddeliverdate); ?></td>
	<td><?php echo htmlentities($result->mop); ?></td>
	<td><span>&#8369;</span><?php echo htmlentities(number_format($result->total_amount,2)); ?></td>
    <td>  <button type="button"  title="Click to view" data-bs-toggle="modal" data-bs-target="#confirmModal" data-orderid="<?php echo htmlentities($result->order_id); ?>"  
    class="openModalBtn" ><i class='bx bx-message-alt-add'></i></button>

	<a href="#"
	onclick ="window.open(
	'printsalespigletdetails.php?id=<?php echo htmlentities($result->order_id); ?>',
	'Print Reciept',
	'width=900,height=650,scrollbars=yes'
	);
	return false;"
	class="openModalBtn print" 
	><i class='bx bx-printer'></i></a>
	
	</td>
	
	
</tr>

<?php 
} 
?>	
						</tbody>
					</table>



					
			

<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header custom-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Order Details</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
     
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
       
      </div>
    </div>
  </div>
                </div>
				</div>
</div>



<div class="table-data">
				<div class="order">
				<div class="left">
					<h1>Culls Sold</h1>
				</div>
				<div class="date-filter">
				
    <label for="startDate">Date Range Sorting: &nbsp;  Start Date:</label>
    <input type="date" id="startDate" name="startDate">

    <label for="endDate">&nbsp; &nbsp; End Date:</label>
    <input type="date" id="endDate" name="endDate">

    <button id="filterButtons"><i class='bx bx-filter-alt'></i></button>
</div>
					<table id="mysecondTable">
						<thead>
						<tr>
                                <th>ID</th>
								<th>Customer Name</th>
								<th>Date Ordered</th>
                                <th>Date Delivered</th>
                                <th>Mode Of Payment</th>
                                <th>Total Amount</th>
								<th>Details</th>
							</tr>
						</thead>
						<tbody>
						<?php 
$sql ="SELECT tblusers.id, tblusers.FullName, tblorders.id as order_id,tblorders.orderdate,tblorders.deliverydate, tblorders.orderstatus,tblorders.total_amount,tblorders.mop, tblorders.cust_id 
FROM tblusers 
JOIN tblorders ON tblusers.id = tblorders.cust_id WHERE tblorders.orderstatus = 'Completed' AND tblorders.cull = 1";
$query4 = $dbh->prepare($sql);
$query4->execute();
$res=$query4->fetchAll(PDO::FETCH_OBJ);


foreach($res as $cull){
	$orderdate = new DateTime($cull->orderdate);
	$formattedorderdate = $orderdate->format('F j, Y');

    $deliverdate = new DateTime($cull->deliverydate);
	$formatteddeliverdate = $deliverdate->format('F j, Y');
    
?>
							<tr>
                            <td>
	<p><?php echo htmlentities($cull->id); ?></p>
		</td>
	<td>
	<p><?php echo htmlentities($cull->FullName); ?></p>
		</td>
	<td><?php echo htmlentities($formattedorderdate); ?></td>
    <td><?php echo htmlentities($formatteddeliverdate); ?></td>
	<td><?php echo htmlentities($cull->mop); ?></td>
	<td><span>&#8369;</span><?php echo htmlentities(number_format($cull->total_amount,2)); ?></td>
	
	<td>  <button type="button"  title="Click to view" data-bs-toggle="modal" data-bs-target="#confirmModal" data-orderid="<?php echo htmlentities($cull->order_id); ?>"  
    class="openModalBtn" ><i class='bx bx-message-alt-add'></i></button>

	<a href="#"
	onclick ="window.open(
	'printsalespigletdetails.php?id=<?php echo htmlentities($cull->order_id); ?>',
	'Print Reciept',
	'width=900,height=650,scrollbars=yes'
	);
	return false;"
	class="openModalBtn print" 
	><i class='bx bx-printer'></i></a>
	
	</td>
	
	
</tr>

<?php 
} 
?>	
						</tbody>
					</table>



					
			

<div class="modal fade" id="conModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header custom-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Sow Details</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
     
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
       
      </div>
    </div>
  </div>
                </div>
				</div>
</div>
		</main>
		<!-- MAIN -->
			<!-- FOOTER -->
		<?php include('includes/footer.php');?>
		<!-- FOOTER -->
	</section>
	<!-- CONTENT -->

 

	<script>
$(document).ready(function () {
    // Initialize DataTable
    $('#myTable').DataTable();
	$('#mysecondTable').DataTable();


	var dataTable = $('#myTable').DataTable();
    var secondTable = $('#mysecondTable').DataTable();

    $('#filterButton').on('click', function () {
        var startDate = $('#startDate').val();
        var endDate = $('#endDate').val();
        
        // Reload table data with the new date filters
        dataTable.ajax.url('fetch_data.php?startDate=' + startDate + '&endDate=' + endDate).load();
        // Do the same for the second table if needed
    });

	$('#filterButtons').on('click', function () {
        var startDate = $('#startDate').val();
        var endDate = $('#endDate').val();
        
        // Reload table data with the new date filters
        secondTable.ajax.url('fetch_datas.php?startDate=' + startDate + '&endDate=' + endDate).load();
        // Do the same for the second table if needed
    });

   
    $('.openModalBtn').click(function() {
    var orderId = $(this).data('orderid');

    $.ajax({
        url: 'order_details.php',
        type: 'POST',
        data: { orderId: orderId },
        success: function(response) {
            $('#confirmModal .modal-body').html(response);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error(textStatus, errorThrown);
        }
    });
});

$('.sowModalBtn').click(function() {
    var sowId = $(this).data('sowid');

    $.ajax({
        url: 'order_details.php',
        type: 'POST',
        data: { sowId: sowId },
        success: function(response) {
            $('#conModal .modal-body').html(response);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error(textStatus, errorThrown);
        }
    });
});

});
   
</script>

	<script src="script.js"></script>
	
</body>
</html>
<?php } ?>
