<?php
error_reporting(1);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
	{	
		
header('location:index.php');
}
else{
    
  
    if(isset($_GET['id'])) {
        $pigletId = intval($_GET['id']);
    } else {
        // Handle error or redirect to another page
        die('ID not provided.');
    }

// Retrieve the pig details from the database using the $pigId
$query = "SELECT up.*,up.id as id ,p.name as piglet_name,up.status as piglet_status,p.img as img,p.gender as gender
FROM unhealthy_piglets up 
 LEFT JOIN  piglets p ON p.id = up.piglet_id WHERE up.id = :pigId";

$stmt = $dbh->prepare($query);
$stmt->bindParam(':pigId', $pigletId, PDO::PARAM_INT);
$stmt->execute();
$pig = $stmt->fetch(PDO::FETCH_ASSOC);
$pigname = $pig['piglet_name'];


$date = !empty($pig['date']) ? new DateTime($pig['date']) : null;
$formatteddate = $date ? $date->format('F j, Y') : null;


if (isset($_POST['move'])) {
    $seatPricesJson = $_POST['seat_prices'] ?? null;
    $seatPrices = json_decode($seatPricesJson, true);

    if (!$seatPrices || !is_array($seatPrices)) {
        header("Location: unhealthypigletdetails.php?id=$pigletId&error=" . urlencode("Invalid data"));
        exit;
    }

    try {
        $dbh->beginTransaction();

        $stmt = $dbh->prepare("INSERT INTO vaccines_guide (piglet_id, vaccine_name, details, date) VALUES (:piglet_id, :vaccine_name, :details, :date)");
        $stmt1 = $dbh->prepare("INSERT INTO tbltodo (piglet_id, details, time) VALUES (:piglet_id, :details, :time)");

        foreach ($seatPrices as $seatPrice) {
            $stmt->execute([
                ':piglet_id' => $pigletId,
                ':vaccine_name' => $seatPrice['title'],
                ':details' => $seatPrice['details'],
                ':date' => $seatPrice['vaccince_date'], 
            ]);

            $stmt1->execute([
                ':piglet_id' => $pigletId,
                ':details' => $seatPrice['title'],
                ':time' => $seatPrice['vaccince_date'],
            ]);
        }

        $dbh->commit();
        header("Location: unhealthypigletdetails.php?id=$pigletId");
        exit;

    } catch (PDOException $e) {
        $dbh->rollBack();
        header("Location: unhealthypigletdetails.php?id=$pigletId&error=" . urlencode("PDO Exception: " . $e->getMessage()));
        exit;
    }
}








if(isset($_POST['update'])){
    $Id = intval($_POST['id']);  // Convert ID to integer
    $date = $_POST['date'];
    $status = $_POST['status'];

        $query = $dbh->prepare("UPDATE unhealthy_piglets SET status=:status, date=:date, status=:status WHERE id=:id");
        $query->bindParam(':status', $status, PDO::PARAM_STR);
        $query->bindParam(':date', $date, PDO::PARAM_STR);
        $query->bindParam(':id', $Id, PDO::PARAM_INT);
    // Execute the query
    try {
        $query->execute();
        echo "<script type='text/javascript'>alert('Updated Successfully'); window.location.href = 'unhealthypigletdetails.php?id=" . $Id . "';</script>";

    } catch (PDOException $ex) {
        echo $ex->getMessage();
        exit;
    } 

}








	
	?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Pig Breeders</title>
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
<script src="js/swal.js"></script>

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
					<h1>Piglet Details</h1>
				
				</div>
			</div>

           
            <div class="care-timeline">
               <div class="guide">
            <h5>Vaccination Guide</h5>
            <button type="button" title="Click to Add" data-bs-toggle="modal" data-bs-target="#addModal" class="openModalBtn" 
            >
    <i class='bx bx-up-arrow-circle'></i>Add Vaccination Guide
</button>

 <!-- guide vaccine pig modal -->

 <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header custom-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Vaccination Guide</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        
      <form action="<?=$_SERVER['REQUEST_URI']?>" method="POST" enctype="multipart/form-data">
      <div class="row">
      <input type="hidden" name="sow_id" class="form-control" value="<?php echo $pig['id']; ?>">
      <input type="hidden" name="seat_prices" id="seat-prices">

  <div class="col">
  <label for="title">Title</label>
    <input type="text" name="title" id="title" class="form-control" placeholder="Guide Title" aria-label="Guide Title" autocomplete="given-name" />
  </div>
  <div class="col">
        <label for="date" class="me-1">Date</label>
        <input type="date" name="vaccince_date" id="vaccince_date" class="form-control"  />
        </div>
</div>
<br>
<div class="row">
        
        <div class="col">
        <label for="details">Details</label>
          <input type="text" id="details" name="details" class="form-control" placeholder="Guide Details" aria-label="Guide Details">
        </div>
</div>
<br>
<div class="row">
    <div class="col">
    <input type="button" class="form-control btn btn-dark" id="price-add" value="ADD">
    </div>
</div>
<br>
<div class="row">
<div class="table-responsive">
                      <table class="table table-bordered">
                        <thead>
                          <tr>
                            <th scope="col">Title</th>
                            <th scope="col">Details</th>
                            <th scope="col">Date</th>
                            <th scope="col">Control</th>
                          </tr>
                        </thead>
                        <tbody id="spouse-children">
                        </tbody>
                      </table>
                    </div>
</div>
      <div class="modal-footer">
      <button type="button" class="btn btn-secondary" id="cancelBtn" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="move" class="btn btn-primary" id="confirmBtn">Confirm</button>
      </div>
      </form>
    </div>
  </div>
                </div>

				</div>    
<!-- movepig modal -->


</div>

                <br>
                <div class="care-tasks">
                <?php
// Assuming you have the vaccines array from the database query
$referenceDate = new DateTime(); 
$sql = "SELECT vaccine_name, details, date FROM vaccines_guide WHERE piglet_id = :piglet_id ORDER BY date ASC";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':piglet_id', $pigletId, PDO::PARAM_INT);
$stmt->execute();

