<?php


namespace Mleczek\CBuilder\Modules;

use JsonSchema\Validator as SchemaValidator;
use Mleczek\CBuilder\Console\Tools\PathResolver;


/**
 * Check module correctness.
 */
class Validator
{
    /**
     * @var string
     */
    private $dir;

    /**
     * @var SchemaValidator
     */
    private $schema;

    /**
     * @var PathResolver
     */
    private $path;

    /**
     * @param SchemaValidator $schema
     * @param PathResolver $path
     * @param string $dir
     */
    public function __construct(SchemaValidator $schema, PathResolver $path, $dir = '.')
    {
        $this->path = $path;
        $this->schema = $schema;
        $this->dir = $dir;
    }

    /**
     * Check whether all aspects of the module
     * are valid and ready to use.
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->isPackageFileValid()
            && $this->isPackageLockFileValid();
    }

    /**
     * @return bool
     */
    public function isPackageFileValid()
    {
        $package = $this->dir . '/' . $this->path->getPackageFileName();
        $schema = PROJECT_DIR . '/resources/package.schema.json';

        $this->schema->check(
            json_decode(file_get_contents($package)),
            json_decode(file_get_contents($schema))
        );

        return $this->schema->isValid();
    }

    /**
     * @return bool
     */
    public function isPackageLockFileValid()
    {
        // TODO: ...
        return true;
    }
}