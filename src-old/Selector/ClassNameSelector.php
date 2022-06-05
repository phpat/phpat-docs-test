<?php

declare(strict_types=1);

namespace PHPatOld\Selector;

use PHPatOld\Parser\Ast\ClassLike;
use PHPatOld\Parser\Ast\FullClassName;
use PHPatOld\Parser\Ast\ReferenceMap;
use PHPatOld\Parser\Ast\RegexClassName;

class ClassNameSelector implements Selector
{
    private string $fqcn;
    private ?ReferenceMap $map = null;

    public function __construct(string $fqcn)
    {
        $this->fqcn = $fqcn;
    }

    public function getDependencies(): array
    {
        return [];
    }

    public function injectDependencies(array $dependencies): void
    {
    }

    public function setReferenceMap(ReferenceMap $map): void
    {
        $this->map = $map;
    }

    /**
     * @return array<ClassLike>
     */
    public function select(): array
    {
        foreach ($this->map->getSrcNodes() as $srcNode) {
            if ($this->matchesPattern($srcNode->getClassName(), $this->fqcn)) {
                $result[] = FullClassName::createFromFQCN($srcNode->getClassName());
            }
        }

        if ($this->isRegex($this->fqcn)) {
            $result[] = new RegexClassName($this->fqcn);
        } elseif (empty($result)) {
            $result[] = FullClassName::createFromFQCN($this->fqcn);
        }

        return $result;
    }

    public function getParameter(): string
    {
        return $this->fqcn;
    }

    private function isRegex(string $str): bool
    {
        return strpos($str, '*') !== false;
    }

    private function matchesPattern(string $className, string $pattern): bool
    {
        $pattern = preg_replace_callback(
            '/([^*])/',
            function ($m) {
                return preg_quote($m[0], '/');
            },
            $pattern
        );
        $pattern = str_replace('*', '.*', $pattern);

        return (bool) preg_match('/^' . $pattern . '$/i', $className);
    }
}
