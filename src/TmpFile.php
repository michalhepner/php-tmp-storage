<?php
declare(strict_types=1);

namespace MichalHepner\PhpTmpStorage;

use Closure;
use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;

class TmpFile extends SplFileInfo
{
    protected ?Closure $onShutdown;

    public function __construct(
        protected string $tmpStoragePath,
        protected string $tmpFileName,
        bool $removeOnShutdown = true
    ) {
        $removeOnShutdown && $this->removeOnShutdown();
        register_shutdown_function([$this, 'onShutdown']);

        parent::__construct(implode(DIRECTORY_SEPARATOR, [$this->tmpStoragePath, $this->tmpFileName]));
    }

    public function getTmpStoragePath(): string
    {
        return $this->tmpStoragePath;
    }

    public function getTmpFileName(): string
    {
        return $this->tmpFileName;
    }

    public function retainOnShutdown(): void
    {
        $this->onShutdown = null;
    }

    public function removeOnShutdown(): void
    {
        $this->onShutdown = fn () => (new Filesystem())->remove($this->getPathname());
    }

    public function onShutdown(): void
    {
        $this->onShutdown !== null && ($this->onShutdown)();
    }
}
