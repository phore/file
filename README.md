# Phore :: file

file wrapper

> Proof of concept: Don't use in production!

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

