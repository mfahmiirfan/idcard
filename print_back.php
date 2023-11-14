<?php
if (isset($_POST['company_code']) && isset($_POST['number_copies'])) {
    $companyCode = $_POST['company_code'];
    $numberCopies = $_POST['number_copies'];
    switch ($companyCode) {
        case "J2":
            $companyName = "PT. Shoetown Ligung Indonesia";
            break;
        case "JCS":
            $companyName = "PT. Shoetown Kasokandel Indonesia";
            break;
        case "IA":
            $companyName = "PT. Adis Dimension Footwear";
            break;
    }
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
            /* background-color: white; */
            /* box-shadow: 0 0 10px rgba(0, 0, 0, 0.3); */
            display: inline-block;

            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
            font-size: 5.3pt;
            padding-top: 8px;
            position: relative;

        }

        .sti-card::before {
            content: "";
            background-image: url('img/cropped-cropped-LOGO-STG-1.png');
            background-position: center center;
            background-repeat: no-repeat;
            background-size: contain;
            background-size: 80%;
            position: absolute;
            top: 0px;
            right: 0px;
            bottom: 0px;
            left: 0px;
            opacity: 0.2;
        }

        .sti-card .row:not(.row-lists) {
            text-align: center;
        }

        .row-lists {
            border: solid 1px;
            font-weight: bold;
        }

        .row-lists>ol {
            padding: 0 20px;
            margin-top: 4px;
            margin-bottom: 0;
        }

        hr.custom {
            border-top: 1px solid black;
            border-bottom: 2px solid black;
            padding: .5px;
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
        if ($numberCopies > 0) {
            for ($i = 0; $i < $numberCopies; $i++) {

        ?>
                <div class="sti-card">
                    <div class="d-flex flex-column">
                        <div class="row">
                            <!-- <span style="color:black;font-family:calibri;font-size:8px;text-align:center;padding:0px;margin-top:0px;"> -->
                            <b>Visi Perusahaan</b> / 愿景<br>
                            <b>Perusahaan Premium kelas dunia /</b><br>
                            成为世界级顶级工厂<br><br>
                            <b>Misi Perusahaan /</b> 使命<br>
                            <b>Menjadi mitra yang andal dan perusahaan yang menguntungkan&nbsp;melalui perubahan yang cepat dan basis sumber yang berkelanjutan yang menghasilkan produk premium/</b> <br>
                            通过革新和可持续性的生产，提供优质产品，成为可靠的合作伙伴和盈利企业
                            <!-- </span> -->
                        </div>
                        <hr class="custom">
                        <div class="row">
                            <!-- <span style="color:black;font-family:calibri;font-size:8px;text-align:center;padding:0px;margin-top:0px;"> -->
                            <b>Visi Safety 愿景</b><br>
                            <b>-Nol Kecelakaan /</b><br>
                            <b>-Nol Cedera /</b><br>
                            <b>-Nol Dampak Lingkungan /</b><br><br>

                            <b>Misi Safety 使命</b><br>
                            <b>Menciptakan lingkungan kerja yang lebih aman dan lebih sehat untuk seluruh karyawan /</b> <br>
                            <b>为所有员工创造更安全、更健康的工作环境</b>
                            <!-- </span> -->
                        </div>
                        <hr class="custom">
                        <div class="row row-lists">
                            <!-- <span style="color:black;font-family:calibri;font-size:9px;text-align:justify;padding:0px;"> -->
                            <ol>
                                <li> Kartu ini berlaku sebagai Kartu Tanda Pengenal di lingkungan Perusahaan.</li>
                                <li> Karyawan Wajib menggunakan ID card saat masuk dan selama di lingkungan perushaan.</li>
                                <li> ID Card ini berlaku bagi karyawan aktif.</li>
                                <li> Dilarang memodifikasi ID Card</li>
                                <li> Apabila ID Card Rusak, Hilang dan karyawan Resign, hubungi :</li>
                            </ol>
                            <center>
                                <!-- <span style="color:black;font-family:calibri;font-size:8px;text-align:center;padding:0px;"> -->
                                HRD<br>
                                <?php echo $companyName; ?><br>
                                (0233-8888487)<br><br>
                                <!-- </span> -->
                            </center>
                            <!-- </span> -->
                        </div>

                        <!-- <img class="sti-pt-logo" src="img/sli-logo.png" alt=""> -->
                        <!-- <div class="sti-pt-name">PT <?php echo strtoupper($row['company_name']); ?></div>
                        <div class="sti-emp-photo" data-pos-index="0" style="background:url('<?php echo isset($row['photo']) ? "backend/uploads/photo$row[photo]" : "img/no-image.png"; ?>') no-repeat center center;background-size: cover;"></div>
                        <div class="sti-emp-info"><span class="sti-emp-name"><?php echo $row['name'] !== '' ? strtoupper($row['name']) : '[Employee Name]'; ?></span><br><span id="stiEmpNIK"><?php echo $row['nik']; ?></span><br><span id="stiEmpCC"><?php echo $row['cost_center'] !== '' ? strtoupper($row['cost_center']) : '[Cost Center]'; ?></span></div>
                        <div class="sti-emp-date-induction"><?php echo $row['induction_date']; ?></div> -->
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
        function printImmediate() {
            window.print();
        }
    </script>
</body>

</html>