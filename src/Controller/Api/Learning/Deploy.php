<?php

declare(strict_types=1);

namespace App\Controller\Api\Learning;

use App\Service\Learning\Import as LearningImport;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Deploy
{
    private $serializer;
    private $logger;
    private $learningImport;

    public function __construct(
        SerializerInterface $serializer,
        LoggerInterface $logger,
        LearningImport $learningImport
    ) {
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->learningImport = $learningImport;
    }

    /**
     * @Route("learning/deploy", methods={"POST"})
     */
    public function handle(): Response
    {
        echo 'Deploy ...';
        $this->learningImport->import();

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
