<?php

/**
 * Simple CSS minification function
 *
 * @param string $css CSS code
 * @return string
 */
function minify_css($css)
{
	$css = preg_replace('#\s+#', ' ', $css);
	$css = preg_replace('#/\*.*?\*/#s', '', $css);
	$css = str_replace('; ', ';', $css);
	$css = str_replace(': ', ':', $css);
	$css = str_replace(' {', '{', $css);
	$css = str_replace('{ ', '{', $css);
	$css = str_replace(', ', ',', $css);
	$css = str_replace('} ', '}', $css);
	$css = str_replace(';}', '}', $css);

	return trim($css);
}
