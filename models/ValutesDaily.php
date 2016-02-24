<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "valutes_daily".
 *
 * @property integer $id
 * @property string $Vname
 * @property string $Vnom
 * @property string $Vcurs
 * @property string $Vcode
 * @property string $VchCode
 * @property string $KursDate
 * @property string $RequestDate
 */
class ValutesDaily extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'valutes_daily';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Vcurs'], 'number'],
            [['KursDate', 'RequestDate'], 'safe'],
            [['Vname', 'Vnom', 'Vcode', 'VchCode'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'Vname' => 'Vname',
            'Vnom' => 'Vnom',
            'Vcurs' => 'Vcurs',
            'Vcode' => 'Vcode',
            'VchCode' => 'Vch Code',
            'KursDate' => 'Kurs Date',
            'RequestDate' => 'Request Date',
        ];
    }
}
