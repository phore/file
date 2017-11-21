<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 27.07.17
 * Time: 17:07
 */

namespace Phore\File;


use Phore\File\Exception\FileAccessException;
use Phore\File\Exception\FileNotFoundException;
use Phore\File\Exception\PathOutOfBoundsException;
use Symfony\Component\Yaml\Yaml;

class Path
{

    protected $path;
    protected $type;

    const TYPE_URL = "url";

    public function __construct($path = null, $pathType = "fs")
    {
        $this->type = $pathType;
        if (is_array($path)) {
            if ($this->type == self::TYPE_URL) {
                for($i=0; $i<count ($path); $i++)
                    $path[$i] = urlencode($path[$i]);
            }
            $this->path = implode("/", $path);
            return;
        }
        if (is_string($path)) {
            $this->path = $path;
            return;
        }
        throw new \Exception("Invalid parameter type: expected string or string[]");
    }


    public function sanitize()
    {

    }

    /**
     *
     *
     * @return string|null
     */
    public function getExtension()
    {
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }

    public function isUrl () : bool
    {
        return preg_match("|^https?://|", $this->path);
    }


    public function isRelative() : bool
    {
        return substr($this->path, 0, 1) !== "/";
    }

    public function isAbsolute() : bool
    {
        return ! $this->isRelative();
    }


    /*
    public function isSubpathOf ($rootPath) : bool {
        $rootPath = new PhorePath($rootPath);
        $myPath = new PhorePath($this->path);

        if ($myPath->isRelative() && $rootPath->isAbsolute())
            return true;

        if ($myPath->isAbsolute() && $rootPath->isRelative())
            return false;
        $myPath->resolve();
        $rootPath->resolve();
        if (substr($myPath, 0, strlen($rootPath) == $myPath)

    }
    */

    /**
     * Expand a path
     *
     * <example>
     * ppath("/A/B")->xpath("C/D") == "/A/B/C/D"
     * </example>
     *
     * @param string $subpath
     *
     * @return Path
     */
    public function xpath(string $subpath) : self
    {
        $subpath = new Path($subpath, $this->type);
        $path = $this->path;
        if (substr($path,-1) == "/")
            $path .= $subpath->toRelative();
        else
            $path .= $subpath->toAbsolute();
        return new self($path, $this->type);
    }

    /**
     * strip the last element of the path (directory-name)
     *
     * eqals dirname();
     *
     * @return Path
     */
    public function dirname() : self
    {
        return new self(dirname($this->path), $this->type);
    }


    /**
     * Remove trailing /
     *
     * @param $path
     *
     * @return Path
     */
    public function toRelative() : self
    {
        $path = $this->path;
        while (substr ($path, 0, 1) == "/")
            $path = substr($path, 1);

        return new self($path, $this->type);
    }

    public function toAbsolute() : self
    {
        $path = $this->path;
        if ( ! $this->isAbsolute())
            $path = "/" . $path;
        return new self($path, $this->type);
    }

    /**
     *
     *
     * @return Path
     * @throws PathOutOfBoundsException
     * @internal param $lockDir
     *
     */
    public function resolve() : self
    {
        $wasRelative = $this->isRelative();
        $parts = explode("/", $this->path);
        $ret = [];
        foreach ($parts as $part) {
            if ($part == "")
                continue;
            if ($part == "." || $part == "")
                continue;
            if ($part == "..") {
                if (count ($ret) == 0)
                    throw new PathOutOfBoundsException("Path is out of bounds: $this->path");
                array_pop($ret);
                continue;
            }
            $ret[] = $part;
        }
        $newPath = new self(implode("/", $ret));
        if ($wasRelative)
            return $newPath->toRelative();
        return $newPath->toAbsolute();
    }


    public function __toString()
    {
        return $this->path;
    }


    public static function Use(string $dirname) : self
    {
        return new self($dirname);
    }

}