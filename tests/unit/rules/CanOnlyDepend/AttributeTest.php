<?php

declare(strict_types=1);

namespace Tests\PHPat\Unit\Rules\CanOnlyDepend;

use PHPat\Configuration;
use PHPat\Rule\Assertion\Relation\CanOnlyDepend\CanOnlyDepend;
use PHPat\Rule\Assertion\Relation\CanOnlyDepend\AttributeRule;
use PHPat\Selector\Classname;
use PHPat\Statement\Builder\StatementBuilderFactory;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPStan\Type\FileTypeMapper;
use Tests\PHPat\Fixtures\FixtureClass;
use Tests\PHPat\Fixtures\Simple\SimpleAttribute;
use Tests\PHPat\Unit\FakeTestParser;
use Tests\PHPat\Unit\ErrorMessage;

/**
 * @extends RuleTestCase<AttributeRule>
 */
class AttributeTest extends RuleTestCase
{
    public function testRule(): void
    {
        $this->analyse(['tests/fixtures/FixtureClass.php'], [
            [sprintf(ErrorMessage::SHOULD_NOT_DEPEND, FixtureClass::class, SimpleAttribute::class), 31],
            [sprintf(ErrorMessage::SHOULD_NOT_DEPEND, FixtureClass::class, SimpleAttribute::class), 36],
            [sprintf(ErrorMessage::SHOULD_NOT_DEPEND, FixtureClass::class, SimpleAttribute::class), 39],
            [sprintf(ErrorMessage::SHOULD_NOT_DEPEND, FixtureClass::class, SimpleAttribute::class), 43],
            [sprintf(ErrorMessage::SHOULD_NOT_DEPEND, FixtureClass::class, SimpleAttribute::class), 89],
        ]);
    }

    protected function getRule(): Rule
    {
        $testParser = FakeTestParser::create(
            CanOnlyDepend::class,
            [new Classname(FixtureClass::class, false)],
            [new Classname(\Attribute::class, false)]
        );

        return new AttributeRule(
            new StatementBuilderFactory($testParser),
            new Configuration(false),
            $this->createReflectionProvider(),
            self::getContainer()->getByType(FileTypeMapper::class)
        );
    }
}
