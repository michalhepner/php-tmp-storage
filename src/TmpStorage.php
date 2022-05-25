<?php
declare(strict_types=1);

namespace MichalHepner\PhpTmpStorage;

use ArrayIterator;
use Iterator;
use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;

class TmpStorage implements TmpStorageInterface
{
    public function __construct(
        protected string $tmpDirPath,
        protected string $prefix,
        protected bool $dateTimeInPrefix = true,
        protected ?Filesystem $filesystem = null,
    ) {
        $this->filesystem = $filesystem ?? new Filesystem();
    }

    public function touch(bool $removeOnShutdown = true): TmpFile
    {
        $this->ensureInitialized();
        $tmpDir = new SplFileInfo($this->tmpDirPath);

        $fileId = $this->generateTmpFileId();
        $this->filesystem->touch($tmpDir->getPathname() . DIRECTORY_SEPARATOR . $fileId);

        return new TmpFile($tmpDir->getPathname(), $fileId, $removeOnShutdown);
    }

    public function mkdir(bool $removeOnShutdown = true): TmpFile
    {
        $this->ensureInitialized();
        $tmpDir = new SplFileInfo($this->tmpDirPath);

        $fileId = $this->generateTmpFileId();
        $this->filesystem->mkdir($tmpDir->getPathname() . DIRECTORY_SEPARATOR . $fileId);

        return new TmpFile($tmpDir->getPathname(), $fileId, $removeOnShutdown);
    }

    public function dump(string $contents, bool $removeOnShutdown = true): TmpFile
    {
        $file = $this->touch($removeOnShutdown);
        file_put_contents($file->getPathname(), $contents);

        return $file;
    }

    protected function generateTmpFileId(): string
    {
        return implode('', [
            $this->prefix,
            $this->dateTimeInPrefix ? date('YmdHis') : '',
            bin2hex(openssl_random_pseudo_bytes(20)),
        ]);
    }

    protected function ensureInitialized(): self
    {
        if (!$this->filesystem->exists($this->tmpDirPath)) {
            $this->filesystem->mkdir($this->tmpDirPath);
        }

        return $this;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function setPrefix(string $prefix): void
    {
        $this->prefix = $prefix;
    }

    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->all());
    }

    public function clear(): void
    {
        throw new \Exception('not implemented');
    }

    public function all(): array
    {
        throw new \Exception('not implemented');
    }
}
