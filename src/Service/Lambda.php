<?php declare(strict_types=1);

namespace App\Service;

use App\Exception\BadCredentialsException;
use Aws\Lambda\Exception\LambdaException;
use Aws\Lambda\LambdaClient;
use GuzzleHttp\Psr7\Stream;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Lambda implements LambdaInterface
{
    private $lambdaClient;

    public function __construct(LambdaClient $lambdaClient)
    {
        $this->lambdaClient = $lambdaClient;
    }

    public function invoke($function, $resolver, $arg): string
    {
        try {
            /** @var \Aws\Result $response */
            $response = $this->lambdaClient->invoke([
                'FunctionName' => $function,
                'Payload' => json_encode(['name' => $resolver, 'value' => $arg]),
            ]);
        } catch (LambdaException $exception) {
            if ($exception->getStatusCode() == 404) {
                throw new \RuntimeException("Function $function not found");
            }

            if ($exception->getStatusCode() == 403 || $exception->getStatusCode() == 401) {
                throw new BadCredentialsException('AWS Lambda');
            }

            throw new \RuntimeException($exception->getMessage());
        }

        if ($response->get('FunctionError')) {
            return $arg;
        }

        /** @var Stream $stream */
        $stream = $response->get('Payload');

        $output = '';
        while (!$stream->eof() || strlen($output) > 8096) {
            $output .= $stream->read(255);
        }

        $output = trim($output, '"');

        return $output;
    }
}
