<?php

namespace App\Tests\Provider;

use App\Provider\Github;
use Github\Api\Repo;
use Github\Api\Repository\Contents;
use Github\Client;
use Github\Exception\ErrorException;
use Github\Exception\InvalidArgumentException;
use Github\Exception\RuntimeException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class GithubTest extends TestCase
{
    /** @var Github */
    private $github;

    /** @var MockObject|Contents */
    private $mockContents;
    /** @var MockObject|Repo */
    private $mockRepo;
    /** @var MockObject|Client */
    private $mockClient;
    /** @var MockObject|CacheItemPoolInterface */
    private $mockCache;

    private $githubUser;
    private $githubRepo;
    private $githubBranch;

    protected function setUp()
    {
        $this->mockContents = $this->createMock(Contents::class);

        $this->mockRepo = $this->createMock(Repo::class);
        $this->mockRepo
            ->method('contents')
            ->willReturn($this->mockContents);

        $this->mockClient = $this->createMock(Client::class);
        $this->mockClient
            ->method('api')
            ->with('repo')
            ->willReturn($this->mockRepo);

        $this->mockCache = $this->createMock(CacheItemPoolInterface::class);

        $this->githubUser = 'okty';
        $this->githubRepo = 'okty-config';
        $this->githubBranch = 'master';

        $this->github = new Github(
            $this->mockClient,
            $this->mockCache,
            $this->githubUser,
            $this->githubRepo,
            $this->githubBranch
        );
    }

    public function testGetFileValid()
    {
        $this->mockContents
            ->expects($this->once())
            ->method('download')
            ->with($this->githubUser, $this->githubRepo, 'config/nginx.json', $this->githubBranch)
            ->willReturn('Text');

        $file = $this->github->getFile('config/nginx.json');

        $this->assertSame('Text', $file);
    }

    public function testGetFileWrongPath()
    {
        $this->mockContents
            ->expects($this->once())
            ->method('download')
            ->willThrowException(new InvalidArgumentException());

        $this->expectException(FileNotFoundException::class);

        $this->github->getFile('config/nginx.json');
    }

    public function testGetFileNotFound()
    {
        $this->mockContents
            ->expects($this->once())
            ->method('download')
            ->willThrowException(new ErrorException());

        $this->expectException(FileNotFoundException::class);

        $this->github->getFile('config/nginx.json');
    }

    public function testErrorGetRepoFromGetFile()
    {
        $this->mockClient->method('api')->willThrowException(new InvalidArgumentException());

        $this->expectException(FileNotFoundException::class);
        $this->github->getFile('test.md');
    }

    public function testErrorGetRepoFromGetTree()
    {
        $this->mockClient->method('api')->willThrowException(new InvalidArgumentException());

        $this->expectException(FileNotFoundException::class);
        $this->github->getTree('test');
    }

    public function testGetTreeValid()
    {
        $this->mockContents
            ->expects($this->once())
            ->method('show')
            ->with($this->githubUser, $this->githubRepo, 'config', $this->githubBranch)
            ->willReturn(['something']);

        $tree = $this->github->getTree('config');

        $this->assertCount(1, $tree);
    }

    public function testGetTreeError()
    {
        $this->mockContents
            ->method('show')
            ->willThrowException(new RuntimeException());

        $this->expectException(FileNotFoundException::class);

        $this->github->getTree('ok');
    }
}
