<?php

namespace App\Tests\Builder\Validator;

use App\Builder\Validator\PortConstraint;
use App\Builder\Validator\PortConstraintValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class PortValidatorTest extends TestCase
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

        $this->constraint = new PortConstraint();
        $this->validator = new PortConstraintValidator();

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

        $this->validator->validate('8080:80', $this->constraint);
    }

    public function testHostBelowMin()
    {
        $this->mockViolationBuilder->expects($this->once())->method('addViolation');

        $this->validator->validate('-12:80', $this->constraint);
    }

    public function testHostAboveMax()
    {
        $this->mockViolationBuilder->expects($this->once())->method('addViolation');

        $this->validator->validate('70000:80', $this->constraint);
    }

    public function testContainerBelowMin()
    {
        $this->mockViolationBuilder->expects($this->once())->method('addViolation');

        $this->validator->validate('80:-50', $this->constraint);
    }

    public function testContainerAboveMax()
    {
        $this->mockViolationBuilder->expects($this->once())->method('addViolation');

        $this->validator->validate('70:80000', $this->constraint);
    }
}
