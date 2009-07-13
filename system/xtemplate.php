<?php
/**
 * XTemplate 2.0 class library. Fast and lightweight block template engine
 * written specially for Cotonti.
 *
 * @package Cotonti
 * @version 0.0.6
 * @author Vladimir Sibirov a.k.a. Trustmaster
 * @copyright Copyright (c) 2009 Cotonti Team
 * @license BSD
 */

/**
 * Minimalistic XTemplate implementation for Cotonti
 */
class XTemplate
{
	/**
	 * @var array Assigned template vars
	 */
	public $vars = array();
	/**
	 * @var array Blocks
	 */
	public $blocks = array();
	/**
	 * @var string Template file name
	 */
	public $filename = '';

	/**
	 * Simplified constructor
	 *
	 * @param string $path Template file name
	 */
	public function __construct($path = NULL)
	{
		$this->vars['PHP'] =& $GLOBALS;
		if (is_string($path)) $this->restart($path);
	}

	/**
	 * Assigns a template variable or an array of them
	 *
	 * @param mixed $name Variable name or array of values
	 * @param mixed $val Tag value if $name is not an array
	 */
	public function assign($name, $val = NULL)
	{
		if (is_array($name)) foreach ($name as $key => $val) $this->vars[$key] = $val;
		else $this->vars[$name] = $val;
	}

	/**
	 * Evaluates logical expression
	 * 
	 * @param string $expr Expression
	 * @return mixed Evaluation result
	 */
	public function evaluate($expr)
	{
		// Apply logical operators
		if (mb_strstr($expr, ' OR '))
		{
			$res = FALSE;
			$subs = explode(' OR ', $expr);
			foreach ($subs as $sub) $res |= $this->evaluate($sub);
			return $res;
		}
		if (mb_strstr($expr, ' AND '))
		{
			$res = TRUE;
			$subs = explode(' AND ', $expr);
			foreach ($subs as $sub) $res &= $this->evaluate($sub);
			return $res;
		}
		// Get the first operand which must be a variable
		if ($expr[0] == '!')
		{
			$inv = TRUE;
			$expr = mb_substr($expr, 1);
		}
		else $inv = FALSE;
		$p1 = mb_strpos($expr, '{');
		if ($p1 === FALSE) throw new Exception('Invalid logical block ' . sed_cc($expr) . ' present in '
			. $this->filename);
		$p2 = mb_strpos($expr, '}', $p1 + 1);
		if ($p2 === FALSE) throw new Exception('Variable tag is not closed in logical block ' . sed_cc($expr)
			. ' present in ' . $this->filename);
		$name = mb_substr($expr, $p1 + 1, $p2 - $p1 - 1);
		$val = $this->get_var($name);
		$expr = trim(mb_substr($expr, $p2 + 1));
		if (empty($expr)) return $inv ? !$val : $val;
		// Get the operator and second operand
		$p1 = mb_strpos($expr, ' ');
		if ($p1 === FALSE) throw new Exception('Missing spaces in logical block ' . sed_cc($expr) . ' in file '
			. $this->filename);
		$op = mb_substr($expr, 0, $p1);
		$expr = trim(mb_substr($expr, $p1 + 1));
		if ($expr[0] == '{') $val2 = $this->evaluate($expr);
		elseif (is_numeric($expr)) $val2 = $expr;
		else $val2 = str_replace(array('"', "'"), '', $val2);
		// Apply operator
		switch ($op)
		{
			case '==': $res = $val == $val2; break;
			case '!=': $res = $val != $val2; break;
			case '>': $res = $val > $val2; break;
			case '<': $res = $val < $val2; break;
			case '>=': $res = $val >= $val2; break;
			case '<=': $res = $val <= $val2; break;
			default: $res = FALSE;
		}
		return $inv ? !$res : $res;
	}

