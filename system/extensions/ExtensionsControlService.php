<?php
/**
 * @package Extensions
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\extensions;

use Cot;
use cot\traits\GetInstanceTrait;
use InvalidArgumentException;
use Throwable;

/**
 * Extensions control Service
 * @package Extensions
 */
class ExtensionsControlService
{
    use GetInstanceTrait;

    /**
     * Check if extension is active and update data in "cot_core" table
     * @return void
     * @todo more understandable method name
     */
    public function checkIsActive(string $extensionCode): bool
    {
        if ($this->hasActiveParts($extensionCode)) {
            $data = ['ct_state' => 1];
        } else {
            $data = ['ct_state' => 0];
        }

        $result = Cot::$db->update(Cot::$db->core, $data, 'ct_code = :code', ['code' => $extensionCode]);

        return $result > 0;
    }

    /**
     * Suspends a plugin or one of its parts
     *
     * @param string $extensionCode Module or plugin code
     * @param int|string|null $part ID of the extension part to suspend or NULL to suspend all;
     *  if part name is passed, then that part is suspended
     * @return bool
     * @throws Throwable
     */
    public function pause(string $extensionCode, $part = null): bool
    {
        $condition = 'pl_code = :code';
        $params = ['code' => $extensionCode];
        if (is_numeric($part)) {
            if ($part <= 0) {
                throw new InvalidArgumentException();
            }
            $condition .= ' AND pl_id = :pluginId';
            $params['pluginId'] = $part;
        } elseif (is_string($part)) {
            $condition .= ' AND pl_part = :part';
            $params['part'] = $part;
        }

        Cot::$db->beginTransaction();
        try {
            $result = Cot::$db->update(Cot::$db->plugins, ['pl_active' => 0], $condition, $params);
            if ($result < 1) {
                return false;
            }

            $this->checkIsActive($extensionCode);

            Cot::$db->commit();
        } catch (Throwable $e) {
            Cot::$db->rollBack();
            throw $e;
        }

        return true;
    }

    /**
     * Resumes a suspended extension or one of its parts
     *
     * @param string $extensionCode Module or plugin code
     * @param mixed $part ID of the extension part to resume or NULL to resume all;
     *  if part name is passed, then that part is resumed
     * @return bool
     */
    public function resume(string $extensionCode, $part = null): bool
    {
        $condition = 'pl_code = :code';
        $params = ['code' => $extensionCode];
        if (is_numeric($part)) {
            if ($part <= 0) {
                throw new InvalidArgumentException();
            }
            $condition .= ' AND pl_id = :pluginId';
            $params['pluginId'] = $part;
        } elseif (is_string($part)) {
            $condition .= ' AND pl_part = :part';
            $params['part'] = $part;
        }

        Cot::$db->beginTransaction();
        try {
            $result = Cot::$db->update(Cot::$db->plugins, ['pl_active' => 1], $condition, $params) > 0;
            if ($result < 1) {
                return false;
            }

            Cot::$db->update(
                Cot::$db->core,
                ['ct_state' => 1],
                'ct_code = :code',
                ['code' => $extensionCode]
            ) > 0;

            Cot::$db->commit();
        } catch (Throwable $e) {
            Cot::$db->rollBack();
            throw $e;
        }

        return true;
    }

    private function hasActiveParts(string $extensionCode): bool
    {
        $service = ExtensionsService::getInstance();
        if (
            $service->getDefaultAction($extensionCode) !== null
            || $service->getDefaultAction($extensionCode, '', true) !== null
        ) {
            return true;
        }

        $activeCount = Cot::$db->query(
            'SELECT COUNT(*) FROM ' . Cot::$db->plugins. ' WHERE pl_code = :code AND pl_active = 1',
            ['code' => $extensionCode]
        )->fetchColumn();

        return $activeCount > 1;
    }
}