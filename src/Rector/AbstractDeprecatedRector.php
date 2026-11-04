<?php
/**
 * @copyright Copyright © 2026 Arnaud Amant. All rights reserved.
 * @author Arnaud Amant <contact@arnaudamant.fr>
 */
declare(strict_types=1);

namespace Aamant\OpenMageRector\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\CallLike;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\NullsafePropertyFetch;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar;
use PhpParser\Node\Scalar\InterpolatedString;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\VarLikeIdentifier;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\TypeCombinator;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\Rector\AbstractRector;

/**
 * Résolution commune aux règles des dépréciations #[Deprecated] d'OpenMage 20.19 : un appel n'est réécrit
 * que s'il atteint la déclaration dépréciée du core.
 */
abstract class AbstractDeprecatedRector extends AbstractRector
{
    public function __construct(
        protected readonly ReflectionProvider $reflectionProvider,
    ) {
    }

    /**
     * Nom à produire : qualifié seulement dans un fichier à namespace.
     */
    protected function createName(string $name, Node $node): Name
    {
        return $this->getScope($node)?->getNamespace() === null ? new Name($name) : new FullyQualified($name);
    }

    protected function isGlobalFunctionCall(FuncCall $node, string $functionName): bool
    {
        if (!$node->name instanceof Name || $node->isFirstClassCallable()
            || $node->name->toLowerString() !== strtolower($functionName)
        ) {
            return false;
        }

        // Dans un namespace, un appel non qualifié vise la fonction du namespace si elle existe.
        $namespace = $this->getScope($node)?->getNamespace();

        return $namespace === null
            || $node->name instanceof FullyQualified
            || !$this->reflectionProvider->hasFunction(new FullyQualified($namespace . '\\' . $functionName), null);
    }

    protected function callsDeprecatedMethod(MethodCall|NullsafeMethodCall|StaticCall $node, string $className): bool
    {
        $methodName = $this->getName($node->name);
        if ($methodName === null) {
            return false;
        }

        foreach ($this->resolveCalledClassNames($node) as $calledClassName) {
            if (!$this->reflectionProvider->hasClass($calledClassName)) {
                continue;
            }

            $classReflection = $this->reflectionProvider->getClass($calledClassName);
            if ($classReflection->hasNativeMethod($methodName)
                && $this->isClass($classReflection->getNativeMethod($methodName)->getDeclaringClass(), $className)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Variante tolérante de callsDeprecatedMethod() pour les méthodes sans homonyme connu : un objet non typé
     * (retour de Mage::getSingleton()) est accepté, seule une classe qui redéclare la méthode est exclue.
     */
    protected function mayCallDeprecatedMethod(MethodCall|NullsafeMethodCall|StaticCall $node, string $className): bool
    {
        if ($node instanceof StaticCall) {
            return $this->callsDeprecatedMethod($node, $className);
        }

        $methodName = $this->getName($node->name);
        if ($methodName === null) {
            return false;
        }

        foreach ($this->resolveCalledClassNames($node) as $calledClassName) {
            if (!$this->reflectionProvider->hasClass($calledClassName)) {
                continue;
            }

            $classReflection = $this->reflectionProvider->getClass($calledClassName);
            if ($classReflection->hasNativeMethod($methodName)
                && !$this->isClass($classReflection->getNativeMethod($methodName)->getDeclaringClass(), $className)
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Valeurs des arguments, ou null si l'appel utilise un argument nommé, une décompression ou la syntaxe `f(...)`.
     *
     * @return Expr[]|null
     */
    protected function getPositionalArgValues(CallLike $node): ?array
    {
        if ($node->isFirstClassCallable()) {
            return null;
        }

        $values = [];
        foreach ($node->getArgs() as $arg) {
            if ($arg->unpack || $arg->name !== null) {
                return null;
            }

            $values[] = $arg->value;
        }

        return $values;
    }

    /**
     * Expression que l'on peut supprimer sans perdre d'effet : variable, propriété, constante, scalaire,
     * ou Mage::getSingleton() / Mage::getModel() d'un alias littéral.
     */
    protected function isDroppable(Expr $expr): bool
    {
        return match (true) {
            $expr instanceof Variable => is_string($expr->name),
            $expr instanceof PropertyFetch, $expr instanceof NullsafePropertyFetch => $expr->name instanceof Identifier
                && $this->isDroppable($expr->var),
            $expr instanceof StaticPropertyFetch => $expr->class instanceof Name
                && $expr->name instanceof VarLikeIdentifier,
            $expr instanceof ArrayDimFetch => $expr->dim instanceof Expr && $this->isDroppable($expr->var)
                && $this->isDroppable($expr->dim),
            $expr instanceof Scalar => !$expr instanceof InterpolatedString,
            $expr instanceof ClassConstFetch, $expr instanceof ConstFetch => true,
            $expr instanceof StaticCall => $this->isMageFactoryCall($expr),
            default => false,
        };
    }

    protected function isMageFactoryCall(StaticCall $node): bool
    {
        if (!$node->class instanceof Name || strcasecmp(ltrim($node->class->toString(), '\\'), 'Mage') !== 0
            || !$this->isNames($node->name, ['getSingleton', 'getModel'])
        ) {
            return false;
        }

        $args = $this->getPositionalArgValues($node);

        return $args !== null && count($args) === 1 && $args[0] instanceof String_;
    }

    protected function resolveClassName(Name $name, Node $node): ?string
    {
        $classReflection = $this->getScope($node)?->getClassReflection();

        return match ($name->toLowerString()) {
            'self', 'static' => $classReflection?->getName(),
            'parent' => $classReflection?->getParentClass()?->getName(),
            default => $this->getName($name),
        };
    }

    protected function isClass(ClassReflection $classReflection, string $className): bool
    {
        return strcasecmp($classReflection->getName(), $className) === 0;
    }

    /**
     * @return string[]
     */
    protected function resolveCalledClassNames(MethodCall|NullsafeMethodCall|StaticCall $node): array
    {
        if (!$node instanceof StaticCall) {
            return TypeCombinator::removeNull($this->getType($node->var))->getObjectClassNames();
        }

        // Seuls parent::, self:: et static:: appellent une méthode d'instance de façon statique.
        if (!$node->class instanceof Name
            || !in_array($node->class->toLowerString(), ['parent', 'self', 'static'], true)
        ) {
            return [];
        }

        $className = $this->resolveClassName($node->class, $node);

        return $className === null ? [] : [$className];
    }

    protected function getScope(Node $node): ?Scope
    {
        $scope = $node->getAttribute(AttributeKey::SCOPE);

        return $scope instanceof Scope ? $scope : null;
    }
}
