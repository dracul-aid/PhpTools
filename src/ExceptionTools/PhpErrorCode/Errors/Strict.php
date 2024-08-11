<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\ExceptionTools\PhpErrorCode\Errors;

use DraculAid\PhpTools\ExceptionTools\PhpErrorCode\Errors\Interfaces\PhpCodeErrorInterface;

/**
 * Предупреждения времени выполнения (Включаются для того, чтобы PHP предлагал изменения в коде, которые обеспечат лучшее взаимодействие и совместимость кода)
 */
class Strict extends \Error implements PhpCodeErrorInterface
{
    /** @inheritdoc */
    public static function getPhpErrorCode(): int
    {
        return E_STRICT;
    }
}