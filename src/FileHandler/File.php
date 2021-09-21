<?php

declare(strict_types=1);

namespace kissj\FileHandler;

use Psr\Http\Message\StreamInterface;

class File
{
    public function __construct(
        public StreamInterface $stream,
        public string $mimeContentType,
    ) {
    }
}
