<?php
include('includes/config.php');
// echo password_hash('ADMIN', PASSWORD_DEFAULT);
    if (isset($_GET['id']) ) {
        $pigletsId = intval($_GET['id']);
    } else {
        die('Pig not found.');
    }
 $pigletqr = $dbh->prepare("SELECT img FROM piglets_qr WHERE piglet_id = :id");
$pigletqr->bindparam(':id',$pigletsId,PDO::PARAM_INT);
$pigletqr->execute();
$pigletsqr = $pigletqr->fetch(PDO::FETCH_ASSOC);

$qrImagePath = isset($pigletsqr['img']) ? $pigletsqr['img'] : '';



    $queryDates = "SELECT weaneddate, piggybloom, prestarter, starter, grower, finisher FROM tblgrowingphase tg LEFT JOIN piglets p ON tg.id = p.growinphase_id WHERE tg.id = p.growinphase_id  AND p.id = :pigId ";
$stmtDates = $dbh->prepare($queryDates);
$stmtDates->bindParam(':pigId', $pigletsId, PDO::PARAM_INT);
$stmtDates->execute();
$pigDates = $stmtDates->fetch(PDO::FETCH_ASSOC);

$currentDate = new DateTime();
// Format both dates for comparison
$formattedCurrentDates = $currentDate->format('Y-m-d');
$formattedthirtyoneDay = (new DateTime($pigDates['piggybloom']))->format('Y-m-d');
$formattedfiftyoneDay = (new DateTime($pigDates['prestarter']))->format('Y-m-d');
$formattedeightyoneDay = (new DateTime($pigDates['starter']))->format('Y-m-d');
$formattedgrowerDay = (new DateTime($pigDates['grower']))->format('Y-m-d');
$formattedfinisherDay = (new DateTime($pigDates['finisher']))->format('Y-m-d');

if ($formattedCurrentDates >= $formattedfinisherDay) {
    $stat = "Finisher";
} elseif ($formattedCurrentDates >= $formattedgrowerDay) {
    $stat = "Finisher";
} elseif ($formattedCurrentDates >= $formattedeightyoneDay) {
    $stat = "Grower";
} elseif ($formattedCurrentDates >= $formattedfiftyoneDay) {
    $stat = "Starter";
} elseif ($formattedCurrentDates >= $formattedthirtyoneDay) {
    $stat = "Pre-Starter";
} else {
    // If none of the above conditions are met, set a default status or don't update
    $stat = "PiggyBloom"; // replace 'DefaultStatus' with whatever default status you want or simply don't set the $stats variable
}
if (isset($stat)) {
    $updateQuery = "UPDATE tblgrowingphase SET status = :status WHERE id = :pigId";
    $stmt = $dbh->prepare($updateQuery);
    $stmt->bindParam(':status', $stat);
    $stmt->bindParam(':pigId', $pigletsId, PDO::PARAM_INT);
    
    try {
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Error updating status: " . $e->getMessage();
    }
}

// Retrieve the pig details from the database using the $pigId
    $query = "SELECT tg.*, p.*,p.status as pstatus ,(tg.pigs - COUNT(p.growinphase_id)) AS totalpigs 
FROM piglets p
LEFT JOIN tblgrowingphase tg ON p.growinphase_id = tg.id
WHERE p.id = :pigId;
";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':pigId', $pigletsId, PDO::PARAM_INT);
$stmt->execute();
$pig = $stmt->fetch(PDO::FETCH_ASSOC);

$growingphase_id = $pig['growinphase_id'];


$query = "SELECT COUNT(id) AS totalpigs 
FROM piglets 

WHERE growinphase_id = :growingphase_id;
";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':growingphase_id', $growingphase_id, PDO::PARAM_INT);
$stmt->execute();
$pigtotals = $stmt->fetch(PDO::FETCH_ASSOC);
$totalpigs = $pigtotals['totalpigs'];


