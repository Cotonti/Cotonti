<?php
/**
 * CoTemplate class library. Fast and lightweight block template engine.
 * - Compatible with XTemplate (http://www.phpxtemplate.org)
 * - Compiling into PHP objects
 * - Cotonti special
 *
 * @package API - CoTemplate
 * @version 2.8.0
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

/**
 * Minimalistic XTemplate implementation for Cotonti
 */
class XTemplate
{
	/**
	 * @var string Template file name
	 */
	public $filename = '';
	/**
	 * @var array Assigned template vars
	 */
	public $vars = array();
	/**
	 * @var array Blocks
	 */
	protected $blocks = array();
	/**
	 * @var array Blocks already displayed (for debug mode)
	 */
	protected $displayed_blocks = array();
	/**
	 * Maps block paths to actual array indices.
	 * @var array Index for quick block search.
	 */
	protected $index = array();
	/**
	 * Contains a list of names of all tags present in the template
	 * @var array
	 */
	protected $tags = null;
	/**
	 * @var bool Enables disk caching of precompiled templates
	 */
	protected static $cache_enabled = false;
	/**
	 * @var string Cache directory path
	 */
	protected static $cache_dir = '';
	/**
	 * @var array Stores debug data
	 */
	protected static $debug_data = array();
	/**
	 * @var boolean Enables debug dumping
	 */
	protected static $debug_mode = false;
	/**
	 * @var boolean Prints debug mode screen
	 */
	protected static $debug_output = false;
	/**
	 * @var bool Indicates that root-level blocks were found during another run
	 */
	private $found = false;

	/**
	 * Simplified constructor
	 *
	 * @param string $path Template file name
	 */
	public function __construct($path = NULL)
	{
		// Apply theme redefinitions if necessary
		global $theme_reload;
		if (is_array($theme_reload))
		{
			foreach($theme_reload as $key_reload => $val_reload)
			{
				$GLOBALS[$key_reload] = (is_array($GLOBALS[$key_reload]) && is_array($val_reload)) ? array_merge($GLOBALS[$key_reload], $val_reload) : $val_reload;
			}
		}
		if (is_string($path))
		{
			$this->restart($path);
		}
	}

	/**
	 * TPL code representation of the entire CoTemplate object for debugging
	 *
	 * @return string
	 */
	public function __toString()
	{
		$str = '';
		foreach ($this->blocks as $name => $block)
		{
			$str .= "<!-- BEGIN: $name -->\n" . $block->__toString() . "<!-- END: $name -->\n";
		}
		return $str;
	}

	/**
	 * Assigns a template variable or an array of them
	 *
	 * @param mixed $name Variable name or array of values
	 * @param mixed $val Tag value if $name is not an array
	 * @param string $prefix An optional prefix for variable keys
	 * @return XTemplate $this object for call chaining
	 */
	public function assign($name, $val = NULL, $prefix = '')
	{
		if (is_array($name))
		{
			foreach ($name as $key => $val)
			{
				$this->vars[$prefix.$key] = $val;
			}
		}
		else
		{
			$this->vars[$prefix.$name] = $val;
		}
		return $this;
	}

	/**
	 * Returns debug data dumped by CoTemplate if debug option is on.
	 * Debug data has the following format:
	 * <code>
	 * array(
	 * 	'filename.tpl' => array(
	 * 		'BLOCK.NAME' => array(
	 * 			'TAG_NAME' => 'tag value',
	 * 			// ...
	 * 		),
	 * 		// ...
	 * 	),
	 * 	// ...
	 * );
	 * </code>
	 *
	 * @return array Debug dump
	 */
	public static function debugData()
	{
		return self::$debug_data;
	}

	/**
	 * Debugging output of a tag name and current value
	 *
	 * @param string $name Tag name
	 * @param mixed $value Tag value, will be casted to string
	 * @return string A list elemented for debug output
	 */
	public static function debugVar($name, $value)
	{
		if (is_numeric($value))
		{
			$val_disp = (string) $value;
		}
		elseif(is_object($value))
		{
			$val_disp = get_class ($value). ' ' . json_encode( (array)$value );;
		}
		else
		{
			if (!is_string($value))
			{
				$value = (string) $value;
			}
			$val_disp = '&quot;' . htmlspecialchars($value) . '&quot;';
		}
		return  '<li>{' . htmlspecialchars($name) . '} =&gt; <em>' . $val_disp . '</em></li>';
	}

	/**
	 * Returns current template variable value
	 *
	 * @param string $name Variable name
	 * @return mixed
	 */
	public function get($name)
	{
		return $this->vars[$name];
	}

	/**
	 * Returns the list of names of all tags present in the template
	 * @return array
	 */
	public function getTags()
	{
		if (is_null($this->tags))
		{
			// Collect all tags
			$this->tags = array();
			foreach ($this->blocks as $block)
			{
				$this->tags = array_merge($this->tags, $block->getTags());
			}
		}
		return array_keys($this->tags);
	}

	/**
	 * Returns TRUE if the block is present in template or FALSE otherwise
	 * @param string $name Full block name including dots and parent blocks
	 * @return boolean
	 */
	public function hasBlock($name)
	{
		return isset($this->index[$name]);
	}

	/**
	 * Returns TRUE if the tag is present in template or FALSE otherwise
	 * @param string $name Tag name (case-sensitive)
	 * @return boolean
	 */
	public function hasTag($name)
	{
		if (is_null($this->tags))
		{
			$this->getTags();
		}
		return isset($this->tags[$name]);
	}

	/**
	 * Initializes static class configuration.
	 *
	 * Options:
	 * * cache - Enable template pre-compilation on disk.
	 * * cache_dir - Directory to store pre-compiled templates.
	 * * cleanup - Cleanup HTML output removing comments, spaces and blanks.
	 * * debug - Enable dumping debug information.
	 * * debug_output - Switch output to TPL-debug mode.
	 *
	 * Default values:
	 * <code>
	 * $options = array(
	 *		'cache'        => false,
	 *		'cache_dir'    => '',
	 *		'cleanup'      => false,
	 *		'debug'        => false,
	 *		'debug_output' => false,
	 *	);
	 * </code>
	 *
	 * @param array $options CoTemplate options
	 */
	public static function init($options = array())
	{
		$defaults = array(
			'cache'        => false,
			'cache_dir'    => '',
			'cleanup'      => false,
			'debug'        => false,
			'debug_output' => false,
		);

		$options = array_merge($defaults, $options);

		self::$cache_enabled = $options['cache'];
		self::$cache_dir     = $options['cache_dir'];
		self::$debug_mode    = $options['debug'];
		self::$debug_output  = $options['debug_output'];
		Cotpl_data::init($options['cleanup']);
	}

