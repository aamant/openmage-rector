<?php

declare(strict_types=1);

namespace Aamant\OpenMageRector\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * OpenMage 20.17 retire htmlEscape() et urlEscape(), alias d'escapeHtml() et escapeUrl()
 * dans Mage_Core_Block_Abstract et Mage_Core_Helper_Abstract.
 */
final class RemovedEscapeAliasRector extends AbstractRector
{
    private const RENAMES = [
        'htmlEscape' => 'escapeHtml',
        'urlEscape' => 'escapeUrl',
    ];

    private const CORE_CLASSES = [
        'Mage_Core_Block_Abstract',
        'Mage_Core_Helper_Abstract',
    ];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Renomme htmlEscape() en escapeHtml() et urlEscape() en escapeUrl() sur les blocs et helpers, et sur les objets non typés des templates',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
<a href="<?php echo $this->urlEscape($_item->getUrl()) ?>"><?php echo $this->htmlEscape($_item->getName()) ?></a>
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
<a href="<?php echo $this->escapeUrl($_item->getUrl()) ?>"><?php echo $this->escapeHtml($_item->getName()) ?></a>
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
        $methodName = $this->getName($node->name);
        if ($methodName === null || !isset(self::RENAMES[$methodName])) {
            return null;
        }

        // Dans un .phtml, $this n'a pas de type : l'appel est renommé faute de pouvoir l'exclure.
        if ($this->getType($node->var)->getObjectClassNames() !== [] && !$this->isCoreEscaper($node)) {
            return null;
        }

        $node->name = new Identifier(self::RENAMES[$methodName]);

        return $node;
    }

    private function isCoreEscaper(MethodCall|NullsafeMethodCall $node): bool
    {
        foreach (self::CORE_CLASSES as $coreClass) {
            if ($this->isObjectType($node->var, new ObjectType($coreClass))) {
                return true;
            }
        }

        return false;
    }
}
