<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\tests\Arrays\Objects\_resources;

use DraculAid\PhpTools\Arrays\Objects\ListObject;

/**
 * Тестовая версия {@see ListObject} с функциями, для получения "курсора" и "списка значений"
 */
class ListObjectForTesting extends ListObject
{
    /** @inheritdoc */
    protected bool $warningOn = false;

    /**
     * Вернет "по ссылке" курсор
     *
     * @return int
     */
    public function &getCursor(): int
    {
        return $this->cursor;
    }
}