	/**
	 * restart() replace callback for FILE inclusion
	 *
	 * @param array $m PCRE matches
	 * @return string
	 */
	private static function restart_include_files($m)
	{
		$fname = preg_replace_callback('`\{((?:[\w\.\-]+)(?:\|.+?)?)\}`', 'XTemplate::substitute_var', $m[2]);
		if (preg_match('`\.tpl$`i', $fname) && file_exists($fname))
		{
			$code = cotpl_read_file($fname);
			if ($code[0] == chr(0xEF) && $code[1] == chr(0xBB) && $code[2] == chr(0xBF)) $code = mb_substr($code, 0);
			$code = preg_replace_callback('`\{FILE\s+("|\')(.+?)\1\}`', 'XTemplate::restart_include_files', $code);
			return $code;
		}
		return $fname;
	}

	/**
	 * restart() replace callback for root-level blocks
	 *
	 * @param array $m PCRE matches
	 * @return string
	 */
	private function restart_root_blocks($m)
	{
		$name = $m[1];
		$text = trim($m[2]);
		$this->index[$name] = array($name);
		$this->blocks[$name] = new Cotpl_block($text, $this->index, array($name));
		$this->found = true;
		return '';
	}

	/**
	 * Loads template file structure into memory
	 *
	 * @param string $path Template file path
	 * @return XTemplate $this object for call chaining
	 */
	public function restart($path)
	{
		if (!file_exists($path))
		{
			throw new Exception("Template file not found: $path");
			return false;
		}
		$this->filename = $path;
		$this->vars = array();
		$cache_path = self::$cache_dir . '/templates/' . str_replace(array('./', '/'), '_', $path);
		$cache_idx = $cache_path . '.idx';
		$cache_tags = $cache_path . '.tags';
		if (!self::$cache_enabled || !file_exists($cache_path) || filesize($cache_path) == 0 || !file_exists($cache_idx) || filesize($cache_idx) == 0 || !file_exists($cache_tags) || filesize($cache_tags) == 0 || filemtime($path) > filemtime($cache_path))
		{
			$this->blocks = array();
			$this->index = array();
			$code = cotpl_read_file($path);

			$this->compile($code);

			if (self::$cache_enabled)
			{
                $cache_dir = self::$cache_dir . '/templates/';
                if (!empty(self::$cache_dir) && !file_exists($cache_dir)) mkdir($cache_dir, 0755, true);

				if (is_writeable($cache_dir))
				{
					file_put_contents($cache_path, serialize($this->blocks));
					file_put_contents($cache_idx, serialize($this->index));
					$this->getTags();
					file_put_contents($cache_tags, serialize($this->tags));
				}
				else
				{
					throw new Exception('Your "' . $cache_dir . '" is not writable');
				}
			}
		}
		else
		{
			$this->blocks = unserialize(cotpl_read_file($cache_path));
			$this->index = unserialize(cotpl_read_file($cache_idx));
			$this->tags = unserialize(cotpl_read_file($cache_tags));
		}
		return $this;
	}

	/**
	 * Compiles the template from raw TPL code. Example:
	 *
	 * <code>
	 * $raw_tpl = file_get_contents('some/file.tpl');
	 * // Process $raw_tpl code here
	 * $t = new XTemplate();
	 * $t->compile($raw_tpl);
	 * // Use $t as normal XTemplate object
	 * </code>
	 *
	 * @param  string $code Raw template source code
	 * @return XTemplate $this object for call chaining
	 */
	public function compile($code)
	{
		// Remove BOM if present
		if ($code[0] == chr(0xEF) && $code[1] == chr(0xBB) && $code[2] == chr(0xBF)) $code = mb_substr($code, 0);
		// FILE includes
		$code = preg_replace_callback('`\{FILE\s+("|\')(.+?)\1\}`', 'XTemplate::restart_include_files', $code);
		// Get root-level blocks
		do
		{
			$this->found = false;
			$code = preg_replace_callback('`<!--\s*BEGIN:\s*([\w_]+)\s*-->(.*?)<!--\s*END:\s*\1\s*-->`s',
				array($this, 'restart_root_blocks'), $code);
		} while($this->found);
		return $this;
	}

	/**
	 * PCRE callback which immediately subsitutes a TPL var with its value
	 *
	 * @param array $m PCRE matches
	 * @return string
	 */
	private static function substitute_var($m)
	{
		$var = new Cotpl_var($m[1]);
		return $var->evaluate();
	}

	/**
	 * Prints a parsed block
	 *
	 * @param string $block Block name
	 * @return XTemplate $this object for call chaining
	 */
	public function out($block = 'MAIN')
	{
		if (self::$debug_mode && self::$debug_output)
		{
			// Print debug stuff for current file
			$file = basename($this->filename);
			echo "<h1>$file</h1>";
			foreach (self::$debug_data[$file] as $block => $tags) {
				$block_name = $file . ' / ' . str_replace('.', ' / ', $block);
				echo "<h2>$block_name</h2>";
				echo "<ul>";
				foreach ($tags as $key => $val)
				{
					if (is_array($val))
					{
						// One level of nesting is supported
						foreach ($val as $key2 => $val2)
						{
							echo self::debugVar($key . '.' . $key2, $val2);
						}
					}
					else
					{
						echo self::debugVar($key, $val);
					}
				}
				echo "</ul>";
			}
		}
		else
		{
			echo $this->text($block);
		}
		return $this;
	}

