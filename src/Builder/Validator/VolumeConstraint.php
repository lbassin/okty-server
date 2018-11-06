<?php declare(strict_types=1);

namespace App\Builder\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class VolumeConstraint extends Constraint
{
    public $message = "The volume '{{ path }}' is not valid.";
}
