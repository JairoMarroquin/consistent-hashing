<?php

namespace Jmarr\ConsistentHashing\Hash;

use Jmarr\ConsistentHashing\Hash\HasherInterface;

final class Crc32Hasher implements HasherInterface
{
    public function hash(string $value): int
    {
        return crc32($value);
    }
}