<?php

namespace Mleczek\CBuilder\Lock;

use DI\Container;
use Mleczek\CBuilder\Environment\Exceptions\InvalidPathException;
use Mleczek\CBuilder\Environment\Filesystem;
use Mleczek\CBuilder\Validation\Validator;

class Factory
{
    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var Container
     */
    private $di;

    /**
     * Factory constructor.
     *
     * @param Container $di
     * @param Filesystem $fs
     * @param Validator $validator
     */
    public function __construct(Container $di, Filesystem $fs, Validator $validator)
    {
        $this->fs = $fs;
        $this->validator = $validator;
        $this->di = $di;
    }

    /**
     * Read lock file or create empty lock if file not exists.
     *
     * @param string $file
     * @return Lock
     */
    public function makeFromFileOrEmpty($file)
    {
        if (!$this->fs->existsFile($file)) {
            return $this->makeEmpty();
        }

        $content = $this->fs->readFile($file);

        return $this->makeFromJson($content);
    }

    /**
     * @return Lock
     */
    public function makeEmpty()
    {
        return $this->di->make(Lock::class);
    }

    /**
     * @param string|object $json
     * @return Lock
     * @throws \Mleczek\CBuilder\Validation\Exceptions\ValidationException
     */
    public function makeFromJson($json)
    {
        $this->validator->validate(
            $json,
            $this->fs->readFile(CBUILDER_DIR . '/resources/package-lock.schema.json')
        );

        if (!is_object($json)) {
            $json = json_decode($json);
        }

        return $this->di->make(Lock::class, [
            'json' => $json,
        ]);
    }
}
