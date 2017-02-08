<?php


namespace Mleczek\CBuilder\Dependencies\Versions;

use Composer\Semver\Comparator;
use Composer\Semver\Semver;


/**
 * Common operations performed on the semantic versions.
 *
 * @Injectable
 * @link http://semver.org/
 * @link https://github.com/composer/semver
 */
class VersionsHelper
{
    /**
     * Evaluates the expression: $version1 > $version2.
     *
     * @param string $v1
     * @param string $v2
     * @return bool
     */
    public function greaterThan($v1, $v2)
    {
        return Comparator::greaterThan($v1, $v2);
    }

    /**
     * Evaluates the expression: $version1 >= $version2.
     *
     * @param string $v1
     * @param string $v2
     * @return bool
     */
    public function greaterThanOrEqualTo($v1, $v2)
    {
        return Comparator::greaterThanOrEqualTo($v1, $v2);
    }

    /**
     * Evaluates the expression: $version1 < $version2.
     *
     * @param string $v1
     * @param string $v2
     * @return bool
     */
    public function lessThan($v1, $v2)
    {
        return Comparator::lessThan($v1, $v2);
    }

    /**
     * Evaluates the expression: $version1 <= $version2.
     *
     * @param string $v1
     * @param string $v2
     * @return bool
     */
    public function lessThanOrEqualTo($v1, $v2)
    {
        return Comparator::lessThanOrEqualTo($v1, $v2);
    }

    /**
     * Evaluates the expression: $version1 == $version2.
     *
     * @param string $v1
     * @param string $v2
     * @return bool
     */
    public function equalTo($v1, $v2)
    {
        return Comparator::equalTo($v1, $v2);
    }

    /**
     * Evaluates the expression: $version1 != $version2.
     *
     * @param string $v1
     * @param string $v2
     * @return bool
     */
    public function notEqualTo($v1, $v2)
    {
        return Comparator::notEqualTo($v1, $v2);
    }

    /**
     * Determine if given version satisfies given constraints.
     *
     * @param string $version
     * @param string|string[] $constraints
     * @return bool
     */
    public function satisfies($version, $constraints)
    {
        $constraints = (array)$constraints;
        return Semver::satisfies($version, $constraints);
    }

    /**
     * Return all versions that satisfy given constraints.
     *
     * @param string|string[] $versions
     * @param string $constraint
     * @return string[]
     */
    public function satisfiedBy($versions, $constraint)
    {
        $versions = (array)$versions;
        return Semver::satisfiedBy($versions, $constraint);
    }

    /**
     * Sort given array of versions.
     *
     * @param string[] $versions
     * @return string[]
     */
    public function sort(array $versions)
    {
        return Semver::sort($versions);
    }

    /**
     * Sort given array of versions in reverse order.
     *
     * @param string[] $versions
     * @return string[]
     */
    public function rsort(array $versions)
    {
        return Semver::rsort($versions);
    }
}