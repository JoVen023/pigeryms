<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
	
    header('location:index.php');
    exit; // It's important to stop further script execution
} 
$_SESSION['sidebarname'] = 'Messages';
	if(isset($_POST['delete'])){
		$messageid=$_POST['id'];
		$sql="DELETE FROM tblmessage WHERE ID=:messageid";
		$query=$dbh->prepare($sql);
		$query->bindParam(':messageid',$messageid , PDO::PARAM_INT);
		 try{
           $query->execute();
		 }catch(PDOException $e){
              echo "Qeury Failed:" . $e->getMessage();
			  exit;
		   }
	}

	if(isset($_POST['sent'])){
		$id = htmlentities($_POST['id']);
		$name = htmlentities($_POST['name']);
		$email = htmlentities($_POST['email']);
		$subject = htmlentities($_POST['subject']);
		$message = htmlentities($_POST['message']);
	
		$query = $dbh->prepare("UPDATE tblmessage SET status='sent' WHERE id = :id");
		$query->bindParam(':id', $id, PDO::PARAM_STR);
	
		$mail = new PHPMailer(true);
		try {
			$query->execute();
			
		$mail->isSMTP();
		$mail->Host = 'smtp.gmail.com';
		$mail->SMTPAuth = true;
		$mail->Username = 'chrisamizola93@gmail.com';
		$mail->Password = 'pjiuquzkspputses';
		$mail->Port = 465;
		$mail->SMTPSecure = 'ssl';
		$mail->isHTML(true);
		$mail->setFrom('chrisamizola93@gmail.com', $name);
		$mail->addAddress($email);
		$mail->Subject = $subject;
		$mail->Body = $message;
		$mail->send();

		echo "<script type='text/javascript'>alert('Email Sent'); window.location.href = 'messages.php';</script>";
	  } catch (Exception $e) {
		echo $e->getMessage();
		echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		exit;
	}
	}

	?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Messages</title>
	<link rel="stylesheet" type="text/css" href="style.css">
<link rel="icon" type="image/x-icon" href="img/logos.jpeg">
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">

<!-- SCRIPTS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

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
					<h1>Messages List</h1>
				</div>
					<table id="myTable">
						<thead>
							<tr>
                                <th>ID</th>
								<th>FullName</th>
                                <th>Email Address</th>
								<th>Message</th>
                                <th>Date</th>
								<th>Action</th>
							</tr>
						</thead>
                        
						<tbody>

						<?php 
$sql ="SELECT * FROM tblmessage";
$query3 = $dbh->prepare($sql);
$query3->execute();
$results=$query3->fetchAll(PDO::FETCH_OBJ);

foreach($results as $result){
	$date = new DateTime($result->tbldate);
	$formatteddate = $date->format('F j, Y');

?>
							<tr>
	<td>
	<p><?php echo htmlentities($result->id); ?></p>
		</td>

	<td><?php echo htmlentities($result->fullname); ?></td>
	<td><?php echo htmlentities($result->emailaddress); ?></td>
    <td><?php echo htmlentities($result->message); ?></td>
    <td><?php echo htmlentities($formatteddate); ?></td>
	<td>
    <?php if($result->status == 'sent'): ?>
        <button type="button" class="btn reply" title="Replied"><i class='bx bx-check'></i></button>
    <?php else: ?>
		<button type="button" class="btn reply" title="Reply Message" data-bs-toggle="modal" data-bs-target="#replymodal-<?php echo htmlentities($result->id); ?>"><i class='bx bx-mail-send'></i></button>
    <?php endif; ?>
    <button type="button" class="btn delete" title="Delete Message"  data-bs-toggle="modal" data-bs-target="#deleteModal-<?php echo htmlentities($result->id); ?>"><i class='bx bx-trash'></i></button>
</td>
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
                    <img src="img/mess.svg" alt="Profile Picture" width="150px" height="150px">
                    <h3 class="confirm">Are you sure you want to delete this Message?</h3>
                  </div>
                    
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
					<input type="hidden" name="id" value="<?php echo htmlentities($result->id); ?>">
                    <button type="submit" name="delete" class="btn btn-danger">Confirm</button>
</form>
                </div>
            </div>
        </div>
    </div>
<!-- delete pig Modal -->
</tr>
 <!-- send  Modal -->

 <div class="modal fade" id="replymodal-<?php echo htmlentities($result->id); ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header custom-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Send Email</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
	  <div class="modal-body">
				<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">  
				<div class="mb-3">
  <label for="exampleFormControlInput1" class="form-label">Name</label>
  <input type="text" class="form-control" name="name" id="exampleFormControlInput1" value="Ronalds Baboyan" autocomplete="off" required>
</div>
				<div class="mb-3">
  <label for="exampleFormControlInput3" class="form-label">Email address</label>
  <input type="email" class="form-control" name="email" id="exampleFormControlInput3" value="<?php echo htmlentities($result->emailaddress); ?>" autocomplete="off" required >
</div>
<div class="mb-3">
  <label for="exampleFormControlInput4" class="form-label">Subject</label>
  <input type="text" class="form-control" name="subject" id="exampleFormControlInput4" value="Response to Your Inquiry,<?php echo htmlentities($result->fullname); ?>" autocomplete="off" required >
</div>
<div class="mb-3">
  <label for="exampleFormControlTextarea1" class="form-label">Message</label>
  <textarea class="form-control" name="message" id="exampleFormControlTextarea1" rows="3" required></textarea>
</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					<input type="hidden" name="id" value="<?php echo htmlentities($result->id); ?>">
                    <button type="submit" name="sent" class="btn btn-primary">Confirm</button>
</form>
                </div>
  </div>
                </div>

				</div>
<!-- send Modal -->
<?php 
} 
?>	

							
						
							
						</tbody>
						</div>
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
  $(document).ready( function () {
    $('#myTable').DataTable();
  });

</script>


	<script src="script.js"></script>
</body>
</html>