	/**
	 * Parses a block
	 *
	 * @param string $block Block name
	 * @return XTemplate $this object for call chaining
	 */
	public function parse($block = 'MAIN')
	{
		$path = $this->index[$block];
		if ($path)
		{
			$blk = $this->blocks[array_shift($path)];
			foreach ($path as $node)
			{
				if (is_object($blk))
				{
					$blk =& $blk->blocks[$node];
				}
				else
				{
					$blk =& $blk[$node];
				}
			}
			$blk->parse($this);
		}
		//else throw new Exception("Block $block is not found in " . $this->filename);

		if (self::$debug_mode)
		{
			if (!in_array($block, $this->displayed_blocks))
			{
				$file = basename($this->filename);
				$tags = $this->vars;
				ksort($tags);
				foreach ($tags as $key => $val)
				{
					if (is_array($val))
					{
						// One level of nesting is supported
						foreach ($val as $key2 => $val2)
						{
							if (is_string($val2) && mb_strlen($val2) > 60)
							{
								$val2 = mb_substr($val2, 0, 60) . '...';
							}
							self::$debug_data[$file][$block][$key . '.' . $key2] = $val2;
						}
					}
					else
					{
						if (is_string($val) && mb_strlen($val) > 60)
						{
							$val = mb_substr($val, 0, 60) . '...';
						}
						self::$debug_data[$file][$block][$key] = $val;
					}
				}
				unset($tags);
				$this->displayed_blocks[] = $block;
			}
		}
		return $this;
	}

	/**
	 * Clears a parset block data
	 *
	 * @param string $block Block name
	 * @return XTemplate $this object for call chaining
	 */
	public function reset($block = 'MAIN')
	{
		$path = $this->index[$block];
		if ($path)
		{
			$blk = $this->blocks[array_shift($path)];
			foreach ($path as $node)
			{
				if (is_object($blk))
				{
					$blk =& $blk->blocks[$node];
				}
				else
				{
					$blk =& $blk[$node];
				}
			}
			$blk->reset();
		}
		//else throw new Exception("Block $block is not found in " . $this->filename);
		return $this;
	}

	/**
	 * Returns parsed block HTML
	 *
	 * @param string $block Block name
	 * @return string
	 */
	public function text($block = 'MAIN')
	{
		$path = $this->index[$block];
		if ($path)
		{
			$blk = $this->blocks[array_shift($path)];
			foreach ($path as $node)
			{
				if (is_object($blk))
				{
					$blk =& $blk->blocks[$node];
				}
				else
				{
					$blk =& $blk[$node];
				}
			}
			return $blk->text($this);
		}
		else
		{
			// throw new Exception("Block $block is not found in " . $this->filename);
			return '';
		}
	}
}

/**
 * CoTemplate block class
 */
class Cotpl_block
{
	/**
	 * @var array Parsed block instances
	 */
	protected $data = array();
	/**
	 * @var array Contained blocks
	 */
	public $blocks = array();

	/**
	 * Block constructor
	 *
	 * @param string $code TPL contents
	 * @param array $index Reference to CoTemplate index being built
	 * @param array $path Path to current block
	 */
	public function __construct($code, &$index, $path)
	{
		$this->compile($code, $this->blocks, $index, $path);
	}

	/**
	 * TPL code representation for debugging
	 *
	 * @return string
	 */
	public function  __toString()
	{
		return $this->blocks_toString($this->blocks);
	}

	/**
	 * Generates string representation for given set of blocks
	 *
	 * @param array $blocks Cotpl block objects (logical and data too)
	 * @return string
	 */
	protected function blocks_toString(&$blocks)
	{
		$str = '';
		foreach ($blocks as $name => $block)
		{
			if (is_string($name) && !is_numeric($name))
			{
				$str .= "<!-- BEGIN: $name -->\n" . $block->__toString() . "<!-- END: $name -->\n";
			}
			else
			{
				$str .= $block->__toString();
			}
		}
		return $str;
	}

