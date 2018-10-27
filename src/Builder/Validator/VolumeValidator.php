<?php declare(strict_types=1);

namespace App\Builder\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class VolumeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        $volumes = explode(':', $value);

        $volumes[0] = $volumes[0] ?? '';
        $volumes[1] = $volumes[1] ?? '';

        if (empty($volumes[0]) || empty($volumes[1]) || $volumes[1][0] !== '/') {
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ path }}', $value)
                ->addViolation();
        }
    }
}
