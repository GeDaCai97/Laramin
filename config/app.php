<?php

use Dotenv\Dotenv;
use App\Models\Model;
require __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv::createImmutable('../');
$dotenv->safeLoad();
require 'tools.php';
require 'database.php';

//Conecta a la base de datos
Model::setDB($db);