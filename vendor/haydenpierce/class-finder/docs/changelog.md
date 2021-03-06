Version 0.2.0
-------------

* Added support for finding classes declared via `classmap`.
* Exceptions will no longer be thrown when PSR4 can't find a registered namespace (because it could be a valid class
declared in a `classmap`)

Example composer.json that is now supported:
```
  ...
  "autoload": {
    ...
    "classmap": [ "src/foo/", "src/bar/" ]
  }
  ...
```


Version 0.1.2
-------------

* Fixed composer.json so that it can be correctly installed on PHP 7+.

Version 0.1.1
-------------

* Fixed a Linux specific bug that caused absolute paths to fail to resolve and erroneously throw exceptions. If you were
affected by this bug, you would see errors like `Unknown namespace Acme\Whatever. Checked for files in , but that directory did not exist. [...]`
when that namespace does indeed exist.
* Support for PHP 5.3 is now under testing harness and should work now. 

Version 0.1.0
-------------

* Vastly improved PSR4 support
    * Loading classes from Composer packages is now supported.
    * Namespaces that map to multiple directories is now supported.
    * Fixed a bug where ClassFinder would use a more generic (and therefore _wrong_) namespace over a better one. 
    (Selecting `Acme`, when `Acme\Foo` is a better choice)
* Manually overriding the AppRoot is now done with a static method instead of a static property

Mapping a namespace to multiple directories:
```
    ...
    "autoload": {
        "psr-4": {
            "Acme\\Foo\\": [ "src/", "srcButDifferent/" ]
        }
    }
    ...
```

Old overriding app root: 
```
ClassFinder::appRoot = '/home/hpierce/whatevs'; 
```

New overriding app root:
```
ClassFinder::setAppRoot('/home/hpierce/whatevs'); 
```