# Introduction #

There is a few differences on implementation compared to the original, NS version bypasses .webgrind files, and only read from xdebug output file. This are the reasons:
  * Files .webgrind will be deleted on given occasion
  * There is double of processing logic, first xdebug file, and second, webgrind file, and this is not necessarily needed


# Details #

Overall, _look and feel_ of WebGrind (UserInterface) does not change (I love its simplicity :P) from its original. The changes **are on source code** written in PHP. I am using the latest technique of PHP (5.3+) and embrace the namespace and classes improvement from PHP core.

I decoupled a lot of WebGrind library, such as:
  * index.php => splitted by two files: index.php and app.php
  * config.php => simpler than its original
  * preprocessor.php => decoupled to: ioreadwebgrind.php, iowritewebgrind.php, wgread.php and wgwrite.php.
  * reader.php => more or less, still use same logic, different implementation
  * filehandler.php => simpler, and not implemented invokeUrl

And my Implementation different on this file: _preprocessor.php_. I bypass the code and get a shortcut flow using wgreader.php and ioreadwebgrind.php, both with a specific purpose:
  1. IOReadWebGrind has specific purpose: read xdebug format and return the result on array format.
  1. WGReader has specific purpose translate the array result on webgrind format presentation.

So, that's all my documented logic of WebGrind for Chilik Framework