$vaccines = $stmt->fetchAll(PDO::FETCH_ASSOC);
$hasData = false;
$firstVaccineDate = null;
$totalVaccines = count($vaccines);  // Total vaccine guides (rows)

if ($vaccines) {
    $hasData = true;
    // Get the first vaccine's date
    $firstVaccineDate = new DateTime($vaccines[0]['date']);
}

// Render the vaccine guides
if ($vaccines) {
    foreach ($vaccines as $vaccine) {
        $vaccineDate = new DateTime($vaccine['date']);
        $interval = $referenceDate->diff($vaccineDate);
        $daysDifference = (int)$interval->format('%r%a');

        echo '<div class="care-task" data-day="' . $daysDifference . '" ';
        echo 'data-bs-toggle="popover" data-bs-title="' . htmlspecialchars($vaccine['vaccine_name']) . '" ';
        echo 'data-bs-content="' . htmlspecialchars($vaccine['details']) . '">';
        echo htmlspecialchars($vaccine['vaccine_name']);
        echo ' <button type="button" class="plus"><i class="bx bx-comment-add"></i></button>';
        echo '<br>('. htmlentities($vaccineDate->format('Y-m-d')).')<br><i class="bx bxs-chevrons-down"></i></div>';
    }
} else {
    echo '<h1 class="text-center text-secondary">Empty</h1>';
}

// Pass the vaccines array, first vaccine date, and total vaccines count to JavaScript
echo '<script>';
echo 'const vaccines = ' . json_encode($vaccines) . ';';  // Encode the vaccines array into a JSON object
if ($firstVaccineDate) {
    echo 'const firstVaccineDate = "' . $firstVaccineDate->format('Y-m-d') . '";';
}
echo 'const totalVaccines = ' . $totalVaccines . ';';
echo '</script>';
?>


</div>

<?php if ($hasData): ?>
    <input type="range" min="0" max="40" value="0" id="pigCareSlider" disabled>
<?php endif; ?>

    
</div>




<div class="table-data bred">
            <div class="card mb-3">
  <div class="row g-0">
    <div class="col-md-4" >
    <div class="image-container">
    <img src="img/<?php echo $pig['img']; ?>" class="img-fluid rounded-start" alt="pig">
            <div class="image-overlay"></div> 
        </div>
    </div>
    <div class="col-md-8">
      <div class="card-body">
      <div class="pigsts">
    <div class="left-section"> <!-- A container to group the title and the status text -->
        <h2 class="card-title"><?php echo $pig['piglet_name']; ?></h2>
    </div>
    <div class="right-section"> <!-- A container for the trash icon -->
    <p class="card-text <?php echo $pig['status']; ?>"> <?php echo $pig['status']; ?></p>
    </div>
