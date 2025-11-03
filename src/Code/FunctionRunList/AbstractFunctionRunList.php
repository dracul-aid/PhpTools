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

use DraculAid\PhpTools\Classes\Patterns\Runner\RunnerInterface;
use DraculAid\PhpTools\tests\Code\FunctionRunList\AbstractFunctionRunListTest;
use DraculAid\PhpTools\tests\Code\FunctionRunList\AbstractFunctionRunExecuteTest;

/**
 * Абстрактный класс для создания списка функций для последовательного выполнения
 *
 * Может использоваться, для накопления "действий", например, компоненту оплаты, необходимо выполнить рассылки писем,
 * вы можете накопить функции отправки писем и выполнить их разом, сразу после завершения всех записей в БД
 *
 * См также самую простую реализацию такого списка - {@see FunctionRunList}
 *
 * Оглавление:
 * <br>- {@see self::addFunction()} - Добавляет функцию в список выполняемых функций
 * <br>- {@see self::run()} - Выполнит установленный список функций
 * <br>- {@see self::getFailList()} - Вернет массив Ошибок/Исключений выброшенных при выполнении функций
 * <br>- {@see self::getFailRollbackList()} - Вернет массив Ошибок/Исключений выброшенных при выполнении ролбек-функций
 *
 * Test cases for class {@see AbstractFunctionRunListTest} и {@see AbstractFunctionRunExecuteTest}
 *
 * @link https://github.com/dracul-aid/PhpTools/blob/master/Documentation-ru/FunctionRunList.md Докуметация (как это работает)
 */
abstract class AbstractFunctionRunList implements RunnerInterface
{
    /** Внутренние компоненты "Объекта-Транзакции" */
    protected FunctionTransactionElements $elements;

    /**
     * Создает список функций для последовательного выполнения
     */
    public function __construct()
    {
        $this->elements = new ($this->getFunctionTransactionElementsClass());
    }

    /**
     * Добавляет функцию в список выполняемых функций
     *
     * @param   callable|callable(bool): void              $function             Функция для выполнения
     *                                                                           (примет FALSE, если хотя бы одна предыдущая функция упала при выполнении)
     * @param   null|callable|callable(\Throwable): void   $functionRollback     Функция будет выполнена, если в ходе выполнения $function была выброшена ошибка или исключение
     *                                                                           (Примет объект-ошибку, с которой упала $function)
     *
     * @return $this
     */
    public function addFunction(callable $function, null|callable $functionRollback = null): self
    {
        $newPosition = count($this->elements->functionList);

        /** @var FunctionElement $functionObject */
        $functionObject = new ($this->getFunctionElementClass());
        $functionObject->initVars($this->elements, $newPosition, $function, $functionRollback);

        $this->elements->functionList[$newPosition] = $functionObject;

        return $this;
    }

    /**
     * Выполнит установленный список функций
     *
     * (!) Если {@see self::beforeRun()} Вернет FALSE, список функций не будет выполнен, а сама функция вернет 0 (т.е. ошибок не было).
     *
     * @return int<0, max> Вернет кол-во пойманных исключений/ошибок в ходе выполнения "списка функций". 0 - все выполнилось без ошибок.
     */
    public function run(): int
    {
        if ($this->beforeRun() === false) return 0;

        foreach ($this->elements->functionList as $function) $function->run();

        $this->afterRun();

        return count($this->elements->failList) + count($this->elements->failRollbackList);
    }

    /**
     * Вернет массив Ошибок/Исключений выброшенных при выполнении функций. Ключ - "позиция" функции, значение - пойманное исключение
     * (т.е. ошибки выброшенные в ходе выполнения $function в addFunction())
     *
     * @return array<int<0, max>, \Throwable>
     */
    public function getFailList(): array
    {
        return $this->elements->failList;
    }

    /**
     * Вернет массив Ошибок/Исключений выброшенных при выполнении ролбек-функций. Ключ - "позиция" функции, значение - пойманное исключение
     * (т.е. ошибки выброшенные в ходе выполнения $functionRollback в addFunction())
     *
     * @return array<int<0, max>, \Throwable>
     */
    public function getFailRollbackList(): array
    {
        return $this->elements->failRollbackList;
    }

    /**
     * Выполнится ПЕРЕД началом выполнения установленного списка функций
     *
     * @return bool Если вернет FALSE, список функций не будет выполнен
     */
    abstract protected function beforeRun(): bool;

    /**
     * Выполнится ПОСЛЕ выполнения установленного списка функций
     *
     * @return void
     */
    abstract protected function afterRun(): void;

    /**
     * Вернет имя класса для хранения внутренних компонентов "Объекта-Транзакции"
     *
     * @return class-string<FunctionTransactionElements>
     */
    protected function getFunctionTransactionElementsClass(): string
    {
        return FunctionTransactionElements::class;
    }

    /**
     * Вернет имя класса для хранения функций в списке транзакций
     *
     * @return class-string<FunctionElement>
     */
    protected function getFunctionElementClass(): string
    {
        return FunctionElement::class;
    }
}
