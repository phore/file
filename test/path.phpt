<?php



namespace Phore\File\Test;


use Phore\File\Exception\PathOutOfBoundsException;
use function Phore\File\pe_file;
use function Phore\File\pe_path;
use Tester\Assert;
use Tester\Environment;



require __DIR__ . "/../vendor/autoload.php";

Environment::setup();

// Changing methods must return new objects
$p = ppath("/some");
Assert::notSame($p, $p->toAbsolute());
Assert::notSame($p, $p->toRelative());
Assert::notSame($p, $p->resolve());
Assert::notSame($p, $p->xpath("/b/c"));

$p = pe_path("/some");
Assert::true($p->isAbsolute());
Assert::false($p->isRelative());

$p = pe_path("some");
Assert::false($p->isAbsolute());
Assert::true($p->isRelative());


Assert::equal("/some",  (string)pe_path("some")->toAbsolute());
Assert::equal("/some",  (string)pe_path("/some")->toAbsolute());

Assert::equal("some",  (string)pe_path("/some")->toRelative());
Assert::equal("some",  (string)pe_path("some")->toRelative());
Assert::equal("some",  (string)pe_path("///////some")->toRelative());

Assert::equal("A/B/C",  (string)pe_path("A")->xpath("B/C"));
Assert::equal("A/B/C",  (string)pe_path("A/")->xpath("B/C"));
Assert::equal("A/B/C",  (string)pe_path("A/")->xpath("/B/C"));


Assert::equal("B/C",    (string)pe_path("A/./..//B/C")->resolve());
Assert::equal("/B/C",   (string)pe_path("/A/./..//B/C")->resolve());
Assert::equal("/B/C",  (string)pe_path("/A/./..//B/C/")->resolve());

Assert::exception(function () { pe_path("/A/../../C/D")->resolve(); }, PathOutOfBoundsException::class);