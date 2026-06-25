<?php
$lines = file('storage/logs/laravel.log');
foreach(array_reverse($lines) as $l) {
    if (strpos($l, 'local.ERROR') !== false) {
        echo $l;
        break;
    }
}
