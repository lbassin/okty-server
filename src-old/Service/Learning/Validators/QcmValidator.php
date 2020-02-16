<?php

declare(strict_types=1);

namespace App\Service\Learning\Validators;

use App\Entity\Learning\Action;
use App\ValueObject\Learning\Action\ActionResponse;
use App\ValueObject\Learning\Action\Qcm\QcmConfig;
use App\ValueObject\Learning\Action\Qcm\QcmConfigQuestion;
use App\ValueObject\Learning\Action\Qcm\QcmRequest;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class QcmValidator implements ValidatorInterface
{
    public function supports(string $type): bool
    {
        return strtolower($type) === 'qcm';
    }

    public function validate(Action $action, array $data): ActionResponse
    {
        $errors = [];

        $config = new QcmConfig($action->getConfig());
        $request = new QcmRequest($data);

        /** @var QcmConfigQuestion $question */
        foreach ($config->getQuestions() as $questionId => $question) {
            $expected = array_values($question->getResponses());
            $userData = array_values($request->getResponsesByQuestion($questionId));

            sort($expected);
            sort($userData);

            if (implode(',', $expected) !== implode(',', $userData)) {
                $errors[] = $questionId;
            }
        }

        return new ActionResponse(count($errors) === 0, ['errors' => $errors]);
    }

}
