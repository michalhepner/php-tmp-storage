<?php
declare(strict_types = 1);

namespace MichalHepner\PhpTmpStorage;

use IteratorAggregate;

interface TmpStorageInterface extends IteratorAggregate
{
    public function touch(): TmpFile;
    public function dump(string $contents, bool $removeOnShutdown = true): TmpFile;
    public function mkdir(bool $removeOnShutdown = true): TmpFile;
    public function clear(): void;

    /**
     * @return TmpFile[]
     */
    public function all(): array;
}
