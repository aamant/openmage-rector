<?php
/**
 * @copyright Copyright © 2026 Arnaud Amant. All rights reserved.
 * @author Arnaud Amant <contact@arnaudamant.fr>
 */
declare(strict_types=1);

namespace Aamant\OpenMageRector\Rector\Expression;

use Aamant\OpenMageRector\Rector\AbstractDeprecatedRector;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Expression;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * OpenMage 20.19 marque #[Deprecated] Mage_Sales_Model_Order::addStatusToHistory($status, $comment, $notified),
 * qui appelle addStatusHistoryComment($comment, $status)->setIsCustomerNotified($notified) et renvoie la commande.
 */
final class DeprecatedAddStatusToHistoryRector extends AbstractDeprecatedRector
{
    private const string ORDER_CLASS = 'Mage_Sales_Model_Order';

    private const string METHOD = 'addStatusToHistory';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Remplace addStatusToHistory() de Mage_Sales_Model_Order par addStatusHistoryComment()->setIsCustomerNotified(), si sa valeur de retour n\'est pas utilisée',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
$order->addStatusToHistory($order->getStatus(), $comment, true);
$order->addStatusToHistory(Mage_Sales_Model_Order::STATE_CANCELED, $comment);
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
$order->addStatusHistoryComment($comment, $order->getStatus())->setIsCustomerNotified(true);
$order->addStatusHistoryComment($comment, Mage_Sales_Model_Order::STATE_CANCELED)->setIsCustomerNotified(false);
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
        return [Expression::class];
    }

    /**
     * @param Expression $node
     */
    public function refactor(Node $node): ?Node
    {
        $call = $node->expr;
        if (!$call instanceof MethodCall && !$call instanceof NullsafeMethodCall && !$call instanceof StaticCall) {
            return null;
        }

        if (!$this->isName($call->name, self::METHOD) || !$this->mayCallDeprecatedMethod($call, self::ORDER_CLASS)) {
            return null;
        }

        $args = $this->getPositionalArgValues($call);
        if ($args === null || $args === [] || count($args) > 3) {
            return null;
        }

        [$status, $comment, $notified] = $args + [1 => new String_(''), 2 => new ConstFetch(new Name('false'))];

        // addStatusHistoryComment() renvoie l'historique : le chaînage ne vaut que si le retour est ignoré.
        $call->name = new Identifier('addStatusHistoryComment');
        $call->args = [new Arg($comment), new Arg($status)];
        $node->expr = new MethodCall($call, 'setIsCustomerNotified', [new Arg($notified)]);

        return $node;
    }
}