</div>

           

        	<?php 
   echo '<p class="card-text"><span>Gender:</span> ' . htmlentities($pig['gender']) . '</p>';
        echo '<p class="card-text"><span>Detailst:</span> ' .  htmlentities($pig['details']) . '</p>';
        echo '<p class="card-text"><span>Date Started:</span> ' . htmlentities($formatteddate) .'</p>';
        echo  '<br>';
    
?>
<div class="d-flex gap-2">
  <button type="button" 
          class="btn btn-md btn-success flex-fill" 
          title="Cull Sow" 
          data-bs-toggle="modal" 
          data-bs-target="#cullingModal-<?php echo $pig['id']; ?>" 
          data-pigid="<?php echo $pig['id']; ?>" 
          <?= ($pig['status'] == 'Recovered') ? 'disabled' : ''; ?>>
    Move To Healthy
  </button>

  <button type="button" 
          class="btn btn-md btn-primary flex-fill" 
          title="Update Pig" 
          data-bs-toggle="modal" 
          data-bs-target="#confirmModal" 
          data-pigid="<?php echo $pig['id']; ?>">
    Update
  </button>
</div>

<!-- move to culling  Modal -->
<div class="modal fade" id="cullingModal-<?php echo $pig['id']; ?>" tabindex="-1"  aria-labelledby="cullingModalLabel-<?php echo $pig['id']; ?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                  
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        
                    </button>
                </div>
                <div class="modal-body">
                <div class="text-center">
                    <img src="img/culling.svg" alt="Profile Picture" width="150px" height="150px">
                    <h3 class="confirm">Are you sure you want to move this pig Healthy?</h3>
                  </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger culling-confirm-btn" data-id="<?php echo $pig['id']; ?>">Confirm</button>

                </div>
            </div>
        </div>
    </div>

<!-- move to culling Modal -->

</div>

    </div>
    <!-- update pig Modal -->

    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header custom-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Update Status</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        
      <form id="myForm" action="<?=$_SERVER['REQUEST_URI']?>" method="POST" enctype="multipart/form-data">
      <div class="row">
      <input type="hidden" name="id" class="form-control" placeholder="Pig name" aria-label="First name" value="<?php echo $pig['id']; ?>"/>
  <div class="col">
  <label for="fullname">Date</label>
    <input type="date"  id="date"name="date" class="form-control" autocomplete="given-name" placeholder="Pig name" aria-label="First name" required/>
  </div>

</div>
<br>
<div class="row">
        <div class="col">
        <label for="fullname">Status</label>
  <select name="status" id="statusSelect" class="form-select form-select-sm" aria-label="weightclass">
  <option value="<?php echo $pig['status'];?>" selected><?php echo $pig['status'];?></option>
  <option value="Treatment">Under Treatment</option>
  <option value="Deceased">Deceased</option>
</select>
        </div>
</div>
<br>
      <div class="modal-footer">
      <button type="button" class="btn btn-secondary" id="cancelBtn" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="update" class="btn btn-primary" id="confirmBtn">Confirm</button>
      </div>
      </form>
    </div>
  </div>
                </div>

				</div>    
<!-- update pig Modal -->
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
                  $query3->bindParam(':piglets_id',$pigletId, PDO::PARAM_INT);
                  $query3->execute();
                  $results = $query3->fetchAll(PDO::FETCH_OBJ);
                  $totalRows = count($results);
          
                  ?>
				<div class="left">
					<h1>Records Lists</h1>
                    <button type="button" title="Click to Add" data-bs-toggle="modal" data-bs-target="#confirmModals" class="openModalBtn">
  <i class='bx bx-plus-circle'></i> Add New
