<?php

declare(strict_types=1);

namespace App\Service\Learning;

use App\Entity\Learning\Action;
use App\Exception\Learning\ActionValidatorNotFound;
use App\Service\Learning\Validators\ValidatorInterface;
use App\ValueObject\Learning\Action\ActionResponse;
use Traversable;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class ActionValidator
{
    private $validators;

    public function __construct(Traversable $validators)
    {
        $this->validators = $validators;
    }

    public function validate(Action $action, array $data): ActionResponse
    {
        /** @var ValidatorInterface $validator */
        foreach ($this->validators as $validator) {
            if ($validator->supports($action->getType())) {
                return $validator->validate($action, $data);
            }
        }

        throw new ActionValidatorNotFound($action->getType());
    }
}