	/**
	 * Compiles TPL text into CoTemplate objects
	 *
	 * @param string $code TPL source
	 * @param array $blocks Array of Ctpl_block/Ctpl_data objects
	 * @param array $index CoTemplate index
	 * @param array $path Current path
	 */
	protected function compile($code, &$blocks, &$index, $path)
	{
		// Find nested blocks and conditionals
		$i = 0;
		do
		{
			$block_found = false;
			$loop_found = false;
			$log_found = false;
			// finds first block for further analizing
			preg_match('`<!--\s*(?:(BEGIN):\s*([\w_]+)|(FOR|IF)\s+).+?-->.*?<!--\s*(?:END:\s*\2|END\3)\s*-->`s', $code,$mt);
			if ($mt[1])
			{
				$block_found = true;
			}
			elseif ($mt[3]==='IF')
			{
				$log_found = true;
			}
			elseif ($mt[3]==='FOR')
			{
				$loop_found = true;
			}
			else
			{
				// No blocks found
				if (!empty($code))
				{
					$blocks[$i++] = new Cotpl_data($code);
					$code = '';
				}
			}
			if ($block_found && preg_match('`(?:(?:(?<=\n|\r)[^\S\n\r]*)(?=<!--\s*BEGIN:\s*(?:[\w_]+)\s*-->(?:\s*(?:\r?\n|\r))))?<!--\s*BEGIN:\s*([\w_]+)\s*-->(?:\s*(?:\r?\n|\r))?((?:.*?))?((?<=\n|\r)[^\S\n\r]*)?<!--\s*END:\s*\1\s*-->(?(3)(\s*(?:\r?\n|\r))?)`s', $code, $mt))
			{
				$block_pos = mb_strpos($code, $mt[0]);
				$block_mt = $mt;
				$block_name = $mt[1];
				// Extract preceeding plain data chunk
				if ($block_pos > 0)
				{
					$chunk = mb_substr($code, 0, $block_pos);
					if (!empty($chunk))
					{
						$blocks[$i++] = new Cotpl_data($chunk);
					}
				}
				// Extract the block
				$bpath = $path;
				array_push($bpath, $block_name);
				$index[cotpl_index_glue($bpath)] = $bpath;
				$blocks[$block_name] = new Cotpl_block($block_mt[2], $index, $bpath);
				$code = mb_substr($code, $block_pos + mb_strlen($block_mt[0]));
			}
			if ($loop_found && preg_match('`((?:(?<=\n|\r)[^\S\n\r]*)(?=<!--\s*FOR\s+[^>]+\s*-->(?:\s*(?:\r?\n|\r))))?<!--\s*FOR\s+(.+?)\s*-->(?(1)(?:\s*(?:\r?\n|\r))?)`', $code, $mt))
			{
				$loop_pos = mb_strpos($code, $mt[0]);
				$loop_len = mb_strlen($mt[0]);
				$loop_mt = $mt;
				// Extract preceeding plain data chunk
				if ($loop_pos > 0)
				{
					$chunk = mb_substr($code, 0, $loop_pos);
					if (!empty($chunk))
					{
						$blocks[$i++] = new Cotpl_data($chunk);
					}
				}
				// Get the FOR loop contents
				$scope = 1;
				$loop_code = '';
				$code = mb_substr($code, $loop_pos + $loop_len);
				while ($scope > 0 && preg_match('`((?:(?<=\n|\r)[^\S\n\r]*)(?=<!--\s*(?:FOR\s+[^>]|ENDFOR)\s*-->(?:\s*(?:\r?\n|\r))))?<!--\s*(FOR\s+.+?|ENDFOR)\s*-->(?(1)(?:\s*(?:\r?\n|\r))?)`', $code, $m))
				{
					$m_pos = mb_strpos($code, $m[0]);
					$m_len = mb_strlen($m[0]);
					if ($m[2] === 'ENDFOR')
					{
						$scope--;
					}
					else
					{
						$scope++;
					}
					$postfix_len = $scope === 0 ? 0 : $m_len;
					$loop_code .= mb_substr($code, 0, $m_pos + $postfix_len);
					$code = mb_substr($code, $m_pos + $m_len);
				}
				if ($scope === 0)
				{
					$bpath = $path;
					array_push($bpath, $i);
					$blocks[$i++] = new Cotpl_loop($loop_mt[2], $loop_code, $index, $bpath);
					//$code = trim($code, "\t\r\n");
				}
				else
				{
					throw new Exception('Loop ' . htmlspecialchars($loop_mt[0]) . ' not closed');
				}

			}
			if ($log_found && preg_match('`((?:(?<=\n|\r)[^\S\n\r]*)(?=<!--\s*IF\s+[^>]+\s*-->(?:\s*(?:\r?\n|\r))))?<!--\s*IF\s+(.+?)\s*-->(?(1)(?:\s*(?:\r?\n|\r))?)`', $code, $mt))
			{
				$log_pos = mb_strpos($code, $mt[0]);
				$log_len = mb_strlen($mt[0]);
				$log_mt = $mt;
							// Extract preceeding plain data chunk
				if ($log_pos > 0)
				{
					$chunk = mb_substr($code, 0, $log_pos);
					if (!empty($chunk))
					{
						$blocks[$i++] = new Cotpl_data($chunk);
					}
				}
				// Get the IF/ELSE contents
				$scope = 1;
				$if_code = '';
				$else_code = '';
				$else = false;
				$code = mb_substr($code, $log_pos + $log_len);
				while ($scope > 0 && preg_match('`((?:(?<=\n|\r)[^\S\n\r]*)(?=<!--\s*(?:IF\s+[^>]+?|ELSE|ENDIF)\s*-->(?:\s*(?:\r?\n|\r))))?<!--\s*(IF\s+.+?|ELSE|ENDIF)\s*-->(?(1)(?:\s*(?:\r?\n|\r))?)`', $code, $m))
				{
						$m_pos = mb_strpos($code, $m[0]);
						$m_len = mb_strlen($m[0]);
						if ($m[2] === 'ENDIF')
						{
							$scope--;
						}
						elseif ($m[2] === 'ELSE')
						{
							if ($scope === 1)
							{
								$if_code .= mb_substr($code, 0, $m_pos);
								$else = true;
								$code = mb_substr($code, $m_pos + $m_len);
								continue;
							}
						}
						else
						{
							$scope++;
						}
						$postfix_len = $scope === 0 ? 0 : $m_len;
						if ($else === false)
						{
							$if_code .= mb_substr($code, 0, $m_pos + $postfix_len);
						}
						else
						{
							$else_code .= mb_substr($code, 0, $m_pos + $postfix_len);
						}
						$code = mb_substr($code, $m_pos + $m_len);
				}
				if ($scope === 0)
				{
					$bpath = $path;
					array_push($bpath, $i);
					$blocks[$i++] = new Cotpl_logical($log_mt[2], $if_code, $else_code, $index, $bpath);
				}
				else
				{
					throw new Exception('Logical block ' . htmlspecialchars($log_mt[0]) . ' not closed');
				}
			}
		}
		while (!empty($code));
	}

	/**
	 * Returns the list of tag names present in the block
	 * @return array
	 */
	public function getTags()
	{
		$list = array();
		foreach ($this->blocks as $block)
		{
			if ($block instanceof Cotpl_data || $block instanceof Cotpl_block)
			{
				$list = array_merge($list, $block->getTags());
			}
		}
		return $list;
	}

	/**
	 * Parses block contents
	 *
	 * @param XTemplate $tpl Reference to XTemplate object
	 */
	public function parse($tpl)
	{
		foreach ($this->blocks as $block)
		{
			$data .= $block->text($tpl);
		}
		$this->data[] = $data;
	}

	/**
	 * Clears parsed block data
	 */
	public function reset($path = array())
	{
		$this->data = array();
	}

	/**
	 * Returns parsed block HTML
	 *
	 * @param XTemplate $tpl XTemplate object reference
	 * @return string
	 */
	public function text($tpl)
	{
		$text = implode('', $this->data);
		$this->data = array();
		return $text;
	}
}

/**
 * A simple nameless block of data which may parse variables
 */
class Cotpl_data
{
	/**
	 * @var array Block data consisting of strings and Cotpl_vars
	 */
	protected $chunks = array();
	/**
	 * @var bool Enables space removal for compact output
	 */
	protected static $cleanup_enabled = false;

	/**
	 * Block constructor
	 *
	 * @param string $code TPL contents
	 */
	public function __construct($code)
	{
		if (self::$cleanup_enabled)
		{
			$code = $this->cleanup($code);
		}
		$chunks = preg_split('`(?<!\{)(\{(?:[\w\.\-]+)(?:\|.+?)?\})`', $code, -1, PREG_SPLIT_DELIM_CAPTURE);
		foreach ($chunks as $chunk)
		{
			if (preg_match('`^(?<!\{)\{((?:[\w\.\-]+)(?:\|.+?)?)\}$`', $chunk, $m))
			{
				$this->chunks[] = new Cotpl_var($m[1]);
			}
			else
			{
				$this->chunks[] = $chunk;
			}
		}
	}

