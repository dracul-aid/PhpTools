<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\Code\FunctionRunList;

use DraculAid\PhpTools\Code\CallFunctionHelper;
use DraculAid\PhpTools\tests\Code\FunctionRunList\AbstractFunctionRunListTest;
use DraculAid\PhpTools\tests\Code\FunctionRunList\AbstractFunctionRunExecuteTest;

/**
 * Структура для хранения и выполнения функции из списка функций ({@see AbstractFunctionRunList})
 *
 * Оглавление:
 * <br>- {@see self::initVars()} - Массовая установка свойств
 * <br>- {@see self::run()} - Выполнит функцию
 *
 * Test cases for class - Тестируется в рамках {@see AbstractFunctionRunListTest} и {@see AbstractFunctionRunExecuteTest}
 */
class FunctionElement
{
    /** Структура для хранения свойств объекта "списка функций" */
    readonly protected FunctionTransactionElements $transactionElements;

    /**
     * Позиция функции в списке функций (см позицию в {@see FunctionTransactionElements::$functionList})
     * @var int<0, max>
     */
    readonly protected int $number;

    /**
     * Функция, которую нужно выполнить
     * (примет FALSE, если хотя бы одна предыдущая функция упала при выполнении)
     *
     * @var callable|callable(bool): void
     */
    readonly protected string|array|object $function;

    /**
     * Функция, которая должна выполниться, если упало выполнение $function, NULL - в случае падения ничего делать не надо
     * (Примет объект-ошибку, с которой упала $function)
     *
     * @var null|callable|callable(\Throwable): void
     */
    readonly protected null|string|array|object $localeRolledBackFunction;

    /**
     * Массовая установка свойств
     *
     * @param   FunctionTransactionElements                $transactionElements        Структура для хранения свойств объекта "списка функций"
     * @param   int<0, max>                                $number                     Позиция функции в списке функций (см позицию в {@see FunctionTransactionElements::$functionList})
     * @param   callable|callable(bool): void              $function                   Функция, которую нужно выполнить
     * @param   null|callable|callable(\Throwable): void   $localeRolledBackFunction   Функция, которая должна выполниться, если упало выполнение $function, NULL - в случае падения ничего делать не надо
     *
     * @return void
     *
     * @psalm-suppress InaccessibleProperty PSALM не умеет корректно работать с readonly свойствами, он считает, что устанавливать их можно только в конструкторе
     */
    public function initVars(FunctionTransactionElements $transactionElements, int $number, callable $function, null|callable $localeRolledBackFunction): void
    {
        $this->transactionElements = $transactionElements;
        $this->number = $number;
        /** @psalm-suppress InvalidPropertyAssignmentValue в PHP нет возможности описать свойство класса, как нативное callable */
        $this->function = $function;
        /** @psalm-suppress InvalidPropertyAssignmentValue в PHP нет возможности описать свойство класса, как нативное callable */
        $this->localeRolledBackFunction = $localeRolledBackFunction;
    }

    /**
     * Выполнит функцию
     *
     * @return void
     */
    public function run(): void
    {
        try
        {
            CallFunctionHelper::exe($this->function, count($this->transactionElements->failList) === 0);
        }
        catch (\Throwable $throwable)
        {
            $this->transactionElements->failList[$this->number] = $throwable;

            if ($this->localeRolledBackFunction !== null)
            {
                try
                {
                    CallFunctionHelper::exe($this->localeRolledBackFunction, $throwable);
                }
                catch (\Throwable $throwable)
                {
                    $this->transactionElements->failRollbackList[$this->number] = $throwable;
                }
            }
        }
    }
}
