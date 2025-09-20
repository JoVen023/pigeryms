<?php 
$today = (new DateTime())->format('Y-m-d');
$currentDates = date('Y-m-d'); 

$querynotif = $dbh->prepare("SELECT 
  id,
  sow_id,
  sacks,
  sowname,
  pigs,
  mortality,
  weaneddate,
  img,
  status,
  CASE 
    WHEN status = 'piggybloom' THEN piggybloom
    WHEN status = 'prestarter' THEN prestarter
    WHEN status = 'starter' THEN starter
    WHEN status = 'grower' THEN grower
    WHEN status = 'finisher' THEN finisher
    ELSE NULL
  END AS status_date
FROM tblgrowingphase
WHERE (
  (status = 'piggybloom' AND piggybloom > NOW()) OR
  (status = 'prestarter' AND prestarter > NOW()) OR
  (status = 'starter' AND starter > NOW()) OR
  (status = 'grower' AND grower > NOW()) OR
  (status = 'finisher' AND finisher > NOW())  
) order by status_date
");
$querynotif->execute();
$notif=$querynotif->fetchall(PDO::FETCH_OBJ);


$stmttodo = $dbh->prepare("SELECT *, tblpigbreeders.name
                       FROM tbltodo 
                      LEFT JOIN tblpigbreeders ON tbltodo.sow_id = tblpigbreeders.id
                       WHERE tbltodo.time >= :currentDate1
                       ORDER BY ABS(DATEDIFF(tbltodo.time, :currentDate1)) ASC");
$stmttodo->bindParam(':currentDate1', $currentDates, PDO::PARAM_STR);
$stmttodo->execute();
$todoo=$stmttodo->fetchall(PDO::FETCH_OBJ);



$stmttodovaccine = $dbh->prepare("SELECT *,vg.piglet_id as id, p.name
                       FROM vaccines_guide  vg
                      LEFT JOIN piglets p ON vg.piglet_id = p.id
                       WHERE vg.date >= :currentDate1
                       ORDER BY ABS(DATEDIFF(vg.date, :currentDate1)) ASC");
$stmttodovaccine->bindParam(':currentDate1', $currentDates, PDO::PARAM_STR);
$stmttodovaccine->execute();
$todoovaccine=$stmttodovaccine->fetchall(PDO::FETCH_OBJ);



$notifContent = "<div class='list-group' style='max-height: 300px; overflow-y: auto; width: 300px;'>";



$todayCount = 0;

foreach ($notif as $not) {
    $status = $not->status;
    $date = new DateTime($not->status_date);
    $formattedDate = $date->format('F j, Y');
    $statusDateOnly = $date->format('Y-m-d');
    
    $sowname = htmlentities($not->sowname);
    $id = htmlentities($not->id);

    if ($statusDateOnly === $today) {
        $todayCount++;
    }
    $isToday = ($statusDateOnly === $today);
    $class = $isToday ? 'bg-info' : 'bg-none';

    $notifContent .= "<a href='growingphasedetails.php?id={$id}' class='list-group-item list-group-item-action {$class}'>";
    $notifContent .= "Proceed to <strong>{$status}</strong> on <em>{$formattedDate}</em> — {$sowname}";
    $notifContent .= "</a>";
}

foreach ($todoo as $to) {
  $statustodo = $to->details;
  $datetodo = new DateTime($to->time);
  $formattedDatetodo = $datetodo->format('F j, Y');
  $statusDateOnlytodo = $datetodo->format('Y-m-d');
  
  $sownametodo = htmlentities($to->name ?? '');
  $idtodo = htmlentities($to->id ?? '');
  
  if ($statusDateOnlytodo === $today) {
      $todayCount++;
  }
  $isTodaytodo = ($statusDateOnlytodo === $today);
  $classtodo = $isTodaytodo ? 'bg-info' : 'bg-none';

  $notifContent .= "<a href='breederdetails.php?id={$idtodo}' class='list-group-item list-group-item-action {$classtodo}'>";
  if($statustodo == 'Farrowing'){
    $notifContent .= "{$sownametodo} is on Stage <strong>{$statustodo}</strong>.Scheduled for birthing on or after <em>{$formattedDatetodo}</em>";
  }
  // elseif($statustodo = 'Lactating'){
  //   $notifContent .= "{$sownametodo} is on Stage <strong>{$statustodo}</strong>.Scheduled for separating piglets on or after <em>{$formattedDatetodo}</em>";
  // }
  elseif(in_array($statustodo, ['Kapon' , 'Vitamins' , 'Injecting Iron', 'Weaning'] ) ){
    $notifContent .= "Proceed <strong>{$statustodo}</strong> on {$sownametodo} piglets on <em>{$formattedDatetodo}</em>";
  }
  else{
    $notifContent .= "{$sownametodo} is on Stage <strong>{$statustodo}</strong> at <em>{$formattedDatetodo}</em>";
  }
  $notifContent .= "</a>";
}



foreach ($todoovaccine as $tovaccines) {
  $statustodos = $tovaccines->details;
  $datetodovaccine = new DateTime($tovaccines->date);
  $formattedDatetodovaccine = $datetodovaccine->format('F j, Y');
  $statusDateOnlytodovaccine = $datetodovaccine->format('Y-m-d');
  
  $sownametodovaccine = htmlentities($tovaccines->name ?? '');
  $idtodos = htmlentities($tovaccines->id ?? '');
  
  if ($statusDateOnlytodovaccine === $today) {
      $todayCount++;
  }
  $isTodaytodos = ($statusDateOnlytodovaccine === $today);
  $classtodos = $isTodaytodos ? 'bg-info' : 'bg-none';

  $notifContent .= "<a href='unhealthypigletdetails.php?id={$idtodos}' class='list-group-item list-group-item-action {$classtodos}'>";
    $notifContent .= "Vaccine  {$statustodos} for <strong>{$sownametodovaccine}</strong> piglet on <em>{$formattedDatetodovaccine}</em>";
  $notifContent .= "</a>";
}





$notifContent .= "</div>";


if (!isset($_SESSION['dark_mode'])) {
		$_SESSION['dark_mode'] = false;
	}
    $sidebarname = isset($_SESSION['sidebarname']) ? $_SESSION['sidebarname'] : '';
	 ?>

	 <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css"/>
	 
<nav>

    <div class="left-side">
        <i class='bx bx-menu'></i>
        <a href="#" class="nav-link"><?php echo $sidebarname ?></a>
    </div>
    <div class="right-side gap-3">

   
        <form action="includes/toggle_dark_mode.php" method="POST" id="dark-mode-form">
            <input type="checkbox" id="switch-mode" name="dark-mode" <?= $_SESSION['dark_mode'] ? 'checked' : '' ?> hidden>
            <label for="switch-mode" class="switch-mode">
            <i class='bx bxs-sun'></i>
            <i class='bx bxs-moon' ></i>
            </label>
            
        </form>
      <!-- Notification Button -->
      <button 
  type="button" 
  class="btn bg-transparent border-0 position-relative" 
  id="notificationButton"
  data-bs-toggle="popover"
  data-bs-html="true"
  title="Notifications"
  data-bs-placement="bottom"
  data-bs-custom-class="custom-popover"
  data-bs-content="<?php echo htmlspecialchars($notifContent, ENT_QUOTES, 'UTF-8'); ?>">
  
  <span class="position-relative d-inline-block">
    <i class='bx bxs-bell fs-4'></i>
    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger <?php echo ($todayCount > 0) ? '' : 'd-none'; ?>">
      <?php echo $todayCount; ?>
    </span>
  </span>
</button>




    <!--    
        <a href="#" class="profile">
            <img src="img/user.png" alt="human">
        </a> -->
       
    </div>
</nav>

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

    