<?php
/**
 * Base Comments widget class
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\plugins\comments\inc;

use cot\extensions\ExtensionsDictionary;
use XTemplate;

abstract class BaseCommentsWidget
{
    /**
     * @var string
     */
    public $template = 'comments';

    public $paginationParam = 'dcm';

    /**
     * @var ?XTemplate
     */
    protected $tmpl = null;

    /**
     * @var array
     */
    protected $auth = ['read' => false, 'write' => false, 'admin' => false];

    public function __construct($config = [])
    {
        foreach ($config as $name => $value) {
            if ($name === 'sourceId') {
                $value = (string) $value;
            }

            $this->$name = $value;
        }

        [$this->auth['read'], $this->auth['write'], $this->auth['admin']] =
            cot_auth(ExtensionsDictionary::TYPE_PLUGIN, 'comments');
    }

    protected function getTemplate(): XTemplate
    {
        if ($this->tmpl === null) {
            $this->tmpl = new XTemplate(
                cot_tplfile($this->template, ExtensionsDictionary::TYPE_PLUGIN, defined('COT_ADMIN'))
            );
        }

        return $this->tmpl;
    }
}