$date = !empty($pig['weaneddate']) ? new DateTime($pig['weaneddate']) : null;
$weaneddate = $date ? $date->format('F j, Y') : null;
$piggybloom = !empty($pig['piggybloom']) ? new DateTime($pig['piggybloom']) : null;
$piggybloomdate = $piggybloom ? $piggybloom->format('F j, Y') : null;
$prestarter = !empty($pig['prestarter']) ? new DateTime($pig['prestarter']) : null;
$prestarterdate = $prestarter ? $prestarter->format('F j, Y') : null;
$grower = !empty($pig['grower']) ? new DateTime($pig['grower']) : null;
$growerdate = $grower ? $grower->format('F j, Y') : null;
$starter = !empty($pig['starter']) ? new DateTime($pig['starter']) : null;
$starterdate = $starter ? $starter->format('F j, Y') : null;
$finisher = !empty($pig['finisher']) ? new DateTime($pig['finisher']) : null;
$finisherdate = $finisher ? $finisher->format('F j, Y') : null;

$formattedCurrentDate = $currentDate->format('Y-m-d');
$formattedthirtyoneDayAfter = $piggybloom->format('Y-m-d');
$formattedfiftyoneDayAfter = $prestarter->format('Y-m-d');
$formattedeightyoneDayAfter = $starter->format('Y-m-d');
$formattedgrowerDayAfter = $grower->format('Y-m-d');
$formattedfinisherDayAfter = $finisher->format('Y-m-d');

// age
$weaningDate = new DateTime($pig['weaneddate']);
$currentDate = new DateTime();  
$weaningDate->setTime(0, 0, 0);
$currentDate->setTime(0, 0, 0);
$interval = $currentDate->diff($weaningDate);

$daysDifference = $interval->days;
$age = $daysDifference;
// age


    if ($formattedCurrentDate >= $formattedfinisherDayAfter) {
        $interval = $currentDate->diff($finisher);
        
        $stats = "Finisher";
        $feedConsumptionRate = $pig['pigs'] * 2.2; // average of 2.2kg/day and 2.5kg/day
        $feedsConsumptionRate = $pig['pigs'] *  2.5; // average of 2.2kg/day and 2.5kg/day
        $totalFeed = ($feedConsumptionRate *  $interval->days) / $pig['totalpigs'];
        $totalFeeds = ($feedsConsumptionRate *  $interval->days) / $pig['totalpigs'];
} elseif ($formattedCurrentDate >= $formattedgrowerDayAfter) {
    $stats = "Finisher";
    $feedConsumptionRate = $pig['pigs'] * 2.2; // average of 2.2kg/day and 2.5kg/day
    $feedsConsumptionRate = $pig['pigs'] *  2.5; // average of 2.2kg/day and 2.5kg/day
    $totalFeed = ($feedConsumptionRate *  $interval->days) / $pig['totalpigs'];
    $totalFeeds = ($feedsConsumptionRate *  $interval->days) / $pig['totalpigs'];

} elseif ($formattedCurrentDate >= $formattedeightyoneDayAfter) {
    $stats = "Grower";
    $feedConsumptionRate = $pig['pigs'] * 1.5; // average of 2.2kg/day and 2.5kg/day
    $feedsConsumptionRate = $pig['pigs'] *  2.2; // average of 2.2kg/day and 2.5kg/day
    $totalFeed = ($feedConsumptionRate *  $interval->days) / $pig['totalpigs'];
    $totalFeeds = ($feedsConsumptionRate *  $interval->days) / $pig['totalpigs'];
} elseif ($formattedCurrentDate >= $formattedfiftyoneDayAfter) {
    $stats = "Starter";
    $feedConsumptionRate = $pig['pigs'] * 0.8; // average of 2.2kg/day and 2.5kg/day
    $feedsConsumptionRate = $pig['pigs'] *  1.5; // average of 2.2kg/day and 2.5kg/day
    $totalFeed = ($feedConsumptionRate *  $interval->days) / $pig['totalpigs'];
    $totalFeeds = ($feedsConsumptionRate *  $interval->days) / $pig['totalpigs'];
} elseif ($formattedCurrentDate >= $formattedthirtyoneDayAfter) {
    $stats = "Pre-Starter";
    $feedConsumptionRate = $pig['pigs'] * 0.4; // average of 2.2kg/day and 2.5kg/day
    $feedsConsumptionRate = $pig['pigs'] *  0.8; // average of 2.2kg/day and 2.5kg/day
    $totalFeed = ($feedConsumptionRate *  $interval->days) / $pig['totalpigs'];
    $totalFeeds = ($feedsConsumptionRate *  $interval->days) / $pig['totalpigs'];
} else {
    // If none of the above conditions are met, set a default status or don't update
    $stats = "PiggyBloom"; // replace 'DefaultStatus' with whatever default status you want or simply don't set the $stats variable
    $feedConsumptionRate = $pig['pigs'] * 0.02; // average of 2.2kg/day and 2.5kg/day
    $feedsConsumptionRate = $pig['pigs'] *  0.025; // average of 2.2kg/day and 2.5kg/day
   $totalFeed = ($feedConsumptionRate *  $interval->days) / $pig['totalpigs'];
        $totalFeeds = ($feedsConsumptionRate *  $interval->days) / $pig['totalpigs'];
}
// Determine the total sacks needed
// status dates interval


	
	?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Pig</title>
        <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="./admin/style.css">
    <link rel="icon" type="image/x-icon" href="./admin/img/logos.jpeg">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <!-- SCRIPTS -->

