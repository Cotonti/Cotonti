<?php
/**
 * CoTemplate class library. Fast and lightweight block template engine.
 * - Compatible with XTemplate (http://www.phpxtemplate.org)
 * - Compiling into PHP objects
 * - Cotonti special
 *
 * @package Cotonti
 * @version 2.5
 * @author Vladimir Sibirov a.k.a. Trustmaster
 * @copyright Copyright (c) Cotonti Team 2009-2010
 * @license BSD
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
	 * Maps block paths to actual array indices.
	 * @var array Index for quick block search.
	 */
	protected $index = array();
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
	 */
	public function assign($name, $val = NULL)
	{
		if (is_array($name))
		{
			foreach ($name as $key => $val)
			{
				$this->vars[$key] = $val;
			}
		}
		else
		{
			$this->vars[$name] = $val;
		}
	}

	/**
	 * restart() replace callback for FILE inclusion
	 *
	 * @param array $m PCRE matches
	 * @return string
	 */
	private static function restart_include_files($m)
	{
		if (preg_match('`\.tpl$`i', $m[2]) && file_exists($m[2]))
		{
			return file_get_contents($m[2]);
		}
		else
		{
			return $m[0];
		}
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
	 */
	public function restart($path)
	{
		global $cfg;
		if (!file_exists($path))
		{
			throw new Exception("Template file not found: $path");
			return false;
		}
		$this->filename = $path;
		$cache = $cfg['cache_dir'] . '/templates/' . str_replace(array('./', '/'), '_', $path);
		$cache_idx = $cache . '.idx';
		if (!$cfg['xtpl_cache'] || !file_exists($cache) || filemtime($path) > filemtime($cache))
		{
			$this->blocks = array();
			$this->index = array();
			$code = file_get_contents($path);
			// Remove BOM if present
			if ($code[0] == chr(0xEF) && $code[1] == chr(0xBB) && $code[2] == chr(0xBF)) $code = mb_substr($code, 0);
			// FILE includes
			$code = preg_replace_callback('`\{FILE\s+("|\'|)(.+?)\1\}`', 'XTemplate::restart_include_files', $code);
			// Get root-level blocks
			do
			{
				$this->found = false;
				$code = preg_replace_callback('`<!--\s*BEGIN:\s*([\w_]+)\s*-->(.*?)<!--\s*END:\s*\1\s*-->`s',
					array($this, 'restart_root_blocks'), $code);
			} while($this->found);

			if ($cfg['xtpl_cache'])
			{
				if (is_writeable($cfg['cache_dir'] . '/templates/'))
				{
					file_put_contents($cache, serialize($this->blocks));
					file_put_contents($cache_idx, serialize($this->index));
				}
				else
				{
					throw new Exception('Your "' . $cfg['cache_dir'] . '/templates/" is not writable');
				}
			}
		}
		else
		{
			$this->blocks = unserialize(file_get_contents($cache));
			$this->index = unserialize(file_get_contents($cache_idx));
		}
	}

	/**
	 * Prints a parsed block
	 *
	 * @param string $block Block name
	 */
	public function out($block = 'MAIN')
	{
		echo $this->text($block);
	}

	/**
	 * Parses a block
	 *
	 * @param string $block Block name
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
	}

	/**
	 * Clears a parset block data
	 *
	 * @param string $block Block name
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
			$log_found = false;
			if (preg_match('`<!--\s*BEGIN:\s*([\w_]+)\s*-->(.*?)<!--\s*END:\s*\1\s*-->`s', $code, $mt, PREG_OFFSET_CAPTURE))
			{
				$block_found = true;
				$block_pos = $mt[0][1];
				$block_mt = $mt;
				$block_name = $mt[1][0];
			}
			if (preg_match('`<!-- IF (.+?) -->`', $code, $mt, PREG_OFFSET_CAPTURE))
			{
				$log_found = true;
				$log_pos = $mt[0][1];
				$log_mt = $mt;
			}
			if ($block_found && (!$log_found || $block_pos < $log_pos))
			{
				// Extract preceeding plain data chunk
				if ($block_pos > 0)
				{
					$chunk = trim(mb_substr($code, 0, $block_pos));
					if (!empty($chunk))
					{
						$blocks[$i++] = new Cotpl_data($chunk);
					}
				}
				// Extract the block
				$bpath = $path;
				array_push($bpath, $block_name);
				$index[cotpl_index_glue($bpath)] = $bpath;
				$blocks[$block_name] = new Cotpl_block(trim($block_mt[2][0]), $index, $bpath);
				$code = trim(mb_substr($code, $block_pos + mb_strlen($block_mt[0][0])));
			}
			elseif ($log_found)
			{
				// Extract preceeding plain data chunk
				if ($log_pos > 0)
				{
					$chunk = trim(mb_substr($code, 0, $log_pos));
					if (!empty($chunk))
					{
						$blocks[$i++] = new Cotpl_data($chunk);
					}
				}
				// Get the IF/ELSE contents
				$scope = 1;
				$if_code = '';
				$else_code = '';
				$else_pos = false;
				if (preg_match_all('`<!-- (IF (.+?)|ELSE|ENDIF) -->`', $code, $mt, PREG_SET_ORDER | PREG_OFFSET_CAPTURE))
				{
					foreach ($mt as $m)
					{
						if ($m[1][0] === 'ENDIF')
						{
							$scope--;
							if ($scope === 0)
							{
								if ($else_pos === false)
								{
									$if_code = mb_substr($code, $log_pos + mb_strlen($log_mt[0][0]),
										$m[0][1] - $log_pos - mb_strlen($log_mt[0][0]));
								}
								else
								{
									$else_code = mb_substr($code, $else_pos, $m[0][1] - $else_pos);
								}
								break;
							}
						}
						elseif ($m[1][0] === 'ELSE')
						{
							if ($scope === 1)
							{
								$else_pos = true;
								$if_code = mb_substr($code, $log_pos + mb_strlen($log_mt[0][0]),
									$m[0][1] - $log_pos - mb_strlen($log_mt[0][0]));
								$else_pos = $m[0][1] + mb_strlen($m[0][0]);
							}
						}
						elseif ($m[0][1] !== $log_pos)
						{
							$scope++;
						}
					}
					if ($scope === 0)
					{
						$bpath = $path;
						array_push($bpath, $i);
						$blocks[$i++] = new Cotpl_logical($log_mt[1][0], $if_code, $else_code, $index, $bpath);
						$code = trim(mb_substr($code, $m[0][1] + mb_strlen($m[0][0])));
					}
					else
					{
						throw new Exception('Logical block ' . htmlspecialchars($log_mt[0][0]) . ' not closed');
					}
				}
				else
				{
					throw new Exception('Logical block ' . htmlspecialchars($log_mt[0][0]) . ' not closed');
				}
			}
			else
			{
				// No blocks found
				$code = trim($code);
				if (!empty($code))
				{
					$blocks[$i++] = new Cotpl_data($code);
					$code = '';
				}
			}
		}
		while (!empty($code));
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
	 * Block constructor
	 *
	 * @param string $code TPL contents
	 */
	public function __construct($code)
	{
		global $cfg;
		if ($cfg['html_cleanup'])
		{
			$code = $this->cleanup($code);
		}
		if (preg_match_all('`(.*?){([\w\.\(\),]+?)}`s', $code, $mt, PREG_SET_ORDER | PREG_OFFSET_CAPTURE))
		{
			foreach ($mt as $m)
			{
				if (!empty($m[1][0]))
				{
					$this->chunks[] = $m[1][0];
				}
				$this->chunks[] = new Cotpl_var($m[2][0]);
			}
			if ($m[0][1] + mb_strlen($m[0][0]) < mb_strlen($code))
			{
				$this->chunks[] = mb_substr($code, $m[0][1] + mb_strlen($m[0][0]));
			}
		}
		else
		{
			$this->chunks[0] = $code;
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
		$text = str_replace('(', ' ) ', $text);
		$text = str_replace('!', ' ! ', $text);
		// Splitting into words
		$words = preg_split('`\s+`', $text);
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
					$haystack = array_pop($stack);
					$needle = array_pop($stack);
					array_push($stack, is_string($haystack) && is_string($needle)
						&& strpos($haystack, $needle) !== false);
					break;
				case COTPL_OP_DIV:
					array_push($stack, array_pop($stack) / array_pop($stack));
					break;
				case COTPL_OP_EQ:
					array_push($stack, array_pop($stack) == array_pop($stack));
					break;
				case COTPL_OP_GE:
					array_push($stack, array_pop($stack) >= array_pop($stack));
					break;
				case COTPL_OP_GT:
					array_push($stack, array_pop($stack) > array_pop($stack));
					break;
				case COTPL_OP_HAS:
					$haystack = array_pop($stack);
					$needle = array_pop($stack);
					array_push($stack, is_array($haystack) && in_array($needle, $haystack));
					break;
				case COTPL_OP_LE:
					array_push($stack, array_pop($stack) <= array_pop($stack));
					break;
				case COTPL_OP_LT:
					array_push($stack, array_pop($stack) < array_pop($stack));
					break;
				case COTPL_OP_MOD:
					array_push($stack, array_pop($stack) % array_pop($stack));
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
					array_push($stack, array_pop($stack) || array_pop($stack));
					break;
				case COTPL_OP_SUB:
					array_push($stack, array_pop($stack) && array_pop($stack));
					break;
				case COTPL_OP_XOR:
					array_push($stack, array_pop($stack) xor array_pop($stack));
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
		$str .= $this->blocks_toString($this->blocks);
		if (count($this->else_blocks) > 0)
		{
			$str .= "<!-- ELSE -->\n" . $this->blocks_toString($this->else_blocks);
		}
		$str .= "<!-- ENDIF -->\n";
		return $str;
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
	 */
	public function reset()
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
 * CoTemplate variable with callback extensions support
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
			$this->callbacks = $chain;
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
			// TODO callbacks with more args
			$str .= '|' . implode('|', $this->callbacks);
		}
		$str .= '}';
		return $str;
	}

	/**
	 * Evaluates a variable
	 *
	 * @param XTemplate $tpl Reference to CoTemplate storing local variables
	 * @return mixed Variable value or NULL if variable was not found
	 */
	public function evaluate($tpl)
	{
		if ($this->name === 'PHP')
		{
			$var =& $GLOBALS;
			$val = null;
		}
		else
		{
			$val = $tpl->vars[$this->name];
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
				else
				{
					$var =& $var[$key];
				}
			}
			if (is_object($var))
			{
				$val = $var->{$last_key};
			}
			else
			{
				$val = $var[$last_key];
			}
		}
		if ($this->callbacks)
		{
			foreach ($this->callbacks as $func)
			{
				$val = $func($val);
			}
		}
		return $val;
	}
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

?>