</button>
				</div>
                <table id="myTable">
						<thead>
							<tr>
                                <th  class="text-center">ID</th>
                                <th  class="text-center">Vaccined By</th>
                                <th  class="text-center">Vaccined Name</th>
                                <th  class="text-center">Date Vaccinated</th>
                                <th  class="text-center" >Action</th>
                                
							</tr>
						</thead>
                        
						<tbody>
                   
                     
                  <?php 
                          foreach($results as $result){
                            $date = new DateTime($result->date_vaccinated);
                            $formatteddates = $date->format('F j, Y');
                        
                          
                          ?>
                              
                              <tr>
	<td class="text-center">
	<p><?php echo htmlentities($result->id); ?></p>
		</td>
        <td class="text-center"><?php echo htmlentities($result->vaccined_by); ?></td>
	<td class="text-center"><?php echo htmlentities($result->vaccine_name); ?></td>
	<td><?php echo htmlentities($formatteddates); ?></td>

 
    <!-- Button trigger modal -->
    <td class="action text-center">
    <button type="button" class="btn deleterecord" title="Delete Record" data-bs-toggle="modal" data-bs-target="#deleteModalrecord-<?php echo htmlentities($result->id); ?>" data-id="<?php echo htmlentities($result->id); ?>" data-breeder-id="<?php echo htmlentities($result->breeder_id); ?>"> <i class='bx bx-trash'></i></button>
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
                    <img src="img/deletepig.svg" alt="Profile Picture" width="150px" height="150px">
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

					<!-- add pig Modal -->
<div class="modal fade" id="confirmModals" tabindex="-1"  aria-hidden="true" tabindex="-1" aria-labelledby="exampleModalLabels" aria-hidden="true">
<div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header custom-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Add New Record</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <form  action="add_vaccine.php?id=<?php echo $pigletId; ?>" method="POST">
      <div class="col">
      <div class="row">
  <!-- <label for="name">Vaccine Name</label>
    <input type="text" name="vaccine" id="vaccine" class="form-control" placeholder="Vaccine Name" aria-label="vaccine" autocomplete="given-name">
  
</div> -->
  <div class="row">
  <label for="name">Vaccine Name</label>
    <input type="text" name="vaccine" id="vaccine" class="form-control" placeholder="Vaccine Name" aria-label="vaccine" autocomplete="given-name">
  
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
		<!-- MAIN -->
			<!-- FOOTER -->
		<?php include('includes/footer.php');?>
		<!-- FOOTER -->
	</section>
	<!-- CONTENT -->
	
<script>
$(document).on('click', '.culling-btn', function() {
    cullingPigId = $(this).data('id');
    $('#cullingModal-' + cullingPigId).modal('show');
});

$(document).on('click', '.culling-confirm-btn', function() {
    const pigId = $(this).data('id');
    cullingpig(pigId);
});

function cullingpig(id) {
    $.ajax({
        url: 'move_tohealthy.php',
        type: 'POST',
        data: { healthy_id: id },
        success: function(response) {
            $('#cullingModal-' + id).modal('hide');
            location.reload();
        },
        error: function() {
            alert('An error occurred while trying to move the sow into culling.');
        }
    });
}



let spouseAddChildrenButton = document.getElementById('price-add');
let spouseChildren = [];

spouseAddChildrenButton.addEventListener('click', () => {
    let title = document.getElementById("title");
    let entereddate = document.getElementById("vaccince_date");
    let details = document.getElementById("details");

    if (title.value === "" || entereddate.value === "") {
        alert("Please input a title and a date.");
        return;
    }

    let newChildDetail = {
        "title": title.value,
        "vaccince_date": entereddate.value,
        "details": details.value
    };

    spouseChildren.push(newChildDetail);

    let spouseChildrenTable = document.getElementById('spouse-children');
    let childrenRow = document.createElement('tr');

    let td1 = document.createElement('td');
    let td2 = document.createElement('td');
    let td3 = document.createElement('td');
    let td4 = document.createElement('td');

    td1.innerText = newChildDetail.title;
    td2.innerText = newChildDetail.details;
    td3.innerText = newChildDetail.vaccince_date;

    [td1, td2, td3, td4].forEach(td => td.classList.add("text-center"));

    let removeButton = document.createElement('button');
    removeButton.innerText = "Remove";
    removeButton.classList.add("btn", "btn-dark");
    removeButton.addEventListener("click", () => {
        childrenRow.remove();
        spouseChildren = spouseChildren.filter(child =>
            !(child.title === newChildDetail.title &&
              child.details === newChildDetail.details &&
              child.vaccince_date === newChildDetail.vaccince_date)
        );
        document.getElementById('seat-prices').value = JSON.stringify(spouseChildren);
    });

    td4.appendChild(removeButton);
    childrenRow.appendChild(td1);
    childrenRow.appendChild(td2);
    childrenRow.appendChild(td3);
    childrenRow.appendChild(td4);
    spouseChildrenTable.appendChild(childrenRow);

    document.getElementById('seat-prices').value = JSON.stringify(spouseChildren);

    title.value = '';
    details.value = '';
    entereddate.value = '';
});



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
        url: 'delete.php',
        type: 'POST',
        data: { vaccinerecordid: id, breeder_id: breederId }, 
        success: function(response) {
            $('#deleteModalrecord-' + id).modal('hide');
             location.reload();
            
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
            alert('An error occurred while trying to delete the record.');
        }
    });
}





	$(document).ready(function() {
    $("#searchInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();

        $("#carList li").filter(function() {
            var combinedData = $(this).data('make') + " " + $(this).data('model') + " " + $(this).data('year');

            $(this).toggle(combinedData.toLowerCase().indexOf(value) > -1);
        });
    });
	$('[data-bs-toggle="popover"]').popover();


});

