<?php
defined('COT_CODE') or die('Wrong URL');

/**
 * Cotonti Resource control class
 *
 * @package API - Resources
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
class Resources
{

	/**
	 * @var array predefined aliases
	 */
	protected static $alias = array (
		'@jQuery' => 'js/jquery.min.js',

		'@ckeditor' => 'plugins/ckeditor/lib/ckeditor.js',
		'@ckeditorPreset.js' => 'plugins/ckeditor/presets/ckeditor.default.set.js',

		'@bootstrap.js' => 'lib/bootstrap/js/bootstrap.min.js',
		'@bootstrap.css' => 'lib/bootstrap/css/bootstrap.min.css',
		'@bootstrapTheme.css' => null  // Undefined value. You can set to: lib/bootstrap/css/bootstrap-theme.min.css
	);

	// ==== predefined alias constants ====
	const jQuery = '@jQuery';
	const bootstrap = '@bootstrap.js';
	const ckeditor = '@ckeditor';
	// ==== /predefined alias constants ====

	/**
	 * @var array Resources Registry
	 */
	protected static $registry = array();

	/**
	 * @var array footer Resources Registry
	 */
	protected static $footerRc = array();

	/**
	 * @var array header Resources Registry
	 */
	protected static $headerRc = array();

	protected static $addedFiles = array();

	protected static $skip_minification = false;

	protected static $cacheOn = false;

	protected static $consolidate = false;

	protected static $isAdmin = false;

	protected static $minify = false;

	/**
	 * @var bool header.php executed?
	 */
	protected static $headerComplete = false;

	/**
	 * @var string $dir Resources cache dir
	 */
	protected static $dir = '';

	public static function __init()
	{
		if (defined('COT_HEADER_COMPLETE')) static::$headerComplete = true;

		static::$cacheOn = cot::$cfg['cache'];
		static::$consolidate = (bool) cot::$cfg['headrc_consolidate'];
		static::$dir = cot::$cfg['cache_dir'] . '/static/';
		static::$isAdmin = defined('COT_ADMIN');
		static::$minify = (bool) cot::$cfg['headrc_minify'];

		// Consolidate resources?
		static::$consolidate = static::$cacheOn && static::$consolidate && !static::$isAdmin;
		if (static::$consolidate)
		{
			if (!file_exists(cot::$cfg['cache_dir'])) mkdir(cot::$cfg['cache_dir'], cot::$cfg['dir_perms']);
			if (!file_exists(static::$dir)) mkdir(static::$dir, cot::$cfg['dir_perms']);
		}
	}

	/**
	 * Puts a JS/CSS file into the header resource registry to be consolidated with other
	 * such resources and stored in cache.
	 *
	 * It is recommened to use files instead of embedded code and use this method
	 * instead of Resources::addEmbed(). Use this way for any sort of static JavaScript or
	 * CSS linking.
	 *
	 * Do not put any private data in any of resource files - it is not secure. If you really need it,
	 * then use direct output instead.
	 *
	 * @param string $path Path to a *.js script or *.css stylesheet
	 * @param string $type
	 * @param int $order Order priority number
	 * @param string $scope Resource scope. Scope is a selector of domain where resource is used. Valid scopes are:
	 *        'global' - global for entire site, will be included everywhere, this is the most static and persistent scope;
	 *        'guest' - for unregistered visitors only;
	 *        'user' - for registered members only;
	 *        'group_123' - for members of a specific group (maingrp), in this example of group with id=123.
	 *        It is recommended to use 'global' scope whenever possible because it delivers best caching opportunities.
	 * @return bool Returns TRUE normally, FALSE is file was not found
	 * @throws Exception
	 */
	public static function addFile($path, $type = '', $order = 50, $scope = 'global')
	{
		// header.php executed. Try add file to footer
		if (static::$headerComplete) return Resources::linkFileFooter($path, $type, $order);

		$tmp = explode('?', $path);
		$fileName = $tmp[0];

		if (in_array($fileName, static::$addedFiles)) return false;

		if (mb_strpos($fileName, '@') === 0)
		{
			$fileName = static::$alias[$fileName];
		}
		elseif (mb_strpos($fileName, 'http://') === false && mb_strpos($fileName, 'https://') === false && mb_strpos($fileName, '//') !== 0)
		{
			if (!file_exists($fileName))
			{
				throw new Exception('Resource file «' . $fileName . '» not exists');
			}
		}

		if (empty($type)) $type = preg_match('#\.(min\.)?(js|css)$#', mb_strtolower($fileName), $m) ? $m[2] : 'js';

		static::$addedFiles[] = $tmp[0];

		if (static::$consolidate && static::$minify && !static::$skip_minification && mb_strpos($fileName, '.min.') === false &&
			 mb_strpos($fileName, '.pack.') === false)
		{

			if ($fileName != '')
			{
				$bname = ($type == 'css') ? str_replace('/', '._.', $fileName) : basename($fileName) . '.min';
				$content = file_get_contents($fileName);
				if($content == '' || $content === false) return false;
				$code = static::minify($content, $type);
				$path = static::$dir . $bname;
				file_put_contents($path, $code);
			}
		}

		if (static::$consolidate && static::$cacheOn && !static::$isAdmin)
		{
			static::$registry[$type][$scope][$order][] = $path;
		}
		else
		{
			static::$registry[$type]['files'][$scope][$order][] = $path;
		}

		foreach (static::additionalFiles($tmp[0]) as $file)
		{
			static::addFile($file, '', $order, $scope);
		}

		return true;
	}

	protected static function additionalFiles($file)
	{
		$ret = array();

		switch ($file)
		{
			case '@bootstrap.js':
				$ret[] = '@bootstrap.css';
				$ret[] = '@bootstrapTheme.css';
				break;

			case '@ckeditor':
				$ret[] = '@ckeditorPreset.js';
				break;
		}

		return $ret;
	}

	/**
	 * Puts a portion of embedded code into the header CSS/JS resource registry.
	 *
	 * It is strongly recommended to use files for CSS/JS whenever possible
	 * and call Resources::AddFile() function for them instead of embedding code
	 * into the page and using this function. This function should be used for
	 * dynamically generated code, which cannot be stored in static files.
	 *
	 * @param string $identifier Alphanumeric identifier for the piece, used to control updates, etc.
	 * @param string $code Embedded stylesheet or script code
	 * @param string $scope Resource scope. See description of this parameter in Resources::AddFile() docs.
	 * @param string $type Resource type: 'js' or 'css'
	 * @param int $order Order priority number
	 *
	 * @return bool This function always returns TRUE
	 * @see Resources::AddFile()
	 */
	public static function addEmbed($code, $type = 'js', $order = 50, $scope = 'global', $identifier = '')
	{
		// header.php executed. Try add code to footer
		if (static::$headerComplete) Resources::embedFooter($code, $type, $order);

		// Если используем консолидацию и минификацию, сохранить в файл
		if (static::$consolidate && static::$cacheOn && !static::$isAdmin)
		{
			if (!$identifier) $identifier = md5($code . $type);
			// Save as file
			$path = static::$dir . $identifier . '.' . $type;
			if (!file_exists($path) || md5($code) != md5_file($path))
			{
				if (static::$minify && !static::$skip_minification)
				{
					$code = static::minify($code, $type);
				}
				file_put_contents($path, $code);
			}
			static::$registry[$type][$scope][$order][] = $path;
		}
		else
		{
			$separator = "\n";
			if ($type == 'js')
			{
				$code = trim($code);
				$last = (substr($code, -1));
				if ($last != ';') $separator = ";\n";
			}
			static::$registry[$type]['embed'][$scope][$order] .= $code . $separator;
		}
		return true;
	}

	public static function render()
	{
		global $theme, $cot_rc_html;

		if (!isset($cot_rc_html[$theme]) || !static::$consolidate)
		{
			$cot_rc_html = static::consolidate();
		}

		$ret = '';
		$pass = true;
		if (is_array($cot_rc_html) && isset($cot_rc_html[$theme]))
		{

			foreach ($cot_rc_html[$theme] as $scope => $html)
			{
				switch ($scope)
				{
					case 'global':
						$pass = true;
						break;
					case 'guest':
						$pass = cot::$usr['id'] == 0;
						break;
					case 'user':
						$pass = cot::$usr['id'] > 0;
						break;
					default:
						$parts = explode('_', $scope);
						$pass = count($parts) == 2 && $parts[0] == 'group' && $parts[1] == cot::$usr['maingrp'];
				}
				if ($pass) $ret = $html . $ret;
			}
		}

		// Now collect resources should not be minified
		if (!is_array(static::$headerRc)) return $ret;

		// CSS should go first
		ksort(static::$headerRc);
		foreach (static::$headerRc as $type => $data)
		{
			if (!empty(static::$headerRc[$type]) && is_array(static::$headerRc[$type]))
			{
				ksort(static::$headerRc[$type]);
				foreach (static::$headerRc[$type] as $order => $htmlArr)
				{
					foreach ($htmlArr as $key => $path)
					{
						if (mb_strpos($type, '_embed') !== false)
						{
							$ret .= $path . "\n";
						}
						else
						{
							if (mb_strpos($path, '@') === 0)
							{
								$path = static::$alias[$path];
								if (empty($path)) continue;
							}

							$ret .= cot_rc("code_rc_{$type}_file", array(
								'url' => $path
							)) . "\n";
						}
					}
				}
			}
		}
		static::$headerComplete = true;

		return trim($ret);
	}

	/**
	 * Consolidate all resources and make single file
	 */
	protected static function consolidate()
	{
		global $cot_rc_html, $theme;

		// Если нужно собирать и ужимать делаем это
		if (!is_array(static::$registry)) return false;

		// CSS should go first
		ksort(static::$registry);

		// Build the header outputs
		$cot_rc_html[$theme] = array();

		// Consolidate resources
		if (static::$cacheOn && static::$consolidate && !static::$isAdmin)
		{
			clearstatcache();

			foreach (static::$registry as $type => $scope_data)
			{
				// Consolidation
				foreach ($scope_data as $scope => $ordered_files)
				{
					ksort($ordered_files);
					$target_path = static::$dir . $scope . '.' . $theme . '.' . $type;

					$files = array();
					foreach ($ordered_files as $order => $o_files)
					{
						$files = array_merge($files, $o_files);
					}
					$files = array_unique($files);

					foreach ($files as $key => $file)
					{
						if (mb_strpos($file, '@') === 0)
						{
							$tmp = static::$alias[$file];
							if (empty($tmp))
							{
								unset($files[$key]);
							}
							else
							{
								$files[$key] = $tmp;
							}
						}
					}

					$code = '';
					$modified = false;
					$fileTime = 0;

					if (!file_exists($target_path))
					{
						// Just compile a new cache file
						$file_list = $files;
						$modified = true;
					}
					else
					{
						$fileTime = filemtime($target_path);

						// Load the list of files already cached
						$file_list = unserialize(file_get_contents("$target_path.idx"));

						// Check presense or modification time for each file
						foreach ($files as $path)
						{
							if (!in_array($path, $file_list) || filemtime($path) >= $fileTime)
							{
								$modified = true;
								break;
							}
						}
					}

					if ($modified)
					{
						// Reconsolidate cache
						$current_path = str_replace('\\', '/', realpath('.'));
						foreach ($files as $path)
						{
							// Get file contents and remove BOM
							$file_code = str_replace(pack('CCC', 0xef, 0xbb, 0xbf), '', file_get_contents($path));

							if ($type == 'css')
							{
								if (strpos($path, '._.') !== false)
								{
									// Restore original file path
									$path = str_replace('._.', '/', basename($path));
								}
								if ($path[0] === '/')
								{
									$path = mb_substr($path, 1);
								}
								$file_path = str_replace('\\', '/', dirname(realpath($path)));
								$relative_path = str_replace($current_path, '', $file_path);
								if ($relative_path[0] === '/')
								{
									$relative_path = mb_substr($relative_path, 1);
								}
								// Apply CSS imports
								if (preg_match_all('#@import\s+url\((\'|")?([^\'")]+)\1?\);#i', $file_code, $mt, PREG_SET_ORDER))
								{
									foreach ($mt as $m)
									{
										if (preg_match('#^https?://#i', $m[2]))
										{
											$filename = $m[2];
										}
										else
										{
											$filename = empty($relative_path) ? $m[2] : $relative_path . '/' . $m[2];
										}
										$file_code = str_replace($m[0], file_get_contents($filename), $file_code);
									}
								}
								// Fix URLs
								if (preg_match_all('#\burl\((\'|")?([^\)"\']+)\1?\)#i', $file_code, $mt, PREG_SET_ORDER))
								{
									foreach ($mt as $m)
									{
										$fileFullName = trim(empty($relative_path) ? $m[2] : $relative_path . '/' . $m[2]);
										$tmp = explode('?', $fileFullName);
										$fileName = $tmp[0];

										$fileName = str_replace($current_path, '', str_replace('\\', '/', realpath($fileName)));
										if (!$fileName) continue;

										if ($fileName[0] === '/')
										{
											$fileName = mb_substr($fileName, 1);
										}
										if (!empty($tmp[1]))
										{
											$fileName .= '?' . $tmp[1];
										}
										$file_code = str_replace($m[0], 'url("' . $fileName . '")', $file_code);
									}
								}
							}
							$separator = "\n";
							if ($type == 'js')
							{
								$file_code = trim($file_code);
								$last = (substr($file_code, -1));
								if ($last != ';') $separator = ";\n";
							}
							$code .= $file_code . $separator;
						}

						file_put_contents($target_path, $code);
						if (cot::$cfg['gzip']) file_put_contents("$target_path.gz", gzencode($code));
						file_put_contents("$target_path.idx", serialize($files));

						$fileTime = filemtime($target_path);
					}

					$rc_url = "rc.php?rc=$scope.$theme.$type&amp;nc=" . $fileTime;

					if (empty($cot_rc_html[$theme][$scope])) $cot_rc_html[$theme][$scope] = '';
					$cot_rc_html[$theme][$scope] .= cot_rc("code_rc_{$type}_file", array(
						'url' => $rc_url
					)) . "\n";
				}
			}
			// Save the output
			static::$cacheOn && cot::$cache && cot::$cache->db->store('cot_rc_html', $cot_rc_html);
		}
		else
		{

			$log = array(); // log paths to avoid duplicates
			foreach (static::$registry as $type => $resData)
			{
				if (!empty(static::$registry[$type]['files']) && is_array(static::$registry[$type]['files']))
				{
					foreach (static::$registry[$type]['files'] as $scope => $scope_data)
					{
						ksort($scope_data);
						foreach ($scope_data as $order => $files)
						{
							foreach ($files as $file)
							{
								if (!in_array($file, $log))
								{
									$fileName = $file;
									if (mb_strpos($file, '@') === 0)
									{
										$fileName = static::$alias[$file];
										if (empty($fileName)) continue;
									}
									if (empty($cot_rc_html[$theme][$scope])) $cot_rc_html[$theme][$scope] = '';
									$cot_rc_html[$theme][$scope] .= cot_rc("code_rc_{$type}_file", array(
										'url' => $fileName
									)) . "\n";
									$log[] = $file;
								}
							}
						}
					}
				}
				if (!empty(static::$registry[$type]['embed']) && is_array(static::$registry[$type]['embed']))
				{
					foreach (static::$registry[$type]['embed'] as $scope => $scope_data)
					{
						ksort($scope_data);
						foreach ($scope_data as $order => $code)
						{
							if (empty($cot_rc_html[$theme][$scope])) $cot_rc_html[$theme][$scope] = '';

							$cot_rc_html[$theme][$scope] .= cot_rc("code_rc_{$type}_embed", array(
								'code' => $code
							)) . "\n";
						}
					}
				}
			}
		}
		return $cot_rc_html;
	}

	/**
	 * Render footer resources
	 */
	public static function renderFooter()
	{
		if (!is_array(static::$footerRc)) return false;

		// CSS should go first
		ksort(static::$footerRc);
		$ret = '';

		foreach (static::$footerRc as $type => $data)
		{
			if (!empty(static::$footerRc[$type]) && is_array(static::$footerRc[$type]))
			{
				ksort(static::$footerRc[$type]);
				foreach (static::$footerRc[$type] as $order => $htmlArr)
				{
					foreach ($htmlArr as $key => $path)
					{
						if (mb_strpos($type, '_embed') !== false)
						{
							$ret .= $path . "\n";
						}
						else
						{
							if (mb_strpos($path, '@') === 0)
							{
								$path = static::$alias[$path];
								if ($path == '') continue;
							}

							$ret .= cot_rc("code_rc_{$type}_file", array(
								'url' => $path
							)) . "\n";
						}
					}
				}
			}
		}

		return trim($ret);
	}

	/**
	 * A shortcut for plain output of a link to a CSS/JS file in the header of the page
	 *
	 * @param string $path Stylesheet *.css or script *.js path/url
	 * @param string $type
	 * @param int $order
	 * @return bool
	 *
	 * @throws Exception
	 */
	public static function linkFile($path, $type = '', $order = 50)
	{
		// header.php executed. Try add file to footer
		if (static::$headerComplete) return Resources::linkFileFooter($path, $type, $order);

		$tmp = explode('?', $path);
		$fileName = $tmp[0];

		if (in_array($fileName, static::$addedFiles)) return false;

		if (mb_strpos($fileName, '@') === 0)
		{
			$fileName = static::$alias[$fileName];
		}
		elseif (mb_strpos($fileName, 'http://') === false && mb_strpos($fileName, 'https://') === false && mb_strpos($fileName, '//') !== 0)
		{
			if (!file_exists($fileName))
			{
				throw new Exception('Resource file «' . $fileName . '» not exists');
			}
		}

		if (empty($type)) $type = preg_match('#\.(js|css)$#i', $fileName, $m) ? strtolower($m[1]) : 'js';

		static::$addedFiles[] = $tmp[0];
		static::$headerRc[$type][$order][] = $path;

		foreach (static::additionalFiles($tmp[0]) as $file)
		{
			static::linkFile($file, '', $order);
		}
	}

	/**
	 * A shortcut to append a JavaScript or CSS file to the footer
	 *
	 * @param string $path JavaScript or CSS file path
	 * @param string $type
	 * @param int $order
	 * @return bool
	 * @throws Exception
	 */
	public static function linkFileFooter($path, $type = '', $order = 50)
	{
		$tmp = explode('?', $path);
		$fileName = $tmp[0];

		if (in_array($fileName, static::$addedFiles)) return false;

		if (mb_strpos($fileName, '@') === 0)
		{
			$fileName = static::$alias[$fileName];
		}
		elseif (mb_strpos($fileName, 'http://') === false && mb_strpos($fileName, 'https://') === false && mb_strpos($fileName, '//') !== 0)
		{
			if (!file_exists($fileName))
			{
				throw new Exception('Resource file «' . $fileName . '» not exists');
			}
		}

		if (empty($type)) $type = preg_match('#\.(js|css)$#i', $fileName, $m) ? strtolower($m[1]) : 'js';

		static::$addedFiles[] = $tmp[0];
		static::$footerRc[$type][$order][] = $path;

		foreach (static::additionalFiles($tmp[0]) as $file)
		{
			$type = preg_match('#\.(js|css)$#i', $file, $m) ? strtolower($m[1]) : 'js';
			if ($type == 'css' && !static::$headerComplete)
			{
				static::linkFile($file, 'css', $order);
			}
			else
			{
				static::linkFileFooter($file, '', $order);
			}
		}
	}

	/**
	 * A shortcut for plain output of an embedded stylesheet/javascript in the header of the page
	 *
	 * Example: Resources::embed(" alert('ssssss') ");
	 * Resources::embed(" .blablabla {color: #000000} ", 'css');
	 *
	 * @param string $code Stylesheet or javascript code
	 * @param int $order
	 * @param string $type Resource type: 'js' or 'css'
	 */
	public static function embed($code, $type = 'js', $order = 50)
	{
		// header.php executed. Try add code to footer
		if (static::$headerComplete) Resources::embedFooter($code, $type, $order);

		static::$headerRc[$type . '_embed'][$order][] = cot_rc("code_rc_{$type}_embed", array(
			'code' => $code
		));
	}

	/**
	 * A shortcut for plain output of an embedded stylesheet/javascript in the footer of the page
	 *
	 * Example: Resources::embedFooter(" alert('ssssss') ");
	 *
	 * @param string $code Stylesheet or javascript code
	 * @param string $type Resource type: 'js' or 'css'
	 * @param int $order
	 */
	public static function embedFooter($code, $type = 'js', $order = 50)
	{
		static::$footerRc[$type . '_embed'][$order][] = cot_rc("code_rc_{$type}_embed", array(
			'code' => $code
		));
	}

	/**
	 * JS/CSS minification function
	 *
	 * @param string $code Code to minify
	 * @param string $type Type: 'js' or 'css'
	 *
	 * @return string Minified code
	 */
	public static function minify($code, $type = 'js')
	{
		if ($type == 'js')
		{
			require_once './lib/jsmin.php';
			$code = JSMin::minify($code);
		}
		elseif ($type == 'css')
		{
			require_once './lib/cssmin.php';
			$code = minify_css($code);
		}
		return $code;
	}

	/**
	 * Set Resource alias
	 * @param string $newAlias
	 * @param string $value
	 * @param bool   $canReWrite
	 * @return bool
	 */
	public static function setAlias($newAlias, $value = '', $canReWrite = false)
	{
		if ($newAlias == '') return false;

		if (mb_strpos($newAlias, '@') === false) $newAlias = '@' . $newAlias;

        if(!$canReWrite && isset(static::$alias[$newAlias]) && static::$alias[$newAlias] !== null) return false;

		static::$alias[$newAlias] = $value;

		return true;
	}

	public static function getAlias($aliasName)
	{
		if ($aliasName == '') return null;

		if (mb_strpos($aliasName, '@') === false) $aliasName = '@' . $aliasName;

		if (!isset(static::$alias[$aliasName])) return null;

		return static::$alias[$aliasName];
	}

    /**
     * Check if file was already added
     *
     * File aliases are not resolved. They can be redefined. Use aliases when it is possible.
     *
     * @param string $fileName file name or it alias
     * @return bool
     */
	public static function isFileAdded($fileName)
    {
        if (in_array($fileName, static::$addedFiles)) return true;
        return false;
    }
}

Resources::__init();
