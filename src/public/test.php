<?php

function f(array &$a): array
{
    unset($a[1]);
    return $a;
}

$a = [1, 2, 3];
var_dump($a);
f($a);
var_dump($a);
/*try {
    throw new Exception('Omaeua');
    echo '!!!!!!!!';
} catch (Exception $exception) {
    echo $exception->getMessage();
}
echo 34131;*/

/*$affiliatesOfTheParent = [1, 2, 3, 4, 5, 6];
for ($i = count($affiliatesOfTheParent); $i > 4; $i--) {
    echo $affiliatesOfTheParent[$i - 1] . ' ';
}*/

/*$a = [ 2 => 'a', 4 => 'b'];
$a[] = 'c';
var_dump($a);*/
/*echo date('Y m h i s', 12234312313);
$a[][] = 1;
var_dump($a);*/
/*$a = [1, '3', 6];
list($one, $two, $three) = $a;
echo $one . '  ' . $two . '  ' . $three . '<br>';

$a = [1, 2, 3];
$b = [4, 5, 6];

echo $a + $b;*/

/*$a[] = null;
echo ($a[0] != null) ? 1 : 0;*/

/*phpinfo();
echo date('d M Y h i s', 1273449600);
echo '<br>';
//echo time();
echo rand(1273449600, time() - 86400); //in the day 86400 sec

echo '     ' . time() - 15638400;

$a = 1;

function inc()
{
    return [1, 2];
}

function abc()
{
    return [3, 4];
}

$a = inc();
$a = abc();

echo '      ' . $a[1] . $a[0];*/
