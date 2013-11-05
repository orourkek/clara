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
use Clara\Storage\Filesystem;
use DateTime;
use DateTimeZone;


class Writer extends AbstractLogger {

	/**
	 * @var \Clara\Storage\Filesystem
	 */
	protected $filesystem;

	/**
	 * @var string
	 */
	protected $logsDirectory;

	/**
	 * @var string
	 */
	protected $timestampFormat = 'd M Y H:i:s T';

	/**
	 * @param string $logsDirectory
	 */
	public function __construct($logsDirectory) {
		$this->filesystem = new Filesystem();
		if( ! $this->filesystem->isReadableDirectory($logsDirectory)) {
			throw new ClaraRuntimeException(sprintf('Invalid logs directory specified: "%s"', $logsDirectory));
		}
		$this->logsDirectory = $logsDirectory;
	}

	/**
	 * @param string $timestampFormat
	 */
	public function setTimestampFormat($timestampFormat) {
		$this->timestampFormat = $timestampFormat;
		return $this;
	}

	/**
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
	 * @param $message
	 * @return string
	 */
	protected function formatMessage($message) {
		$dt = new DateTime("now", new DateTimeZone("UTC"));
		return sprintf('[%s] %s%s', $dt->format($this->timestampFormat), $message, PHP_EOL);
	}




} 