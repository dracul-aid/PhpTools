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

use DraculAid\PhpTools\Classes\ClassParents;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see ClassParents}
 *
 * @run php tests/run.php tests/Classes/ClassParentsTest.php
 */
class ClassParentsTest extends TestCase
{
    /** @var class-string */
    private string $className;
    /** @var class-string[] */
    private array $classParentNames;

    /**
     * Test for {@see ClassParents::getAllParents()}
     */
    public function testGetAllParents(): void
    {
        $this->generateClass();

        $testParents = ClassParents::getAllParents($this->className);

        self::assertCount(count($this->classParentNames), $testParents);

        foreach ($this->classParentNames as $varName => $className)
        {
            self::assertArrayHasKey($className, $testParents, "Not Found element {$varName}, className {$className}");
        }
    }

    /**
     * Test for {@see ClassParents::getWithoutInterfaces()}
     */
    public function testGetWithoutInterfaces(): void
    {
        $this->generateClass();

        $testParents = ClassParents::getWithoutInterfaces($this->className);

        $traits = [];
        foreach ($this->classParentNames as $varName => $className)
        {
            if (!(bool)strpos($varName,'Interface')) $traits[$varName] = $className;
        }

        self::assertCount(count($traits), $testParents);

        foreach ($traits as $varName => $className)
        {
            self::assertArrayHasKey($className, $testParents, "Not Found element {$varName}, className {$className}");
        }
    }

    /**
     * Test for {@see ClassParents::getTraits()}
     */
    public function testGetTraits(): void
    {
        $this->generateClass();

        $testParents = ClassParents::getTraits($this->className);

        $traits = [];
        foreach ($this->classParentNames as $varName => $className)
        {
            if (strpos($varName,'Trait') > 0) $traits[$varName] = $className;
        }

        self::assertCount(count($traits), $testParents);

        foreach ($traits as $varName => $className)
        {
            self::assertArrayHasKey($className, $testParents, "Not Found element {$varName}, className {$className}");
        }
    }

    private function generateClass(): void
    {
        $this->className = $this->getClassName();

        $this->classParentNames['testParent1'] = $this->getClassName();
        $this->classParentNames['testParent2'] = $this->getClassName();

        $this->classParentNames['testInnerInterface1'] = $this->getClassName();
        $this->classParentNames['testInnerInterface2'] = $this->getClassName();
        $this->classParentNames['testClassInterface1'] = $this->getClassName();
        $this->classParentNames['testClassInterface2'] = $this->getClassName();
        $this->classParentNames['testClassInterface3'] = $this->getClassName();
        $this->classParentNames['testParent1Interface1'] = $this->getClassName();
        $this->classParentNames['testParent1Interface2'] = $this->getClassName();
        $this->classParentNames['testParent2Interface1'] = $this->getClassName();
        $this->classParentNames['testParent2Interface2'] = $this->getClassName();

        $this->classParentNames['testInnerTrait1'] = $this->getClassName();
        $this->classParentNames['testInnerTrait2'] = $this->getClassName();
        $this->classParentNames['testInnerTrait3'] = $this->getClassName();
        $this->classParentNames['testClassTrait1'] = $this->getClassName();
        $this->classParentNames['testClassTrait2'] = $this->getClassName();
        $this->classParentNames['testParent1Trait1'] = $this->getClassName();
        $this->classParentNames['testParent1Trait2'] = $this->getClassName();
        $this->classParentNames['testParent2Trait1'] = $this->getClassName();
        $this->classParentNames['testParent2Trait2'] = $this->getClassName();

        foreach ($this->classParentNames as $name => $value)
        {
            /** @psalm-suppress PropertyTypeCoercion мы на самом деле составляем тут список имен классов */
            $this->classParentNames[$name] = $value . $name;
        }

        eval(<<<CODE
                interface {$this->classParentNames['testInnerInterface1']} {}
                interface {$this->classParentNames['testInnerInterface2']} {}
                interface {$this->classParentNames['testClassInterface1']} extends {$this->classParentNames['testInnerInterface1']}, {$this->classParentNames['testInnerInterface2']} {}
                interface {$this->classParentNames['testClassInterface2']} {}
                interface {$this->classParentNames['testClassInterface3']} {}
                interface {$this->classParentNames['testParent1Interface1']} {}
                interface {$this->classParentNames['testParent1Interface2']} {}
                interface {$this->classParentNames['testParent2Interface1']} {}
                interface {$this->classParentNames['testParent2Interface2']} {}
                
                trait {$this->classParentNames['testInnerTrait1']} {}
                trait {$this->classParentNames['testInnerTrait2']}
                {
                    use {$this->classParentNames['testInnerTrait1']};
                }
                trait {$this->classParentNames['testInnerTrait3']} {}
                trait {$this->classParentNames['testClassTrait1']}
                {
                    use {$this->classParentNames['testInnerTrait2']};
                    use {$this->classParentNames['testInnerTrait3']};                 
                }
                trait {$this->classParentNames['testClassTrait2']} {}
                trait {$this->classParentNames['testParent1Trait1']} {}
                trait {$this->classParentNames['testParent1Trait2']} {}
                trait {$this->classParentNames['testParent2Trait1']} {}
                trait {$this->classParentNames['testParent2Trait2']} {}
                
                class {$this->classParentNames['testParent1']} implements {$this->classParentNames['testParent1Interface1']} , {$this->classParentNames['testParent1Interface2']}
                {
                    use {$this->classParentNames['testParent1Trait1']};
                    use {$this->classParentNames['testParent1Trait2']};
                }
                class {$this->classParentNames['testParent2']} extends {$this->classParentNames['testParent1']} implements {$this->classParentNames['testParent2Interface1']} , {$this->classParentNames['testParent2Interface2']}
                {
                    use {$this->classParentNames['testParent2Trait1']};
                    use {$this->classParentNames['testParent2Trait2']};
                } 
            
                class {$this->className} extends {$this->classParentNames['testParent2']} implements {$this->classParentNames['testClassInterface1']}, {$this->classParentNames['testClassInterface2']}, {$this->classParentNames['testClassInterface3']}
                {
                    use {$this->classParentNames['testClassTrait1']};
                    use {$this->classParentNames['testClassTrait2']};
                }
            CODE
        );
    }

    /**
     * @return class-string
     *
     * @psalm-suppress MoreSpecificReturnType Псалм считает что мы возвращаем не имя класса, но мы будем использовать эту строку как имя класса
     */
    private function getClassName(): string
    {
        /** @psalm-suppress LessSpecificReturnStatement Псалм считает что это не имя класса, но мы будем использовать эту строку как имя класса */
        return '___test_class_name_' . uniqid() . '___';
    }
}
