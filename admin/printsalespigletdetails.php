<?php
include('includes/config.php');
function numberToWords($number) {
    $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
    $words = $f->format($number);
    return ucfirst($words) . " pesos only";
}

$id = $_GET['id'] ?? 0;

$stmt = $dbh->prepare("SELECT tblusers.id, 
 IF(tblorders.cust_id = 0, tblorders.walkin_customer, tblusers.FullName) AS FullName,
    IF(tblorders.cust_id = 0, 'None', IF(tblusers.ContactNo = '' OR tblusers.ContactNo IS NULL, 'None', tblusers.ContactNo)) AS ContactNo,
  tblorders.id as order_id,
   tblorders.orderdate, 
   tblorders.deliverydate, 
   tblorders.orderstatus,
    tblorders.total_amount, tblorders.mop, tblorders.cust_id 
FROM  tblorders
LEFT JOIN  tblusers ON tblusers.id = tblorders.cust_id 
WHERE tblorders.id = :id");

$stmt->execute([':id' => $id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if ($order) {
    $buyerName = htmlspecialchars($order['FullName']);
    $amountNumeric = $order['total_amount'];
    $amount = "₱" . number_format($amountNumeric, 2);
    $amountWords = numberToWords($amountNumeric); 
    $paymentType = htmlspecialchars($order['mop']);
    $contactNumber = htmlspecialchars($order['ContactNo']);
    $date = date("F j, Y", strtotime($order['orderdate']));
} else {
    $buyerName = "N/A";
    $amount = "₱0.00";
    $paymentType = "N/A";
    $contactNumber = "N/A";
    $date = date("F j, Y");
}

$companyName = "Ronalds Baboyan";
$companyLocation = "Brgy. San Isidro, San Fernando Cebu";
$companyContact = "0912-345-6789";
$logoPath = "img/logos.jpeg";


?>


    <!DOCTYPE html>
    <html>
    <head>
        <title>Receipt - Piglet Sale</title>
        <style>
            @media print {
                @page {
                    size: landscape;
                    margin: 1cm;
                }

                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                }

                .receipt-container {
                    width: 100%;
                    height: 30vh; /* top 1/3 of page */
                    display: flex;
                    flex-direction: column;
                    justify-content: start;
                    padding: 20px;
                }

                .header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                
                    padding-bottom: 10px;
                    margin:20px;
                }

                .header .logo img {
                    max-height: 60px;
                }

                .header .title {
                    font-size: 24px;
                    font-weight: bold;
                }

                .header .company-info {
                    text-align: right;
                    font-size: 14px;
                }

                .body {
                    margin-top: 20px;
                    font-size: 16px;
                }
    .memo{
    display:flex; 
    align-items:center;
    justify-content:space-between;
    }
    .memoc{
    display:flex; 
    align-items:center;
    justify-content:flex-start;
    }
    .piglet-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    font-size: 14px;
}

.piglet-table th,
.piglet-table td {
    border: 1px solid #000;
    padding: 6px 10px;
    text-align: left;
}

.piglet-table th {
    background-color: #f2f2f2;
}

                .body .info-row {
                    margin-bottom: 16pxpx;
                }
               
                .memob {
  display: flex;
  justify-content: flex-end;
  margin-top: 50px;
  gap:80px;
}

.memob .sig-box {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.memob .sig-box p {
  margin: 5px 0 0 0;
  font-size: 14px;
}

.memob .sig-box::before {
  content: "";
  display: block;
  width: 200px;
  border-bottom: 1px solid black;
  margin-bottom: 4px;
}


            }
        </style>
    </head>
    <body onload="window.print()">

    <div class="receipt-container">
        <div class="header">
            <div class="logo">
        
                <img src="<?= $logoPath ?>" alt="Company Logo">
            </div>
            <div class="title">RECEIPT</div>
            <div class="company-info">
                <strong><?= $companyName ?></strong><br>
                <?= $companyLocation ?><br>
                <?= $companyContact ?><br>
                Date: <?= $date ?>
            </div>
        </div>

        <div class="body">
            <div class="memo">
                <p>Memo No  ____________________________________________</p>
                <p>Data ____________________________________</p>
            </div>
            <table class="piglet-table">
    <thead>
    
        <tr>
            <th>Name</th>
            <th>Amount Pay</th>
            <th>KG</th>
            <th>Classification</th>
        </tr>
    </thead>
    <tbody>
    <?php 
        $stmt1 = $dbh->prepare("SELECT * FROM tblorderdetails 
        WHERE order_id = :id");
        $stmt1->execute([':id' => $id]);
        $orders1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
                ?>
        <tr>
            <?php
            {
foreach($orders1 as $orderdetails )
            ?>
            <td><?=$orderdetails['name']?></td>
            <td><?=$orderdetails['price']?></td>
            <td><?=$orderdetails['weight'] ?></td>
            <td> 
            <?= $orderdetails['cull'] == 1 ?
                'Cull':($orderdetails['piglet'] == 1 ? 'Piglet' : 'Pig')
 ?></td>
        </tr>
  <?php  } ?>
        <!-- You can add more rows dynamically here -->
    </tbody>
</table>

<br>
            <div class="info-row"><strong>Recieve with thanks from: </strong> <?= $buyerName ?></div>
            <div class="info-row"><strong>Amount of pay: </strong> <i><?= $amountWords ?><i></div>
            <div class="memoc"><strong>Note Of Pay: </strong> <p><i><?= $paymentType ?></i></p> </div>
                <div class="memo">
                <p>For the purpose of: ____________________________________________</p>
                <p>Contact No: __<?=$contactNumber ?> ____________________</p>
            </div>
                <div class="info-row"><strong>Pay:</strong><p style="padding:3px;
                        border:2px solid #333;width:90px;text-align:center;"><?= $amount ?></p></div>
            <div class="memob">
  <div class="sig-box">
    <p>Received By</p>
  </div>
  <div class="sig-box">
    <p>Authorized Signature</p>
  </div>
</div>

        </div>
    </div>

    </body>
    </html>