	/**
	 * TPL representation for debugging
	 *
	 * @return string
	 */
	public function  __toString()
	{
		$str = '';
		foreach ($this->chunks as $chunk)
		{
			if ($chunk instanceof Cotpl_var)
			{
				$str .= $chunk->__toString();
			}
			else
			{
				$str .= $chunk;
			}
		}
		return $str . "\n";
	}

	/**
	 * Returns the list of tag names present in data block
	 * @return array
	 */
	public function getTags()
	{
		$list = array();
		foreach ($this->chunks as $chunk)
		{
			if ($chunk instanceof Cotpl_var)
			{
				$list[$chunk->name] = true;
			}
		}
		return $list;
	}

	/**
	 * Initializes static class configuration
	 * @param bool Enables space removal for compact output
	 */
	public static function init($cleanup_enabled = false)
	{
		self::$cleanup_enabled = $cleanup_enabled;
	}

	/**
	 * Returns parsed block contents
	 *
	 * @param XTemplate $tpl Reference to XTemplate object
	 * @return string Block data
	 */
	public function text($tpl)
	{
		$data = '';
		foreach ($this->chunks as $chunk)
		{
			if ($chunk instanceof Cotpl_var)
			{
				$data .= $chunk->evaluate($tpl);
			}
			else
			{
				$data .= $chunk;
			}
		}
		return $data;
	}

	/**
	 * Trims spaces before and after tags
	 *
	 * @param string $html Source HTML
	 * @return string Cleaned HTML
	 */
	private function cleanup($html)
	{
		$html = preg_replace('#\n\s+#', ' ', $html);
		$html = preg_replace('#[\r\n\t]+<#', '<', $html);
		$html = preg_replace('#>[\r\n\t]+#', '>', $html);
		$html = preg_replace('# {2,}#', ' ', $html);
		return $html;
	}
}

// Integer keys are faster on evaluation

/**
 * Operator "(" opening parenthesis
 */
define('COTPL_OP_OPEN', 1);
/**
 * Operator ")" closing parenthesis
 */
define('COTPL_OP_CLOSE', 2);
/**
 * Operator "AND"
 */
define('COTPL_OP_AND', 11);
/**
 * Operator "OR"
 */
define('COTPL_OP_OR', 12);
/**
 * Operator "XOR"
 */
define('COTPL_OP_XOR', 13);
/**
 * Operator "!" negation
 */
define('COTPL_OP_NOT', 14);
/**
 * Operator "=="
 */
define('COTPL_OP_EQ', 21);
/**
 * Operator "!="
 */
define('COTPL_OP_NE', 22);
/**
 * Operator "<"
 */
define('COTPL_OP_LT', 23);
/**
 * Operator ">"
 */
define('COTPL_OP_GT', 24);
/**
 * Operator "<="
 */
define('COTPL_OP_LE', 25);
/**
 * Operator ">="
 */
define('COTPL_OP_GE', 26);
/**
 * Operator "HAS"
 */
define('COTPL_OP_HAS', 31);
/**
 * Operator "~=" contains substring
 */
define('COTPL_OP_CONTAINS', 32);
/**
 * Operator "+"
 */
define('COTPL_OP_ADD', 41);
/**
 * Operator "-"
 */
define('COTPL_OP_SUB', 42);
/**
 * Operator "*"
 */
define('COTPL_OP_MUL', 43);
/**
 * Operator "/"
 */
define('COTPL_OP_DIV', 44);
/**
 * Operator "%"
 */
define('COTPL_OP_MOD', 45);

/**
 * CoTemplate logical expression
 */
class Cotpl_expr
{
	/**
	 * @var array Postfix expression stack
	 */
	protected $tokens = array();

	/**
	 * @var array Operator encoding map
	 */
	protected static $operators = array(
		'('		=> COTPL_OP_OPEN,
		')'		=> COTPL_OP_CLOSE,
		'AND'	=> COTPL_OP_AND,
		'OR'	=> COTPL_OP_OR,
		'XOR'	=> COTPL_OP_XOR,
		'!'		=> COTPL_OP_NOT,
		'=='	=> COTPL_OP_EQ,
		'!='	=> COTPL_OP_NE,
		'<'		=> COTPL_OP_LT,
		'>'		=> COTPL_OP_GT,
		'<='	=> COTPL_OP_LE,
		'>='	=> COTPL_OP_GE,
		'HAS'	=> COTPL_OP_HAS,
		'~='	=> COTPL_OP_CONTAINS,
		'+'		=> COTPL_OP_ADD,
		'-'		=> COTPL_OP_SUB,
		'*'		=> COTPL_OP_MUL,
		'/'		=> COTPL_OP_DIV,
		'%'		=> COTPL_OP_MOD
	);

	/**
	 * @var array Operator precedence (priority) mapping
	 */
	protected static $precedence = array(
		COTPL_OP_OPEN => -1,
		COTPL_OP_MUL => 1, COTPL_OP_DIV => 1, COTPL_OP_MOD => 1,
		COTPL_OP_ADD => 2, COTPL_OP_SUB => 2,
		COTPL_OP_HAS => 3, COTPL_OP_CONTAINS => 3,
		COTPL_OP_EQ => 4, COTPL_OP_NE => 4, COTPL_OP_LT => 4, COTPL_OP_GT => 4, COTPL_OP_LE => 4, COTPL_OP_GE => 4,
		COTPL_OP_NOT => 5,
		COTPL_OP_AND => 6,
		COTPL_OP_OR => 7, COTPL_OP_XOR => 7,
		COTPL_OP_CLOSE => 99
	);

