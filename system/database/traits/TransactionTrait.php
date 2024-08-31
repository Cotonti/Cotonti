<?php

declare(strict_types=1);

namespace cot\database\traits;

use Closure;
use cot\database\exceptions\DeadlockException;
use PDOException;
use Throwable;

trait TransactionTrait
{
    /**
     * @var int
     */
    protected $transactionLevel = 0;

    /**
     * Executes callback provided in a transaction.
     *
     * @param Closure $callback a valid PHP callback that performs the job. Accepts connection instance as parameter.
     * @param ?string $isolationLevel The isolation level to use for this transaction.
     * @param int $attempts defines the number of times a transaction should be retried when a deadlock occurs.
     *    Once these attempts have been exhausted, an exception will be thrown
     * @return mixed result of callback function
     * @throws Throwable if there is any exception during query. In this case the transaction will be rolled back.
     */
    public function transaction(Closure $callback, ?string $isolationLevel = null, int $attempts = 1)
    {
        for ($currentAttempt = 1; $currentAttempt <= $attempts; $currentAttempt++) {
            $this->beginTransaction($isolationLevel);

            try {
                $result = $callback($this);
            } catch (Throwable $e) {
                $this->handleTransactionException(
                    $e, $currentAttempt, $attempts
                );
                continue;
            }

            try {
                $this->commit();
            } catch (Throwable $e) {
                $this->handleCommitTransactionException(
                    $e, $currentAttempt, $attempts
                );

                continue;
            }


            return $result;
        }

        return null;
    }

    /**
     * Starts a transaction.
     * @param ?string $isolationLevel The isolation level to use for this transaction.
     */
    public function beginTransaction(?string $isolationLevel = null): void
    {
        $this->createTransaction($isolationLevel);
        $this->transactionLevel++;
    }

    protected function createTransaction(?string $isolationLevel = null): void
    {
        if ($this->transactionLevel === 0) {
            if ($isolationLevel !== null) {
                $this->setTransactionIsolationLevel($isolationLevel);
            }
            try {
                $this->adapter->beginTransaction();
            } catch (Throwable $e) {
                $this->handleBeginTransactionException($e);
            }
            return;
        }

        if (!$this->supportsSavepoints()) {
            return;
        }

        if ($this->adapter->inTransaction()) {
            $this->createSavepoint('LEVEL_' . $this->transactionLevel);
        }
    }

    /**
     * @param ?string $isolationLevel
     */
    public function setTransactionIsolationLevel(string $isolationLevel): void
    {
        $this->query("SET TRANSACTION ISOLATION LEVEL $isolationLevel")->execute();
    }

    public function supportsSavepoints(): bool
    {
        return true;
    }

    public function commit()
    {
        $this->transactionLevel--;
        if ($this->transactionLevel === 0) {
            // make sure the transaction wasn't autocommitted
            if ($this->adapter->inTransaction()) {
                $this->adapter->commit();
            }
            return;
        }

        // @todo is it really needed?
//        if ($this->supportsSavepoints()) {
//            if ($this->adapter->inTransaction()) {
//                $this->releaseSavepoint('LEVEL_' . $this->transactionLevel);
//            }
//        }
    }

    /**
     * Rolls back a transaction.
     */
    public function rollBack(?int $toLevel = null): void
    {
        $toLevel = $toLevel === null ? $this->transactionLevel - 1 : $toLevel;
        if ($toLevel < 0 || $toLevel >= $this->transactionLevel) {
            return;
        }

        try {
            $this->performRollBack($toLevel);
        } catch (Throwable $e) {
            $this->handleRollBackException($e);
        }

        $this->transactionLevel = $toLevel;
    }

    protected function performRollBack(int $toLevel): void
    {
        if ($toLevel === 0) {
            if ($this->adapter->inTransaction()) {
                $this->adapter->rollBack();
            }
            return;
        }

        if ($this->supportsSavepoints()) {
            if ($this->adapter->inTransaction()) {
                $this->rollBackSavepoint('LEVEL_' . $toLevel);
            }
        }
    }

    protected function createSavepoint(string $name): void
    {
        $this->query("SAVEPOINT $name")->execute();
    }

    protected function releaseSavepoint(string $name): void
    {
        // For some reason it does not work in MySQL with PDO
        //$this->query("RELEASE SAVEPOINT $name")->execute();
    }

    public function rollBackSavepoint($name)
    {
        $this->query("ROLLBACK TO SAVEPOINT $name")->execute();
    }

    /**
     * Handle an exception encountered when begin a transacted.
     */
    protected function handleBeginTransactionException(Throwable $e): void
    {
        if ($this->isLostConnectionError($e)) {
            $this->reconnect();

            $this->adapter->beginTransaction();
            return;
        }
        throw $e;
    }

    protected function handleCommitTransactionException(Throwable $e, int $currentAttempt, int $maxAttempts): void
    {
        $this->transactionLevel = max(0, $this->transactionLevel - 1);

        if ($this->isConcurrencyError($e)) {
            if ($currentAttempt < $maxAttempts) {
                return;
            }
            throw new DeadlockException($e->getMessage(), is_int($e->getCode()) ? $e->getCode() : 0, $e);
        }

        if ($this->isLostConnectionError($e)) {
            $this->transactionLevel = 0;
        }

        throw $e;
    }

    protected function handleRollBackException(Throwable $e)
    {
        if ($this->isLostConnectionError($e)) {
            $this->transactionLevel = 0;
        }

        throw $e;
    }

    /**
     * Handle an exception encountered when running a transacted statement.
     */
    protected function handleTransactionException(Throwable $e, int $currentAttempt, int $maxAttempts)
    {
        // On a deadlock, MySQL rolls back the entire transaction so we can't just
        // retry the query. We have to throw this exception all the way out and
        // let the developer handle it in another way. We will decrement too.
        if ($this->isConcurrencyError($e)) {
            if ($this->transactionLevel > 1) {
                $this->transactionLevel--;
                throw new DeadlockException($e->getMessage(), is_int($e->getCode()) ? $e->getCode() : 0, $e);
            }

            $this->rollBack();

            if ($currentAttempt < $maxAttempts) {
                return;
            }
        }

        throw $e;
    }

    protected function isConcurrencyError(Throwable $e): bool
    {
        if ($e instanceof PDOException && ($e->getCode() === 40001 || $e->getCode() === '40001')) {
            return true;
        }

        $needles = [
            'Deadlock found when trying to get lock',
            'deadlock detected',
            'The database file is locked',
            'database is locked',
            'database table is locked',
            'A table in the database is locked',
            'has been chosen as the deadlock victim',
            'Lock wait timeout exceeded; try restarting transaction',
            'WSREP detected deadlock/conflict and aborted the transaction. Try restarting the transaction',
        ];

        $haystack = mb_strtolower($e->getMessage());

        foreach ($needles as $needle) {
            if (mb_stripos($haystack, $needle) !== null) {
                return true;
            }
        }

        return false;
    }
}