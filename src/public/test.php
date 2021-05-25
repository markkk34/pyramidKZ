<?php
echo date('d M Y h i s', 1273449600);
echo '<br>';
//echo time();
echo rand(1273449600, time() - 86400); //in the day 86400 sec

echo '     ' . time() - 15638400;

$a = 1;

function inc($a)
{
    $a++;
    return $a;

}

$a = inc($a);

echo '      ' . $a;