<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<!-- Then load Bootstrap and its dependencies -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS should be loaded after jQuery -->
<script src="./admin/js/swal.js"></script>
 <!--Load Swal-->
 <?php if (isset($success)) { ?>
        <!--This code for injecting success alert-->
        <script>
            setTimeout(function() {
                    swal("Success", "<?php echo $success; ?>", "success");
                },
                100);
                $('[data-bs-toggle="popover"]').popover();
    document.addEventListener('DOMContentLoaded', function () {
      const trigger = document.getElementById('notificationButton');
      const popover = new bootstrap.Popover(trigger);

      // Optional: Close popover when clicking outside
      document.addEventListener('click', function (e) {
        if (!trigger.contains(e.target)) {
          popover.hide();
        }
      });
    });
        </script>

    <?php } ?>

    </head>
    <style>
        #content {
	position: relative;
	width:100%;
	left: 0px;
	transition: .3s ease;
}

#content main .head-title {
	display: flex;
	margin-top: 1rem;
	align-items: center;
	justify-content: center;
	grid-gap: 16px;
	flex-wrap: wrap;
}
    </style>
    <body>
        <section id="content">
            <main>

            <div class="head-title">
                   
                        <h1>Feeding Guide</h1>
                </div>
                <div class="feedingguide">
                
            <figure>
            <img src="./admin/img/<?php echo $stats?>.png" class="img-fluid rounded-start" alt="starter">
    </figure>
    </div>

    <div class="table-data bred">
                <div class="card mb-3">
    <div class="row g-0">
        <div class="col-md-4" >
        <div class="image-container">
        <img src="./admin/img/<?php echo $pig['img']; ?>" class="img-fluid rounded-start" alt="pig">
                <div class="image-overlay"></div> 
            </div>
        </div>
        <div class="col-md-8">
        <div class="card-body">
        <div class="pigsts">
        <div class="left-section"> 
            <h2 class="card-title"><?php echo $pig['name']; ?></h2>
        </div>
        <div class="right-section"> 
        <!-- <?php if (!empty($qrImagePath)): ?>
    <a href="print_qr.php?img=<?php echo urlencode($qrImagePath); ?>&name=<?php echo urlencode($pig['name']); ?>"
       class="btn btn-sm"
       title="Print QR"
       target="_blank">
       <i class='bx bx-qr-scan'></i>
    </a>
