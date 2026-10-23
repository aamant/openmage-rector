# aamant/openmage-rector

Règles [Rector](https://getrector.com) pour adapter les modules Magento 1 / OpenMage aux changements
de signature du core OpenMage.

## Installation

```bash
composer require --dev aamant/openmage-rector
```

## Utilisation

Par set, dans `rector.php` :

```php
use Aamant\OpenMageRector\Set\OpenMageSetList;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withSets([OpenMageSetList::OPENMAGE_20_17])
    // Classes du core et de lib/ indexées pour résoudre l'héritage des modules.
    ->withAutoloadPaths([
        __DIR__ . '/htdocs/app/code/core',
        __DIR__ . '/htdocs/lib',
    ])
    ->withPaths([__DIR__ . '/htdocs/app/code/local', __DIR__ . '/htdocs/app/code/community']);
```

Ou règle par règle avec `->withRules([...])`.

Les règles s'appuient sur l'héritage des classes : le core OpenMage doit être déclaré dans
`withAutoloadPaths()`, sinon aucun contrôleur n'est reconnu.

Le Finder de Rector ne suit pas les dossiers en lien symbolique : pour des modules déployés par modman,
passer à `withPaths()` la liste des fichiers résolus plutôt que les dossiers.

## Règles

### OpenMage 20.17 — `OpenMageSetList::OPENMAGE_20_17`

| Règle | Effet |
|---|---|
| `AdminhtmlIsAllowedReturnTypeRector` | Ajoute `: bool` à `_isAllowed()` dans les descendants de `Mage_Adminhtml_Controller_Action` (OpenMage #5351) et caste en `(bool)` les retours dont le type n'est pas booléen. Les appels `->isAllowed()` ne sont pas castés. |
| `RemovedEscapeAliasRector` | Renomme `htmlEscape()` en `escapeHtml()` et `urlEscape()` en `escapeUrl()`, alias retirés de `Mage_Core_Block_Abstract` et `Mage_Core_Helper_Abstract` en 20.17. Renomme aussi quand le type de l'objet est inconnu (`$this` d'un `.phtml`) ; ignore les classes typées hors de ces deux hiérarchies. |

```diff
 class Foo_Adminhtml_BarController extends Mage_Adminhtml_Controller_Action
 {
-    protected function _isAllowed()
+    protected function _isAllowed(): bool
     {
-        return $helper->getAllowView();
+        return (bool) $helper->getAllowView();
     }
 }
```

```diff
-<a href="<?php echo $this->urlEscape($_item->getUrl()) ?>"><?php echo $this->htmlEscape($_item->getName()) ?></a>
+<a href="<?php echo $this->escapeUrl($_item->getUrl()) ?>"><?php echo $this->escapeHtml($_item->getName()) ?></a>
```

Les templates ne sont traités que s'ils sont passés à Rector : `->withFileExtensions(['php', 'phtml'])` et les
fichiers de `app/design` dans `withPaths()`. Les autres règles de la configuration s'y appliquent aussi.

## Tests

```bash
composer install
composer test
```

Une règle = un dossier sous `tests/Rector/`, avec ses fixtures `*.php.inc` (avant / `-----` / après ;
sans séparateur, le fichier ne doit pas changer) et les classes du core simulées dans `Source/`.
