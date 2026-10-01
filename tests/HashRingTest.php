<?php

declare(strict_types=1);

namespace Jmarr\ConsistentHashing\Tests;

use Jmarr\ConsistentHashing\HashRing;
use Jmarr\ConsistentHashing\Tests\Support\FakeHasher;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class HashRingTest extends TestCase
{
    public function testCannotGetNodeFromEmptyRing(): void
    {
        $ring = new HashRing();

        $this->expectException(RuntimeException::class);

        $ring->getNode('user-123');

    }

    public function testSingleNodeAlwaysReturnThatNode(): void
    {
        $ring = new HashRing();

        $ring->addNode('server-1');

        $node = $ring->getNode('user-123');

        $this->assertSame('server-1', $node);
    }

    public function testKeyReturnsCorrectNodeWithMultipleNodes(): void
    {
        $ring = new HashRing();

        $ring->addNode('server-1');
        $ring->addNode('server-2');
        $ring->addNode('server-3');

        $firstResult = $ring->getNode('user-123');

        $this->assertSame($firstResult, $ring->getNode('user-123'));
        $this->assertSame($firstResult, $ring->getNode('user-123'));
        $this->assertSame($firstResult, $ring->getNode('user-123'));
    }

    public function testReturnsOneOfTheRegisteredNodes(): void
    {
        $ring = new HashRing();
        $haystack = [
            'server-1',
            'server-2',
            'server-3'
        ];

        foreach($haystack as $straw){
            $ring->addNode($straw);
        }

        for($i=0;$i<100;$i++){
            $needle = $ring->getNode('key-'.$i);

            $this->assertContains($needle, $haystack);
        }
    }

    public function testWrapsAroundToFirstNode(): void
    {

        $hasher = new FakeHasher([
            'server-1' => 100,
            'server-2' => 500,
            'server-3' => 900,
            'my-key' => 950
        ]); 
        $ring = new HashRing($hasher);

        $nodes = [
            'server-1',
            'server-2',
            'server-3'
        ];

        foreach($nodes as $node){
            $ring->addNode($node);
        }

        $node = $ring->getNode('my-key');

        $this->assertSame('server-1', $node);
    }
}