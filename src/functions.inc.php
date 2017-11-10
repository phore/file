<?php
/**
 * @param string $filename <p>
 * Relative Path to the file.
 * </p>
 * &tip.fopen-wrapper;
 *
 * @return \Phore\File\File
 */
function pfile($filename) : \Phore\File\File {
    return new Phore\File\File($filename);
}

function ppath($path) : \Phore\File\Path {
    return new \Phore\File\Path($path);
}