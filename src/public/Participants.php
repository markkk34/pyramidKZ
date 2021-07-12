<html>
<head>
    <title>Pyramid</title>
    <link rel="stylesheet" href="style/style.scss">
    <link rel="stylesheet" href="style/fontawesome/css/all.min.css">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript" src="js/GoogleChart.js"></script>
</head>
<body>
<h2>Welcome</h2>
<br>
<br>
<br>

<table border="1">
    <tr>
        <td>id</td>
        <td>firstname</td>
        <td>lastname</td>
        <td>email</td>
        <td>position</td>
        <td>shares_amount</td>
        <td>start_date</td>
        <td>parent_id</td>
    </tr>
    <?php
    require_once '../Controllers/PyramidController.php';
    $this->showParticipants();

    echo '<br>Amount of members:' . count($persons);
    ?>
</table>

<script>
    var persons =
    <?php
        echo json_encode($this->getParticipants());
    ?>
</script>

<div id="chart_div"></div>

</body>
</html>
