<?php
$img = urldecode($_GET['img']);
$pigletname = urldecode($_GET['name']);

$qrImagePath = $img;
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Ronald's Baboyan</title>
<link rel="icon" type="image/x-icon" href="img/logos.jpeg">
    <style>
        body { text-align: center; padding-top: 50px; }
        img { width: 600px; height: 600px; }
    </style>
</head>
<body onload="window.print();">
    <h3>Piglet <?php echo $pigletname; ?> QR Code</h3>
    <img src="<?php echo $qrImagePath; ?>" alt="QR Code">
</body>
</html>
