<?php

declare(strict_types=1);

namespace Jmarr\ConsistentHashing\Test;

use Jmarr\ConsistentHashing\HashRing;
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
        $ring = new HashRing();
        $nodes = [
            'server-1',
            'server-2',
            'server-3'
        ];

        foreach($nodes as $node){
            $ring->addNode($node);
        }

        $nodesHashes = array_map(
            fn (string $node): int => crc32($node),
            $nodes
        );

        $maxNodeHash = max($nodesHashes);

        $key = null;

        for($i=0;$i<10000;$i++){
            $candidate = "key-".$i;
            if(crc32($candidate) > $maxNodeHash){
                $key = $candidate;
                break;
            }
        }

        $this->assertNotNull($key);

        $expectedFirstNode = $nodes[
            array_search(min($nodesHashes), $nodesHashes, true)
        ];

        $this->assertSame($expectedFirstNode, $ring->getNode($key));
    }
}