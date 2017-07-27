<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 27.07.17
 * Time: 17:07
 */

namespace Phore\File;


use Phore\File\Exception\FileAccessException;
use Phore\File\Exception\PathOutOfBoundsException;
use Symfony\Component\Yaml\Yaml;

class PhoreFile
{

    private $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }


    /**
     * Set or get Contents of file
     *
     * @param string|null $setContent
     *
     * @return PhoreFile|string
     * @throws FileAccessException
     */
    public function content(string $setContent=null) {
        if (func_num_args() > 0) {
            if ( ! @file_put_contents($this->filename, $setContent))
                throw new FileAccessException("Cannot write file '{$this->filename}': " . implode(" ", error_get_last()));
            return $this;
        }
        $data = @file_get_contents($this->filename);
        if ($data === false)
            throw new FileAccessException("Cannot read file '{$this->filename}': " . implode(" ", error_get_last()));
        return $data;
    }

    /**
     * @param null $content
     *
     * @return $this|array
     * @throws \Exception
     */
    public function yaml($content=null) {
        if ( ! class_exists(Yaml::class))
            throw new \Exception("Cannot read yaml: library symfony/yaml missing.");
        if (func_num_args() > 0) {
            $this->content(Yaml::dump($content));
            return $this;
        }
        return Yaml::parse($this->content());
    }

    /**
     * @param null $content
     *
     * @return $this|array
     */
    public function json($content=null) {
        if (func_num_args() > 0) {
            $this->content(json_encode($content));
            return $this;
        }
        $json = json_decode($content, true);
        return $json;
    }


    public function resolve() : self {
        $parts = explode("/", $this->filename);
        $ret = [];
        foreach ($parts as $part) {
            if ($part == "." || $part == "")
                continue;
            if ($part == "..") {
                if (count ($ret) == 0)
                    throw new PathOutOfBoundsException("Path is out of bounds: $this->filename");
                array_pop($ret);
                continue;
            }
            $ret[] = $part;
        }
        $this->filename = implode("/", $ret);
        return $this;
    }


    public function unlink() : void {
        if ( ! @unlink($this->filename))
            throw new FileAccessException("Cannot unlink file '{$this->filename}': " . implode(" ", error_get_last()));
    }

    public function mustExist() {

    }


    public function __toString()
    {
        return $this->filename;
    }

}