document.addEventListener('DOMContentLoaded', function() {
    if (typeof firstVaccineDate === 'undefined' || typeof totalVaccines === 'undefined' || typeof vaccines === 'undefined') {
        console.error("Required variables are not defined.");
        return;  
    }

    const referenceDate = new Date('2025-05-01');
    const currentDate = new Date();
    currentDate.setHours(0, 0, 0, 0);

    const firstVaccineDateObj = new Date(firstVaccineDate);

    const daysDifference = Math.floor((currentDate - firstVaccineDateObj) / (1000 * 60 * 60 * 24));

    const slider = document.getElementById('pigCareSlider');
    slider.disabled = false;

    slider.max = 40;

    let validVaccinesCount = 0;

    for (let i = 0; i < totalVaccines; i++) {
        const vaccineDate = new Date(vaccines[i].date);
        if (vaccineDate <= currentDate) {
            validVaccinesCount++;
        }
    }

    const valuePerGuide = 40 / totalVaccines; 
    const sliderValue = validVaccinesCount * valuePerGuide; 

    slider.value = sliderValue;

    if (firstVaccineDateObj <= currentDate) {
        slider.value = sliderValue;
    } else {
        slider.value = 0; 
    }

    slider.dispatchEvent(new Event('input'));
    slider.disabled = true;
});





document.getElementById('pigCareSlider').addEventListener('input', function() {
    const day = parseInt(this.value, 10);
    const tasks = document.querySelectorAll('.care-task');

    let activeCount = 0;  

    tasks.forEach(task => {
        const taskDay = parseInt(task.getAttribute('data-day'), 10);

        if (taskDay <= day && activeCount < 2) {
            task.classList.add('active-task');
            activeCount++;  
        } else {
            task.classList.remove('active-task');
        }
    });
});





$(document).on("click", ".updateModalBtn", function() {
    var feedId = $(this).attr("data-feedIds"); 
  
  $.ajax({
    url: 'getrecords.php',  
    type: 'POST',
    data: { feedIds: feedId},  
    dataType: 'json', 
    success: function(response) {
      console.log(response);
      $("#updateForms").html(`
      <div class="row">
      <div class="col">
        
        <div class="row">
          <input type="hidden" name="id"  class="form-control" value="${response.id}">
      </div>
        <br>
        <div class="row">
        <label for="farrow">Farrowed Date</label>
        <input type="date" name="farrowed" id="farrow" class="form-control"  autocomplete="given-name" value="${response.date_farrowed}">
        </div>
        <br>
        <div class="row">
        <label for="wean">Weaned Date</label>
        <input type="date" name="weaned" id="wean" class="form-control"  autocomplete="given-name" value="${response.weaned_date}">
        </div>
        <br>
        <div class="row">
        <label for="tot">Total Piglets</label>
        <input type="number" name="total" id="tot" class="form-control"  autocomplete="given-name" value="${response.total_piglets}">
        </div>
        <br>
        <div class="row">
        <label for="survive">Survived</label>
        <input type="number" name="survive" id="survive" class="form-control"  autocomplete="given-name" value="${response.survived}">
        </div>
      </div>

    
        
      `);
    },
    error: function(jqXHR, textStatus, errorThrown) {
      console.log(textStatus, errorThrown); 
    }
  });

});











</script>
	<script src="script.js"></script>
</body>
</html>
<?php } ?>