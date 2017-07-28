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
function pe_file($filename) : PhoreFile {
    return new PhoreFile($filename);
}


function pe_path($path) : PhorePath {
    return new PhorePath($path);
}

function pe_url(string $url) : PhoreUrl {
    return new PhoreUrl($url);
}
