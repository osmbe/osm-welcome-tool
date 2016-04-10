<?php

class ReadFileBackwards implements Iterator {
	const BUFFER_SIZE = 4096;
	const SEPARATOR = PHP_EOL;

	private $_fh;
	private $filename;

	public function get_filename () {
		return $this->filename;
	}

	public function __construct($filename) {
		$this->_fh = @fopen($filename, 'r');
		if ($this->_fh === false) {
			$this->_fh = @fopen($filename, 'a+');
			
			if ($this->_fh === false) {
				return false;
			}
		}
		$this->filename = $filename;
		$this->_filesize = filesize($filename);
		$this->_pos = -1;
		$this->_buffer = null;
		$this->_key = -1;
		$this->_value = null;
	}

	public function __destruct() {
		fclose($this->_fh);
	}

	public function _read($size) {
		$this->_pos -= $size;
		fseek($this->_fh, $this->_pos);
		return fread($this->_fh, $size);
	}

	public function _readline() {
		$buffer =& $this->_buffer;
		while (true) {
			if ($this->_pos == 0) {
				return array_pop($buffer);
			}
			if (count($buffer) > 1) {
				return array_pop($buffer);
			}
			$buffer = explode(self::SEPARATOR, $this->_read(self::BUFFER_SIZE) . $buffer[0]);
		}
	}

	public function next() {
		$this->_key = $this->_key+1;
		$this->_value = $this->_readline();
	}

	public function rewind() {
		if ($this->_filesize > 0) {
			$this->_pos = $this->_filesize;
			$this->_value = null;
			$this->_key = -1;
			$this->_buffer = explode(self::SEPARATOR, $this->_read($this->_filesize % self::BUFFER_SIZE ?: self::BUFFER_SIZE));
			$this->next();
		}
	}

	public function key() { return $this->_key; }
	public function current() { return $this->_value; }
	public function valid() { return ! is_null($this->_value); }
}

#Example code:
#$f = new ReadFileBackwards(__FILE__);
#foreach ($f as $line) echo $line, "\n";

?>
