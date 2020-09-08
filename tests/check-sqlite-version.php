<?php

namespace Bazar\Tests;

use SQLite3;

const SQLITE_MIN_VERSION = '3.33.0';

exit(
    \version_compare(SQLite3::version()['versionString'], SQLITE_MIN_VERSION, '>=')
        ? 0
        : 1
);
