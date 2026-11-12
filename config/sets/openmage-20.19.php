<?php
/**
 * @copyright Copyright © 2026 Arnaud Amant. All rights reserved.
 * @author Arnaud Amant <contact@arnaudamant.fr>
 */
declare(strict_types=1);

use Aamant\OpenMageRector\Rector\ClassConstFetch\DeprecatedClassConstantRector;
use Aamant\OpenMageRector\Rector\ClassMethod\FormContainerSaveUrlOverrideRector;
use Aamant\OpenMageRector\Rector\Expression\DeprecatedAddStatusToHistoryRector;
use Aamant\OpenMageRector\Rector\Expression\DeprecatedNoopProductStatusFilterRector;
use Aamant\OpenMageRector\Rector\Expression\DeprecatedVisibilityFilterRector;
use Aamant\OpenMageRector\Rector\FuncCall\DeprecatedFunctionToStaticCallRector;
use Aamant\OpenMageRector\Rector\FuncCall\DeprecatedTranslateFunctionRector;
use Aamant\OpenMageRector\Rector\MethodCall\DeprecatedGetterToGetDataRector;
use Aamant\OpenMageRector\Rector\MethodCall\DeprecatedMethodRenameRector;
use Aamant\OpenMageRector\Rector\MethodCall\DeprecatedMethodToStaticCallRector;
use Aamant\OpenMageRector\ValueObject\ClassConstantReplacement;
use Aamant\OpenMageRector\ValueObject\FunctionToStaticCall;
use Aamant\OpenMageRector\ValueObject\GetterToGetData;
use Aamant\OpenMageRector\ValueObject\MethodRename;
use Aamant\OpenMageRector\ValueObject\MethodToStaticCall;
use Rector\Config\RectorConfig;

// Dépréciations #[Deprecated] d'OpenMage 20.19 dont le remplacement a un comportement identique.
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(DeprecatedTranslateFunctionRector::class);
    $rectorConfig->rule(DeprecatedAddStatusToHistoryRector::class);
    $rectorConfig->rule(DeprecatedNoopProductStatusFilterRector::class);
    $rectorConfig->rule(DeprecatedVisibilityFilterRector::class);
    $rectorConfig->rule(FormContainerSaveUrlOverrideRector::class);

    $rectorConfig->ruleWithConfiguration(DeprecatedFunctionToStaticCallRector::class, [
        new FunctionToStaticCall('now', 'Varien_Date', 'now'),
    ]);

    $rectorConfig->ruleWithConfiguration(DeprecatedMethodRenameRector::class, [
        new MethodRename('Mage_Adminhtml_Block_Widget_Form_Container', 'getSaveUrl', 'getFormActionUrl'),
        new MethodRename('Mage_Adminhtml_Block_Widget_Grid', 'getGridUrl', 'getAbsoluteGridUrl'),
        new MethodRename('Mage_Core_Controller_Varien_Router_Standard', '_inludeControllerClass', '_includeControllerClass'),
        new MethodRename('Mage_Sales_Model_Service_Quote', 'submit', 'submitOrder'),
        new MethodRename('Varien_File_Uploader', 'chechAllowedExtension', 'checkAllowedExtension'),
    ]);

    $rectorConfig->ruleWithConfiguration(DeprecatedGetterToGetDataRector::class, [
        new GetterToGetData('Mage_Eav_Model_Entity_Attribute_Abstract', 'getIsGlobal', 'is_global'),
        new GetterToGetData('Mage_Sales_Model_Quote_Item_Abstract', 'getBaseTaxAmount', 'base_tax_amount'),
        new GetterToGetData('Mage_Sales_Model_Quote_Item_Abstract', 'getTaxAmount', 'tax_amount'),
    ]);

    $rectorConfig->ruleWithConfiguration(DeprecatedMethodToStaticCallRector::class, [
        new MethodToStaticCall('Mage_Adminhtml_Tax_RuleController', '_getHelperModel', 'Mage', 'helper'),
        new MethodToStaticCall('Mage_Adminhtml_Tax_RuleController', '_getSingletonModel', 'Mage', 'getSingleton'),
        new MethodToStaticCall('Mage_Core_Model_App', 'isInstalled', 'Mage', 'isInstalled'),
        new MethodToStaticCall('Mage_Customer_AccountController', '_getModel', 'Mage', 'getModel'),
        new MethodToStaticCall('Mage_Wishlist_Helper_Data', '_getHelperInstance', 'Mage', 'helper'),
        new MethodToStaticCall('Mage_Wishlist_Helper_Data', '_getSingletonModel', 'Mage', 'getSingleton'),
    ]);

    $logConstants = [
        'XML_PATH_DEV_LOG_ALLOWED_EXTENSIONS',
        'XML_PATH_DEV_LOG_ENABLED',
        'XML_PATH_DEV_LOG_EXCEPTION_FILE',
        'XML_PATH_DEV_LOG_FILE',
        'XML_PATH_DEV_LOG_MAX_LEVEL',
    ];

    $rectorConfig->ruleWithConfiguration(DeprecatedClassConstantRector::class, [
        ...array_map(
            static fn (string $constant): ClassConstantReplacement => new ClassConstantReplacement('Mage_Core_Helper_Data', $constant, 'Mage_Core_Helper_Log', $constant),
            $logConstants
        ),
        new ClassConstantReplacement('Mage_Adminhtml_Block_Dashboard', 'XML_PATH_ENABLE_CHARTS', 'Mage_Adminhtml_Helper_Dashboard_Data', 'XML_PATH_ENABLE_CHARTS'),
        new ClassConstantReplacement('Mage_Core_Model_Locale', 'XML_PATH_DEFAULT_COUNTRY', 'Mage_Core_Helper_Data', 'XML_PATH_DEFAULT_COUNTRY'),
        new ClassConstantReplacement('Mage_Customer_Model_Customer', 'MINIMUM_PASSWORD_LENGTH', 'Mage_Core_Model_App', 'ABSOLUTE_MIN_PASSWORD_LENGTH'),
        new ClassConstantReplacement('Mage_Newsletter_Model_Subscriber', 'XML_PATH_SENDING_SET_RETURN_PATH', 'Mage_Core_Model_Email_Template', 'XML_PATH_SENDING_SET_RETURN_PATH'),
        new ClassConstantReplacement('Mage_Paypal_Model_System_Config_Backend_MerchantCountry', 'XML_PATH_COUNTRY_DEFAULT', 'Mage_Core_Helper_Data', 'XML_PATH_DEFAULT_COUNTRY'),
        new ClassConstantReplacement('Varien_File_Uploader', 'TMP_NAME_EMPTY', null, 'UPLOAD_ERR_NO_FILE'),
    ]);
};
