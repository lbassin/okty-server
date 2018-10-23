<?php

namespace App\Tests\Builder\Validator;

use App\Builder\Validator\Volume;
use App\Builder\Validator\VolumeValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class VolumeValidatorTest extends TestCase
{
    /** @var ConstraintValidator */
    private $validator;
    /** @var MockObject|Constraint */
    private $constraint;
    /** @var MockObject|ExecutionContextInterface */
    private $mockContext;
    /** @var MockObject|ConstraintViolationBuilderInterface */
    private $mockViolationBuilder;

    protected function setUp()
    {
        $this->mockViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $this->mockContext = $this->createMock(ExecutionContextInterface::class);

        $this->constraint = new Volume();
        $this->validator = new VolumeValidator();

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

        $this->validator->validate('./:/app', $this->constraint);
    }

    public function testEmptyHost()
    {
        $this->mockViolationBuilder->expects($this->once())->method('addViolation');

        $this->validator->validate(':/app', $this->constraint);
    }

    public function testEmptyContainer()
    {
        $this->mockViolationBuilder->expects($this->once())->method('addViolation');

        $this->validator->validate('./:', $this->constraint);
    }

    public function testContainerNotFromRoot()
    {
        $this->mockViolationBuilder->expects($this->once())->method('addViolation');

        $this->validator->validate('./:app', $this->constraint);
    }
}
