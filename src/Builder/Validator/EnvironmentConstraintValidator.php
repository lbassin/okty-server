<?php declare(strict_types=1);

namespace App\Builder\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class EnvironmentConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {

    }
}
