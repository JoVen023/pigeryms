<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
	{	
header('location:index.php');
}
else{
    
    $_SESSION['sidebarname'] = 'Cull Orders';
	?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="style.css">
	<title>Orders</title>
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
        <div class="table-data">
				<div class="order">
				<div class="left">
					<h1>Cull Orders</h1>
				</div>
					<table id="myTable">
						<thead>
							<tr>
                                <th>ID</th>
								<th>Customer Name</th>
								<th>Date Order</th>
                                <th>Type Of Payment</th>
                                <th>Total Amount</th>
								<th>Details</th>
								<th>Status</th>
                                <th>Action</th>
							</tr>
						</thead>
						<tbody>
						<?php 
$sql ="SELECT 
    tblusers.id, 
    tblusers.FullName, 
    tblorders.id AS order_id, 
    tblorders.orderdate, 
    tblorders.orderstatus, 
    tblorders.total_amount, 
    tblorders.mop, 
    tblorders.cust_id,
    tblorderdetails.pig_id
FROM tblusers
LEFT JOIN tblorders ON tblusers.id = tblorders.cust_id 
LEFT JOIN tblorderdetails ON tblorders.id = tblorderdetails.order_id 
WHERE tblorders.deleted = 0 
  AND tblorderdetails.sow_id = 0 
ORDER BY FIELD(tblorders.orderstatus, 'Pending', 'Completed')";

try {
    $query3 = $dbh->prepare($sql);
    $query3->execute();
    $results = $query3->fetchAll(PDO::FETCH_OBJ);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
foreach($results as $result){
	$orderdate = new DateTime($result->orderdate);
	$formattedorderdate = $orderdate->format('F j, Y');
    
?>
							<tr>
                            <td>
	<p><?php echo htmlentities($result->order_id); ?></p>
		</td>
	<td>
	<p><?php echo htmlentities($result->FullName); ?></p>
		</td>
	<td><?php echo htmlentities($formattedorderdate); ?></td>
	<td><?php echo htmlentities($result->mop); ?></td>
	<td><span>&#8369;</span><?php echo htmlentities(number_format($result->total_amount, 2)); ?></td>
    <td>  <button type="button" title="Click to view" data-bs-toggle="modal" data-bs-target="#confirmModal" data-orderid="<?php echo htmlentities($result->order_id); ?>"  
    class="openModalBtn" ><i class='bx bx-message-alt-add'></i></button></td>
	<td class="select">
    <?php if($result->orderstatus == "Completed"): ?>
        <span class="status completed"><?php echo htmlentities($result->orderstatus); ?></span>
    <?php else: ?>
        <select class="status pending" data-orderid="<?php echo $result->order_id; ?>" data-details="">
            <option value="Pending" <?php echo $result->orderstatus == 'Pending' ? 'selected' : ''; ?>>Pending</option>
            <option value="Completed" <?php echo $result->orderstatus == 'Completed' ? 'selected' : ''; ?>>Completed</option>
        </select>
    <?php endif; ?>
</td>

<td><button type="button" class="delete" title="Delete Pig"  data-bs-toggle="modal" data-bs-target="#deleteModal-<?php echo htmlentities($result->id); ?>"><i class='bx bx-trash'></i></button></td>
</tr>


<!-- deleteorder  Modal -->
<div class="modal fade" id="deleteModal-<?php echo htmlentities($result->id);?>" tabindex="-1"  aria-labelledby="cancelModalLabel-<?php echo htmlentities($result->id); ?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                  
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        
                    </button>
                </div>
                <div class="modal-body">
                <div class="text-center">
                    <img src="img/cancel.svg" alt="Profile Picture" width="150px" height="150px">
                    <h3 class="confirm">Are you sure you want to delete this order?</h3>
                  </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" onclick="deleteorder('<?php echo htmlentities($result->order_id); ?>')" name="delete">Confirm</button>
                </div>
            </div>
        </div>
    </div>

<!-- delete  Modal -->

<?php 
} 
?>	
						</tbody>
					</table>



					
					<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header custom-header">
                <h5 class="modal-title" id="exampleModalLabel">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBody">
            
            </div>
            <div class="modal-footer">
                <p>Please input the weight for each pig.</p>
                <button type="button" class="btn btn-secondary" id="cancelBtn" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmBtn">Confirm</button>
            </div>
        </div>
    </div>
</div>

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
    $.fn.dataTable.ext.type.order['status-pre'] = function (d) {
    switch (d) {
        case 'Pending':    return 1;
        case 'Completed':  return 2;
        default:           return 3;
    }
};

$('#myTable').DataTable({
    "columnDefs": [
        { "type": "status", "targets": 6 }
    ],
    "order": [[6, 'asc']]
});

    var currentStatusSelect;  

    $(document).on('change', '.status', function() {
        if ($(this).val() == 'Completed') {
            var details = $(this).data('details');

            currentStatusSelect = $(this);

            $('#modalBody').html(details);

            $('#myModal').modal('show');

            var orderId = currentStatusSelect.data('orderid');

$.ajax({
    url: 'fetch_order_details_cull.php',
    type: 'POST',
    data: { orderId: orderId },
    success: function(response) {
        $('#modalBody').html(response);

        $('#myModal').modal('show');
    },
    error: function(jqXHR, textStatus, errorThrown) {
        console.error(textStatus, errorThrown);
    }
});
            
        }
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


    $('#confirmBtn').click(function () {
        var allFilled = true;
        var weights = {};
        var sowIds = {}; 
        var totalPrice = 0;
    $('.orderWeight').each(function() {
        var detailId = $(this).data('detail-id');
        var sowId = $(this).data('sow-id');
        var weight = $(this).val();
        var priceInput = $('.orderPrice[data-detail-id="' + detailId + '"]');
        var price = parseFloat(priceInput.val());
        weights[detailId] = weight;
        
    if (!sowIds[sowId]) {
        sowIds[sowId] = 0;
    }
    sowIds[sowId]++;

        totalPrice += weight * price;

        if ($(this).val() === '') {
            allFilled = false;
            return false; 
        }
    });
    console.log(totalPrice);

    if (!allFilled) {
        alert('Please fill all the weight fields.');
        return;
    }
    
        currentStatusSelect.attr('disabled', true);

        var orderId = currentStatusSelect.data('orderid');
        let today = new Date();
let date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
        $.ajax({
            url: 'update_order_status_cull.php',
            type: 'POST',
            data: { orderId: orderId, status: 'Completed',   weights: weights , sowIds: sowIds, totalPrice: totalPrice.toFixed(2), date: date},
            success: function(response) {
                alert(response);
                console.log(response);

                location.reload();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error(textStatus, errorThrown);
            }
        });

        $('#myModal').modal('hide');
    });

    $('#cancelBtn').click(function () {
        currentStatusSelect.val('Pending');
    });

    $('#myModal').on('hidden.bs.modal', function (e) {
        if (currentStatusSelect.val() == 'Completed' && !currentStatusSelect.is(':disabled')) {
            currentStatusSelect.val('Pending');
        }
    });

    $(document).on('click', '.delete-btn', function() {
        deleteorder = $(this).data('id');
        $('#deleteModal-' + deleteorder).modal('show');
    });

    $(document).on('click', '#confirmDelete', function() {
        deleteorder(id);
    });
});

function deleteorder(id) {
    $.ajax({
        url: 'delete.php',  
        type: 'POST',
        data: { order_id: id },
        success: function(response) {
            $('#deleteModal-' + id).modal('hide');
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
