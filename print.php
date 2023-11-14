<?php
require_once 'config.php';

if (isset($_POST['idcard_nik'])) {
    // $serverName = "serverName\\sqlexpress"; //serverName\instanceName
    $serverName = DB_HOST; //serverName\instanceName
    $connectionInfo = array("Database" => DB_NAME, "UID" => DB_USER, "PWD" => DB_PASSWORD);
    $conn = sqlsrv_connect($serverName, $connectionInfo);

    if ($conn === false) {

        die(print_r(sqlsrv_errors(), true));
    }

    $ids = $_POST['idcard_nik'];
    array_walk($ids, function (&$item1, $key) {
        $item1 = "'$item1'";
    });
    $stringIds = implode(',', $ids);
    $sql = "SELECT c.name company_name, id.nik, id.name, id.cost_center, id.photo, CONVERT(varchar,id.induction_date,103)induction_date FROM id_card id join company c on c.code=id.company_code where id.nik in($stringIds)";
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }


    $data = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        array_push($data, $row);
    }
    // var_dump($data);exit;

    sqlsrv_free_stmt($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/cropped-cropped-LOGO-STG-1.png" type="image/x-icon">
    <!-- <title>Print ID</title> -->
    <style>
        .sti-card {
            margin: 10px;
            width: 5.4cm;
            height: 8.6cm;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 0 8px;
            background-color: white;
            /* box-shadow: 0 0 10px rgba(0, 0, 0, 0.3); */
            display: inline-block
        }

        .sti-card>div {
            text-align: center;
        }

        .sti-pt-logo {
            width: 47px;
            align-self: center;
            margin-top: .5cm;
        }

        .sti-pt-name {
            font-weight: bold;
            font-size: 11px;
            color: #003cfa;
            font-family: Cambria;
            padding: 5px 0;
            margin-bottom: 8px;
        }

        .sti-emp-photo {
            width: 2.5cm;
            height: 3.5cm;
            background: url('img/no-image.png') no-repeat center center;
            background-size: cover;
            align-self: center;
            margin: auto;
        }

        .sti-emp-info {
            font-weight: bold;
            font-size: 16px;
            margin-top: 2px;
            font-family: Calibri;
            color: black;
            line-height: 1.2;
        }

        .sti-emp-date-induction {
            font-weight: bold;
            font-size: 10px;
            padding-left: 130px;
            margin: 0px;
            text-align: right;
            font-family: Calibri;
            color: black;
            padding-top: 4px;
        }

        .container {
            padding: 0 20px;
        }

        @media print {
            .pagebreak {
                clear: both;
                page-break-after: always;
            }
        }
    </style>
    <style media="print">
        @page {
            size: auto;
            margin: 0mm;
        }

        body {
            -webkit-print-color-adjust: exact;
        }
    </style>
</head>

<body onload="printImmediate()">
    <div class="container">
        <br>
        <?php
        if (isset($data)) {
            foreach ($data as $i => $row) {
                
        ?>
                <div class="sti-card">
                    <div class="d-flex flex-column">
                        <img class="sti-pt-logo" src="img/sli-logo.png" alt="">
                        <div class="sti-pt-name">PT <?php echo strtoupper($row['company_name']); ?></div>
                        <div class="sti-emp-photo" data-pos-index="0" style="background:url('<?php echo isset($row['photo']) ? "backend/uploads/photo$row[photo]" : "img/no-image.png"; ?>') no-repeat center center;background-size: cover;"></div>
                        <div class="sti-emp-info"><span class="sti-emp-name" contenteditable="true"><?php echo $row['name'] !== '' ? strtoupper($row['name']) : '[Employee Name]'; ?></span><br><span id="stiEmpNIK"><?php echo $row['nik']; ?></span><br><span id="stiEmpCC" <?php echo $row['cost_center'] == '' ? 'style="visibility:hidden;"' : ''; ?>><?php echo $row['cost_center'] !== '' ? strtoupper($row['cost_center']) : '[Cost Center]'; ?></span></div>
                        <div class="sti-emp-date-induction"><?php echo $row['induction_date']; ?></div>
                    </div>
                </div>
        <?php
        echo ($i + 1) % 9 == 0 ? '<div class="pagebreak"></div><br>' : "";
            }
        } else {
        }
        ?>
    </div>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script>
        $(document).on('click', '.sti-emp-photo', function() {
            var imgUrl = $(this).css('background-image').replace(/^url\(['"](.+)['"]\)/, '$1');

            var arrPosition = ['center', 'top', 'bottom'];
            var index = $(this).attr('data-pos-index')

            if (index == 0) {
                var newIndex = 1
            } else if (index == 1) {
                var newIndex = 2
            } else if (index == 2) {
                var newIndex = 0
            }
            $(this).css('background', 'url(' + imgUrl + ') no-repeat center ' + arrPosition[newIndex] + '')
                .css('background-size', 'cover')
            $(this).attr('data-pos-index', newIndex)
        })

        function printImmediate() {
            window.print();
        }
    </script>
</body>

</html>