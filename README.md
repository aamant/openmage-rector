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
    ->withSets([OpenMageSetList::OPENMAGE_20_17, OpenMageSetList::OPENMAGE_20_19])
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
| `RemovedEditionConstantRector` | Remplace les constantes `Mage::EDITION_*`, supprimées en OpenMage 20, par leur valeur (`'Community'`, `'Enterprise'`, `'Professional'`, `'Go'`). `Mage::getEdition()` renvoie toujours `'Community'`. |
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
-$isEnterprise = Mage::getEdition() === Mage::EDITION_ENTERPRISE;
+$isEnterprise = Mage::getEdition() === 'Enterprise';
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

`getUnusedPath()` de `Mage_Catalog_Model_Url`, retiré en OpenMage 20, n'a pas de règle : son remplaçant
`getUnusedPathByUrlKey()` demande la clé d'URL en plus. Une surcharge de `getUnusedPath()` dans un rewrite
n'est plus appelée par le core.

Les templates ne sont traités que s'ils sont passés à Rector : `->withFileExtensions(['php', 'phtml'])` et les
fichiers de `app/design` dans `withPaths()`. Les autres règles de la configuration s'y appliquent aussi.

### OpenMage 20.19 — `OpenMageSetList::OPENMAGE_20_19`

OpenMage 20.19 marque environ 390 méthodes, fonctions et constantes de l'attribut natif `#[Deprecated]`.
Dès PHP 8.4, chaque appel émet un `E_USER_DEPRECATED`, que le gestionnaire d'erreurs d'OpenMage ne filtre
pas : exception en mode développeur, erreur journalisée sinon. Le set ne remplace que les dépréciations
dont le remplaçant a un comportement identique (la méthode dépréciée ne fait que l'appeler, ou la constante
a la même valeur).

