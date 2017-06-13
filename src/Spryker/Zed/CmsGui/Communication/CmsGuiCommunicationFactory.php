<?php
/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CmsGui\Communication;

use Generated\Shared\Transfer\CmsBlockGlossaryTransfer;
use Generated\Shared\Transfer\CmsGlossaryTransfer;
use Spryker\Zed\CmsGui\CmsGuiDependencyProvider;
use Spryker\Zed\CmsGui\Communication\Autocomplete\AutocompleteDataProvider;
use Spryker\Zed\CmsGui\Communication\Form\Block\CmsBlockForm;
use Spryker\Zed\CmsGui\Communication\Form\Constraint\UniqueGlossaryForSearchType;
use Spryker\Zed\CmsGui\Communication\Form\Constraint\UniqueName;
use Spryker\Zed\CmsGui\Communication\Form\Constraint\UniqueUrl;
use Spryker\Zed\CmsGui\Communication\Form\DataProvider\CmsBlockFormDataProvider;
use Spryker\Zed\CmsGui\Communication\Form\DataProvider\CmsBlockGlossaryFormDataProvider;
use Spryker\Zed\CmsGui\Communication\Form\DataProvider\CmsGlossaryFormTypeDataProvider;
use Spryker\Zed\CmsGui\Communication\Form\DataProvider\CmsPageFormTypeDataProvider;
use Spryker\Zed\CmsGui\Communication\Form\DataProvider\CmsVersionDataProvider;
use Spryker\Zed\CmsGui\Communication\Form\Glossary\CmsBlockGlossaryForm;
use Spryker\Zed\CmsGui\Communication\Form\Glossary\CmsBlockGlossaryPlaceholderForm;
use Spryker\Zed\CmsGui\Communication\Form\Glossary\CmsGlossaryAttributesFormType;
use Spryker\Zed\CmsGui\Communication\Form\Glossary\CmsGlossaryFormType;
use Spryker\Zed\CmsGui\Communication\Form\Page\CmsPageAttributesFormType;
use Spryker\Zed\CmsGui\Communication\Form\Page\CmsPageFormType;
use Spryker\Zed\CmsGui\Communication\Form\Page\CmsPageMetaAttributesFormType;
use Spryker\Zed\CmsGui\Communication\Form\Version\CmsVersionFormType;
use Spryker\Zed\CmsGui\Communication\Mapper\CmsVersionMapper;
use Spryker\Zed\CmsGui\Communication\Table\CmsBlockTable;
use Spryker\Zed\CmsGui\Communication\Table\CmsPageTable;
use Spryker\Zed\CmsGui\Communication\Tabs\CmsBlockGlossaryTabs;
use Spryker\Zed\CmsGui\Communication\Tabs\GlossaryTabs;
use Spryker\Zed\CmsGui\Communication\Tabs\PageTabs;
use Spryker\Zed\CmsGui\Dependency\Facade\CmsGuiToCmsBlockInterface;
use Spryker\Zed\CmsGui\Dependency\QueryContainer\CmsGuiToCmsBlockQueryContainerInterface;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;

/**
 * @method \Spryker\Zed\CmsGui\CmsGuiConfig getConfig()
 */
class CmsGuiCommunicationFactory extends AbstractCommunicationFactory
{

    /**
     * @return \Spryker\Zed\CmsGui\Communication\Table\CmsPageTable
     */
    public function createCmsPageTable()
    {
        return new CmsPageTable(
            $this->getCmsQueryContainer(),
            $this->getLocaleFacade(),
            $this->getConfig(),
            $this->getCmsFacade()
        );
    }

    /**
     * @return \Spryker\Zed\Gui\Communication\Tabs\TabsInterface
     */
    public function createPageTabs()
    {
        return new PageTabs();
    }

    /**
     * @param \Generated\Shared\Transfer\CmsGlossaryTransfer $cmsGlossaryTransfer
     *
     * @return \Spryker\Zed\CmsGui\Communication\Tabs\GlossaryTabs
     */
    public function createPlaceholderTabs(CmsGlossaryTransfer $cmsGlossaryTransfer)
    {
        return new GlossaryTabs($cmsGlossaryTransfer);
    }

    /**
     * @param CmsBlockGlossaryTransfer $glossaryTransfer
     *
     * @return CmsBlockGlossaryTabs
     */
    public function createCmsBlockPlaceholderTabs(CmsBlockGlossaryTransfer $glossaryTransfer)
    {
        return new CmsBlockGlossaryTabs($glossaryTransfer);
    }

