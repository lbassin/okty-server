<?php

namespace App\Tests\Builder\Validator;

use App\Builder\Validator\EnvironmentConstraint;
use App\Builder\Validator\EnvironmentConstraintValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class EnvironmentValidatorTest extends TestCase
{
    /** @var ConstraintValidator */
    private $validator;
    /** @var Constraint */
    private $constraint;
    /** @var MockObject|ExecutionContextInterface */
    private $mockContext;
    /** @var MockObject|ConstraintViolationBuilderInterface */
    private $mockViolationBuilder;

    protected function setUp()
    {
        $this->mockViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $this->mockContext = $this->createMock(ExecutionContextInterface::class);

        $this->constraint = new EnvironmentConstraint();
        $this->validator = new EnvironmentConstraintValidator();

        $this->mockContext
            ->method('buildViolation')
            ->with($this->constraint->message)
            ->willReturn($this->mockViolationBuilder);

        $this->mockViolationBuilder->method('setParameter')->willReturn($this->mockViolationBuilder);

        $this->validator->initialize($this->mockContext);
    }

    public function testValidData()
    {
        $this->mockViolationBuilder->expects($this->never())->method('addViolation');

        $this->validator->validate('TEST=42', $this->constraint);
    }
}
