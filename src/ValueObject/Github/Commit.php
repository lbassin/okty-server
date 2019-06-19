<?php

declare(strict_types=1);

namespace App\ValueObject\Github;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class Commit
{
    private $commitSha;
    private $commitUrl;

    private $treeSha;
    private $treeUrl;

    public function __construct(array $data)
    {
        $this->initCommit($data);
        $this->initTree($data);
    }

    private function initCommit(array $data): void
    {
        if (empty($data['commit']['sha'])) {
            throw new \LogicException('SHA is missing');
        }
        $this->commitSha = $data['commit']['sha'];

        if (empty($data['commit']['url'])) {
            throw new \LogicException('URL Commit is missing');
        }
        $this->commitUrl = $data['commit']['url'];
    }

    private function initTree(array $data): void
    {
        if (empty($data['commit']['commit']['tree'])) {
            throw new \LogicException('Tree data are missing');
        }
        $tree = $data['commit']['commit']['tree'];

        if (empty($tree['sha'])) {
            throw new \LogicException('Tree SHA is missing');
        }
        $this->treeSha = $tree['sha'];

        if (empty($tree['url'])) {
            throw new \LogicException('Tree URL is missing');
        }
        $this->treeUrl = $tree['url'];
    }

    public function getTreeSha(): string
    {
        return $this->treeSha;
    }

    public function getCommitSha(): string
    {
        return $this->commitSha;
    }

}
