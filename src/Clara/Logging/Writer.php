<?php
/**
 * Writer.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Logging;

use Clara\Exception\ClaraRuntimeException;
use Clara\Storage\Exception\IOException;
use Clara\Storage\Filesystem;
use DateTime;
use DateTimeZone;

/**
 * Writes stuff to log files
 *
 * @package Clara\Logging
 */
class Writer extends AbstractLogger {

	/**
	 * Filesystem object used for putting content into files
	 *
	 * @var \Clara\Storage\Filesystem
	 */
	protected $filesystem;

	/**
	 * Where the Writer is writing to
	 *
	 * @var string
	 */
	protected $logsDirectory;

	/**
	 * How to present timestamps in the log(s)
	 *
	 * @var string
	 */
	protected $timestampFormat = 'd M Y H:i:s T';

	/**
	 * @param string $logsDirectory
	 * @throws \Clara\Exception\ClaraRuntimeException
	 */
	public function __construct($logsDirectory) {
		$this->filesystem = new Filesystem();
		if( ! $this->filesystem->isReadableDirectory($logsDirectory)) {
			try {
				$this->filesystem->mkdir($logsDirectory);
			} catch(IOException $e) {
				throw new ClaraRuntimeException(sprintf('Invalid logs directory specified: "%s"', $logsDirectory));
			}
		}
		$this->logsDirectory = $logsDirectory;
	}

	/**
	 * @param string $timestampFormat
	 * @return $this
	 * @link http://php.net/manual/en/datetime.formats.php
	 */
	public function setTimestampFormat($timestampFormat) {
		$this->timestampFormat = $timestampFormat;
		return $this;
	}

	/**
	 * Logs the $message at a severity of $level
	 *
	 * @param        $level
	 * @param string $message
	 * @return mixed|void
	 */
	protected function log($level, $message) {
		$logFile = sprintf('%s/%s.log', $this->logsDirectory, $level);
		$messageFinal = $this->formatMessage($message);
		$this->filesystem->append($logFile, $messageFinal);
		return $this;
	}

	/**
	 * Formats a message to be written to a log
	 *
	 * @param $message
	 * @return string
	 */
	protected function formatMessage($message) {
		$dt = new DateTime("now", new DateTimeZone("UTC"));
		return sprintf('[%s] %s%s', $dt->format($this->timestampFormat), $message, PHP_EOL);
	}

} 