    /**
     * @param \Spryker\Zed\CmsGui\Communication\Form\DataProvider\CmsVersionDataProvider $cmsVersionDataProvider
     * @param int|null $idCmsPage
     * @param int|null $version
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createCmsVersionForm(CmsVersionDataProvider $cmsVersionDataProvider, $idCmsPage = null, $version = null)
    {
        $cmsVersionFormType = $this->createCmsVersionFormType();

        return $this->getFormFactory()->create(
            $cmsVersionFormType,
            $cmsVersionDataProvider->getData($idCmsPage, $version),
            $cmsVersionDataProvider->getOptions($idCmsPage)
        );
    }

    /**
     * @param \Spryker\Zed\CmsGui\Communication\Form\DataProvider\CmsPageFormTypeDataProvider $cmsPageFormTypeDataProvider
     * @param int|null $idCmsPage
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createCmsPageForm(CmsPageFormTypeDataProvider $cmsPageFormTypeDataProvider, $idCmsPage = null)
    {
        $cmsPageFormType = $this->createCmsPageFormType();

        return $this->getFormFactory()->create(
            $cmsPageFormType,
            $cmsPageFormTypeDataProvider->getData($idCmsPage),
            $cmsPageFormTypeDataProvider->getOptions()
        );
    }

    public function createCmsBlockForm(CmsBlockFormDataProvider $cmsBlockFormDataProvider, $idCmsBlock = null)
    {
        $cmsBlockForm = new CmsBlockForm(
            $this->getProvidedDependency(CmsGuiDependencyProvider::QUERY_CONTAINER_CMS_BLOCK)
        );

        return $this->getFormFactory()->create(
            $cmsBlockForm,
            $cmsBlockFormDataProvider->getData($idCmsBlock),
            $cmsBlockFormDataProvider->getOptions()
        );
    }

    /**
     * @param \Spryker\Zed\CmsGui\Communication\Form\DataProvider\CmsGlossaryFormTypeDataProvider $cmsGlossaryFormTypeDataProvider
     * @param int $idCmsPage
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createCmsGlossaryForm(
        CmsGlossaryFormTypeDataProvider $cmsGlossaryFormTypeDataProvider,
        $idCmsPage
    ) {
        $cmsGlossaryFormType = $this->createCmsGlossaryFormType();

        return $this->getFormFactory()->create(
            $cmsGlossaryFormType,
            $cmsGlossaryFormTypeDataProvider->getData($idCmsPage),
            $cmsGlossaryFormTypeDataProvider->getOptions()
        );
    }

    /**
     * @param CmsBlockGlossaryFormDataProvider $cmsBlockGlossaryFormDataProvider
     * @param $idCmsBlock
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createCmsBlockGlossaryForm(
      CmsBlockGlossaryFormDataProvider $cmsBlockGlossaryFormDataProvider,
      $idCmsBlock
    ) {
        $cmsBlockGlossaryForm = $this->createCmsBlockGlossaryFormType();

        return $this->getFormFactory()
            ->create(
                $cmsBlockGlossaryForm,
                $cmsBlockGlossaryFormDataProvider->getData($idCmsBlock),
                $cmsBlockGlossaryFormDataProvider->getOptions()
            );
    }

    /**
     * @return \Spryker\Zed\CmsGui\Communication\Form\DataProvider\CmsPageFormTypeDataProvider
     */
    public function createCmsPageFormTypeDataProvider()
    {
        return new CmsPageFormTypeDataProvider(
            $this->getCmsQueryContainer(),
            $this->getCmsFacade(),
            $this->getLocaleFacade()
        );
    }

    /**
     * @return CmsBlockFormDataProvider
     */
    public function createCmsBlockFormDataProvider()
    {
        return new CmsBlockFormDataProvider(
            $this->getCmsBlockQueryContainer(),
            $this->getCmsBlockFacade(),
            $this->getLocaleFacade()
        );
    }

