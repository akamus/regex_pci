<?php

require_once '../vendor/autoload.php';

use Package\Controller;

$main = new Controller();


$filtros = ['banco-de-dados','2018'];

$main->processar($filtros);
