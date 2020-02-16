<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Cache
{
    private $adapter;
    private $logger;

    public function __construct(AdapterInterface $adapter, LoggerInterface $logger)
    {
        $this->adapter = $adapter;
        $this->logger = $logger;
    }

    public function has(string $key): bool
    {
        $key = $this->normalizeKey($key);

        $item = $this->adapter->getItem($key);

        return $item->isHit();
    }

    public function get(string $key)
    {
        $key = $this->normalizeKey($key);

        $item = $this->adapter->getItem($key);
        if (!$item->isHit()) {
            return null;
        }

        return $item->get();
    }

    public function set(string $key, $data): void
    {
        $key = $this->normalizeKey($key);

        $item = $this->adapter->getItem($key);
        $item->set($data);
        $item->expiresAt(null);

        $this->adapter->save($item);
    }

    private function normalizeKey(string $key): string
    {
        $key = str_replace('/', '.', $key);

        return $key;
    }
}
