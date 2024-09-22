<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Для тестирования загрузки файлов в {@see \DraculAid\PhpTools\tests\Code\ScriptLoaderTest}
 *
 * В коде еть куски помеченные как "Что бы PSLAM не сходил с ума", пришлось их вставить, так как заглушить проблему
 * получалось только через "техдолг", по какой-то причине ПСАЛМ отказывался воспринимать тег @psalm-suppress
 */

// Что бы PSLAM не сходил с ума - НАЧАЛО
if (!isset($testCounter)) $testCounter = -100;
if (!isset($varIn)) $varIn = 'for-psalm';
// Что бы PSLAM не сходил с ума - КОНЕЦ


// эта переменная во время теста будет передана по ссылке
$testCounter++;

echo "==={$varIn}===";

// эта переменная во время выполнения передана по значению
$varIn = 'ZZZZZ';

// Что бы PSLAM не сходил с ума - НАЧАЛО
$testCounter === $testCounter;
$varIn === $varIn;
// Что бы PSLAM не сходил с ума - КОНЕЦ

return 'test-return';
