<?php

declare(strict_types=1);

namespace Jmarr\ConsistentHashing;

use Jmarr\ConsistentHashing\Hash\Crc32Hasher;
use Jmarr\ConsistentHashing\Hash\HasherInterface;

final class HashRing
{
    private HasherInterface $hasher;
    private array $ring = [];

    public function __construct(?HasherInterface $hasher = null)
    {
        $this->hasher = $hasher ?? new Crc32Hasher();
    }

    public function addNode(string $node)
    {
        $this->ring[$this->hasher->hash($node)] = $node;
        $this->sortRing();
    }

    public function getNode(string $key): string
    {
        if(empty($this->ring)) {
            throw new \RuntimeException("No nodes in the ring");
        }
        $hash = $this->hasher->hash($key);
        $node_index = $this->findNodeIndex($hash);

        $keys = array_keys($this->ring);

        if($node_index > count($this->ring)-1){
            $node = $keys[0];
            return $this->ring[$node];
        }

        $node = $keys[$node_index];
        return $this->ring[$node];
    }

    public function removeNode(string $node): void
    {
        $hash = $this->hasher->hash($node);
        
        if(!isset($this->ring[$hash])){
            throw new \RuntimeException("Node doesn't exist in the ring");
        }
        unset($this->ring[$hash]);
    }

    private function sortRing()
    {
        ksort($this->ring);
    }

    private function findNodeIndex(int $target): int
    {
        $arr = array_keys($this->ring);
        $low = 0;
        $high = count($arr);

        while($low < $high){
            $mid = $low + intdiv($high - $low, 2);
            if($arr[$mid] >= $target){
                $high = $mid;
            }else{
                $low = $mid+1;
            }
        }
        return $low;
    }
}