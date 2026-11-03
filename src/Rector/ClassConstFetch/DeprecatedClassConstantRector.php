<?php
/**
 * @copyright Copyright © 2026 Arnaud Amant. All rights reserved.
 * @author Arnaud Amant <contact@arnaudamant.fr>
 */
declare(strict_types=1);

namespace Aamant\OpenMageRector\Rector\ClassConstFetch;

use Aamant\OpenMageRector\Rector\AbstractDeprecatedRector;
use Aamant\OpenMageRector\ValueObject\ClassConstantReplacement;
use InvalidArgumentException;
use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * OpenMage 20.19 marque #[Deprecated] des constantes de classe qui ont la même valeur qu'une autre constante.
 */
final class DeprecatedClassConstantRector extends AbstractDeprecatedRector implements ConfigurableRectorInterface
{
    /**
     * @var ClassConstantReplacement[]
     */
    protected array $replacements = [];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Remplace une constante dépréciée du core par la constante de même valeur, si la constante lue est bien celle du core',
            [
                new ConfiguredCodeSample(
                    <<<'CODE_SAMPLE'
$file = Mage::getStoreConfig(Mage_Core_Helper_Data::XML_PATH_DEV_LOG_FILE);
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
$file = Mage::getStoreConfig(Mage_Core_Helper_Log::XML_PATH_DEV_LOG_FILE);
CODE_SAMPLE,
                    [new ClassConstantReplacement('Mage_Core_Helper_Data', 'XML_PATH_DEV_LOG_FILE', 'Mage_Core_Helper_Log', 'XML_PATH_DEV_LOG_FILE')]
                ),
            ]
        );
    }

    /**
     * @param mixed[] $configuration
     */
    public function configure(array $configuration): void
    {
        foreach ($configuration as $replacement) {
            if (!$replacement instanceof ClassConstantReplacement) {
                throw new InvalidArgumentException('Configuration attendue : ' . ClassConstantReplacement::class);
            }
        }

        $this->replacements = $configuration;
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ClassConstFetch::class];
    }

    /**
     * @param ClassConstFetch $node
     */
    public function refactor(Node $node): ?Node
    {
        if (!$node->class instanceof Name || !$node->name instanceof Identifier) {
            return null;
        }

        $className = $this->resolveClassName($node->class, $node);
        $constantName = $node->name->toString();
        if ($className === null || !$this->reflectionProvider->hasClass($className)) {
            return null;
        }

        $classReflection = $this->reflectionProvider->getClass($className);
        if (!$classReflection->hasConstant($constantName)) {
            return null;
        }

        $declaringClass = $classReflection->getConstant($constantName)->getDeclaringClass();
        foreach ($this->replacements as $replacement) {
            if ($replacement->constant !== $constantName || !$this->isClass($declaringClass, $replacement->class)) {
                continue;
            }

            if ($replacement->newClass === null) {
                return new ConstFetch($this->createName($replacement->newConstant, $node));
            }

            return new ClassConstFetch($this->createName($replacement->newClass, $node), $replacement->newConstant);
        }

        return null;
    }
}
