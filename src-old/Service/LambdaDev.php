<?php declare(strict_types=1);

namespace App\Service;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class LambdaDev implements LambdaInterface
{
    private $apiUrl;

    public function __construct()
    {
        $this->apiUrl = 'http://lambda-api/';
    }

    public function invoke($function, $resolver, $arg): string
    {
        $options = [
            'http' => [
                'method' => 'POST',
                'content' => json_encode(['container' => $function, 'resolver' => $resolver, 'args' => $arg]),
                'header' => [
                    'Content-type: application/json',
                ],
            ],
        ];
        $context = stream_context_create($options);

        $output = file_get_contents($this->apiUrl, false, $context);
        $output = trim($output, '"');

        return $output;
    }
}
