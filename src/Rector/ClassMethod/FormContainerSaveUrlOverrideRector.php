<?php
/**
 * @copyright Copyright © 2026 Arnaud Amant. All rights reserved.
 * @author Arnaud Amant <contact@arnaudamant.fr>
 */
declare(strict_types=1);

namespace Aamant\OpenMageRector\Rector\ClassMethod;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeVisitor;
use PHPStan\Reflection\ReflectionProvider;
use Rector\PhpParser\Node\BetterNodeFinder;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Depuis OpenMage 20.19, Mage_Adminhtml_Block_Widget_Form_Container::getFormHtml() lit l'URL du formulaire
 * dans getFormActionUrl() ; getSaveUrl(), déprécié, n'est plus appelé par le core.
 */
final class FormContainerSaveUrlOverrideRector extends AbstractRector
{
    protected const string PARENT_CLASS = 'Mage_Adminhtml_Block_Widget_Form_Container';

    protected const string OLD_METHOD = 'getSaveUrl';

    protected const string NEW_METHOD = 'getFormActionUrl';

    public function __construct(
        protected readonly ReflectionProvider $reflectionProvider,
        protected readonly BetterNodeFinder $betterNodeFinder,
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Renomme la surcharge getSaveUrl() d\'un descendant de Mage_Adminhtml_Block_Widget_Form_Container en getFormActionUrl()',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
class Foo_Block_Adminhtml_Bar_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function getSaveUrl()
    {
        return $this->getUrl('*/foo_bar/save', ['_current' => true]);
    }
}
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
class Foo_Block_Adminhtml_Bar_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function getFormActionUrl()
    {
        return $this->getUrl('*/foo_bar/save', ['_current' => true]);
    }
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
        return [Class_::class];
    }

    /**
     * @param Class_ $node
     */
    public function refactor(Node $node): ?Node
    {
        $method = $node->getMethod(self::OLD_METHOD);
        if (!$method instanceof ClassMethod || $method->isStatic() || $method->params !== []
            || $node->getMethod(self::NEW_METHOD) instanceof ClassMethod
            || !$this->isFormContainer($node)
            || $this->callsOwnSaveUrl($method)
        ) {
            return null;
        }

        $method->name = new Identifier(self::NEW_METHOD);

        // Le getSaveUrl() du core et l'appel getFormActionUrl() du corps visaient la méthode héritée :
        // après renommage, $this->getFormActionUrl() ou parent::getSaveUrl() rappellent la méthode renommée.
        $this->traverseNodesWithCallable((array) $method->stmts, function (Node $subNode): int|Node|null {
            if ($subNode instanceof Class_) {
                return NodeVisitor::DONT_TRAVERSE_CURRENT_AND_CHILDREN;
            }
            if ($this->isParentCall($subNode, self::OLD_METHOD) || $this->isOwnCall($subNode, self::NEW_METHOD)) {
                /** @var MethodCall|StaticCall $subNode */
                return new StaticCall(new Name('parent'), self::NEW_METHOD, $subNode->args);
            }

            return null;
        });

        return $node;
    }

    protected function isFormContainer(Class_ $class): bool
    {
        $className = $this->getName($class);
        if ($className === null || !$this->reflectionProvider->hasClass($className)) {
            return false;
        }

        $parentNames = array_map('strtolower', $this->reflectionProvider->getClass($className)->getParentClassesNames());

        return in_array(strtolower(self::PARENT_CLASS), $parentNames, true);
    }

    /**
     * Appel de getSaveUrl() sur $this, self:: ou static:: dans le corps de la méthode.
     */
    protected function callsOwnSaveUrl(ClassMethod $method): bool
    {
        return $this->betterNodeFinder->findFirst(
            (array) $method->stmts,
            fn (Node $subNode): bool => $this->isOwnCall($subNode, self::OLD_METHOD)
        ) instanceof Node;
    }

    /**
     * Appel de $methodName sur $this, self:: ou static::.
     */
    protected function isOwnCall(Node $node, string $methodName): bool
    {
        if ($node instanceof MethodCall || $node instanceof NullsafeMethodCall) {
            return $node->var instanceof Variable && $this->isName($node->var, 'this')
                && $this->isName($node->name, $methodName);
        }

        return $node instanceof StaticCall && $node->class instanceof Name
            && in_array($node->class->toLowerString(), ['self', 'static'], true)
            && $this->isName($node->name, $methodName);
    }

    protected function isParentCall(Node $node, string $methodName): bool
    {
        return $node instanceof StaticCall && $node->class instanceof Name
            && $node->class->toLowerString() === 'parent' && $this->isName($node->name, $methodName);
    }
}
