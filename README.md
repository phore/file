# Phore :: file

file wrapper

> Proof of concept: Don't use in production!


## Features

- __File-Locking__: `content()` uses flock to lock file access
 

## Examples

```php
pfile("../file.txt")->content()
pfile("../file.txt")->lock()->content("New Content")->unlock();
pfile("directory/../file.txt")->resolve()->mustExist()->unlink();
```

## Load Encoded data

```php
pfile("file.json")->json();
pfile("file.yml")->yaml();
```

## Write Encoded data

```php
pfile("file.json")->json(["some"=>"data"]);
pfile("file.yml")->yaml(["some"=>"data"]);
```

## Stream data

```php
pfile("file.json")->fopen("w+")->fwrite("someData")->fclose();
```

## Access Web-Services

```php
purl("https://google.de")->GET(["some"=>"what")->to("/tmp/someFile.tpl");
purl("https://gootle.de")->path("/dev/google")->gets("some"=>"file")->what();

purl("https://google.de")->path(["wurst", "brot", "semper"])->POST->json($someData)->onData(function ($str) {})->header();
```

```php
purl("https://download.org")->header()->post()->X();
purl("https://download.org")->header()->get()->saveAs("someFile")->X();
```