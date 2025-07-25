<?php
include('includes/config.php');


try{
    if(isset($_POST['pigbreeder'])){
		$pigname=$_POST['name'];
        $pigletsid=$_POST['pigid'];
        
		$month=$_POST['age'];
		$pigs=$_POST['pigs'];
		$farrowed=$_POST['farrowed'];
		$forrowingdate=$_POST['forrowingdate'];
		$forrowingDateTime = new DateTime($forrowingdate);
$forrowingDateTime->add(new DateInterval('P40D'));  
$newDate = $forrowingDateTime->format('Y-m-d'); 

		$breedingdate=$_POST['breedingdate'];
		$breedingdateTime = new DateTime($breedingdate);
		$breedingdateTime->add(new DateInterval('P116D')); 
		$newDates = $breedingdateTime->format('Y-m-d'); 

		$age = $month . " Months";
		$status=$_POST['status'];
	
		
		if ($_FILES['pict']['error'] == UPLOAD_ERR_OK) {
		  $filename =basename($_FILES['pict']['name']);
		
		  $uploadPath = 'img/' . $filename;
		
		  if (move_uploaded_file($_FILES['pict']['tmp_name'], $uploadPath)) {

            $query2 = $dbh->prepare("UPDATE piglets SET status = 'Breeder' , move = 1  WHERE id =:pigletid");
    
            $query2->bindParam(':pigletid', $pigletsid, PDO::PARAM_STR);
       
            
		   
			if ($status == "Farrowing") {
			  $query = $dbh->prepare("INSERT INTO tblpigbreeders (name,age, status,total_farrowed,img,breedingstart,forrowingdate) VALUES (:name,:age,:status,:farrowed,:pict,:breedingdate,:forrowingdate)");
		
			  $query->bindParam(':name', $pigname, PDO::PARAM_STR);
			  $query->bindParam(':age', $age, PDO::PARAM_STR);
			  $query->bindParam(':status', $status, PDO::PARAM_STR);
			  $query->bindParam(':farrowed', $farrowed, PDO::PARAM_STR);
			  $query->bindParam(':breedingdate', $breedingdate, PDO::PARAM_STR);
			  $query->bindParam(':forrowingdate', $newDates, PDO::PARAM_STR);
			  $query->bindParam(':pict', $filename, PDO::PARAM_STR);

			}
			elseif ($status == "Gestating") {
				 $query = $dbh->prepare("INSERT INTO tblpigbreeders (name,age, status,img,forrowingdate,piglets,gestateends) VALUES (:name,:age,:status,:pict,:forrowingdate,:pigs,:gestateend)");
		
				 $query->bindParam(':name', $pigname, PDO::PARAM_STR);
				 $query->bindParam(':age', $age, PDO::PARAM_STR);
				 $query->bindParam(':status', $status, PDO::PARAM_STR);
				 $query->bindParam(':pigs', $pigs, PDO::PARAM_INT);
				 $query->bindParam(':forrowingdate', $forrowingdate, PDO::PARAM_STR);
				 $query->bindParam(':gestateend', $newDate, PDO::PARAM_STR);
				 $query->bindParam(':pict', $filename, PDO::PARAM_STR);

			}
			else{
				$query = $dbh->prepare("INSERT INTO tblpigbreeders (name,age, status,img) VALUES (:name,:age,:status,:pict)");
		
				$query->bindParam(':name', $pigname, PDO::PARAM_STR);
				$query->bindParam(':age', $age, PDO::PARAM_STR);
				$query->bindParam(':status', $status, PDO::PARAM_STR);
				$query->bindParam(':pict', $filename, PDO::PARAM_STR);
			}
			  try {
				  $query->execute();
                  $query2->execute();
				  if ($query) {
                    echo "<script type='text/javascript'>alert('Added Successfully'); window.location.href = 'pigbreeders.php';</script>";
				  } else {
					$err = "Please Try Again Or Try Later";
				  }
			  } catch (PDOException $ex) {
				  echo $ex->getMessage();
				  exit;
			  }
		  } else {
			  echo "Could not move the uploaded file";
		  }
		} else {
		  echo "File upload error";
		}
		
		  }

}catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}