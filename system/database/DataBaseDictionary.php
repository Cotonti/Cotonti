<?php

namespace cot\database;

class DataBaseDictionary
{
    /**
     * Isolation level `READ UNCOMMITTED`.
     * @see https://en.wikipedia.org/wiki/Isolation_%28database_systems%29#Isolation_levels
     */
    public const READ_UNCOMMITTED = 'READ UNCOMMITTED';

    /**
     * Isolation  `READ COMMITTED`.
     */
    const READ_COMMITTED = 'READ COMMITTED';

    /**
     * Isolation level `REPEATABLE READ`.
     */
    const REPEATABLE_READ = 'REPEATABLE READ';

    /**
     * Isolation level `SERIALIZABLE`.
     */
    const SERIALIZABLE = 'SERIALIZABLE';
}