<?php endif; ?> -->

        <p class="card-text <?php echo $pig['pstatus']; ?>"><?php echo $pig['pstatus']; ?></p>
        
    </div>
    </div>
                <p class="card-text"><span>Gender:</span> <?php echo $pig['gender']; ?></p>
                <p class="card-text"><span>Breed:</span> <?php echo $pig['breed']; ?></p>
                <p class="card-text"><span>Age:</span> <?php echo $age; ?> days</p>
                <p class="card-text"><span>Weaned Date:</span> <?php echo $weaneddate ?></p>
                <p class="card-text"><span>Proceed to <?php 
        if ($pig['status'] == "PiggyBloom") {
            echo "Pre-Starter:</span> $piggybloomdate ";
        } elseif ($pig['status'] == "Pre-Starter") {
            echo "Starter:</span> $prestarterdate";
        }
        elseif ($pig['status'] == "Starter") {
            echo "Grower:</span> $starterdate";
        }
        elseif ($pig['status'] == "Grower") {
            echo "Finisher:</span> $growerdate";
        }
        elseif ($pig['status'] == "Finisher") {
            echo "Completed:</span> $finisherdate";
        }
        else{
            echo 'Sell';
        }
        
    ?></p>
    <p class="card-text"><span>Total Feeds Consumption:</span> <?php echo  round($totalFeed / $totalpigs, 2); ?> - <?php echo round($totalFeeds /  $totalpigs,2); ?> Kilograms</p>
    </div>


        </div>
     

 

    </div>
    </div>
    </div>

    <section class="records">
<div class="head-title">
				<div class="left">
					<h1>Vaccination Records</h1>
				
				</div>
                
			</div>
        <div class="table-data">
				<div class="order">
                <?php 
                          $sql = "SELECT * FROM vaccines_shot
                          WHERE piglets_id = :piglets_id";
                  
                  $query3 = $dbh->prepare($sql);
                  $query3->bindParam(':piglets_id',$pigletsId, PDO::PARAM_INT);
                  $query3->execute();
                  $results = $query3->fetchAll(PDO::FETCH_OBJ);
                  $totalRows = count($results);
          
                  ?>
				<div class="left">
					<h1>Records Lists</h1>
                    <button type="button" class="openModalBtn" id="openEdit">
  <i class='bx bx-plus-circle' ></i> Add New
</button>
				</div>
                <table id="myTable">
						<thead>
							<tr>
                                <th  class="text-center">ID</th>
								<th  class="text-center">Vaccine Name</th>
                                <th  class="text-center">Date Vaccinated</th>
                                <th  class="text-center">Action</th>
                                
							</tr>
						</thead>
                        
						<tbody>
                   
                     
                  <?php 
                          foreach($results as $result){
                            $date = new DateTime($result->date_vaccinated);
                            $formatteddates = $date->format('F j, Y');
                        
                          
                          ?>
                              
                              <tr>
	<td>
	<p><?php echo htmlentities($result->id); ?></p>
		</td>

	<td><?php echo htmlentities($result->vaccine_name); ?></td>
	<td><?php echo htmlentities($formatteddates); ?></td>

 
    <!-- Button trigger modal -->
    <td class="action">
    <button type="button" class="btn deleterecord" title="Delete Record" data-bs-toggle="modal" data-bs-target="#deleteModalrecord-<?php echo htmlentities($result->id); ?>" data-id="<?php echo htmlentities($result->id); ?>" data-breeder-id="<?php echo htmlentities($result->breeder_id); ?>"> <i class='bx bx-trash'></i></button>


                          </td>
    <!-- Button trigger modal -->
  </tr>
  
<!-- deletepig  Modal -->
<div class="modal fade" id="deleteModalrecord-<?php echo htmlentities($result->id); ?>" tabindex="-1"  aria-labelledby="cancelModalLabel-<?php echo htmlentities($result->id); ?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                  
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        
                    </button>
                </div>
                <div class="modal-body">
                <div class="text-center">
                    <img src="./admin/img/deletepig.svg" alt="Profile Picture" width="150px" height="150px">
                    <h3 class="confirm">Are you sure you want to delete this Record?</h3>
                  </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" onclick="deleterecord('<?php echo htmlentities($result->id); ?>')" name="deleterecord">Confirm</button>
                    <input type="hidden" id="breederIdHiddenField" value="<?php echo htmlentities($result->piglets_id); ?>">
                </div>
            </div>
        </div>
    </div>

<!-- delete record Modal -->
<!-- update pig Modal -->

<div class="modal fade" id="updateModals" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header custom-header">
        <h5 class="modal-title" id="exampleModalLabel">Update Pig</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="updateForms" action="breederdetails.php?id=<?php echo $breederId; ?>" method="POST">
          
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" name="updaterecord" class="btn btn-primary">Update</button>
      </div>
      </form>
    </div>
  </div>
</div>                 
<!-- update pig Modal -->



<?php 
} 
?>	
						</tbody>
					</table>

                    <!-- Password Modal -->
