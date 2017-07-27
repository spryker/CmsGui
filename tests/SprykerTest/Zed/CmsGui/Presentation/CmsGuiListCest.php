<?php

/**
 * Copyright © 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\CmsGui\Presentation;

use SprykerTest\Zed\CmsGui\PageObject\CmsGuiListPage;
use SprykerTest\Zed\CmsGui\PresentationTester;

/**
 * Auto-generated group annotations
 * @group SprykerTest
 * @group Zed
 * @group CmsGui
 * @group Presentation
 * @group CmsGuiListCest
 * Add your own group annotations below this line
 */
class CmsGuiListCest
{

    /**
     * @param \SprykerTest\Zed\CmsGui\PresentationTester $i
     *
     * @return void
     */
    public function breadcrumbIsVisible(PresentationTester $i)
    {
        $i->amOnPage(CmsGuiListPage::URL);
        $i->seeBreadcrumbNavigation('Dashboard / CMS / Pages');
    }

}