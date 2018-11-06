<?php declare(strict_types=1);

namespace App\Builder\Resolver;

use App\Provider\ContainerProvider;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class FileResolver
{
    private $warnings = [];
    private $containerProvider;

    public function __construct(ContainerProvider $containerProvider)
    {
        $this->containerProvider = $containerProvider;
    }

    public function resolve(string $image, array $manifest, string $file, array $userConfig): array
    {
        try {
            $content = $this->containerProvider->getSource($image, $file);
        } catch (FileNotFoundException $ex) {
            $this->warnings[] = $ex->getMessage();

            return [];
        }

        $fileConfig = $manifest['config'][$file];

        // Get all {{ }} values from current file
        preg_match_all('/{{(?P<name>\w+)}}/', $content, $editableConfig);
        foreach ($editableConfig['name'] as $configName) {
            // Get default value from manifest file
            $defaultValue = $fileConfig['args'][$configName]['default'] ?? '';

            // If no value provided by user => Default value
            $value = $userConfig[$configName] ?? $defaultValue;

            // Apply resolver on output value
            // $value = ''; // Call resolver

            // Write value in file
            $content = str_replace('{{' . $configName . '}}', $value, $content);
        }

        // Get output path and ensure it end with a /
        $outputPath = rtrim($fileConfig['output'] ?? '', '/') . '/';

        $files[] = [
            'name' => $outputPath . $file,
            'content' => $content
        ];

        return $files;
    }
}
