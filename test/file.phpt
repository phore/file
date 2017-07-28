<?php



namespace Phore\File\Test;


use function Phore\File\pfile;

require __DIR__ . "/../vendor/autoload.php";



pfile(__DIR__ . "/../")->content();
