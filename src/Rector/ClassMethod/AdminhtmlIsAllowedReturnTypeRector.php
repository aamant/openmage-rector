<?php

declare(strict_types=1);

namespace Aamant\OpenMageRector\Rector\ClassMethod;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\AssignOp;
use PhpParser\Node\Expr\BinaryOp;
use PhpParser\Node\Expr\Cast\Bool_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Ternary;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeVisitor;
use PHPStan\Reflection\ClassReflection;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\PHPStan\ScopeFetcher;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * OpenMage 20.17 déclare Mage_Adminhtml_Controller_Action::_isAllowed(): bool ; une surcharge sans type
 * de retour provoque une erreur fatale au chargement de la classe.
 */
final class AdminhtmlIsAllowedReturnTypeRector extends AbstractRector
{
    private const PARENT_CLASS = 'Mage_Adminhtml_Controller_Action';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Type _isAllowed() en bool dans les contrôleurs adminhtml et convertit les retours non booléens',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
class Foo_Adminhtml_BarController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getStoreConfig('foo/bar/enabled');
    }
}
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
class Foo_Adminhtml_BarController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed(): bool
    {
        return (bool) Mage::getStoreConfig('foo/bar/enabled');
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
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($node->returnType !== null || $node->isStatic() || !$this->isName($node, '_isAllowed')) {
            return null;
        }

        $classReflection = ScopeFetcher::fetch($node)->getClassReflection();
        if (!$classReflection instanceof ClassReflection
            || !in_array(self::PARENT_CLASS, $classReflection->getParentClassesNames(), true)
        ) {
            return null;
        }

        $node->returnType = new Identifier('bool');

        // En mode coercitif, null, un tableau ou un objet retournés pour un type bool lèvent une TypeError.
        $this->traverseNodesWithCallable((array) $node->stmts, function (Node $subNode): int|Node|null {
            if ($subNode instanceof Class_ || $subNode instanceof FunctionLike) {
                return NodeVisitor::DONT_TRAVERSE_CURRENT_AND_CHILDREN;
            }
            if (!$subNode instanceof Return_ || $subNode->expr === null) {
                return null;
            }
            // Mage::getSingleton() n'est pas typé sans l'extension phpstan-magento1 :
            // isAllowed() y est celui de Mage_Admin_Model_Session, qui renvoie un bool.
            if ($this->getType($subNode->expr)->isBoolean()->yes()
                || ($subNode->expr instanceof MethodCall && $this->isName($subNode->expr->name, 'isAllowed'))
            ) {
                return null;
            }

            // Le printer n'ajoute pas les parenthèses : « (bool) $a ? $b : $c » ne caste que $a.
            if ($subNode->expr instanceof BinaryOp || $subNode->expr instanceof Ternary
                || $subNode->expr instanceof Assign || $subNode->expr instanceof AssignOp
            ) {
                $subNode->expr->setAttribute(AttributeKey::WRAPPED_IN_PARENTHESES, true);
            }
            $subNode->expr = new Bool_($subNode->expr);

            return $subNode;
        });

        return $node;
    }
}
