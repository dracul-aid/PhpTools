<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\tests\Code;

use DraculAid\PhpTools\Code\ScriptLoader;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see ScriptLoader}
 *
 * @run php tests/run.php tests/Code/ScriptLoaderTest.php
 *
 * @runTestsInSeparateProcesses Все тест-методы в данном классе будут вызваться в отдельном потоке
 */
class ScriptLoaderTest extends TestCase
{
    /** Путь до тестового скрипта */
    private const SCRIPT_PATH = __DIR__ . '/resources/script.php';

    /** Переменная, которая будет передаваться по значению (не будет меняться) */
    private string $varIn = 'ABC';

    /** Переменная, будет передаваться по ссылке (для отслеживания изменений) */
    private int $testCounter = 0;

    /**
     * Test for {@see ScriptLoader::exeRequireOnce()}
     *
     * @return void
     */
    public function testExeRequireOnce(): void
    {
        $this->forOnceScript([ScriptLoader::class, 'exeRequireOnce'], false);
    }

    /**
     * Test for {@see ScriptLoader::exeIncludeOnce()}
     *
     * @return void
     */
    public function testExeIncludeOnce(): void
    {
        $this->forOnceScript([ScriptLoader::class, 'exeIncludeOnce'], false);
    }

    /**
     * Test for {@see ScriptLoader::obRequireOnce()}
     *
     * @return void
     */
    public function testObRequireOnce(): void
    {
        $this->forOnceScript([ScriptLoader::class, 'obRequireOnce'], true);
    }

    /**
     * Test for {@see ScriptLoader::obIncludeOnce()}
     *
     * @return void
     */
    public function testObIncludeOnce(): void
    {
        $this->forOnceScript([ScriptLoader::class, 'obIncludeOnce'], true);
    }

    /**
     * Test for {@see ScriptLoader::exeRequire()}
     * Test for {@see ScriptLoader::exeInclude()}
     * Test for {@see ScriptLoader::obRequire()}
     * Test for {@see ScriptLoader::obInclude()}
     */
    public function testRequireAndInclude(): void
    {
        // передаем переменную по ссылке (отслеживаем изменение переменной)
        $this->testCounter = 0;

        // * * * Тест без перехвата вывода

        ob_start();

        $this->forRequireAndInclude([ScriptLoader::class, 'exeRequire'], false, 1);
        $this->forRequireAndInclude([ScriptLoader::class, 'exeRequire'], false, 2);
        $this->forRequireAndInclude([ScriptLoader::class, 'exeInclude'], false, 3);
        $this->forRequireAndInclude([ScriptLoader::class, 'exeInclude'], false, 4);

        ob_end_clean();

        // * * * Тест с перехватом вывода

        $this->forRequireAndInclude([ScriptLoader::class, 'obRequire'], true, 5);
        $this->forRequireAndInclude([ScriptLoader::class, 'obRequire'], true, 6);
        $this->forRequireAndInclude([ScriptLoader::class, 'obInclude'], true, 7);
        $this->forRequireAndInclude([ScriptLoader::class, 'obInclude'], true, 8);
    }

    public function testEval(): void
    {
        // передаем переменную по ссылке (отслеживаем изменение переменной)
        $this->testCounter = 0;

        // PHP код для выполнения в тесте
        $phpCode = <<<CODE
// эта переменная во время теста будет передана по ссылке
\$testCounter++;

echo "==={\$varIn}===";

// эта переменная во время выполнения передана по значению
\$varIn = 'ZZZZZ';

return 'test-return';
CODE;

        // * * * Тест без перехвата вывода

        ob_start();

        self::assertEquals(
            'test-return',
            ScriptLoader::exeEval($phpCode, ['varIn' => $this->varIn], ['testCounter' => &$this->testCounter])
        );
        self::assertEquals(1, $this->testCounter);
        self::assertEquals('ABC', $this->varIn);
        self::assertEquals('===ABC===', ob_get_contents());

        ob_end_clean();

        // * * * Тест с перехватом вывода

        $functionReturn = null;
        self::assertEquals(
            '===ABC===',
            ScriptLoader::obEval($phpCode, ['varIn' => $this->varIn], ['testCounter' => &$this->testCounter], $functionReturn)
        );
        self::assertEquals(2, $this->testCounter);
        self::assertEquals('ABC', $this->varIn);
        self::assertEquals('test-return', $functionReturn);
    }

    private function forOnceScript(callable $testFunction, bool $withOb): void
    {
        if (!$withOb) ob_start();

        // передаем переменную по значению
        $this->varIn = 'ABC';
        // передаем переменную по ссылке (отслеживаем изменение переменной)
        $this->testCounter = 0;

        // * * * присоединяем файл

        if (!$withOb)
        {
            self::assertEquals(
                'test-return',
                $testFunction(self::SCRIPT_PATH, ['varIn' => $this->varIn], ['testCounter' => &$this->testCounter])
            );
            self::assertEquals('===ABC===', ob_get_contents());
            ob_clean();
        }
        else
        {
            $functionReturn = null;
            self::assertEquals(
                '===ABC===',
                $testFunction(self::SCRIPT_PATH, ['varIn' => $this->varIn], ['testCounter' => &$this->testCounter], $functionReturn)
            );
            self::assertEquals('test-return', $functionReturn);
        }

        self::assertEquals(1, $this->testCounter);
        self::assertEquals('ABC', $this->varIn);

        // * * * повторное присоединение не случится

        $testFunction(self::SCRIPT_PATH, ['varIn' => $this->varIn], ['testCounter' => &$this->testCounter]);
        self::assertEquals(1, $this->testCounter);
        self::assertEquals('ABC', $this->varIn);

        // * * *

        if (!$withOb) ob_end_clean();
    }

    private function forRequireAndInclude(callable $testFunction, bool $withOb, int $testCounterValue): void
    {
        if ($withOb)
        {
            $functionReturn = null;
            self::assertEquals(
                '===ABC===',
                $testFunction(self::SCRIPT_PATH, ['varIn' => $this->varIn], ['testCounter' => &$this->testCounter], $functionReturn)
            );
        }
        else
        {
            $functionReturn = $testFunction(self::SCRIPT_PATH, ['varIn' => $this->varIn], ['testCounter' => &$this->testCounter]);
            self::assertEquals('===ABC===', ob_get_contents());
            ob_clean();
        }

        self::assertEquals($testCounterValue, $this->testCounter);
        self::assertEquals('ABC', $this->varIn);
        self::assertEquals('test-return', $functionReturn);
    }
}
