<?php

declare(strict_types=1);

namespace Jmarr\ConsistentHashing\Hash;

interface HasherInterface
{
    public function hash(string $value): int;
}