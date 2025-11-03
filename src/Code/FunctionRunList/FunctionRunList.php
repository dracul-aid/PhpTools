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

/**
 * Создает список функций для последовательного выполнения
 *
 * Может использоваться, для накопления "действий", например, компоненту оплаты, необходимо выполнить рассылки писем,
 * * вы можете накопить функции отправки писем и выполнить их разом, сразу после завершения всех записей в БД
 * *
 * * Оглавление:
 * * <br>- {@see self::addFunction()} - Добавляет функцию в список выполняемых функций
 * * <br>- {@see self::run()} - Выполнит установленный список функций
 * * <br>- {@see self::getFailList()} - Вернет массив Ошибок/Исключений выброшенных при выполнении функций
 * * <br>- {@see self::getFailRollbackList()} - Вернет массив Ошибок/Исключений выброшенных при выполнении ролбек-функций
 *
 * @link https://github.com/dracul-aid/PhpTools/blob/master/Documentation-ru/FunctionRunList.md Докуметация (как это работает)
 */
final class FunctionRunList extends AbstractFunctionRunList
{
    /** @inheritdoc */
    protected function beforeRun(): bool
    {
        return true;
    }

    /** @inheritdoc */
    protected function afterRun(): void {}
}
