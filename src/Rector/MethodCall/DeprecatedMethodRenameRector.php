<?php
/**
 * @copyright Copyright © 2026 Arnaud Amant. All rights reserved.
 * @author Arnaud Amant <contact@arnaudamant.fr>
 */
declare(strict_types=1);

namespace Aamant\OpenMageRector\Rector\MethodCall;

use Aamant\OpenMageRector\Rector\AbstractDeprecatedRector;
use Aamant\OpenMageRector\ValueObject\MethodRename;
use InvalidArgumentException;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * OpenMage 20.19 marque #[Deprecated] des méthodes qui ne font qu'appeler une autre méthode
 * de la même classe avec les mêmes arguments.
 */
final class DeprecatedMethodRenameRector extends AbstractDeprecatedRector implements ConfigurableRectorInterface
{
    /**
     * @var MethodRename[]
     */
    protected array $methodRenames = [];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Renomme l\'appel d\'une méthode dépréciée du core vers la méthode qu\'elle appelle, si la méthode appelée est bien celle du core',
            [
                new ConfiguredCodeSample(
                    <<<'CODE_SAMPLE'
return $uploader->chechAllowedExtension($extension);
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
return $uploader->checkAllowedExtension($extension);
CODE_SAMPLE,
                    [new MethodRename('Varien_File_Uploader', 'chechAllowedExtension', 'checkAllowedExtension')]
                ),
            ]
        );
    }

    /**
     * @param mixed[] $configuration
     */
    public function configure(array $configuration): void
    {
        foreach ($configuration as $methodRename) {
            if (!$methodRename instanceof MethodRename) {
                throw new InvalidArgumentException('Configuration attendue : ' . MethodRename::class);
            }
        }

        $this->methodRenames = $configuration;
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class, NullsafeMethodCall::class, StaticCall::class];
    }

    /**
     * @param MethodCall|NullsafeMethodCall|StaticCall $node
     */
    public function refactor(Node $node): ?Node
    {
        foreach ($this->methodRenames as $methodRename) {
            if ($this->isName($node->name, $methodRename->method)
                && $this->callsDeprecatedMethod($node, $methodRename->class)
            ) {
                $node->name = new Identifier($methodRename->newMethod);

                return $node;
            }
        }

        return null;
    }
}
