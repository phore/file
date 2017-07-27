# Phore :: file
file wrapper


## Examples

```php
pfile("../file.txt")->content()
pfile("../file.txt")->lock()->content("New Content")->unlock();
pfile("directory/../file.txt")->resolve()->mustExist()->unlink();
```