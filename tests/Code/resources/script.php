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
 */

// эта переменная во время теста будет передана по ссылке
$testCounter++;

echo "==={$varIn}===";

// эта переменная во время выполнения передана по значению
$varIn = 'ZZZZZ';

return 'test-return';
