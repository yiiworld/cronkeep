<?php

namespace models\Crontab;

/**
 * Job model.
 * 
 * @author Bogdan Ghervan <bogdan.ghervan@gmail.com>
 */
class Job
{
	/**
	 * @var string
	 */
	protected $_originalRaw;
	
	/**
	 * @var string
	 */
	protected $_raw;
	
	/**
	 * @var string|Expression
	 */
	protected $_expression;
	
	/**
	 * @var string
	 */
	protected $_command;
	
	/**
	 * @var boolean
	 */
	protected $_isPaused;
	
	/**
	 * @var string
	 */
	protected $_comment;
	
	/**
	 * @var string
	 */
	protected $_hash;
	
	/**
	 * Default line separator.
	 * 
	 * @var string
	 */
	protected $_lineSeparator = PHP_EOL;
	
	/**
	 * Sets raw job representation.
	 * @todo Find another way to keep original data and changed data 
	 *
	 * @param string $raw
	 * @return Job
	 */
	public function setRaw($raw)
	{
		if (is_null($this->_originalRaw)) {
			$this->_originalRaw = $raw;
		}
		
		$this->_raw = $raw;
		$this->_generateHash();
		
		return $this;
	}
	
	/**
	 * Sets expression part.
	 * 
	 * @param string|Expression $expression
	 * @return Job
	 */
	public function setExpression($expression)
	{
		$this->_expression = $expression;
		$this->_updateRaw();
		
		return $this;
	}
	
	/**
	 * Sets command part.
	 * 
	 * @param string $command
	 * @return Job
	 */
	public function setCommand($command)
	{
		$this->_command = $command;
		$this->_updateRaw();
		
		return $this;
	}
	
	/**
	 * Sets comment.
	 * 
	 * @param string $comment
	 * @return Job
	 */
	public function setComment($comment)
	{
		$this->_comment = $comment;
		$this->_updateRaw();
		
		return $this;
	}
	
	/**
	 * Sets job schedule status (paused or ready to run).
	 *  
	 * @param boolean $isPaused
	 * @return Job
	 */
	public function setIsPaused($isPaused)
	{
		$this->_isPaused = (bool) $isPaused;
		$this->_updateRaw();
		
		return $this;
	}
	
	/**
	 * Retrieves initial raw job representation.
	 * 
	 * @return string
	 */
	public function getOriginalRaw()
	{
		return $this->_originalRaw;
	}
	
	/**
	 * Retrieves raw job representation.
	 * 
	 * @return string
	 * @throws \BadMethodCallException
	 */
	public function getRaw()
	{
		if (empty($this->_raw)) {
			throw new \BadMethodCallException('Raw job definition could not be generated for '
				. 'incomplete object (either expression or command fields are missing)');
		}
		
		return $this->_raw;
	}
	
	/**
	 * Retrieves expression part.
	 * 
	 * @return string
	 */
	public function getExpression()
	{
		return $this->_expression;
	}
	
	/**
	 * Retrieves command part.
	 * 
	 * @return string
	 */
	public function getCommand()
	{
		return $this->_command;
	}
	
	/**
	 * Retrieves job schedule status (paused or ready to run).
	 * 
	 * @return string
	 */
	public function getIsPaused()
	{
		return $this->_isPaused;
	}
	
	/**
	 * Retrieves comment.
	 * 
	 * @return string
	 */
	public function getComment()
	{
		return $this->_comment;
	}
	
	/**
	 * Retrieves crc32 generated hash unique to this job.
	 * 
	 * @return string
	 */
	public function getHash()
	{
		return $this->_hash;
	}
	
	/**
	 * Rebuilds internal raw job representation from parts and returns it.
	 * 
	 * @return string
	 */
	protected function _updateRaw()
	{
		if ($this->_command && $this->_expression) {
			$newRaw = ($this->_comment ?  '# ' . ltrim($this->_comment, '# ') . $this->_lineSeparator : '')
					. ($this->_isPaused ? '# ' : '')
					. $this->_expression . ' ' . $this->_command
					. $this->_lineSeparator;
			
			// Update raw job representation (this in turn refreshes the hash)
			$this->setRaw($newRaw);
		}
		
		return $this;
	}
	
	/**
	 * Generates unique hash for this job using crc32.
	 * 
	 * @return Job
	 */
	protected function _generateHash()
	{
		$this->_hash = hash('crc32', $this->_raw);
		return $this;
	}
}