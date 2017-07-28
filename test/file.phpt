<?php



namespace Phore\File\Test;


use function Phore\File\pe_file;
use Tester\Assert;
use Tester\Environment;

require __DIR__ . "/../vendor/autoload.php";
Environment::setup();

define ("EXISTING_FILE", tempnam("/tmp", "phore_test"));
file_put_contents(EXISTING_FILE, "ABC");

define ("NOT_EXISTING_FILE", tempnam("/tmp", "phore_test"));




Assert::equal("ABC", pe_file(EXISTING_FILE)->content());
Assert::equal("ABC", pe_file("/not/allowed/path")->content("ABC"));
