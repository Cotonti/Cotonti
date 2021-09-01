<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=global
Order=4
[END_COT_EXT]
==================== */
/**
 * Cotonti Lib plugin for Cotonti Siena
 *
 * @package Cotonti Lib
 * @author  Kalnov Alexey    <kalnovalexey@yandex.ru>
 * @copyright © Portal30 Studio http://portal30.ru
 */
defined('COT_CODE') or die('Wrong URL.');

// Autoloader
require_once 'lib/Loader.php';
Loader::register();

include cot_langfile('cotontilib', 'plug');

if(!function_exists('mb_ucfirst')) {
    /**
     * Make a string's first character uppercase
     * @link http://php.net/manual/en/function.ucfirst.php
     *
     * @param string $str The input string.
     * @return string the resulting string.
     */
    function mb_ucfirst($str)
    {
        $fc = mb_strtoupper(mb_substr($str, 0, 1));
        return $fc . mb_substr($str, 1);
    }
}

if(!function_exists('mb_lcfirst')) {
    /**
     * Make a string's first character lowercase
     * @link http://php.net/manual/en/function.lcfirst.php
     *
     * @param string $str The input string.
     * @return string the resulting string.
     */
    function mb_lcfirst($str)
    {
        $fc = mb_strtolower(mb_substr($str, 0, 1));
        return $fc . mb_substr($str, 1);
    }
}

/**
 * Returns transliterated version of a string.
 *
 * If intl extension isn't available uses fallback
 * You may customize characters map via $cot_translit_custom variable. See file lang/ru/translit.ru.lang.php
 *
 * @param string $string input string
 * @param string|\Transliterator $transliterator either a \Transliterator or a string
 *                                               from which a \Transliterator can be built.
 * @see http://php.net/manual/en/transliterator.transliterate.php
 *
 * @return string
 *
 * Todo check if russian settings are right
 */
function cot_transliterate($string, $transliterator = null)
{
    global $cot_translit, $cot_translit_custom, $cot_transliterator;

    include cot_langfile('translit', 'core');

    if(is_array($cot_translit_custom)) return strtr($string, $cot_translit_custom);

    if (extension_loaded('intl')) {
        if($transliterator === null) $transliterator = $cot_transliterator;
        if ($transliterator === null)  $transliterator = 'Any-Latin; Latin-ASCII; [\u0080-\uffff] remove';

        return transliterator_transliterate($transliterator, $string);
    }

    if(is_array($cot_translit)) {
        return strtr($string, $cot_translit);
    }

    return $string;
}

function cot_slug($string)
{

}

/**
 * Standard var_dump with <pre>
 *
 * @param mixed $var[,$var1],[.. varN]
 */
function var_dump_()
{
    static $cnt = 0;
    $cnt++;
    echo '<div id="var-dump-'.$cnt.'" class="var-dump" style="z-index:1000;opacity:0.8"><pre style="color:black;background-color:white;">';
    $params = func_get_args();
    call_user_func_array('var_dump', $params);
    echo '</pre></div>';
    ob_flush();
}

/**
 * Standard var_dump with <pre> and exit
 *
 * @param mixed $var[,$var1],[.. varN]
 */
function var_dump__()
{
    cot_sendheaders();
    $params = func_get_args();
    call_user_func_array('var_dump_', $params);
    exit;
}

// ==== View template functions ====
if(!function_exists('cot_formGroupClass')) {
    /**
     * Класс для элемента формы
     * @param $name
     * @return string
     */
    function cot_formGroupClass($name)
    {
        global $currentMessages;

        if (!cot::$cfg['msg_separate']) return '';

        $error = '';
        $error .= cot_implode_messages($name, 'error');

        if ($error) return 'has-error';

        if (!empty($currentMessages[$name]) && is_array($currentMessages[$name])) {
            foreach ($currentMessages[$name] as $msg) {
                if ($msg['class'] == 'error') return 'has-error';
            }
        }

        return '';
    }
}
// ==== /View template functions ====