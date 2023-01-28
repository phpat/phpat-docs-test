<?php

declare(strict_types=1);

namespace Tests\PHPat\Unit\Rules\ShouldExtend;

use PHPat\Configuration;
use PHPat\Rule\Assertion\Relation\ShouldExtend\ParentClassRule;
use PHPat\Rule\Assertion\Relation\ShouldExtend\ShouldExtend;
use PHPat\Selector\Classname;
use PHPat\Statement\Builder\StatementBuilderFactory;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPStan\Type\FileTypeMapper;
use Tests\PHPat\Fixtures\FixtureClass;
use Tests\PHPat\Fixtures\Simple\SimpleAbstractClassTwo;
use Tests\PHPat\Unit\FakeTestParser;
use Tests\PHPat\Unit\ErrorMessage;

/**
 * @extends RuleTestCase<ParentClassRule>
 */
class ParentClassTest extends RuleTestCase
{
    public function testRule(): void
    {
        $this->analyse(['tests/fixtures/FixtureClass.php'], [
            [sprintf(ErrorMessage::SHOULD_EXTEND, FixtureClass::class, SimpleAbstractClassTwo::class), 31],
        ]);
    }

    protected function getRule(): Rule
    {
        $testParser = FakeTestParser::create(
            ShouldExtend::class,
            [new Classname(FixtureClass::class, false)],
            [new Classname(SimpleAbstractClassTwo::class, false)]
        );

        return new ParentClassRule(
            new StatementBuilderFactory($testParser),
            new Configuration(false),
            $this->createReflectionProvider(),
            self::getContainer()->getByType(FileTypeMapper::class)
        );
    }
}
