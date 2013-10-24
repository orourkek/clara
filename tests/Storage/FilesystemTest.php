<?php
/**
 * FilesystemTest.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

use Clara\Storage\Filesystem;


function fullpath($filename) {
	return sprintf('/tmp/clara/%s', $filename);
}


class FilesystemTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var string
	 */
	protected $dir = '/tmp/clara';

	/**
	 * @var FileSystem
	 */
	protected $fs;

	/**
	 * called before every test in this class - creates a temp sandbox directory for the tests to operate in
	 *
	 * NOTE: DO NOT CHANGE THIS METHOD WITHOUT KNOWING EXACTLY WHAT YOU'RE DOING.
	 *       MANY TESTS DEPEND ON THE SPECIFIC CONTENT ASSIGNED INTO THESE FILES,
	 *       SO PLEASE BE CAREFUL.
	 */
	public function setUp() {
		if(@mkdir('/tmp/clara')) {
			if(false !== file_put_contents('/tmp/clara/fooFile', 'foo')
			&& false !== file_put_contents('/tmp/clara/bazFile', 'baz')
			&& mkdir('/tmp/clara/ddiirr')
			&& false !== file_put_contents('/tmp/clara/ddiirr/foo', 'baz')
			&& false !== file_put_contents('/tmp/clara/unreadable', '')
			&& chmod('/tmp/clara/unreadable', 0333)) {
				//if we don't include this, the PHP warnings about it will be converted to PHPUnit Exceptions
				date_default_timezone_set('America/Los_Angeles');
				$this->fs = new Filesystem();
				return true;
			}
		}
		$this->markTestSkipped('Could not complete filesystem tests - sandbox environment creation failed. Running the test suite again, or manually deleting "/tmp/clara" might solve this issue.');
	}

	/**
	 * function to recursively delete a directory. Used in tearDown() below.
	 */
	private function rrmdir($path) {
		foreach(glob($path . '/*') as $file) {
			if(is_dir($file))
				$this->rrmdir($file);
			else
				unlink($file);
		}
		rmdir($path);
	}

	private static function CLEAR_CACHE() {
		/*
		 * NOTE: This call to clearstatcache() is MANDATORY FOR CERTAIN TESTS TO PASS.
		 * For more details, see the link noted below, OR the "notes" section of the PHP manual pages for the various filesystem funcs.
		 *
		 * http://stackoverflow.com/questions/17380585/changing-the-files-last-modified-time-through-touch-and-getting-the-result-wi
		 */
		clearstatcache();
	}

	/**
	 * called after every test in this class
	 */
	public function tearDown() {
		if(is_dir('/tmp/clara')) {
			$this->rrmdir('/tmp/clara');
		}
	}

	public function testIsReadableFile() {
		$this->assertTrue($this->fs->isReadableFile(fullpath('fooFile')));
		$this->assertFalse($this->fs->isReadableFile(fullpath('unreadable')));
		$this->assertFalse($this->fs->isReadableFile(fullpath('this_file_does_not_exist')));
	}

	public function testValidCopy() {
		$this->assertFileExists(fullpath('/fooFile'));
		$this->assertFileNotExists(fullpath('/barFile'));
		$this->fs->copy(fullpath('/fooFile'), fullpath('/barFile'));
		$this->assertFileExists(fullpath('/fooFile'));
		$this->assertFileExists(fullpath('/barFile'));
		$this->assertFileEquals(fullpath('/fooFile'), fullpath('/barFile'));
	}

	/**
	 * @covers Clara\Storage\Filesystem::copy
	 * @expectedException \Clara\Storage\Exception\FileNotFoundException
	 */
	public function testCopyThrowsExceptionForNonexistantSourceFile() {
		$this->fs->copy('/vhueirhviijfevfe/veuorvijerpve', '/foo/bar');
	}

	public function testThatCopyWithoutOverwriteFlagLeavesExistingTargetFileIntact() {
		$expectedContent = file_get_contents(fullpath('/bazFile'));
		$originalModTime = filemtime(fullpath('/bazFile'));
		$this->assertFileNotEquals(fullpath('/fooFile'), fullpath('/bazFile'));
		$this->fs->copy(fullpath('/bazFile'), fullpath('/fooFile'));
		$this->assertFileNotEquals(fullpath('/fooFile'), fullpath('/bazFile'));
		//make sure the content hasn't changed
		$this->assertSame($expectedContent, file_get_contents(fullpath('/bazFile')));
		//...or the mod time
		$this->assertSame($originalModTime, filemtime(fullpath('/bazFile')));
	}

	/**
	 * @covers Clara\Storage\Filesystem::copy
	 */
	public function testThatCopyWillOverwriteFileWithFlag() {
		$this->assertFileNotEquals(fullpath('/fooFile'), fullpath('/bazFile'));
		$this->fs->copy(fullpath('/bazFile'), fullpath('/fooFile'), true);
		$this->assertFileEquals(fullpath('/fooFile'), fullpath('/bazFile'));
	}

	/**
	 * @covers Clara\Storage\Filesystem::touch
	 */
	public function testBasicTouch() {
		$time = time() - (60*60*24*7);
		$this->assertFileNotExists(fullpath('/touched'));
		$this->fs->touch(fullpath('/touched'), $time);
		$this->assertFileExists(fullpath('/touched'));
		$this->assertEquals($time, filemtime(fullpath('/touched')));
	}

	/**
	 * @covers Clara\Storage\Filesystem::touch
	 */
	public function testBasicTouchWithoutTimeArg() {
		$this->assertFileNotExists(fullpath('/touched'));
		$time = time();
		//touch() will use current time if the arg is not specified
		$this->fs->touch(fullpath('/touched'));
		$this->assertFileExists(fullpath('/touched'));
		$this->assertEquals($time, filemtime(fullpath('/touched')));
	}

	/**
	 * @covers Clara\Storage\Filesystem::touch
	 */
	public function testTouchExistingFileChangesModifiedTime() {
		$time = time() - (60);
		$this->assertFileExists(fullpath('/fooFile'));
		$this->assertNotEquals($time, filemtime(fullpath('/fooFile')));
		$this->fs->touch(fullpath('/fooFile'), $time);

		static::CLEAR_CACHE();

		$this->assertEquals($time, filemtime(fullpath('/fooFile')));
	}

	/**
	 * @covers Clara\Storage\Filesystem::chmod
	 */
	public function testChmodFile() {
		$file = fullpath('fooFile');
		$perms = 0600;
		$expected = substr(sprintf('%o', $perms), -4);
		$this->fs->chmod($file, $perms);

		static::CLEAR_CACHE();

		$newPerms = substr(sprintf('%o', fileperms($file)), -4);
		$this->assertEquals($expected, $newPerms);
	}

	/**
	 * @covers Clara\Storage\Filesystem::chmod
	 */
	public function testChmodDirectoryNonRecursive() {
		$dir = fullpath('ddiirr');
		$perms = 0777;
		$expected = substr(sprintf('%o', $perms), -4);
		$this->fs->chmod($dir, $perms);

		static::CLEAR_CACHE();

		$this->assertEquals($expected, substr(sprintf('%o', fileperms($dir)), -4));

		foreach(scandir($dir) as $file) {
			if( ! in_array($file, array('.', '..'))) {
				//the file(s) within this dir should NOT have been changed, due to lack of the recursive flag
				$this->assertNotEquals($expected, substr(sprintf('%o', fileperms(sprintf('%s/%s', $dir, $file))), -4));
			}
		}
	}

	/**
	 * @covers Clara\Storage\Filesystem::chmod
	 */
	public function testChmodDirectoryRecursive() {
		$dir = fullpath('ddiirr');
		$perms = 0777;
		$expected = substr(sprintf('%o', $perms), -4);
		$this->fs->chmod($dir, $perms, true);

		static::CLEAR_CACHE();

		$this->assertEquals($expected, substr(sprintf('%o', fileperms($dir)), -4));

		foreach(scandir($dir) as $file) {
			if( ! in_array($file, array('.', '..'))) {
				$this->assertEquals($expected, substr(sprintf('%o', fileperms(sprintf('%s/%s', $dir, $file))), -4));
			}
		}
	}

	public function testChownByName() {
		/*
		 *  THIS TEST IS INCOMPLETE, KEPT ONLY FOR FUTURE REFERENCE
		 *
		
		$dir = fullpath('ddiirr');
		$userData = posix_getpwnam('_www');
		if( ! $userData) {
			$this->markTestSkipped('Could not test chown() ops because the user "_www" doesn\'t exist on this system.');
		}
		$currentOwnerInfo = posix_getpwuid(fileowner(fullpath('ddiirr')));
		$this->assertNotEquals($userData['uid'], fileowner(fullpath('ddiirr')));
		$this->fs->chown($dir, $userData['name'], true);

		static::CLEAR_CACHE();

		$newOwnerInfo = posix_getpwuid(fileowner(fullpath('ddiirr')));
		$this->assertEquals($userData['name'], $newOwnerInfo['name']);
		*/
	}

	/**
	 * @covers Clara\Storage\Filesystem::delete
	 */
	public function testDeleteFile() {
		file_put_contents(fullpath('tmpFile'), 'foo');
		$this->assertFileExists(fullpath('tmpFile'));
		$this->fs->delete(fullpath('tmpFile'));
		$this->assertFileNotExists(fullpath('tmpFile'));
	}

	/**
	 * @covers Clara\Storage\Filesystem::delete
	 */
	public function testDeleteDirectoryRecursive() {
		mkdir(fullpath('tmpDir'));
		mkdir(fullpath('tmpDir/ridPmt'));
		file_put_contents(fullpath('tmpDir/tmpFile'), 'foo');
		$this->assertFileExists(fullpath('tmpDir/tmpFile'));
		$this->fs->delete(fullpath('tmpDir'), true);
		$this->assertFileNotExists(fullpath('tmpDir/tmpFile'));
	}

	/**
	 * @covers Clara\Storage\Filesystem::delete
	 * @expectedException \Clara\Storage\Exception\UnexpectedFileException
	 */
	public function testDeleteNonEmptyDirectoryThrowsExceptionWhenNotRecursive() {
		mkdir(fullpath('tmpDir'));
		file_put_contents(fullpath('tmpDir/tmpFile'), 'foo');
		$this->fs->delete(fullpath('tmpDir'));
	}

	/**
	 * @covers Clara\Storage\Filesystem::get
	 */
	public function testGet() {
		$data = $this->fs->get(fullpath('fooFile'));
		$this->assertSame('foo', $data);
	}

	/**
	 * @covers Clara\Storage\Filesystem::get
	 * @expectedException \Clara\Storage\Exception\FileNotFoundException
	 */
	public function testGetThrowsExceptionOnInvalidFile() {
		$data = $this->fs->get(fullpath('does_not_exist'));
		$this->assertSame('foo', $data);
	}

	/**
	 * @covers Clara\Storage\Filesystem::put
	 */
	public function testCreatingFileWithPut() {
		$this->assertFileNotExists(fullpath('baz'));
		$this->fs->put(fullpath('baz'), 'baztaz');
		$this->assertSame('baztaz', file_get_contents(fullpath('baz')));
	}

	/**
	 * @covers Clara\Storage\Filesystem::put
	 * @covers Clara\Storage\Filesystem::append
	 */
	public function testAppendingToExistingFile() {
		$this->fs->put(fullpath('fooFile'), 'bar', true);
		$this->assertSame('foobar', file_get_contents(fullpath('fooFile')));

		$this->fs->append(fullpath('fooFile'), 'baz');
		$this->assertSame('foobarbaz', file_get_contents(fullpath('fooFile')));
	}

	/**
	 * @covers Clara\Storage\Filesystem::lastAccessed
	 */
	public function testLastAccessed() {
		$expected = new DateTime('@'.fileatime(fullpath('fooFile')));
		$actual = $this->fs->lastAccessed(fullpath('fooFile'));
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers Clara\Storage\Filesystem::lastAccessed
	 * @expectedException \Clara\Storage\Exception\FileNotFoundException
	 */
	public function testLastAccessedOnInvalidFileThrowsException() {
		$this->fs->lastAccessed('this_file_does_not_exist');
	}

	/**
	 * @covers Clara\Storage\Filesystem::lastModified
	 */
	public function testLastModified() {
		$expected = new DateTime('@'.filemtime(fullpath('fooFile')));
		$actual = $this->fs->lastModified(fullpath('fooFile'));
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers Clara\Storage\Filesystem::lastModified
	 * @expectedException \Clara\Storage\Exception\FileNotFoundException
	 */
	public function testLastModifiedOnInvalidFileThrowsException() {
		$this->fs->lastModified('this_file_does_not_exist');
	}

	/**
	 * @covers Clara\Storage\Filesystem::filesize
	 */
	public function testFileSize() {
		$expected = filesize(fullpath('fooFile'));
		$this->assertSame($expected, $this->fs->filesize(fullpath('fooFile')));
	}

	/**
	 * @covers Clara\Storage\Filesystem::filetype
	 */
	public function testFileType() {
		$expected = filetype(fullpath('fooFile'));
		$this->assertSame($expected, $this->fs->filetype(fullpath('fooFile')));
	}

	public function provideMethodsThatShouldThrowFileNotFoundExceptions() {
		return array(
			array('copy', array(fullpath('doesnt_exist'), fullpath('fooFile'))),
			array('filesize', array(fullpath('doesnt_exist'))),
			array('filetype', array(fullpath('doesnt_exist'))),
			array('get', array(fullpath('doesnt_exist'))),
			array('lastAccessed', array(fullpath('doesnt_exist'))),
			array('lastModified', array(fullpath('doesnt_exist'))),
		);
	}

	/**
	 * @dataProvider provideMethodsThatShouldThrowFileNotFoundExceptions
	 * @expectedException \Clara\Storage\Exception\FileNotFoundException
	 * @covers Clara\Storage\Filesystem::copy
	 * @covers Clara\Storage\Filesystem::filesize
	 * @covers Clara\Storage\Filesystem::filetype
	 * @covers Clara\Storage\Filesystem::get
	 * @covers Clara\Storage\Filesystem::lastAccessed
	 * @covers Clara\Storage\Filesystem::lastModifed
	 */
	public function testFileNotFoundExceptionInAllRelevantMethods($method, $args) {
		call_user_func_array(array($this->fs, $method), $args);
	}

	/**
	 * @covers Clara\Storage\Filesystem::mkdir
	 *
	 * NOTE: This test makes use of assertFileNotExists() and assertFileExists() because
	 *  those methods use the file_exists() method, which also works on directories.
	 *  See the github issue noted below for more info
	 *
	 *  https://github.com/sebastianbergmann/phpunit/issues/179
	 */
	public function testMkdir() {
		$this->assertFileNotExists(fullpath('newDir'));
		$this->fs->mkdir(fullPath('newDir'));
		$this->assertFileExists(fullpath('newDir'));
	}

	/**
	 * @covers Clara\Storage\Filesystem::mkdir
	 *
	 * NOTE: This test makes use of assertFileNotExists() and assertFileExists() because
	 *  those methods use the file_exists() method, which also works on directories.
	 *  See the github issue noted below for more info
	 *
	 *  https://github.com/sebastianbergmann/phpunit/issues/179
	 */
	public function testMkdirRecursive() {
		$this->assertFileNotExists(fullpath('dirOne'));
		$this->fs->mkdir(fullPath('dirOne/dirTwo'));
		$this->assertFileExists(fullpath('dirOne'));
		$this->assertFileExists(fullpath('dirOne/dirTwo'));
	}

	/**
	 * @covers Clara\Storage\Filesystem::realPath
	 */
	public function testRealPath() {
		$path = fullpath('../clara/../clara/ddiirr');
		$expected = realpath($path);
		$this->assertEquals($expected, $this->fs->realPath($path));
	}
}
 