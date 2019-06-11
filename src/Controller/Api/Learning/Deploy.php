<?php

declare(strict_types=1);

namespace App\Controller\Api\Learning;

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

    public function __construct(SerializerInterface $serializer, LoggerInterface $logger)
    {
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    /**
     * @Route("learning/deploy", methods={"POST"})
     */
    public function handle(): Response
    {
        echo 'Deploy ...';

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
