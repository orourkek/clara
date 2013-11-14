<?php
/**
 * Filesystem.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Storage;

use Clara\Storage\Exception\FileNotFoundException;
use Clara\Storage\Exception\IOException;
use Clara\Storage\Exception\UnexpectedFileException;
use RecursiveDirectoryIterator;
use DateTime;

/**
 * Filesystem object, for doing filesystem-y things. All methods should be self explanatory.
 *
 * @todo make Filesystem extend Observable & fire events
 * @package Clara\Storage
 */
class Filesystem {

	/**
	 * An alias for Filesystem::put() with the append flag raised
	 *
	 * @param $file
	 * @param $content
	 * @return $this
	 * @see Filesystem::put
	 */
	public function append($file, $content) {
		return $this->put($file, $content, true);
	}

	/**
	 * @param      $fileOrDir
	 * @param int  $mode
	 * @param bool $recursive
	 * @throws \Clara\Storage\Exception\IOException
	 * @return $this
	 */
	public function chmod($fileOrDir, $mode=0666, $recursive=false) {
		if( ! @chmod($fileOrDir, $mode)) {
			throw new IOException(sprintf('chmod failed on file "%s" with mode %o', $fileOrDir, $mode));
		}
		if(is_dir($fileOrDir) && $recursive) {
			foreach(new RecursiveDirectoryIterator($fileOrDir) as $fileInfo) {
				if( ! in_array($fileInfo->getFileName(), array('.', '..'))) {
					return $this->chmod($fileInfo->getPathName(), $mode, true);
				}
			}
		}
		return $this;
	}

	/**
	 * @param      $fileOrDir
	 * @param      $newOwner
	 * @param bool $recursive
	 * @return mixed
	 * @throws \Clara\Storage\Exception\IOException
	 */
	public function chown($fileOrDir, $newOwner, $recursive=false) {
		if(is_link($fileOrDir)) {
			$chownResult = @lchown($fileOrDir, $newOwner);
		} else {
			$chownResult = @chown($fileOrDir, $newOwner);
		}
		if( ! $chownResult) {
			throw new IOException(sprintf('chown failed on file "%s" to new owner "%s"', $fileOrDir, $newOwner));
		}
		if(is_dir($fileOrDir) && $recursive) {
			foreach(new RecursiveDirectoryIterator($fileOrDir) as $fileInfo) {
				if( ! in_array($fileInfo->getFileName(), array('.', '..'))) {
					return $this->chown($fileInfo->getPathName(), $newOwner, true);
				}
			}
		}
	}

	/**
	 * @param      $sourceFile
	 * @param      $targetFile
	 * @param bool $overwriteExisting
	 * @return $this
	 * @throws \Clara\Storage\Exception\IOException
	 * @throws \Clara\Storage\Exception\FileNotFoundException
	 */
	public function copy($sourceFile, $targetFile, $overwriteExisting=false) {
		if( ! $this->isReadableFile($sourceFile)) {
			throw new FileNotFoundException(sprintf('File doesn\'t exist or isn\'t readable: "%s"', $sourceFile));
		}

		if((is_file($targetFile) && $overwriteExisting) || ( ! $overwriteExisting && ! is_file($targetFile))) {
			$source = fopen($sourceFile, 'r');
			$target = fopen($targetFile, 'w+');
			stream_copy_to_stream($source, $target);
			fclose($source);
			fclose($target);

			if ( ! is_file($targetFile)) {
				throw new IOException(sprintf('Failed to copy "%s" to "%s".', $sourceFile, $targetFile));
			}
		}
		return $this;
	}

	/**
	 * @param      $fileOrDir
	 * @param bool $recursive
	 * @return $this
	 * @throws \Clara\Storage\Exception\UnexpectedFileException
	 * @throws \Clara\Storage\Exception\IOException
	 */
	public function delete($fileOrDir, $recursive=false) {
		if(is_dir($fileOrDir)) {
			if(2 !== count(scandir($fileOrDir)) && ! $recursive) {
				throw new UnexpectedFileException(sprintf('Failed to delete "%s" - directory not empty', $fileOrDir));
			}
			foreach(new RecursiveDirectoryIterator($fileOrDir) as $fileInfo) {
				if( ! in_array($fileInfo->getFileName(), array('.', '..'))) {
					$this->delete($fileInfo->getPathName(), true);
				}
			}
			if( ! rmdir($fileOrDir)) {
				throw new IOException(sprintf('Delete failed on directory "%s"', $fileOrDir));
			}
		} else if( ! unlink($fileOrDir)) {
			throw new IOException(sprintf('Delete failed on file "%s"', $fileOrDir));
		}
		return $this;
	}

