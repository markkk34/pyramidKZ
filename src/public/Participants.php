<html>
<head>
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
    foreach ($persons as $person)
    {
        echo $person;
    }
    ?>
</table>

</body>
</html>
