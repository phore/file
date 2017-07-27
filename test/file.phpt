<?php



namespace Phore\File\Test;


use function Phore\File\pfile;
use Tester\Assert;

require __DIR__ . "/../vendor/autoload.php";



Assert::equal("wurst", pfile("wurst")->content())

;