	/**
	 * Gets a template variable
	 *
	 * @param string $name Variable name
	 * @return mixed Variable value or NULL if variable was not found
	 */
	public function get_var($name)
	{
		if (strstr($name, '.'))
		{
			$sub = explode('.', $name);
			$var =& $this->vars[$sub[0]];
			$lim = count($sub) - 1;
			for ($i = 1; $i < $lim; $i++)
			{
				if (is_array($var)) $var =& $var[$sub[$i]];
				elseif (is_object($var)) $var =& $var->{$sub[$i]};
				else return NULL;
			}

			if (is_array($var)) return $var[$sub[$i]];
			elseif (is_object($var)) return $var->{$sub[$i]};
		}
		elseif (isset($this->vars[$name])) return $this->vars[$name];
		else return NULL;
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
			return FALSE;
		}
		$this->filename = $path;
		$this->blocks = array();
		$data = file_get_contents($path);
		// Remove BOM if present
		if ($data[0] == chr(0xEF) && $data[1] == chr(0xBB) && $data[2] == chr(0xBF)) $data = mb_substr($data, 0);
		// Get root-level blocks
		while (($pos = mb_strpos($data, '<!-- BEGIN: ')) !== FALSE)
		{
			$pos2 = mb_strpos($data, ' -->', $pos + 13);
			$name = mb_substr($data, $pos + 12, $pos2 - $pos - 12);
			$begin = '<!-- BEGIN: ' . $name . ' -->';
			$b_len = mb_strlen($begin);
			$end = '<!-- END: ' . $name . ' -->';
			$e_pos = mb_strpos($data, $end);
			if ($e_pos === FALSE) throw new Exception("Block $name is not closed correctly in $path");
			$e_len = mb_strlen($end);
			$bdata = trim(mb_substr($data, $pos + $b_len, $e_pos - $pos - $b_len), " \r\n\t");
			$this->blocks[$name] = new Xtpl_block($this, $bdata, $name);
			$data = mb_substr($data, $e_pos + $e_len);
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
		if (is_object($this->blocks[$block])) $this->blocks[$block]->parse();
		//else throw new Exception("Block $block is not found in " . $this->filename);
	}

	/**
	 * Clears a parset block data
	 *
	 * @param string $block Block name
	 */
	public function reset($block = 'MAIN')
	{
		if (is_object($this->blocks[$block])) $this->blocks[$block]->reset();
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
		if (is_object($this->blocks[$block])) return $this->blocks[$block]->text();
		else
		{
			//throw new Exception("Block $block is not found in " . $this->filename);
			return '';
		}
	}
}

/**
 * A simple nameless block of data which may parse variables
 */
class Xtpl_data
{
	/**
	 * @var XTemplate Parent XTemplate object reference
	 */
	public $xtpl = NULL;
	/**
	 * @var string Block data (HTML/TPL)
	 */
	public $data = '';

	/**
	 * Block constructor
	 *
	 * @param string $data TPL contents
	 */
	public function __construct($xtpl, $data)
	{
		$this->xtpl = $xtpl;
		$this->data = $data;
	}

	/**
	 * Variable substitution callback
	 * 
	 * @param array $input Preg match array
	 * @return string Variable value
	 */
	private function replace($input)
	{
		$val = $this->xtpl->get_var($input[1]);
		if (is_null($val)) return ''; //$input[0];
		else return $val;
	}

