<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "valutes".
 *
 * @property integer $id
 * @property string $Vcode
 * @property string $Vname
 * @property string $VEngname
 * @property integer $Vnom
 * @property string $VcommonCode
 * @property integer $VnumCode
 * @property string $VcharCode
 */
class Valutes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'valutes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Vnom', 'VnumCode'], 'integer'],
            [['Vcode', 'Vname', 'VEngname', 'VcommonCode', 'VcharCode'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'Vcode' => 'Vcode',
            'Vname' => 'Vname',
            'VEngname' => 'Vengname',
            'Vnom' => 'Vnom',
            'VcommonCode' => 'Vcommon Code',
            'VnumCode' => 'Vnum Code',
            'VcharCode' => 'Vchar Code',
        ];
    }
}
