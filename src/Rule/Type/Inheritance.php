<?php declare(strict_types=1);

namespace PhpAT\Rule\Type;

use PhpAT\File\FileFinder;
use PhpAT\Parser\ClassName;
use PhpAT\Parser\ClassNameExtractor;
use PhpAT\Parser\NamespaceExtractor;
use PhpAT\Parser\ParentExtractor;
use PhpParser\NodeTraverserInterface;
use PhpParser\Parser;

class Inheritance implements RuleType
{
    private $traverser;
    private $finder;
    private $parser;
    private $parsedClassNamespace;
    /** @var ClassName */
    private $parsedClassParent;

    public function __construct(FileFinder $finder, Parser $parser, NodeTraverserInterface $traverser)
    {
        $this->finder = $finder;
        $this->parser = $parser;
        $this->traverser = $traverser;
    }

    public function validate(array $parsedClass, array $params): bool
    {
        $this->extractParsedClassInfo($parsedClass);

        $filesFound = $this->finder->findFiles($params['file']);
        $classNameExtractor = new ClassNameExtractor();
        $this->traverser->addVisitor($classNameExtractor);
        /** @var \SplFileInfo $file */
        foreach ($filesFound as $file) {
            $parsedFile = $this->parser->parse(file_get_contents($file->getPathname()));
            $this->traverser->traverse($parsedFile);
        }
        $this->traverser->removeVisitor($classNameExtractor);

        if (empty($this->parsedClassParent->getFQDN())) {
            return false;
        }

        /** @var ClassName $className */
        foreach ($classNameExtractor->getResult() as $className) {
            if (!($className->getFQDN() === $this->parsedClassParent->getFQDN())) {
                return false;
            }
        }

        return true;
    }

    public function getMessageVerb(): string
    {
        return 'extend';
    }

    private function extractParsedClassInfo(array $parsedClass): void
    {
        $parentExtractor = new ParentExtractor();
        $namespaceExtractor = new NamespaceExtractor();

        $this->traverser->addVisitor($parentExtractor);
        $this->traverser->addVisitor($namespaceExtractor);
        $this->traverser->traverse($parsedClass);
        $this->traverser->removeVisitor($parentExtractor);

        $this->parsedClassNamespace = $namespaceExtractor->getResult()[0];
        $this->parsedClassParent = $parentExtractor->getResult()[0];

        if (empty($this->parsedClassParent->getNamespace())) {
            $this->parsedClassParent = new ClassName($this->parsedClassNamespace, $this->parsedClassParent->getName());
        }
    }
}