	/**
	 * Constructs postfix expression from infix string
	 * @param string $text Logical expression
	 */
	public function __construct($text)
	{
		// Fix possible syntactic problems with missing spaces
		$text = str_replace('(', ' ( ', $text);
		$text = str_replace(')', ' ) ', $text);
		$text = str_replace('!{', ' ! {', $text);
		$text = str_replace('!(', ' ! (', $text);
		// Splitting into words
		$words = cotpl_tokenize($text, array(' ', "\t"));
		$operators = array_keys(self::$operators);
		// Splitting infix into tokens
		$tokens = array();
		foreach ($words as $word)
		{
			$token = array();
			if (in_array($word, $operators, true))
			{
				$op = self::$operators[$word];
				$token['op'] = $op;
				$token['prec'] = self::$precedence[$op];
			}
			else
			{
				if (preg_match('`^{(.+?)}$`', $word, $mt))
				{
					$token['var'] = new Cotpl_var($mt[1]);
				}
				elseif (preg_match('`("|\')(.+?)\1`', $word, $mt))
				{
					$token['var'] = $mt[2];
				}
				elseif (is_numeric($word))
				{
					$token['var'] = (double) $word;
				}
				else
				{
					$token['var'] = $word;
				}
				$token['prec'] = 0;
			}
			$tokens[] = $token;
		}
		// Infix to postfix
		$lim = count($tokens) - 1;
		for ($i = 0; $i < $lim; $i++)
		{
			if ($tokens[$i]['prec'] > $tokens[$i + 1]['prec'])
			{
				$j = $i;
				$scopes = 0;
				while ($j < $lim && ($scopes > 0 || $tokens[$j]['prec'] > $tokens[$j + 1]['prec']))
				{
					$tmp = $tokens[$j];
					$tokens[$j] = $tokens[$j + 1];
					$tokens[$j + 1] = $tmp;
					$scopes += (($tokens[$j]['op'] == COTPL_OP_OPEN) ? 1 : 0)
						- (($tokens[$j]['op'] == COTPL_OP_CLOSE) ? 1 : 0);
					$j++;
				}
				$i--;
			}
		}
		// Save
		$this->tokens = $tokens;
	}

	/**
	 * Represents in postfix form rather than infix, so don't be confused
	 *
	 * @return string
	 */
	public function  __toString()
	{
		$str = '';
		foreach ($this->tokens as $tok)
		{
			$str .= $tok['var'] ? (string) $tok['var'] : array_search($tok['op'], self::$operators);
		}
		return $str;
	}

	/**
	 * Evaluates the logical expression
	 *
	 * @param XTemplate $tpl Reference to CoTemplate storing local variables
	 * @return bool
	 */
	public function evaluate($tpl)
	{
		$stack = array();
		foreach ($this->tokens as $token)
		{
			switch ($token['op'])
			{
				case COTPL_OP_ADD:
					array_push($stack, array_pop($stack) + array_pop($stack));
					break;
				case COTPL_OP_AND:
					array_push($stack, array_pop($stack) && array_pop($stack));
					break;
				case COTPL_OP_CONTAINS:
					$needle = array_pop($stack);
					$haystack = array_pop($stack);
					array_push($stack, is_string($haystack) && is_string($needle)
						&& mb_strpos($haystack, $needle) !== false);
					break;
				case COTPL_OP_DIV:
					$divisor = array_pop($stack);
					$dividend = array_pop($stack);
					array_push($stack, $dividend / $divisor);
					break;
				case COTPL_OP_EQ:
					array_push($stack, array_pop($stack) == array_pop($stack));
					break;
				case COTPL_OP_GE:
					$arg2 = array_pop($stack);
					$arg1 = array_pop($stack);
					array_push($stack, $arg1 >= $arg2);
					break;
				case COTPL_OP_GT:
					$arg2 = array_pop($stack);
					$arg1 = array_pop($stack);
					array_push($stack, $arg1 > $arg2);
					break;
				case COTPL_OP_HAS:
					$needle = array_pop($stack);
					$haystack = array_pop($stack);
					array_push($stack, is_array($haystack) && in_array($needle, $haystack));
					break;
				case COTPL_OP_LE:
					$arg2 = array_pop($stack);
					$arg1 = array_pop($stack);
					array_push($stack, $arg1 <= $arg2);
					break;
				case COTPL_OP_LT:
					$arg2 = array_pop($stack);
					$arg1 = array_pop($stack);
					array_push($stack, $arg1 < $arg2);
					break;
				case COTPL_OP_MOD:
					$arg2 = array_pop($stack);
					$arg1 = array_pop($stack);
					array_push($stack, $arg1 % $arg2);
					break;
				case COTPL_OP_MUL:
					array_push($stack, array_pop($stack) * array_pop($stack));
					break;
				case COTPL_OP_NE:
					array_push($stack, array_pop($stack) != array_pop($stack));
					break;
				case COTPL_OP_NOT:
					array_push($stack, !array_pop($stack));
					break;
				case COTPL_OP_OR:
					$arg2 = array_pop($stack);
					$arg1 = array_pop($stack);
					array_push($stack, ($arg1 || $arg2));
					break;
				case COTPL_OP_SUB:
					$sub = array_pop($stack);
					$min = array_pop($stack);
					array_push($stack, $min - $sub);
					break;
				case COTPL_OP_XOR:
					$arg2 = array_pop($stack);
					$arg1 = array_pop($stack);
					array_push($stack, ($arg1 xor $arg2));
					break;
				case COTPL_OP_OPEN:
				case COTPL_OP_CLOSE:
					break;
				default:
					array_push($stack, is_object($token['var']) ? $token['var']->evaluate($tpl) : $token['var']);
					break;
			}
		}
		return (bool) array_pop($stack);
	}
}

/**
 * CoTemplate run-time conditional block class
 */
class Cotpl_logical extends Cotpl_block
{
	/**
	 * @var Cotpl_expr Condition expression
	 */
	protected $expr = null;

	/**
	 * Constructs logical block structure from strings
	 *
	 * @param string $expr_str Condition string
	 * @param string $if_code IF clause body
	 * @param string $else_code ELSE clause body
	 * @param array $index CoTemplate index
	 * @param array $path Current block path
	 */
	public function __construct($expr_str, $if_code, $else_code, &$index, $path)
	{
		$this->expr = new Cotpl_expr($expr_str);
		if (!empty($if_code))
		{
			$bpath = $path;
			array_push($bpath, 0);
			$this->compile($if_code, $this->blocks[0], $index, $bpath);
		}
		if (!empty($else_code))
		{
			$bpath = $path;
			array_push($bpath, 1);
			$this->compile($else_code, $this->blocks[1], $index, $bpath);
		}
	}

	/**
	 * TPL code representation for debugging
	 *
	 * @return string
	 */
	public function  __toString()
	{
		$str = "<!-- IF " . $this->expr->__toString() . " -->\n";
		$str .= $this->blocks_toString($this->blocks[0]);
		if (count($this->blocks[1]) > 0)
		{
			$str .= "<!-- ELSE -->\n" . $this->blocks_toString($this->blocks[1]);
		}
		$str .= "<!-- ENDIF -->\n";
		return $str;
	}