    /**
     * @return \Spryker\Zed\CmsGui\Communication\Form\DataProvider\CmsVersionDataProvider
     */
    public function createCmsVersionFormDataProvider()
    {
        return new CmsVersionDataProvider(
            $this->getCmsFacade()
        );
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function createCmsPageFormType()
    {
        return new CmsPageFormType(
            $this->createCmsPageAttributesFormType(),
            $this->createCmsPageMetaAttributesFormType()
        );
    }

    /**
     * @return \Spryker\Zed\CmsGui\Communication\Form\Version\CmsVersionFormType
     */
    protected function createCmsVersionFormType()
    {
        return new CmsVersionFormType();
    }

    /**
     * @return \Spryker\Zed\CmsGui\Communication\Form\DataProvider\CmsGlossaryFormTypeDataProvider
     */
    public function createCmsGlossaryFormTypeDataProvider()
    {
        return new CmsGlossaryFormTypeDataProvider($this->getCmsFacade());
    }

    /**
     * @return CmsBlockGlossaryFormDataProvider
     */
    public function createCmsBlockGlossaryFormDataProvider()
    {
        return new CmsBlockGlossaryFormDataProvider(
            $this->getCmsBlockFacade()
        );
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function createCmsGlossaryFormType()
    {
        return new CmsGlossaryFormType($this->createCmsGlossaryAttributesFormType());
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function createCmsGlossaryAttributesFormType()
    {
        return new CmsGlossaryAttributesFormType(
            $this->getCmsFacade(),
            $this->createUniqueGlossaryForSearchTypeConstraint()
        );
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function createCmsPageAttributesFormType()
    {
        return new CmsPageAttributesFormType($this->createUniqueUrlConstraint(), $this->createUniqueNameConstraint());
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function createCmsPageMetaAttributesFormType()
    {
        return new CmsPageMetaAttributesFormType();
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    protected function createCmsBlockGlossaryFormType()
    {
        return new CmsBlockGlossaryForm(
            $this->createCmsBlockGlossaryPlaceholderFormType()
        );
    }

    /**
     * @return CmsBlockGlossaryPlaceholderForm
     */
    protected function createCmsBlockGlossaryPlaceholderFormType()
    {
        return new CmsBlockGlossaryPlaceholderForm(
            $this->getCmsBlockFacade(),
            $this->createUniqueGlossaryForSearchTypeConstraint()
        );
    }

    /**
     * @return \Symfony\Component\Validator\Constraint
     */
    protected function createUniqueUrlConstraint()
    {
        return new UniqueUrl([
            UniqueUrl::OPTION_URL_FACADE => $this->getUrlFacade(),
            UniqueUrl::OPTION_CMS_FACADE => $this->getCmsFacade(),
        ]);
    }

    /**
     * @return \Spryker\Zed\CmsGui\Communication\Form\Constraint\UniqueName
     */
    protected function createUniqueNameConstraint()
    {
        return new UniqueName([
            UniqueName::OPTION_CMS_QUERY_CONTAINER => $this->getCmsQueryContainer(),
        ]);
    }

    /**
     * @return \Spryker\Zed\CmsGui\Communication\Form\Constraint\UniqueGlossaryForSearchType
     */
    protected function createUniqueGlossaryForSearchTypeConstraint()
    {
        return new UniqueGlossaryForSearchType([
            UniqueGlossaryForSearchType::OPTION_GLOSSARY_FACADE => $this->getGlossaryFacade(),
        ]);
    }

    /**
     * @return \Spryker\Zed\CmsGui\Communication\Autocomplete\AutocompleteDataProviderInterface
     */
    public function createAutocompleteDataProvider()
    {
        return new AutocompleteDataProvider($this->getCmsQueryContainer());
    }

    /**
     * @return \Spryker\Zed\CmsGui\Communication\Mapper\CmsVersionMapper
     */
    public function createCmsVersionDataHelper()
    {
        return new CmsVersionMapper(
            $this->getCmsQueryContainer(),
            $this->getUtilEncodingService()
        );
    }

    /**
     * @return \Spryker\Zed\CmsGui\Dependency\Facade\CmsGuiToLocaleInterface
     */
    public function getLocaleFacade()
    {
        return $this->getProvidedDependency(CmsGuiDependencyProvider::FACADE_LOCALE);
    }

    /**
     * @return \Spryker\Zed\CmsGui\Dependency\Facade\CmsGuiToCmsInterface
     */
    public function getCmsFacade()
    {
        return $this->getProvidedDependency(CmsGuiDependencyProvider::FACADE_CMS);
    }

    /**
     * @return CmsGuiToCmsBlockInterface
     */
    public function getCmsBlockFacade()
    {
        return $this->getProvidedDependency(CmsGuiDependencyProvider::FACADE_CMS_BLOCK);
    }

    /**
     * @return \Spryker\Zed\CmsGui\Dependency\QueryContainer\CmsGuiToCmsQueryContainerInterface
     */
    public function getCmsQueryContainer()
    {
        return $this->getProvidedDependency(CmsGuiDependencyProvider::QUERY_CONTAINER_CMS);
    }

    /**
     * @return CmsGuiToCmsBlockQueryContainerInterface
     */
    public function getCmsBlockQueryContainer()
    {
        return $this->getProvidedDependency(CmsGuiDependencyProvider::QUERY_CONTAINER_CMS_BLOCK);
    }

    /**
     * @return \Spryker\Zed\CmsGui\Dependency\Facade\CmsGuiToUrlInterface
     */
    public function getUrlFacade()
    {
        return $this->getProvidedDependency(CmsGuiDependencyProvider::FACADE_URL);
    }

    /**
     * @return \Spryker\Zed\CmsGui\Dependency\Facade\CmsGuiToCmsGlossaryFacadeInterface
     */
    public function getGlossaryFacade()
    {
        return $this->getProvidedDependency(CmsGuiDependencyProvider::FACADE_GLOSSARY);
    }

    /**
     * @return \Spryker\Zed\CmsGui\Dependency\Service\CmsGuiToUtilEncodingInterface
     */
    public function getUtilEncodingService()
    {
        return $this->getProvidedDependency(CmsGuiDependencyProvider::SERVICE_UTIL_ENCODING);
    }

    /**
     * @return \Spryker\Zed\CmsGui\Communication\Table\CmsBlockTable
     */
    public function createCmsBlockTable()
    {
        $cmsBlockQuery = $this->getCmsBlockQueryContainer()
            ->queryCmsBlockWithTemplate();

        return new CmsBlockTable($cmsBlockQuery);
    }

}