<div class="modal" id="passwordModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5>For Authorize Personnel Only</h5></div>
      <div class="modal-body">
        <input type="password" id="staffPass" class="form-control" placeholder="Enter password to Edit">
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" id="checkPass">Continue</button>
      </div>
    </div>
  </div>
</div>

					<!-- add pig Modal -->
<div class="modal fade" id="editModal" tabindex="-1"  aria-hidden="true" tabindex="-1" aria-labelledby="exampleModalLabels" aria-hidden="true">
<div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header custom-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Add New Record</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <form  action="includes/add_record.php" method="POST" id="editForm">
      <div class="col">
      <div class="row">
  <label for="by">Vaccined By</label>
    <input type="text" name="by" id="by" class="form-control" placeholder="Vaccine Name" aria-label="vaccine" autocomplete="given-name" required>
  
</div>
<br>
  <div class="row">
  <label for="name">Vaccine Name</label>
    <input type="text" name="vaccine" id="vaccine" class="form-control" placeholder="Vaccine Name" aria-label="vaccine" autocomplete="given-name">
    <input type="hidden" name="id" id="id" class="form-control" placeholder="ID" aria-label="ID" autocomplete="given-name" value="<?php echo $pigletsId?>">
  
</div>
  <br>
  <div class="row">
  <label for="date">Vaccinated Date</label>
  <input type="date" name="date" id="date" class="form-control"  autocomplete="given-name" required>
  </div>

</div>
<br>
      <div class="modal-footer">
        <div class="col">
            <div class="row mb-1">
            <button type="submit" name="record" class="btn btn-primary" id="confirmBtn">Confirm</button>
      </div>
      <div class="row">
      <button type="button" class="btn btn-secondary" id="cancelBtn" data-bs-dismiss="modal">Cancel</button>
                </div>
                </div>
      </div>
      </form>
    </div>
  </div>
                </div>

				</div>
        </div>

        	
        </div>
        </section>
    
            </main>
        </section>
 <script>

$('#openEdit').on('click', function () {
  $('#passwordModal').modal('show');
});

$('#checkPass').on('click', function () {
  $.post('auth_staff.php', { password: $('#staffPass').val() }, function (resp) {
    // let res = JSON.parse(resp);
    if (resp.success) {
      $('#passwordModal').modal('hide');
      $('#editModal').modal('show');
    } else {
      alert(resp.message);
    }
  });
});

// $('#editForm').on('submit', function (e) {
//   e.preventDefault();
//   $.post('.php', $(this).serialize(), function (resp) {
//     if (resp === 'ok') {
//       alert('Saved!');
//       location.reload();
//     } else {
//       alert('Error: ' + resp);
//     }
//   });
// });



    var deletePigId;
var breederId;

$(document).on('click', '.deleterecord', function() {
    deletePigId = $(this).data('id');
    breederId = $(this).data('breeder-id');

    $('#deleteModalrecord-' + deletePigId).modal('show');
});

$(document).on('click', '#confirmDelete', function() {
    deleterecord(deletePigId);
});


function deleterecord(id) {
    // Send a POST request to delete.php
    $.ajax({
        url: './admin/delete.php',
        type: 'POST',
        data: { vaccinerecordid: id, breeder_id: breederId }, 
        success: function(response) {
            $('#deleteModalrecord-' + id).modal('hide');
             location.reload();
             alert('Deleted Successully.');
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
            alert('An error occurred while trying to delete the record.');
        }
    });
}

 </script>
    </body>
    </html>