	/**
	 * Returns the list of tag names present in the block
	 * @return array
	 */
	public function getTags()
	{
		$list = array();
		for ($i = 0; $i < 2; $i++)
		{
			if (is_array($this->blocks[$i]))
			{
				foreach ($this->blocks[$i] as $block)
				{
					if ($block instanceof Cotpl_data || $block instanceof Cotpl_block)
					{
						$list = array_merge($list, $block->getTags());
					}
				}
			}
		}
		return $list;
	}

	/**
	 * Overloads parse()
	 *
	 * @param XTemplate $xtpl Reference to XTemplate object
	 */
	public function parse($xtpl)
	{
		throw new Exception('Calling parse() on logical block');
	}

	/**
	 * Overloads reset()
	 * @param mixed $dummy A stub to match Cotpl_block::reset() declaration (Strict mode)
	 */
	public function reset($dummy = null)
	{
		throw new Exception('Calling reset() on logical block');
	}

	/**
	 * Actually parses a conditional block and returns parsed contents
	 *
	 * @param XTemplate $tpl A reference to XTemplate object containing variables
	 * @return string
	 */
	public function text($tpl)
	{
		$data = '';
		if ($this->expr->evaluate($tpl))
		{
			if ($this->blocks[0])
			{
				foreach ($this->blocks[0] as $block)
				{
					$data .= $block->text($tpl);
				}
			}
		}
		elseif ($this->blocks[1])
		{
			foreach ($this->blocks[1] as $block)
			{
				$data .= $block->text($tpl);
			}
		}
		return $data;
	}
}

/**
 * CoTemplate FOR loop
 */
class Cotpl_loop extends Cotpl_block
{
	/**
	 * Key variable name (optional)
	 * @var string
	 */
	protected $key = '';
	/**
	 * Source set/array variable
	 * @var Cotpl_var
	 */
	protected $set = null;
	/**
	 * Value variable name
	 * @var string
	 */
	protected $val = '';

	/**
	 * Constructs loop block structure from strings
	 *
	 * @param string $header Loop header string
	 * @param string $code Loop body
	 * @param array $index CoTemplate index
	 * @param array $path Current block path
	 */
	public function __construct($header, $code, &$index, $path)
	{
		if (preg_match('`^\{(\w+)\}\s*,\s*\{(\w+)\}\s*IN\s*\{((?:[\w\.\-]+)(?:\|.+?)?)\}$`', $header, $m))
		{
			$this->key = $m[1];
			$this->val = $m[2];
			$this->set = new Cotpl_var($m[3]);
		}
		elseif (preg_match('`^\{(\w+)\}\s*IN\s*\{((?:[\w\.\-]+)(?:\|.+?)?)\}$`', $header, $m))
		{
			$this->val = $m[1];
			$this->set = new Cotpl_var($m[2]);
		}
		$this->compile($code, $this->blocks, $index, $path);
	}

	/**
	 * TPL code representation for debugging
	 *
	 * @return string
	 */
	public function  __toString()
	{
		$header = empty($this->key) ? '{' . $this->val . '}'
				: '{' . $this->key . '}, {' . $this->val . '}';
		$str = "<!-- FOR $header IN " . $this->set->__toString() . " -->\n";
		$str .= $this->blocks_toString($this->blocks);
		$str .= "<!-- ENDFOR -->\n";
		return $str;
	}

	/**
	 * Overloads parse()
	 *
	 * @param XTemplate $xtpl Reference to XTemplate object
	 */
	public function parse($xtpl)
	{
		throw new Exception('Calling parse() on a loop');
	}

	/**
	 * Overloads reset()
	 * @param mixed $dummy A stub to match Cotpl_block::reset() declaration (Strict mode)
	 */
	public function reset($dummy = null)
	{
		throw new Exception('Calling reset() on a loop');
	}

	/**
	 * Actually parses a conditional block and returns parsed contents
	 *
	 * @param XTemplate $tpl A reference to XTemplate object containing variables
	 * @return string
	 */
	public function text($tpl)
	{
		$data = '';
		$set = $this->set->evaluate($tpl);
		if (is_array($set) && $this->blocks)
		{
			foreach ($set as $key => $val)
			{
				$tpl->assign($this->val, $val);
				if (!empty($this->key))
				{
					$tpl->assign($this->key, $key);
				}
				foreach ($this->blocks as $block)
				{
					$data .= $block->text($tpl);
				}
			}
		}
		return $data;
	}
}

/**
 * CoTemplate variable with callback extensions support
 * @property-read string $name Tag name
 */
class Cotpl_var
{
	/**
	 * @var string Variable name
	 */
	protected $name = '';
	/**
	 * @var array Sequence of keys for arrays
	 */
	protected $keys = null;
	/**
	 * @var array Sequential list of callback processors
	 */
	protected $callbacks = null;

	/**
	 * @param string $text Variable code from TPL file
	 */
	public function __construct($text)
	{
		if (mb_strpos($text, '|') !== false)
		{
			$chain = explode('|', $text);
			$text = array_shift($chain);
			foreach ($chain as $cbk)
			{
				if (mb_strpos($cbk, '(') !== false
					&& preg_match('`(\w+)\s*\((.+)\)`', $cbk, $mt))
				{
					$this->callbacks[] = array(
						'name' => $mt[1],
						'args' => cotpl_tokenize(trim($mt[2]), array(',', ' '))
					);
				}
				else
				{
					$this->callbacks[] = str_replace('()', '', $cbk);
				}
			}
		}
		if (mb_strpos($text, '.') !== false)
		{
			$keys = explode('.', $text);
			$text = array_shift($keys);
			$this->keys = $keys;
		}
		$this->name = $text;
	}

	/**
	 * Property getter
	 * @param string $name Property name
	 * @return mixed Property value
	 */
	public function __get($name)
	{
		if (isset($this->{$name}))
		{
			return $this->{$name};
		}
		else
		{
			return null;
		}
	}

	/**
	 * TPL string representation for debugging
	 * @return string
	 */
	public function  __toString()
	{
		$str = '{' . $this->name;
		if (is_array($this->keys))
		{
			$str .= '.' . implode('.', $this->keys);
		}
		if (is_array($this->callbacks))
		{
			foreach ($this->callbacks as $cb)
			{
				if (is_array($cb))
				{
					$str .= '|' . $cb['name'] . '(' . implode(',', $cb['args']) . ')';
				}
				else
				{
					$str .= '|' . $cb;
				}
			}
		}
		$str .= '}';
		return $str;
	}

