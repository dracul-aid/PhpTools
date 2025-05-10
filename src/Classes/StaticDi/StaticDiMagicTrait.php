<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\Classes\StaticDi;

/**
 * Трейт для имитации реализации "Di для статических классов"
 *
 * @link https://github.com/dracul-aid/PhpTools/blob/master/Documentation-ru/StaticDi.md Докуметация (как это работает)
 *
 * Test cases for class {@see StaticDiMagicTraitTest}
 */
trait StaticDiMagicTrait
{
    public static function __callStatic(string $name, array $arguments): mixed
    {
        return [StaticDi::getDefaultInstance()->getClass(static::class), $name](... $arguments);
    }
}
