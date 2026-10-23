<?php

function isEnterprise()
{
    if (Mage::getEdition() === Mage::EDITION_ENTERPRISE) {
        return true;
    }

    return in_array(\Mage::getEdition(), [Mage::EDITION_PROFESSIONAL, Mage::EDITION_GO, \Mage::EDITION_COMMUNITY], true);
}

?>
-----
<?php

function isEnterprise()
{
    if (Mage::getEdition() === 'Enterprise') {
        return true;
    }

    return in_array(\Mage::getEdition(), ['Professional', 'Go', 'Community'], true);
}

?>
