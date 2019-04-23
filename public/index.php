<?php

require_once '../vendor/autoload.php';

use Package\Controller;

$main = new Controller();

//alterar filtros nesta linha
$filtros = ['banco-de-dados','2018'];

$main->processar($filtros);
