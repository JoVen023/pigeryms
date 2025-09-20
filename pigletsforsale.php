<?php
include('includes/config.php');
error_reporting(0);

$query = "SELECT 
    tg.id AS id,
    tg.sowname AS name,
    tg.weaneddate AS farrowed_Date,
    tg.img AS group_image,
    SUM(tfsd.price) AS price,
    COUNT(tfsd.id) AS total_piglets,
    CASE 
        WHEN COUNT(tfsd.id) = 0 THEN 'SOLD OUT'
        ELSE 'AVAILABLE'
    END AS status
FROM tblgrowingphase tg
LEFT JOIN piglets p ON p.growinphase_id = tg.id
LEFT JOIN tblpiglet_for_sale_details tfsd ON tfsd.piglet_id = p.id AND tfsd.status = 'AVAILABLE'
WHERE tfsd.piglet_id = p.id
GROUP BY tg.id";
$stmt = $dbh->query($query);
$pigs = $stmt->fetchAll(PDO::FETCH_ASSOC);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['weight'])) {
    $selectedWeight = $_POST['piglet_weight'];
    $filteredPigs = [];

    if (!empty($selectedWeight)) {
        foreach ($pigs as $pig) {
            if ($pig['piglet_weight'] == $selectedWeight) {
                $filteredPigs[] = $pig;
            }
        }
    } else {
        $filteredPigs = $pigs;
    }

    // Generate the HTML markup for the filtered pigs
    $html = '';
    foreach ($filteredPigs as $pig) {
        $pricePerKg = (float) $pig['price'];
        $weightClass = $pig['piglet_weight'];
        
        preg_match_all('/\d+/', $weightClass, $matches);
        $minWeight = isset($matches[0][0]) ? (int) $matches[0][0] : 0;
        $maxWeight = isset($matches[0][1]) ? (int) $matches[0][1] : 0;
      
        $minPrice = $minWeight * $pricePerKg;
      $maxPrice = $maxWeight * $pricePerKg;
      $html .= '<li class="pigcard">
      <h3 class="weightclass">' . $pig['piglet_weight'] . '</h3>
      <img src="admin/img/img_piglets_for_sale' . $pig['img'] . '" alt="' . $pig['name'] . '">
      <p class="name">' . $pig['name'] . '</p>
      <p class="price">Total Piglets Available:&#8369;' . number_format($pig['total_piglets']) . '/kg</p>
      <p class="price">Total Price: &#8369;' . number_format($pig['price']) . '</p>
      <a href="pig_details.php?id=' . $pig['id'] . '" class="view-btn">View</a>
  </li>';

    }
    if (empty($filteredPigs)) {
        $html .= '<h1 class="empty">Out Of Stock</h1>';
    }

    echo $html;
    exit;
}
?>

<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="keywords" content="Pigs">
    <meta name="description" content="Pigs For Sale">
    <title>SHOP</title>
    <!--Bootstrap -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
    <!--Custome Style -->
    <link rel="stylesheet" href="assets/css/style.css" type="text/css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <!--bootstrap-slider -->
    <!--FontAwesome Font Style -->
    <link href="assets/css/font-awesome.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="assets/images/logos.jpeg">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet">
</head>

<body style="background-color:#eff3f0;">

    <!--Header-->
    <?php include('includes/header.php'); ?>
    <!-- /Header -->

    <!--Listing-->
    <section class="listing-page">
        <div class="header">
            <h2>Available Piglets</h2>
            <form id="sortForm" action="" method="POST">
                <label for="weight">SORT BY</label>
                 <select name="weight" id="weight">
                    <option value="">All</option>
                    <option value="30-40kg">30-40 kg</option>
                    <option value="40-50kg">40-50 kg</option>
                    <option value="50-60kg">50-60 kg</option>
                </select> 
                
            </form>
        </div>
        <div style="clear: both;"></div>

        <ul class="pigs" id="pigsList">
        <?php if(empty($pigs)): ?>
    <h2 class="empty">Out Of Stock</h2>
<?php else: ?>
    <?php foreach ($pigs as $pig):
      $farrowed = $pig['farrowed_Date'];
      $formattedDate = date("F j, Y", strtotime($farrowed));
          $weightClass = $pig['piglet_weight'];
          preg_match_all('/\d+/', $weightClass, $matches);
          $minWeight = isset($matches[0][0]) ? (int) $matches[0][0] : 0;
          $maxWeight = isset($matches[0][1]) ? (int) $matches[0][1] : 0;
          $minPrice = $minWeight * $pricePerKg;
        $maxPrice = $maxWeight * $pricePerKg;
        
        ?>
        <li class="pigcard" >
            <h3 class="weightpigletclass"><?php echo $pig['status']; ?></h3>
            <img src="admin/img/<?php echo $pig['group_image']; ?>" alt="<?php echo $pig['name']; ?>">
            <p class="name"><?php echo $pig['name']; ?></p>
            <div class="row">
            <p class="price">Total Price: &#8369;<?php echo $pig['price']; ?></p>
            <p class="price">Piglets Available: <?php echo $pig['total_piglets']; ?></p>
            <p class="price">Date Weaned: <?php echo $formattedDate; ?></p>
            </div>
           
            <a href="piglet_details.php?id=<?php echo $pig['id']; ?>" class="view-btn">View</a>
        </li>
    <?php endforeach; ?>
<?php endif; ?>
        </ul>

    </section>
    <!-- /Listing -->
   
    
    <!--Footer -->
    <?php include('includes/footerhome.php'); ?>
    <!-- /Footer -->

    <!--Back to top-->
    <div id="back-top" class="back-top"> <a href="#top"><i class="fa fa-angle-up" aria-hidden="true"></i> </a> </div>
    <!--/Back to top-->




<!-- Scripts -->
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/interface.js"></script>
<!--Switcher-->

<!--bootstrap-slider-JS-->
<script src="assets/js/bootstrap-slider.min.js"></script>
<!--Slider-JS-->
<script src="assets/js/owl.carousel.min.js"></script>
<script>
    $(document).ready(function() {
        // Submit the form when weight is selected
        $('#weight').change(function() {
            $('#sortForm').submit();
        });

        // Handle form submission
        $('#sortForm').submit(function(e) {
            e.preventDefault();
            var weight = $('#weight').val();

            $.ajax({
                type: 'POST',
                url: window.location.href,
                data: { weight: weight },
                success: function(response){
                    $('#pigsList').html(response);
                }
            });
        });
    });
    $(document).ready(function() {
  $('#weight').focus();
});
</script>
</body>

</html>
