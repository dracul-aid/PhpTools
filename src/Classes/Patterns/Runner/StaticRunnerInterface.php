<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\Classes\Patterns\Runner;

/**
 * Интерфейс для "статических раннеров". Классов, имеющих статическую функцию `exe()` выполняющую основной
 * скоуп работ класса. Используется для классов выполняющих одно конкретное действие, можно сказать, что это классы-функции
 *
 * (!) Этот интерфейс служит для типизации набора классов, в нем нет описанного метода `exe()`, так как его наличие
 * не позволило бы создавать функции с различными наборами аргументов (ограничение PHP)
 *
 * Выполнить действие заложенное в класс, реализующие интерфейс можно вызывать как
 * <pre>
 * StaticRunnerInterface::exe()
 * </pre>
 */
interface StaticRunnerInterface {}
