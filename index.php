<?php
// Set variables for connection
$servername = "localhost";
$username = "root";
$password = "";
$db = 'demo_csv';

// Create connection
$con = mysqli_connect($servername, $username, $password, $db);

// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$msg = 0;

// Import data code using csv
if (isset($_POST['import'])) {
    $fileName = $_FILES["file"]["tmp_name"];
    if ($_FILES["file"]["size"] > 0) {
        $file = fopen($fileName, "r");
        $i = 0;
        while (($column = fgetcsv($file)) !== FALSE) {
            if ($i > 0) {
                if (!empty($column[0])) {
                    $insertdate = date("Y-m-d", strtotime(str_replace('/', '-', $column[3])));
                    $sql = "INSERT into posts (post_name,description,status,date) 
                    values ('" . $column[0] . "','" . $column[1] . "','" . $column[2] . "','" . $insertdate . "')";
                    $result = mysqli_query($con, $sql);
                    if (isset($result)) {
                        $msg++;
                    }
                }
            }
            $i++;
        }
    }
}

// Export data code using csv
if (isset($_POST['export'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=data.csv');
    $output = fopen("php://output", "w");
    fputcsv($output, array('ID', 'Post Name', 'Description', 'Status', 'Date', 'Entry Time'));
    $query = "SELECT * from posts ORDER BY id DESC";
    $result = mysqli_query($con, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <link href="style.css" rel="stylesheet" type="text/css"/>
</head>
<body>
    <div class="csv_section">
        <div class="export_section">
            <a href="sample.csv">DOWNLOAD SAMPLE</a>
        </div>
        <div class="import_section">
            <form class="form-horizontal" action="" method="post" name="uploadCSV" enctype="multipart/form-data">
                <div class="input-row" style="margin-top: 8px;">
                    <label class="col-md-4 control-label">Choose CSV File</label> <input
                    type="file" name="file" id="file" accept=".csv">
                    <button type="submit" id="submit" name="export" class="btn-submit">EXPORT CSV</button>
                    <button type="submit" id="submit" name="import" class="btn-submit">IMPORT CSV</button>
                </div>
                <div id="response"></div>
            </form>
        </div>
    </div>
    <?php
    if ($msg > 0) {
        ?>
        <div class="msg">CSV data us imported successfully.</div>
        <?php
    }
    ?>
    <div class = "show_records">
        <?php
        $sql = "SELECT * from posts ORDER BY id DESC";
        $records = mysqli_query($con, $sql);
        $rowcount = mysqli_num_rows($records);
        if ($rowcount > 0) {
            ?>
            <h2 class="cl">All Imported List <span style="float:right;">Total : <?php echo $rowcount; ?></span></h2>
            <table id='joblisttable' style="float:left;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Post Name</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Entry Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = mysqli_fetch_object($records)) {
                        ?>
                        <tr class="line-content">
                            <td><?php echo $row->id; ?></td>
                            <td><?php echo $row->post_name; ?></td>
                            <td><?php echo $row->description; ?></td>
                            <td><?php echo $row->status; ?></td>
                            <td><?php echo date('Y-m-d H:i:s', strtotime($row->date)); ?></td>
                            <td><?php echo date('Y-m-d H:i:s', strtotime($row->created)); ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            <ul class="pagin"></ul>
        <?php } ?>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="jquery.js" type="text/javascript"></script>
</body>
</html>