<?php

namespace Phore\File;

/**
 * @param string $filename <p>
 * Relative Path to the file.
 * </p>
 * &tip.fopen-wrapper;
 *
 * @return PhoreFile
 */
function pfile($filename) : PhoreFile {
    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
    print_r ($backtrace);
    return new PhoreFile($filename);
}

function purl (string $url) : PhoreUrl {
    return new PhoreUrl($url);
}
