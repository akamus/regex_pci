<?php

require_once '../vendor/autoload.php';

use Package\Controller;

$main = new Controller();

//alterar filtros nesta linha
$filtros = ['analista-de-sistemas',''];

$main->processar($filtros);
