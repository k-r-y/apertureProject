<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php

    $a = 34;
    $b = 344;
    $c = 999993;

    if ($a > $b && $a > $c) {
        echo "A is the largest {$a}";
    } else if ($b > $a && $b > $c) {
        echo "B is the largest {$b}";
    } else {
        echo "C is the largest {$c}";
    }

    ?>

</body>

</html>