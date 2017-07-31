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
function file($filename) : PhoreFile {
    return new PhoreFile($filename);
}

function path($path) : PhorePath {
    return new PhorePath($path);
}