	/**
	 * Variable debug output handler for {var_name|dump}
	 *
	 * @param mixed $val Var value
	 * @return string
	 */
	private function dump($val)
	{
		$key = $this->keys ? $this->name . '.' . implode('.', $this->keys) : $this->name;
		if ($this->name == 'PHP' && !$this->keys)
		{
			$val =& $GLOBALS;
		}
		return '<ul class="dump">' . self::dump_r($key, $val, 0) . '</ul>';
	}

	/**
	 * Recursively fetches debug representation of a TPL variable
	 *
	 * @param string $key Variable key
	 * @param mixed $val Variable value
	 * @param int $level Current nesting level
	 * @return string
	 */
	private static function dump_r($key, $val, $level)
	{
		if ($level > 5 || $key == 'PHP.GLOBALS')
		{
			return '';
		}
		$ret = '';
		if (is_array($val))
		{
			ksort($val);
			foreach ($val as $key2 => $val2)
			{
				$ret .= self::dump_r($key . '.' . $key2, $val2, $level + 1);
			}
		}
		elseif (is_string($val))
		{
			$ret = XTemplate::debugVar($key, $val);
		}
		return $ret;
	}

	/**
	 * Evaluates a variable
	 *
	 * @param XTemplate $tpl Reference to CoTemplate storing local variables
	 * @return mixed Variable value or NULL if variable was not found
	 */
	public function evaluate($tpl = null)
	{
		if ($this->name === 'PHP')
		{
			$var =& $GLOBALS;
		}
		elseif(!empty($tpl))
		{
			$val = $tpl->vars[$this->name];
			if ($this->keys && (is_array($val) || is_object($val)))
			{
				$var =& $tpl->vars[$this->name];
			}
		}
		if ($this->keys)
		{
			$keys = $this->keys;
			$last_key = array_pop($keys);
			foreach ($keys as $key)
			{
				if (is_object($var))
				{
					$var =& $var->{$key};
				}
				elseif (is_array($var))
				{
					$var =& $var[$key];
				}
				else
				{
					break;
				}
			}
			if (is_object($var))
			{
				$val = $var->{$last_key};
			}
			elseif (is_array($var))
			{
				$val = $var[$last_key];
			}
			else
			{
				$val = null;
			}
		}
		if ($this->callbacks)
		{
			foreach ($this->callbacks as $func)
			{
				if (is_array($func))
				{
					array_walk($func['args'], 'cotpl_callback_replace', $val);
					$f = $func['name'];
					$a = $func['args'];
					if (!function_exists($f))
					{
						return $this->__toString();
					}
					switch (count($a))
					{
						case 0:
							$val = $f();
							break;
						case 1:
							$val = $f($a[0]);
							break;
						case 2:
							$val = $f($a[0], $a[1]);
							break;
						case 3:
							$val =$f($a[0], $a[1], $a[2]);
							break;
						case 4:
							$val = $f($a[0], $a[1], $a[2], $a[3]);
							break;
						default:
							$val = call_user_func_array($f, $a);
							break;
					}
				}
				elseif ($func == 'dump')
				{
					$val = $this->dump($val);
				}
				else
				{
					if (!function_exists($func))
					{
						return $this->__toString();
					}
					$val = isset($val) || is_null($val) ? $func($val) : $func();
				}
			}
		}
		return $val;
	}
}

/**
 * Replaces $this in callback arguments with the template tag value.
 * To be used with array_walk.
 *
 * @param string $arg Callback function argument value
 * @param int $i Callback function argument key
 * @param string $val Tag value
 */
function cotpl_callback_replace(&$arg, $i, $val)
{
	if (mb_strpos($arg, '$this') !== FALSE)
	{
		if (is_array($val) || is_object($val))
		{
			$arg = $val;
		}
		else
		{
			$arg = str_replace('$this', (string)$val, $arg);
		}
	}
}

/**
 * A faster implementation of file_get_contents(). Reads a file into a string.
 * @param string $path File path
 * @return string
 */
function cotpl_read_file($path)
{
	$fp = fopen($path, 'r');
	$size = filesize($path);
	$code = $size > 0 ? fread($fp, $size) : '';
	fclose($fp);
	return $code;
}

/**
 * Glues full block name (block path for parse) from index path
 *
 * @param array $path CoTemplate index path
 * @return string
 */
function cotpl_index_glue($path)
{
	$str = array_shift($path);
	foreach ($path as $node)
	{
		if (!is_numeric($node))
		{
			$str .= '.' . $node;
		}
	}
	return $str;
}

/**
 * Splits a string into tokens by delimiter characters with double and single quotes support.
 * Unicode-aware.
 *
 * @param string $str   Source string
 * @param string $delim Delimiter characters
 * @return array
 */
function cotpl_tokenize($str, $delim = array(' '))
{
	$tokens = array();
	$idx = 0;
	$quote = '';
	$prev_delim = false;
	$len = mb_strlen($str);
	for ($i = 0; $i < $len; $i++)
	{
		$c = mb_substr($str, $i, 1);
		if (in_array($c, $delim))
		{
			if ($quote)
			{
				$tokens[$idx] .= $c;
				$prev_delim = false;
			}
			elseif ($prev_delim)
			{
				continue;
			}
			else
			{
				$idx++;
				$prev_delim = true;
			}
		}
		elseif ($c == '"' || $c == "'")
		{
			if (!$quote)
			{
				$quote = $c;
			}
			elseif ($quote == $c)
			{
				$quote = '';
				if (!isset($tokens[$idx]))
				{
					$tokens[$idx] = '';
				}
			}
			else
			{
				$tokens[$idx] .= $c;
			}
			$prev_delim = false;
		}
		elseif ($c == '{' && !$quote)
		{
			// Avoid variable tokenization
			$quote = $c;
			$tokens[$idx] .= $c;
			$prev_delim = false;
		}
		elseif ($c == '}' && $quote)
		{
			$quote = '';
			$tokens[$idx] .= $c;
			$prev_delim = false;
		}
		else
		{
			$tokens[$idx] .= $c;
			$prev_delim = false;
		}
	}
	return $tokens;
}
