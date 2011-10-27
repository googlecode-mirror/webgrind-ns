<?php
    include_once 'webgrind.config.php';

    include_once 'library/webgrind.app.php';
    include_once 'library/webgrind.filehandler.php';

    include_once 'library/sizer/interface/base.fs.php';
    include_once 'library/sizer/entity.sizer.php';

    include_once 'library/entity/webgrind.entity.ioread.php';
    include_once 'library/entity/webgrind.entity.wgreader.php';
    
    WebGrind\App::start();

    WebGrind\App::run();

    WebGrind\App::stop();

