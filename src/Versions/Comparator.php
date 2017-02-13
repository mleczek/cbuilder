<?php

namespace Mleczek\CBuilder\Versions;

use Composer\Semver\Semver;
use Composer\Semver\Comparator as ComposerComparator;

/**
 * Helper class to operate on the semantic versions.
 *
 * @link http://semver.org/
 */
class Comparator
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
        return ComposerComparator::greaterThan($v1, $v2);
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
        return ComposerComparator::greaterThanOrEqualTo($v1, $v2);
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
        return ComposerComparator::lessThan($v1, $v2);
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
        return ComposerComparator::lessThanOrEqualTo($v1, $v2);
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
        return ComposerComparator::equalTo($v1, $v2);
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
        return ComposerComparator::notEqualTo($v1, $v2);
    }

    /**
     * Determine if given version satisfies given constraints.
     *
     * @param string $version
     * @param string $constraint
     * @return bool
     */
    public function satisfies($version, $constraint)
    {
        return Semver::satisfies($version, $constraint);
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
        $versions = (array) $versions;

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
