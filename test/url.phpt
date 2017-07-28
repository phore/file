<?php



namespace Phore\File\Test;


use function Phore\File\pe_file;
use function Phore\File\pe_url;
use Tester\Assert;
use Tester\Environment;

require __DIR__ . "/../vendor/autoload.php";
Environment::setup();


pe_url("http://google.de")->out($result)->outHeader($header)->run(10);

//print_r ($header);
//print_r ($result);

echo "\n=================";