Les règles de méthodes ne réécrivent un appel que si la méthode appelée est la déclaration dépréciée du
core : un objet typé dont la classe redéfinit la méthode, une classe hors core homonyme ou un objet non
typé (`$this` d'un `.phtml`, retour de `Mage::getModel()`) sont laissés tels quels. `parent::`, `self::`
et `static::` sont traités. Dans un fichier à namespace, les noms produits sont qualifiés (`\Mage`).

| Règle | Remplacements du set |
|---|---|
| `DeprecatedFunctionToStaticCallRector` | `now()` → `Varien_Date::now()` |
| `DeprecatedTranslateFunctionRector` | `__(…)` → `Mage::app()->getTranslator()->translate([…])`, ce qu'exécutait `__()` : sans portée de module, contrairement à `$this->__()` ou `Mage::helper('x')->__()` |
| `DeprecatedMethodRenameRector` | `getSaveUrl()` → `getFormActionUrl()` (`Mage_Adminhtml_Block_Widget_Form_Container`), `getGridUrl()` → `getAbsoluteGridUrl()` (`Mage_Adminhtml_Block_Widget_Grid`), `_inludeControllerClass()` → `_includeControllerClass()` (`Mage_Core_Controller_Varien_Router_Standard`), `submit()` → `submitOrder()` (`Mage_Sales_Model_Service_Quote`), `chechAllowedExtension()` → `checkAllowedExtension()` (`Varien_File_Uploader`) |
| `FormContainerSaveUrlOverrideRector` | Renomme la déclaration `getSaveUrl()` (sans paramètre) d'un descendant de `Mage_Adminhtml_Block_Widget_Form_Container` en `getFormActionUrl()`, seule méthode que `getFormHtml()` appelle depuis 20.19, Dans son corps, `parent::getSaveUrl()` et `$this->getFormActionUrl()` deviennent `parent::getFormActionUrl()`, qu'ils appelaient déjà. Ignore la classe qui déclare déjà `getFormActionUrl()` et la méthode dont le corps appelle `$this->getSaveUrl()`, `self::` ou `static::`. L'URL ne sert que si le `Edit/Form.php` enfant construit son `Varien_Data_Form` avec `'action' => $this->getData('action')` : un formulaire qui fixe sa propre action ignore le container. Un corps qui renvoie une URL relative (`'save'`) n'est pas réécrit et doit passer par `getUrl('*/*/save')`. |
| `DeprecatedGetterToGetDataRector` | `getIsGlobal()` → `getData('is_global')` (`Mage_Eav_Model_Entity_Attribute_Abstract` ; celui de `Mage_Catalog_Model_Resource_Eav_Attribute` n'est pas déprécié), `getTaxAmount()` / `getBaseTaxAmount()` → `getData('tax_amount')` / `getData('base_tax_amount')` (`Mage_Sales_Model_Quote_Item_Abstract`) |
| `DeprecatedMethodToStaticCallRector` | `Mage_Core_Model_App::isInstalled()` → `Mage::isInstalled()`, `Mage_Customer_AccountController::_getModel()` → `Mage::getModel()`, `_getHelperInstance()` / `_getHelperModel()` → `Mage::helper()` et `_getSingletonModel()` → `Mage::getSingleton()` (`Mage_Wishlist_Helper_Data`, `Mage_Adminhtml_Tax_RuleController`) |
| `DeprecatedClassConstantRector` | `Mage_Core_Helper_Data::XML_PATH_DEV_LOG_*` → `Mage_Core_Helper_Log::XML_PATH_DEV_LOG_*`, `Mage_Adminhtml_Block_Dashboard::XML_PATH_ENABLE_CHARTS` → `Mage_Adminhtml_Helper_Dashboard_Data::XML_PATH_ENABLE_CHARTS`, `Mage_Core_Model_Locale::XML_PATH_DEFAULT_COUNTRY` et `Mage_Paypal_Model_System_Config_Backend_MerchantCountry::XML_PATH_COUNTRY_DEFAULT` → `Mage_Core_Helper_Data::XML_PATH_DEFAULT_COUNTRY`, `Mage_Newsletter_Model_Subscriber::XML_PATH_SENDING_SET_RETURN_PATH` → `Mage_Core_Model_Email_Template::XML_PATH_SENDING_SET_RETURN_PATH`, `Mage_Customer_Model_Customer::MINIMUM_PASSWORD_LENGTH` → `Mage_Core_Model_App::ABSOLUTE_MIN_PASSWORD_LENGTH`, `Varien_File_Uploader::TMP_NAME_EMPTY` → `UPLOAD_ERR_NO_FILE` |

```diff
-$order->setUpdatedAt(now());
-Mage::getSingleton('adminhtml/session')->addSuccess(__('Personal data has been erased.'));
+$order->setUpdatedAt(Varien_Date::now());
+Mage::getSingleton('adminhtml/session')->addSuccess(Mage::app()->getTranslator()->translate(['Personal data has been erased.']));
```

```diff
 class Foo_Bar_Model_Attribute extends Mage_Eav_Model_Entity_Attribute
 {
     public function isGlobal()
     {
-        return $this->getIsGlobal() == 1;
+        return $this->getData('is_global') == 1;
     }
 }
```

Trois règles traitent des appels dont la valeur de retour change ou disparaît : elles ne s'appliquent
qu'à un appel qui forme une instruction seule (`$x->methode(…);`), jamais quand le retour est utilisé ou
enchaîné. Ces méthodes n'ayant pas d'homonyme connu, un objet non typé (`Mage::getSingleton('…')`, retour de
`Mage::getModel()->load()`) est accepté ; seule une classe qui déclare elle-même la méthode est exclue.

| Règle | Remplacement |
|---|---|
| `DeprecatedAddStatusToHistoryRector` | `addStatusToHistory($status, $comment, $notified)` → `addStatusHistoryComment($comment, $status)->setIsCustomerNotified($notified)` (`Mage_Sales_Model_Order`), ce qu'exécutait la méthode dépréciée. Arguments absents : `$comment = ''`, `$notified = false`. Ignore les arguments nommés ou décompressés. Les deux premiers arguments sont évalués dans l'ordre inverse. |
| `DeprecatedNoopProductStatusFilterRector` | Supprime `addVisibleFilterToCollection()` et `addSaleableFilterToCollection()` (`Mage_Catalog_Model_Product_Status`), qui ne filtrent rien. L'objet et l'argument doivent être sans effet de bord : variable, propriété, ou `Mage::getSingleton()` / `Mage::getModel()` d'un alias littéral. Une variable devenue inutile ou un `if` resté vide ne sont pas retirés (set `DEAD_CODE` de Rector). |
| `DeprecatedVisibilityFilterRector` | `addVisibleIn{Catalog,Search,Site}FilterToCollection($collection)` → `$collection->setVisibility(Mage_Catalog_Model_Product_Visibility::getVisibleIn{Catalog,Search,Site}Ids())` (`Mage_Catalog_Model_Product_Visibility`). Les méthodes `getVisibleIn*Ids()` sont statiques et le core les appelle par `self::`. L'objet doit être sans effet de bord, comme ci-dessus. |

```diff
-$order->addStatusToHistory($order->getStatus(), $comment, true);
-Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
-Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
+$order->addStatusHistoryComment($comment, $order->getStatus())->setIsCustomerNotified(true);
+$collection->setVisibility(Mage_Catalog_Model_Product_Visibility::getVisibleInCatalogIds());
```

Les règles sont configurables pour d'autres dépréciations, avec les objets de `Aamant\OpenMageRector\ValueObject` :

```php
use Aamant\OpenMageRector\Rector\MethodCall\DeprecatedMethodRenameRector;
use Aamant\OpenMageRector\ValueObject\MethodRename;

return RectorConfig::configure()
    ->withConfiguredRule(DeprecatedMethodRenameRector::class, [
        new MethodRename('Mage_Adminhtml_Block_Widget_Grid', 'getGridUrl', 'getAbsoluteGridUrl'),
    ]);
```

Restent à corriger à la main, leur remplaçant changeant le retour ou les arguments : `getFormated()` →
`format()`, `Mage_Core_Model_Config::getBaseDir()` → `getOptions()->getDir()`,
`Mage_Core_Model_Store::isCurrentlySecure()` → `Mage::app()->isCurrentlySecure()`, les appels à
`addStatusToHistory()` ou aux filtres de collection dont le retour est utilisé,
`Mage_Adminhtml_Block_Store_Switcher::get*Collection()`, `getKeyList()` → `getIndexList()`,
`toOptionArray()` → `toOptionIdArray()` (pages CMS), `export()` → `exportFile()`. Les autres dépréciations
n'ont pas de remplaçant.

## Tests

```bash
composer install
composer test
```

Une règle = un dossier sous `tests/Rector/`, avec ses fixtures `*.php.inc` (avant / `-----` / après ;
sans séparateur, le fichier ne doit pas changer) et les classes du core simulées dans `Source/`.

Les classes simulées sont partagées entre les tests d'un même processus : deux `Source/` qui déclarent une
classe de même nom doivent la déclarer à l'identique.
