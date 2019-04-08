<?php

namespace AbterPhp\Framework\Filesystem;

use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use Opulence\Http\Requests\UploadedFile;
use Opulence\Http\Requests\UploadException;

class Uploader
{
    const DEFAULT_ROOT = '/';

    const DEFAULT_KEY = 'file';

    /** @var Filesystem */
    protected $filesystem;

    /** @var string */
    protected $fileManagerPath;

    /** @var callable[] */
    protected $pathFactories = [];

    /** @var string[] */
    protected $errors = [];

    /**
     * Uploader constructor.
     *
     * @param Filesystem  $filesystem
     * @param string|null $fileManagerPath
     */
    public function __construct(Filesystem $filesystem, ?string $fileManagerPath = null)
    {
        $this->filesystem      = $filesystem;
        $this->fileManagerPath = $fileManagerPath
            ? rtrim($fileManagerPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR
            : static::DEFAULT_ROOT;
    }

    /**
     * @param string   $key
     * @param callable $pathFactory
     *
     * @return Uploader
     */
    public function setPathFactory(string $key, callable $pathFactory): Uploader
    {
        $this->pathFactories[$key] = $pathFactory;

        return $this;
    }

    /**
     * @param UploadedFile[] $fileData
     *
     * @return array file paths of storage
     */
    public function persist(array $fileData): array
    {
        $paths = [];
        foreach ($fileData as $key => $uploadedFile) {
            try {
                $paths[$key] = $this->randFileName();
                $targetDir   = $this->getPath($key);
                $uploadedFile->move($targetDir, $paths[$key]);
            } catch (UploadException $e) {
                $this->errors[$key] = $e->getMessage();

                return [];
            }
        }

        return $paths;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param string $key
     * @param string $fileName
     *
     * @return string
     */
    public function getPath(string $key, string $fileName = ''): string
    {
        if (array_key_exists($key, $this->pathFactories)) {
            return (string)$this->pathFactories[$key]($fileName);
        }

        return $this->fileManagerPath . $fileName;
    }

    /**
     * Please note that md5 is chosen as "security" is not really important here
     * it only serves to make guessing content unrealistic and to ensure a reasonable
     * randomness and uniqueness.
     *
     * @return string
     */
    protected function randFileName(): string
    {
        return substr(md5(rand(0, PHP_INT_MAX)), 0, 12);
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function delete(string $path): bool
    {
        if (!$this->filesystem->has($path)) {
            return false;
        }

        try {
            return $this->filesystem->delete($path);
        } catch (FileNotFoundException $e) {
            return false;
        }
    }

    /**
     * @param string $path
     *
     * @return string|null
     */
    public function getContent(string $path): ?string
    {
        if (!$this->filesystem->has($path)) {
            return null;
        }

        try {
            $content = $this->filesystem->read($path);
            if (false === $content) {
                return null;
            }
            return (string)$content;
        } catch (FileNotFoundException $e) {
            return null;
        }
    }

    /**
     * @param string $path
     *
     * @return bool|resource
     */
    public function getStream(string $path)
    {
        if (!$this->filesystem->has($path)) {
            return false;
        }

        try {
            return $this->filesystem->readStream($path);
        } catch (FileNotFoundException $e) {
            return false;
        }
    }

    /**
     * @param string $path
     *
     * @return int|null
     */
    public function getSize(string $path): ?int
    {
        if (!$this->filesystem->has($path)) {
            return null;
        }

        try {
            $size = $this->filesystem->getSize($path);
            if (is_numeric($size)) {
                return (int)$size;
            }
        } catch (FileNotFoundException $e) {
            return null;
        }

        return null;
    }
}
