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
| `RemovedBundlePricesAliasRector` | Renomme `getPrices()` et `getPricesDependingOnTax()`, alias retirés de `Mage_Bundle_Model_Product_Price` en OpenMage 20, en `getTotalPrices()`, avec les mêmes arguments. S'applique quand l'objet est un modèle de prix (`Mage_Catalog_Model_Product_Type_Price` ou descendant) ou un appel `->getPriceModel()`, même non typé ; ignore les appels sans argument (`getPrices()` magique de l'attribut configurable) et les classes qui déclarent elles-mêmes ces méthodes. |
| `RemovedCollectionAttributeAliasRector` | Renomme `getCollectionAttribute()`, retiré de `Mage_Eav_Model_Config` en OpenMage 20, en `getAttribute()`. S'applique aussi sur un objet non typé (`Mage::getSingleton('eav/config')`) ; ignore les appels qui n'ont pas deux arguments et les classes qui déclarent elles-mêmes la méthode. En OpenMage 20, `getAttribute()` renvoie un modèle vide, et non `null`, pour un attribut inconnu. |
| `RemovedEscapeAliasRector` | Renomme `htmlEscape()` en `escapeHtml()` et `urlEscape()` en `escapeUrl()`, alias retirés de `Mage_Core_Block_Abstract` et `Mage_Core_Helper_Abstract` en 20.17. Renomme aussi quand le type de l'objet est inconnu (`$this` d'un `.phtml`) ; ignore les classes typées hors de ces deux hiérarchies. |
| `RemovedPriceDataAliasRector` | Remplace `addMinimalPrice()` et `addFinalPrice()`, retirés de `Mage_Catalog_Model_Resource_Product_Collection` en OpenMage 20, par `addPriceData()`, qu'ils ne faisaient qu'appeler, et fusionne les appels enchaînés en un seul `addPriceData()`. S'applique aussi quand le type de la collection est inconnu ou générique (`getCollection()`, `.phtml`) ; ignore les appels avec argument et les classes qui déclarent elles-mêmes ces méthodes. |

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
-[$minPrice, $maxPrice] = $product->getPriceModel()->getPrices($product);
+[$minPrice, $maxPrice] = $product->getPriceModel()->getTotalPrices($product);
```

```diff
-$status = Mage::getSingleton('eav/config')->getCollectionAttribute('catalog_product', 'status');
+$status = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'status');
```

```diff
-<a href="<?php echo $this->urlEscape($_item->getUrl()) ?>"><?php echo $this->htmlEscape($_item->getName()) ?></a>
+<a href="<?php echo $this->escapeUrl($_item->getUrl()) ?>"><?php echo $this->escapeHtml($_item->getName()) ?></a>
```

```diff
 $products = $product->getCollection()
     ->addAttributeToSelect('*')
-    ->addMinimalPrice()
-    ->addFinalPrice()
+    ->addPriceData()
     ->addTaxPercents();
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
