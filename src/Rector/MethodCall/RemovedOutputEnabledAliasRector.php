<?php

declare(strict_types=1);

namespace Aamant\OpenMageRector\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * OpenMage 20.17 retire isOutputEnabled() de Mage_Adminhtml_Block_Template, qui ne faisait qu'appeler
 * isModuleOutputEnabled() de Mage_Core_Block_Abstract avec le même argument.
 */
final class RemovedOutputEnabledAliasRector extends AbstractRector
{
    private const string ALIAS = 'isOutputEnabled';

    private const string TARGET = 'isModuleOutputEnabled';

    private const string BLOCK_CLASS = 'Mage_Core_Block_Abstract';

    public function __construct(
        private readonly ReflectionProvider $reflectionProvider,
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Renomme isOutputEnabled() en isModuleOutputEnabled() sur les blocs et sur les objets non typés des templates',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
if (!$this->isOutputEnabled('Mage_GiftMessage')) {
    return '';
}
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
if (!$this->isModuleOutputEnabled('Mage_GiftMessage')) {
    return '';
}
CODE_SAMPLE
                ),
            ]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class, NullsafeMethodCall::class];
    }

    /**
     * @param MethodCall|NullsafeMethodCall $node
     */
    public function refactor(Node $node): ?Node
    {
        if (!$this->isName($node->name, self::ALIAS)) {
            return null;
        }

        // Dans un .phtml, $this n'a pas de type : l'appel est renommé faute de pouvoir l'exclure.
        $classNames = $this->getType($node->var)->getObjectClassNames();
        if ($classNames !== [] && (!$this->isObjectType($node->var, new ObjectType(self::BLOCK_CLASS)) || $this->declaresAlias($classNames))) {
            return null;
        }

        $node->name = new Identifier(self::TARGET);

        return $node;
    }

    /**
     * @param string[] $classNames
     */
    private function declaresAlias(array $classNames): bool
    {
        foreach ($classNames as $className) {
            if ($this->reflectionProvider->hasClass($className)
                && $this->reflectionProvider->getClass($className)->hasNativeMethod(self::ALIAS)
            ) {
                return true;
            }
        }

        return false;
    }
}
