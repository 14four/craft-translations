<?php
/**
 * Translations for Craft plugin for Craft CMS 3.x
 *
 * Translations for Craft eliminates error prone and costly copy/paste workflows for launching human translated Craft CMS web content.
 *
 * @link      http://www.acclaro.com/
 * @copyright Copyright (c) 2018 Acclaro
 */

namespace acclaro\translations\services\job;

use Craft;
use craft\base\Element;
use craft\helpers\Path;
use yii\web\HttpException;
use craft\models\EntryDraft;
use craft\helpers\FileHelper;
use craft\helpers\ElementHelper;
use acclaro\translations\services\App;
use acclaro\translations\Translations;
use acclaro\translations\services\repository\SiteRepository;
use acclaro\translations\models\GlobalSetDraftModel;

class UpdateDraftFromXml implements JobInterface
{
     /**
     * @var \craft\base\Element
     */
    protected $element;

    /**
     * @var \craft\models\EntryDraft|\acclaro\translations\models\GlobalSetDraftModel
     */
    protected $draft;

    /**
     * @var string
     */
    protected $sourceSite;

    /**
     * @var string
     */
    protected $targetSite;

    /**
     * @var string
     */
    protected $xml;

    /**
     * @param \Craft\BaseElementModel                                               $element
     * @param \Craft\EntryDraftModel|\Craft\AcclaroTranslations_GlobalSetDraftModel $draft
     * @param string                                                                $xml
     * @param string                                                                $sourceSite
     * @param string                                                                $targetSite
     */
    public function __construct(
        Element $element,
        $draft,
        $xml,
        $sourceSite,
        $targetSite
    ) {
        $this->element = $element;

        $this->draft = $draft;

        $this->xml = $xml;

        $this->sourceSite = $sourceSite;

        $this->targetSite = $targetSite;
    }

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $targetData = Translations::$plugin->elementTranslator->getTargetDataFromXml($this->xml);

        if ($this->draft instanceof EntryDraft) {
            if (isset($targetData['title'])) {
                $this->draft->title = $targetData['title'];
            }

            if (isset($targetData['slug'])) {
                $this->draft->slug = $targetData['slug'];
            }
        }

        $post = Translations::$plugin->elementTranslator->toPostArrayFromTranslationTarget($this->element, $this->sourceSite, $this->targetSite, $targetData);

        try {
            $this->draft->setFieldValues($post);
        } catch(Exception $e) {
            Craft::dd($post);
        }

        $this->draft->siteId = $this->targetSite;

        // save the draft
        if ($this->draft instanceof EntryDraft) {
            Translations::$plugin->draftRepository->saveDraft($this->draft);
        } elseif ($this->draft instanceof GlobalSetDraftModel) {
            Translations::$plugin->globalSetDraftRepository->saveDraft($this->draft);
        }
    }
}