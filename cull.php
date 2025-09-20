<?php
include('includes/config.php');
error_reporting(0);

$query = "SELECT * FROM tblculling WHERE status IS NULL OR status = 'Culling' AND amount > 0";
$stmt = $dbh->query($query);
$pigs = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['weight'])) {
    $selectedWeight = $_POST['weight'];
    $filteredPigs = [];

    if (!empty($selectedWeight)) {
        foreach ($pigs as $pig) {
            if ($pig['weight'] == $selectedWeight) {
                $filteredPigs[] = $pig;
            }
        }
    } else {
        $filteredPigs = $pigs;
    }

    // Generate the HTML markup for the filtered pigs
    foreach ($filteredPigs as $pig) {
        $html .= '<li class="pigcard">
                    <h3 class="weightclass">' . $pig['weight'] . '</h3>
                    <img src="admin/img/' . $pig['img'] . '" alt="' . $pig['name'] . '">
                    <p class="name">' . $pig['name'] . '</p>
                    <p class="price" >&#8369;' . $pig['amount'] . '</p>
                    <a href="cull_details.php?id=' . $pig['id'] .'" class="view-btn">View</a>
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
            <h2>Available Culls</h2>
            <!-- <form id="sortForm" action="" method="POST">
                <label for="weight">SORT BY</label>
                 <select name="weight" id="weight">
                    <option value="">All</option>
                    <option value="30-40kg">30-40 kg</option>
                    <option value="40-50kg">40-50 kg</option>
                    <option value="50-60kg">50-60 kg</option>
                </select> 
                
            </form> -->
        </div>
        <div style="clear: both;"></div>

        <ul class="pigs" id="pigsList">
        <?php if(empty($pigs)): ?>
    <h2 class="empty">Out Of Stock</h2>
<?php else: ?>
    <?php foreach ($pigs as $pig): ?>
        <li class="pigcard">
            <h3 class="weightclass"><?php echo $pig['age']; ?></h3>
            <img src="admin/img/<?php echo $pig['img']; ?>" alt="<?php echo $pig['name']; ?>">
            <p class="name"><?php echo $pig['name']; ?></p>
            <p class="price">&#8369;<?php echo $pig['amount']; ?></p>
            <a href="cull_details.php?id=<?php echo $pig['id']; ?>" class="view-btn">View</a>
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
