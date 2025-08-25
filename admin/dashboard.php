<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';
include('includes/acc.php');
include('includes/config.php');
include 'fetchsow.php';
if(strlen($_SESSION['alogin'])==0)
	{	

header('location:index.php');
}
else{
	$_SESSION['sidebarname'] = 'Dashboard';
if (!isset($_SESSION['dark_mode'])) {
		$_SESSION['dark_mode'] = false;
	}

	$sow = getsowparent($dbh);

	if (isset($_POST['addnote'])) {
	$sow=$_POST['sow'];
	$details=$_POST['details'];
	$date=$_POST['date'];

	$sql= $dbh->prepare("INSERT INTO tbltodo (sow_id,details,time) VALUES (:sowid,:details,:date)");
	$sql->bindParam(':sowid',$sow,PDO::PARAM_INT);
	$sql->bindParam(':details',$details,PDO::PARAM_STR);
	$sql->bindParam(':date',$date,PDO::PARAM_STR);
    try {
		$sql->execute();
		echo "<script type='text/javascript'>alert('Added Successfully'); window.location.href = 'dashboard.php';</script>";
	} catch (PDOException $ex) {
		echo $ex->getTraceAsString();
		echo $ex->getMessage();
		exit;
	}
}




	if (isset($_POST['deletenote'])) {
		$id = $_POST['id'];
         
		$deletenote="DELETE FROM tbltodo WHERE id=:id";
		$stmts = $dbh->prepare($deletenote);
		$stmts->bindParam(':id',$id,PDO::PARAM_INT);
        try {
			$stmts->execute();
			echo "<script type='text/javascript'>alert('Deleted Successfully'); window.location.href = 'dashboard.php';</script>";
		} catch (PDOException $ex) {
			echo $ex->getTraceAsString();
			echo $ex->getMessage();
			exit;
		}
	}

	function sendEmail($note) {
		global $config;
		
		$mail = new PHPMailer(true);
		$mail->isSMTP();
		$mail->Host = 'smtp.gmail.com';
		$mail->SMTPAuth = true;
		$mail->Username = 'chrisamizola93@gmail.com';
		$mail->Password = 'pjiuquzkspputses';
		$mail->Port = 465;
		$mail->SMTPSecure = 'ssl';
		$mail->isHTML(true);
		$mail->setFrom('chrisamizola93@gmail.com', 'Automated Note Reminder');
		$mail->addAddress('chrisamizola93@gmail.com');
		$mail->Subject = "Reminder for: " . $note->details;
		$mail->Body = "Note Details: " . $note->details . "<br>Date: " . $note->time . " 'Today'";
		
		try {
			$mail->send();
		} catch (Exception $e) {
			// Log or print the error if needed
			echo "Mailer Error: " . $mail->ErrorInfo;
		}
	}

	function markAsSent($dbh, $id) {
		$updateStmt = $dbh->prepare("UPDATE tbltodo SET emailed = 1 WHERE id = :id");
		$updateStmt->bindParam(':id', $id, PDO::PARAM_INT);
		$updateStmt->execute();
	}
	
	
	?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Ronald's Baboyan</title>
	<!-- CSS -->
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="icon" type="image/x-icon" href="img/logos.jpeg">
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<link rel="preconnect" href="https://fonts.googleapis.com">

<!-- SCRIPTS -->
<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<!-- Then load Bootstrap and its dependencies -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

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
			

			<ul class="box-info">
			<?php 
$sql ="SELECT id from tblorders ";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$orders=$query->rowCount();
?>
				<li>
					<i class='bx bxs-calendar-check' ></i>
					<span class="text">
						<h3><?php echo htmlentities($orders);?></h3>
						<p>New Order</p>
					</span>
				</li>

				<?php 
$sql ="SELECT id from tblusers ";
$query1 = $dbh -> prepare($sql);
$query1->execute();
$results=$query1->fetchAll(PDO::FETCH_OBJ);
$regusers=$query1->rowCount();
?>


				<li>
					<i class='bx bxs-group' ></i>
					<span class="text">
						<h3><?php echo htmlentities($regusers);?></h3>
						<p>Registered Customers</p>
					</span>
				</li>

				
				<?php 

$sql = "SELECT (sum(total_amount) + (SELECT sum(totalamount) FROM tblsoworder)) as total_sales 
        FROM tblorders 
        WHERE orderstatus = 'Completed'";

$query2 = $dbh->prepare($sql); 
$query2->execute();
$result = $query2->fetch(PDO::FETCH_ASSOC);

?>

				<li>
					<i class='bx bxs-dollar-circle' ></i>
					<span class="text">
						<h3><span>&#8369;</span><?php echo number_format($result['total_sales'], 2);?></h3>
						<p>Total Sales</p>
					</span>
				</li>
			</ul>

			<ul class="box-info">
			<?php 
$sql ="SELECT id from tblpigbreeders WHERE status= 'Breeding' ";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$breeder=$query->rowCount();
?>
				<li class="pigbreeder">
					<i class='bx bx-female-sign' ></i>
					<span class="text">
						<h3><?php echo htmlentities($breeder);?> Sow</h3>
						<p>For Breeding</p>
					</span>
				</li>

				<?php 
$sql ="SELECT id from tblpigbreeders WHERE status= 'Farrowing' ";
$query1 = $dbh -> prepare($sql);
$query1->execute();
$results=$query1->fetchAll(PDO::FETCH_OBJ);
$farrowing=$query1->rowCount();
?>


				<li class="pigbreeder">
					<i class='bx bxs-baby-carriage' ></i>
					<span class="text">
						<h3><?php echo htmlentities($farrowing);?> Sow</h3>
						<p>Farrowing </p>
					</span>
				</li>

				
				<?php 
$sql ="SELECT id from tblpigbreeders WHERE status= 'Lactating' ";
$query1 = $dbh -> prepare($sql);
$query1->execute();
$results=$query1->fetchAll(PDO::FETCH_OBJ);
$gestating=$query1->rowCount();
?>

				<li class="pigbreeder">
					<i class='bx bxs-donate-blood' ></i>
					<span class="text">
						<h3><?php echo htmlentities($gestating);?> Sow</h3> 
						<p>Lactating</p>
					</span>
				</li>
			</ul>


			<div class="table-data">
				<div class="order">
					<div class="head">
						<h3>Recent Orders</h3>
						<a href="orders.php"><i class='bx bx-search' ></i></a>
						<i class='bx bx-filter' onclick="toggleOrderContent()"></i>
					</div>
					<div class="order-content">
					<table>
						<thead>
							<tr>
								<th>User</th>
								<th>Date Order</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>

						<?php 
$sql ="SELECT tblusers.id, tblusers.FullName, tblorders.orderdate, tblorders.orderstatus, tblorders.cust_id 
FROM tblusers
JOIN tblorders ON tblusers.id = tblorders.cust_id ORDER BY CASE 
             WHEN tblorders.orderstatus = 'Pending' THEN 1
             WHEN tblorders.orderstatus = 'Completed' THEN 2
             ELSE 3
         END
";
$query3 = $dbh->prepare($sql);
$query3->execute();
$results=$query3->fetchAll(PDO::FETCH_OBJ);

foreach($results as $result){
	$orderdate = new DateTime($result->orderdate);
	$formattedorderdate = $orderdate->format('F j, Y');
?>
							<tr>
	<td class="first">
	<img src="img/user.png">
	<p><?php echo htmlentities($result->FullName); ?></p>
		</td>
	<td><?php echo htmlentities($formattedorderdate); ?></td>
	<td><span <?php if($result->orderstatus=="Completed"): echo 'class="status completed"'; else: echo 'class="status pending"'; endif; ?>><?php echo htmlentities($result->orderstatus); ?></span></td>
</tr>

<?php 
} 
?>	

							
						
							
						</tbody>
					</table>
</div>
				</div>

				<div class="todo d-none">
					<div class="head">
						<h3>Reminder/Todos</h3>
						<button type="button" title="Click to Add" data-bs-toggle="modal" data-bs-target="#confirmModal"
    class="add" ><i class='bx bx-plus'></i></button>
<i class='bx bx-filter' onclick="toggleTodoContent()"></i>
	
	<!-- add note-->


	<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header custom-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Add New Note</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <form  action="<?=$_SERVER['PHP_SELF']?>" method="POST">
      <div class="row">
        
  <div class="col">
  <label for="sow">Sow</label>
      <select
              id="sow"
              name="sow"
              class="form-select form-select-sm"
              required="required"
            >
              <?php echo $sow; ?>
            </select>
  </div>
</div>
<br>
<div class="row">
        <div class="col">
        <label for="fullname">Details</label>
          <input type="text" name="details" id="fullname" class="form-control" placeholder="Note" aria-label="Note" required>
        </div>
    
</div>
<br>
        
      <div class="row">
      <div class="col">
                                 <label for="date">Date</label></label>
  									<input type="date" id="date" name="date" class="form-control form-control-sm rounded-0" required>
								</div>
</div>

      <div class="modal-footer">
      <button type="button" class="btn btn-secondary" id="closeBtn" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="addnote" class="btn btn-primary" id="confirmBtn">Confirm</button>
      </div>
      </form>
    </div>
  </div>
                </div>

				</div>
				<!-- add note -->



					</div>
					<ul class="todo-list">
					<?php 
$currentDate = date('Y-m-d');  // Get the current date in the format 'YYYY-MM-DD'

$stmttodo = $dbh->prepare("SELECT * 
                       FROM tbltodo 
                       WHERE tbltodo.time >= :currentDate1
                       ORDER BY ABS(DATEDIFF(tbltodo.time, :currentDate1)) ASC");
$stmttodo->bindParam(':currentDate1', $currentDate, PDO::PARAM_STR);

$stmttodo->execute();

$todo = $stmttodo->fetchAll(PDO::FETCH_OBJ);
foreach ($todo as $to) {
	$date = new DateTime($to->time);
	$dates = $date->format('Y-m-d');
	$formatteddate = $date->format('F j, Y');
	$details = htmlspecialchars($to->details);

	$liClass = ($details != "Farrowing" && $details != "Weaning") ? 'default-class' : htmlspecialchars($details);
	$hrefsow = "breederdetails.php?id=" . htmlspecialchars($to->sow_id);
	$hrefpiglet = "unhealthypigletdetails.php?id=" . htmlspecialchars($to->piglet_id);
	$href = $to->sow_id ? $hrefsow : $hrefpiglet;
	$detailsClass = htmlspecialchars($details);
	$dateText = ($currentDate == $dates) ? '(Today)' : "($formatteddate)";
	
	echo "<a href='$href' data-bs-toggle='tooltip' data-bs-title='View'>";
	echo "<li class='$liClass'>";
	echo "<div class='cont'>";
	// Name 
	echo "<p>" . htmlspecialchars($to->details) . "</p>";
	
	// Details and Date centered
	echo "<div class='mid'>";
	echo "<p class='$liClass'>$details</p>";
	echo "<p class='date'>$dateText</p>";
	echo "</div>";
	
	// Icon
	echo "<form method='post' action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "'>";
	echo "<input type='hidden' name='id' value='" . htmlspecialchars($to->id) . "'>";
	echo "<button type='submit' name='deletenote' title='Delete Note'><i class='bx bx-trash'></i></button>";
	echo "</form>";
	
	echo "</div>";  // Closing tag for the main flex container
	echo "</li>";
	echo "</a>";

	if ($dates >= $currentDate && $to->emailed == 0) {
        sendEmail($to);  // Send the email
        markAsSent($dbh, $to->id);  // Mark as sent in the database
    }

}

?>
						
					</ul>
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
	const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
</script>
	<script src="script.js"></script>
</body>
</html>
<?php } ?>	