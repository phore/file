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
use Phore\File\Exception\FileParsingException;
use Phore\File\Exception\PathOutOfBoundsException;
use Symfony\Component\Yaml\Yaml;

class File
{

    private $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }


    public function path() : Path
    {
        return new Path($this->filename);
    }

    public function fopen(string $mode) : FileStream
    {
        $fp = @fopen($this->filename, $mode);
        if ( ! $fp)
            throw new FileAccessException("fopen($this->filename): " . error_get_last()["message"]);
        return new FileStream($fp, $this);
    }


    private function _read_content_locked ()
    {
        $file = $this->fopen("r")->flock(LOCK_SH);
        $buf = "";
        while ( ! $file->feof())
            $buf .= $file->fread(1024);
        $file->flock(LOCK_UN);
        $file->fclose();
        return $buf;
    }

    private function _write_content_locked ($content)
    {
        $this->fopen("w+")->flock(LOCK_EX)->fwrite($content)->flock(LOCK_UN)->fclose();
    }


    /**
     * Set or get Contents of file
     *
     * @param string|null $setContent
     *
     * @return File|string
     * @throws FileAccessException
     */
    public function content(string $setContent=null)
    {
        try {
            if (func_num_args() > 0) {
                $this->_write_content_locked($setContent);
                return $this;
            }
            return $this->_read_content_locked();
        } catch (\Exception $e) {
            throw new $e($e->getMessage(), $e->getCode(), $e);
        }
    }


    public function fileSize () : int
    {
        return filesize($this->filename);
    }


    /**
     * @param null $content
     *
     * @return $this|array
     * @throws \Exception
     */
    public function yaml($content=null)
    {
        if ( ! class_exists(Yaml::class))
            throw new \Exception("Cannot read yaml: library symfony/yaml missing.");

        if (func_num_args() > 0) {
            $this->content(Yaml::dump($content));
            return $this;
        }
        try {
            return Yaml::parse($this->content());
        } catch (\Exception $e) {
            throw new FileParsingException("YAML Parsing of file '{$this->filename}' failed: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * @param null $content
     *
     * @return $this|array
     * @throws FileParsingException
     */
    public function json($content=null)
    {
        if (func_num_args() > 0) {
            $this->content(json_encode($content));
            return $this;
        }
        $json = json_decode($this->content(), true);
        if ($json === null) {
            throw new FileParsingException(
                "JSON Parsing of file '{$this->filename}' failed: " . json_last_error_msg()
            );
        }

        return $json;
    }

    /**
     * @param null $content
     *
     * @return $this|array
     * @throws FileParsingException
     */
    public function serialized($content=null)
    {
        if (func_num_args() > 0) {
            $this->content(serialize($content));
            return $this;
        }
        $serialize = unserialize($this->content());
        if ($serialize === null) {
            throw new FileParsingException(
                "Unserialize of file '{$this->filename}' failed."
            );
        }

        return $serialize;
    }

    /**
     * @param $lockDir
     *
     * @return File
     * @throws PathOutOfBoundsException
     */
    public function resolve($lockDir) : self
    {
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

    public function isDirectory () : bool
    {
        return file_exists($this->filename) && is_dir($this->filename);
    }

    public function isFile () : bool
    {
        return file_exists($this->filename) && is_file($this->filename);
    }

    public function rename ($newName) : self
    {
        if ( ! @rename($this->filename, $newName))
            throw new FileAccessException("Cannot rename file '{$this->filename}' to '{$newName}': " . implode(" ", error_get_last()));
        $this->filename = $newName;
        return $this;
    }

    public function unlink() : self
    {
        if ( ! @unlink($this->filename))
            throw new FileAccessException("Cannot unlink file '{$this->filename}': " . implode(" ", error_get_last()));
        return $this;
    }

    public function mustExist()
    {
        if ( ! file_exists($this->filename))
            throw new FileNotFoundException("File '$this->filename' not found");
        return $this;
    }


    public function __toString()
    {
        return $this->filename;
    }


    public static function Use(string $filename) : self
    {
        return new self ($filename);
    }
}