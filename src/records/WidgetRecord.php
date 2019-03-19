<?php
/**
 * Translations for Craft plugin for Craft CMS 3.x
 *
 * Translations for Craft eliminates error prone and costly copy/paste workflows for launching human translated Craft CMS web content.
 *
 * @link      http://www.acclaro.com/
 * @copyright Copyright (c) 2018 Acclaro
 */

namespace acclaro\translations\records;

use craft\db\Table;
use craft\db\ActiveRecord;
use yii\db\ActiveQueryInterface;
use acclaro\translations\Translations;

/**
 * @author    Acclaro
 * @package   Translations
 * @since     1.0.0
 */
class WidgetRecord extends ActiveRecord
{
    // Public Methods
    // =========================================================================
    /**
     * @inheritdoc
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%translations_widgets}}';
    }
    
    /**
     * Returns the widget’s user.
     *
     * @return ActiveQueryInterface The relational query object.
     */
    public function getUser(): ActiveQueryInterface
    {
        return $this->hasOne(User::class, ['id' => 'userId']);
    }
}