	/**
	 * Returns parsed block contents
	 *
	 * @return string Block data
	 */
	public function text()
	{
		$data = $this->data;
		// Apply logical operators
		while (($p1 = mb_strpos($data, '<!-- IF ')) !== FALSE)
		{
			$p2 = mb_strpos($data, ' -->', $p1 + 8);
			$expr = mb_substr($data, $p1 + 8, $p2 - $p1 - 8);
			$p3 = mb_strpos($data, '<!-- ENDIF -->');
			if ($p3 === FALSE) throw new Exception('Logical block '.sed_cc($expr).' is not closed correctly in '
				. $this->xtpl->filename);
			$bdata = mb_substr($data, $p2 + 4, $p3 - $p2 - 4);
			if (($p4 = mb_strpos($bdata, '<!-- ELSE -->')) !== FALSE)
			{
				$bdata1 = mb_substr($bdata, 0, $p4);
				$bdata2 = mb_substr($bdata, $p4 + 13);
				if ($this->xtpl->evaluate($expr))
					$data = mb_substr($data, 0, $p1) . $bdata1 . mb_substr($data, $p3 + 14);
				else
					$data = mb_substr($data, 0, $p1) . $bdata2 . mb_substr($data, $p3 + 14);
			}
			else
			{
				if ($this->xtpl->evaluate($expr))
					$data = mb_substr($data, 0, $p1) . $bdata . mb_substr($data, $p3 + 14);
				else
					$data = mb_substr($data, 0, $p1) . mb_substr($data, $p3 + 14);
			}
		}
		// Parse variables
		return preg_replace_callback('`{([\w_\.]+)}`', array($this, 'replace'), $data);
	}
}

/**
 * XTemplate block class
 */
class Xtpl_block
{
	/**
	 * @var string Block name
	 */
	public $name = '';
	/**
	 * @var XTemplate Parent XTemplate object reference
	 */
	public $xtpl = NULL;
	/**
	 * @var array Parsed block instances
	 */
	public $data = array();
	/**
	 * @var int Pointer to current parsed block instance
	 */
	public $ptr = 0;
	/**
	 * @var array<Xtpl_data> Contained blocks
	 */
	public $blocks = array();

	/**
	 * Block constructor
	 *
	 * @param XTemplate $xtpl XTemplate object reference
	 * @param string $data TPL contents
	 */
	public function __construct($xtpl, $data, $name, $path = '')
	{
		$this->xtpl = $xtpl;
		$this->name = $name;
		$path = empty($path) ? $name : $path . '.' . $name;
		// Split the data into nested blocks
		while (!empty($data))
		{
			$pos = mb_strpos($data, '<!-- BEGIN: ');
			if ($pos === FALSE)
			{
				$this->blocks[] = new Xtpl_data($xtpl, $data);
				$data = '';
			}
			else
			{
				// Save plain data
				$chunk = trim(mb_substr($data, 0, $pos), " \r\n\t");
				if (!empty($chunk)) $this->blocks[] = new Xtpl_data($xtpl, $chunk);
				// Get a nested block
				$pos2 = mb_strpos($data, ' -->', $pos + 13);
				$name = mb_substr($data, $pos + 12, $pos2 - $pos - 12);
				$begin = '<!-- BEGIN: ' . $name . ' -->';
				$b_len = mb_strlen($begin);
				$end = '<!-- END: ' . $name . ' -->';
				$e_len = mb_strlen($end);
				$e_pos = mb_strpos($data, $end, $pos2 + 4);
				if ($e_pos === FALSE) throw new Exception("Block $name is not closed correctly in "
					. $this->xtpl->filename);
				$bdata = mb_substr($data, $pos2 + 4, $e_pos - $pos2 - 4);
				// Create block object and link to it
				$block = new Xtpl_block($xtpl, $bdata, $name, $path);
				$this->xtpl->blocks[$path . '.' . $name] = $block;
				$this->blocks[] = $block;
				// Procceed with less data
				$data = mb_substr($data, $e_pos + $e_len);
			}
			$data = trim($data, " \r\n\t");
		}
	}

	/**
	 * Parses block contents
	 */
	public function parse()
	{
		foreach ($this->blocks as $block) $data .= $block->text();
		$this->data[$this->ptr] = $data;
		$this->ptr++;
	}

	/**
	 * Clears parsed block data
	 */
	public function reset()
	{
		$this->data = array();
	}

	/**
	 * Returns parsed block HTML
	 *
	 * @return string
	 */
	public function text()
	{
		$text = implode('', $this->data);
		$this->data = array();
		return $text;
	}
}
?>
