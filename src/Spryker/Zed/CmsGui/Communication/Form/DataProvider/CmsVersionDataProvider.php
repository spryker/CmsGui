<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CmsGui\Communication\Form\DataProvider;

use Generated\Shared\Transfer\CmsVersionTransfer;
use Spryker\Zed\CmsGui\Communication\Form\Version\CmsVersionFormType;
use Spryker\Zed\CmsGui\Dependency\Facade\CmsGuiToCmsInterface;

class CmsVersionDataProvider
{

    /**
     * @var \Spryker\Zed\CmsGui\Dependency\Facade\CmsGuiToCmsInterface
     */
    protected $cmsFacade;

    /**
     * @param \Spryker\Zed\CmsGui\Dependency\Facade\CmsGuiToCmsInterface $cmsFacade
     */
    public function __construct(CmsGuiToCmsInterface $cmsFacade)
    {
        $this->cmsFacade = $cmsFacade;
    }

    /**
     * @param int|null $idCmsPage
     * @param int|null $version
     *
     * @return array|\Generated\Shared\Transfer\CmsVersionTransfer
     */
    public function getData($idCmsPage = null, $version = null)
    {
        if (!$idCmsPage || !$version) {
            return new CmsVersionTransfer();
        }

        return $this->cmsFacade->findCmsVersionByIdCmsPageAndVersion($idCmsPage, $version);
    }

    /**
     * @param int|null $idCmsPage
     *
     * @return array
     */
    public function getOptions($idCmsPage = null)
    {
        return [
            'data_class' => CmsVersionTransfer::class,
            CmsVersionFormType::OPTION_VERSION_NAME_CHOICES => $this->getVersionList($idCmsPage),
        ];
    }

    /**
     * @param int $idCmsPage
     *
     * @return array
     */
    protected function getVersionList($idCmsPage)
    {
        if (!$idCmsPage) {
            return [];
        }

        $cmsVersionTransfers = $this->cmsFacade->findAllCmsVersionByIdCmsPage($idCmsPage);
        array_shift($cmsVersionTransfers);

        $versionList = [];
        foreach ($cmsVersionTransfers as $cmsVersionTransfer) {
            $versionList[$cmsVersionTransfer->getVersion()] = $this->createOptionLabel($cmsVersionTransfer);
        }

        return $versionList;
    }

    /**
     * @param \Generated\Shared\Transfer\CmsVersionTransfer $cmsVersionTransfer
     *
     * @return string
     */
    protected function createOptionLabel(CmsVersionTransfer $cmsVersionTransfer)
    {
        $optionLabel = sprintf(
            '%s published on %s ',
            $cmsVersionTransfer->getVersionName(),
            date('d/m/Y H:i:s', strtotime($cmsVersionTransfer->getCreatedAt()))
        );

        if ($cmsVersionTransfer->getFirstName() !== null) {
            $optionLabel .= sprintf(
                'by %s %s',
                $cmsVersionTransfer->getFirstName(),
                $cmsVersionTransfer->getLastName()
            );
        }

        return $optionLabel;
    }

}