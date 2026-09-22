<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\tests\Classes;

use DraculAid\PhpTools\Classes\ClassNotPublicManager;
use DraculAid\PhpTools\Classes\ClassTools;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@coversDefaultClass ClassTools}
 *
 * @run php tests/run.php tests/Classes/ClassToolsTest.php
 */
class ClassToolsTest extends TestCase
{
    /**
     * Test for {@covers ClassTools::isLoad()}
     */
    public function testIsLoad(): void
    {
        $testFunctionIsLoad = ClassTools::isLoad(...);
        $testFunctionIsInternal = ClassTools::isInternal(...);

        $this->createTestTraitAndEnum($traitName, $enumName);

        // * * *

        self::assertTrue($testFunctionIsLoad(\stdClass::class));
        self::assertTrue($testFunctionIsLoad(static::class));

        self::assertTrue($testFunctionIsLoad(\Throwable::class));
        self::assertTrue($testFunctionIsLoad(Test::class));

        /**
         * @psalm-suppress PossiblyNullArgument Пслам не умеет нормально работать с "ссылками" (а эта переменная получает значение ппо ссылке выше
         * @psalm-suppress ArgumentTypeCoercion Пслам не умеет нормально работать с "ссылками" (а эта переменная получает значение ппо ссылке выше
         */
        self::assertTrue($testFunctionIsLoad($traitName));
        self::assertTrue($testFunctionIsLoad($enumName));

        /** @psalm-suppress UndefinedClass Мы знаем что такой класс не существует, проверяем, как функция отреагирует на такой кейс */
        self::assertFalse($testFunctionIsInternal(_______NoClassName_______::class));
    }

    /**
     * Test for {@covers ClassTools::isInternal()}
     */
    public function testIsInternal(): void
    {
        $testFunction = ClassTools::isInternal(...);

        self::assertTrue($testFunction(\stdClass::class));
        self::assertFalse($testFunction(static::class));
    }

    /**
     * Test for {@covers ClassTools::isAsArray()}
     */
    public function testIsAsArray(): void
    {
        $testFunction = ClassTools::isAsArray(...);

        self::assertFalse($testFunction(\stdClass::class));
        self::assertFalse($testFunction(new \stdClass));

        self::assertTrue($testFunction(\ArrayObject::class));
        self::assertTrue($testFunction(new \ArrayObject([])));
    }

    /**
     * Test for {@covers ClassTools::getNamespace()}
     * Test for {@covers ClassTools::getNameWithoutNamespace()}
     * Test for {@covers ClassTools::getNameAndNamespace()}
     */
    public function testGetNameOrNamespace(): void
    {
        $testFunctionGetNamespace = ClassTools::getNamespace(...);
        $testFunctionGetNameWithoutNamespace = ClassTools::getNameWithoutNamespace(...);
        $testFunctionGetNameAndNamespace = ClassTools::getNameAndNamespace(...);

        self::assertEquals('catalog\\subcatalog', $testFunctionGetNamespace('catalog\\subcatalog\\class'));
        self::assertEquals('catalog', $testFunctionGetNamespace('catalog\\class'));
        self::assertEquals('', $testFunctionGetNamespace('class'));

        // * * *

        self::assertEquals('class', $testFunctionGetNameWithoutNamespace('catalog\\subcatalog\\class'));
        self::assertEquals('class', $testFunctionGetNameWithoutNamespace('catalog\\class'));
        self::assertEquals('class', $testFunctionGetNameWithoutNamespace('class'));

        // * * *

        [$namespace, $name] = $testFunctionGetNameAndNamespace('catalog\\subcatalog\\class');
        self::assertEquals('catalog\\subcatalog', $namespace);
        self::assertEquals('class', $name);

        [$namespace, $name] = $testFunctionGetNameAndNamespace('catalog\\class');
        self::assertEquals('catalog', $namespace);
        self::assertEquals('class', $name);

        [$namespace, $name] = $testFunctionGetNameAndNamespace('class');
        self::assertEquals('', $namespace);
        self::assertEquals('class', $name);
    }

    /**
     * Test for {@covers ClassTools::createObject()}
     */
    public function testCreateObjectWithoutConstructor(): void
    {
        $testFunction = ClassTools::createObject(...);

        $className = get_class($this->createObject());
        $testObject = $testFunction($className, false, ['public_var' => '123', 'private_var' => 'ABC']);

        self::assertEquals("123", $testObject->public_var);
        self::assertEquals("construct_var_not_set", $testObject->construct_var);
        self::assertEquals("construct_argument_var_not_set", $testObject->construct_argument_var);
        self::assertEquals("ABC", ClassNotPublicManager::getInstanceFor($testObject)->get('private_var'));
    }

    /**
     * Test for {@covers ClassTools::createObject()}
     */
    public function testCreateObjectWithConstructor(): void
    {
        $testFunction = ClassTools::createObject(...);

        $className = get_class($this->createObject());
        $testObject = $testFunction($className, ['XXX'], ['public_var' => '123', 'private_var' => 'ABC']);

        self::assertEquals("123", $testObject->public_var);
        self::assertEquals("construct_var_set_ok", $testObject->construct_var);
        self::assertEquals("construct_argument_var_set_XXX", $testObject->construct_argument_var);
        self::assertEquals("ABC", ClassNotPublicManager::getInstanceFor($testObject)->get('private_var'));
    }

    /**
     * @param string|null $traitName
     * @param string|null $enumName
     * @return void
     */
    private function createTestTraitAndEnum(null|string &$traitName, null|string &$enumName): void
    {
        $traitName = '___test_trait_name_' . uniqid() . '___';
        $enumName = '___test_enum_name_' . uniqid() . '___';

        eval("trait {$traitName} {}");
        
        eval("enum {$enumName} {}");
    }

    /**
     * Создает объект для тестирования взаимодействия с непубличными элементами
     *
     * @return object
     */
    private function createObject(string $set_var = 'null'): object
    {
        $className = '___Test_Class_Name_' . uniqid() . '___';

        $classInner = <<<'CODE'
            public string $public_var = 'public_var_value';
            public string $construct_var = 'construct_var_not_set';
            public string $construct_argument_var = 'construct_argument_var_not_set';

            private string $private_var = 'private_var_value';
            private static string $private_static_var = 'private_static_var_value';

            public function __construct(string $set_var = 'null')
            {
                $this->construct_var = 'construct_var_set_ok';
                $this->construct_argument_var = "construct_argument_var_set_{$set_var}";
            }
        CODE;

        eval("class {$className} {{$classInner}}");

        /** @psalm-suppress InvalidStringClass Тут и правда имя класса */
        return new $className($set_var);
    }
}
