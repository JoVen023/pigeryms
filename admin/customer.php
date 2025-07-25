<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
	{	
header('location:index.php');
}
else{
    $_SESSION['sidebarname'] = 'Customers';
	?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Ronald's Baboyan</title>
	
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
        <div class="table-data">
				<div class="order">
					<div class="left">
					<h1>Customers List</h1>
				</div>
					<table id="myTable">
						<thead>
							<tr>
                                <th>ID</th>
								<th>FullName</th>
                                <th>Email Address</th>
								<th>Contact Number</th>
                                <th>Date Of Birth</th>
                                <th>Address</th>
                                <th>Registration Date</th>
								<th>Action</th>
								
							</tr>
						</thead>
                        
						<tbody>

						<?php 
$sql ="SELECT * FROM tblusers
";
$query3 = $dbh->prepare($sql);
$query3->execute();
$results=$query3->fetchAll(PDO::FETCH_OBJ);

foreach($results as $result){
	$birthdate = new DateTime($result->dob);
	$formattedbirthdate = $birthdate->format('F j, Y');

    $regdate = new DateTime($result->RegDate);
	$formattedregdate = $regdate->format('F j, Y');
?>
							<tr>
	<td>
	<p><?php echo htmlentities($result->id); ?></p>
		</td>

	<td><?php echo htmlentities($result->FullName); ?></td>
	<td><?php echo htmlentities($result->EmailId); ?></td>
    <td><?php echo htmlentities($result->ContactNo); ?></td>
    <td><?php echo htmlentities($formattedbirthdate); ?></td>
    <td><?php echo htmlentities($result->Address); ?></td>
    <td><?php echo htmlentities($formattedregdate); ?></td>
	<td>  <button type="button" class="btn delete" title="Delete Pig"  data-bs-toggle="modal" data-bs-target="#deleteModal-<?php echo htmlentities($result->id); ?>"><i class='bx bx-trash'></i></button>
</tr>
<!-- deletepig  Modal -->
<div class="modal fade" id="deleteModal-<?php echo htmlentities($result->id); ?>" tabindex="-1"  aria-labelledby="cancelModalLabel-<?php echo htmlentities($result->id); ?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                  
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        
                    </button>
                </div>
                <div class="modal-body">
                <div class="text-center">
                    <img src="img/deletepig.svg" alt="Profile Picture" width="150px" height="150px">
                    <h3 class="confirm">Are you sure you want to delete this pig?</h3>
                  </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" onclick="deletepig('<?php echo htmlentities($result->id); ?>')" name="delete">Confirm</button>
                </div>
            </div>
        </div>
    </div>

<!-- delete pig Modal -->
<?php 
} 

?>	

							
						
							
						</tbody>
					</table>
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
    $('#myTable').DataTable();

   // Handle delete button clicks
   $(document).on('click', '.delete-btn', function() {
        // Save the pig ID to deletePigId
        deletePigId = $(this).data('id');
        // Show the modal
        $('#deleteModal-' + deletePigId).modal('show');
    });

    // Handle the "Confirm" button click
    $(document).on('click', '#confirmDelete', function() {
        // Call deletepig
        deletepig(deletePigId);
    });



});




function deletepig(id) {
    // Send a POST request to delete.php
    $.ajax({
        url: 'delete.php',  // This sends the request to delete.php
        type: 'POST',
        data: { custid: id },
        success: function(response) {
            // Close the modal
            $('#deleteModal-' + id).modal('hide');
            // Reload the page to update the table
            location.reload();
        },
        error: function() {
            alert('An error occurred while trying to delete the pig.');
        }
    });
}





</script>


	<script src="script.js"></script>
</body>
</html>
<?php } ?>