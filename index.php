<?php

include_once 'config.php';

include_once 'library/app.php';
include_once 'library/preprocessor.php';
include_once 'library/filehandler.php';

include_once 'library/sizer/interface/fs.php';
include_once 'library/sizer/sizer.php';

include_once 'library/entity/wgfilespec.php';

include_once 'library/entity/ioreadwebgrind.php';
include_once 'library/entity/wgread.php';
include_once 'library/entity/wgreader.php';
include_once 'library/entity/ioread.php';

include_once 'library/entity/iowritewebgrind.php';
include_once 'library/entity/wgwrite.php';
include_once 'library/entity/iowrite.php';


    WebGrind\App::start();

    WebGrind\App::run();

    WebGrind\App::stop();

