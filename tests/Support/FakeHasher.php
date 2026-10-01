<?php

declare(strict_types=1);

namespace Jmarr\ConsistentHashing\Tests\Support;

use Jmarr\ConsistentHashing\Hash\HasherInterface;

final class FakeHasher implements HasherInterface
{
    public function __construct(private readonly array $hashes)
    {}

    public function hash(string $value): int
    {
        return $this->hashes[$value];
    }
}