	/**
	 * @param $file
	 * @return int
	 * @throws \Clara\Storage\Exception\FileNotFoundException
	 */
	public function filesize($file) {
		if( ! $this->isReadableFile($file)) {
			throw new FileNotFoundException(sprintf('File doesn\'t exist or isn\'t readable: "%s"', $file));
		}
		return filesize($file);
	}

	/**
	 * @param $file
	 * @return string
	 * @throws \Clara\Storage\Exception\FileNotFoundException
	 */
	public function filetype($file) {
		if( ! $this->isReadableFile($file)) {
			throw new FileNotFoundException(sprintf('File doesn\'t exist or isn\'t readable: "%s"', $file));
		}
		return filetype($file);
	}

	/**
	 * @param $file
	 * @return string
	 * @throws \Clara\Storage\Exception\FileNotFoundException
	 */
	public function get($file) {
		if( ! $this->isReadableFile($file)) {
			throw new FileNotFoundException(sprintf('File doesn\'t exist or isn\'t readable: "%s"', $file));
		}
		return file_get_contents($file);
	}

	/**
	 * @param $path
	 * @return bool
	 */
	public function isReadableFile($path) {
		return (is_file($path) && is_readable($path));
	}

	/**
	 * @param $path
	 * @return bool
	 */
	public function isReadableDirectory($path) {
		return (is_dir($path) && is_readable($path));
	}

	/**
	 * @param $file
	 * @return DateTime
	 * @throws \Clara\Storage\Exception\FileNotFoundException
	 */
	public function lastAccessed($file) {
		if( ! $this->isReadableFile($file)) {
			throw new FileNotFoundException(sprintf('File doesn\'t exist or isn\'t readable: "%s"', $file));
		}
		return new DateTime('@'.fileatime($file));
	}

	/**
	 * @param $file
	 * @return DateTime
	 * @throws \Clara\Storage\Exception\FileNotFoundException
	 */
	public function lastModified($file) {
		if( ! $this->isReadableFile($file)) {
			throw new FileNotFoundException(sprintf('File doesn\'t exist or isn\'t readable: "%s"', $file));
		}
		return new DateTime('@'.filemtime($file));
	}

	/**
	 * @param      $path
	 * @param int  $mode
	 * @param bool $recursive
	 * @throws \Clara\Storage\Exception\IOException
	 * @return bool
	 */
	public function mkdir($path, $mode=0777, $recursive=true) {
		if( ! mkdir($path, $mode, $recursive)) {
			throw new IOException(sprintf('Mkdir failed: "%s" (%o)', $path, $mode));
		}
		return $this;
	}

	/**
	 * @param      $file
	 * @param      $content
	 * @param bool $append
	 * @return $this
	 * @throws \Clara\Storage\Exception\IOException
	 */
	public function put($file, $content, $append=false) {
		$flags = ($append) ? FILE_APPEND : 0;
		if(false === file_put_contents($file, $content, $flags)) {
			throw new IOException(sprintf('Failed to write data into file "%s". Content: "%s"', $file, (strlen($content) > 10)? substr($content, 0, 10) . '...' : $content));
		}
		return $this;
	}

	/**
	 * @param $path
	 * @return string
	 */
	public function realPath($path) {
		return realpath($path);
	}

	/**
	 * @param $source
	 * @param $target
	 * @return $this
	 * @throws \Clara\Storage\Exception\IOException
	 * @throws \Clara\Storage\Exception\FileNotFoundException
	 */
	public function rename($source, $target) {
		if( ! file_exists($source)) {
			throw new FileNotFoundException(sprintf('File doesn\'t exist or isn\'t readable: "%s"', $source));
		}
		if( ! rename($source, $target)) {
			throw new IOException(sprintf('Rename failed: "%s" ==> "%s"', $source, $target));
		}
		return $this;
	}

	/**
	 * @param      $file
	 * @param null $time
	 * @param null $aTime
	 * @return $this
	 * @throws \Clara\Storage\Exception\IOException
	 */
	public function touch($file, $time=null, $aTime=null) {
		if(empty($time)) {
			$time = time();
		}
		if( ! touch($file, $time, $aTime)) {
			throw new IOException(sprintf('Touch failed for file "%s" with time "%s"', $file, $time));
		}
		return $this;
	}
}