<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\Valutes;
use app\models\ValutesDaily;
use yii\console\Controller;

class CbrController extends Controller
{
    /**
     * Команда для первоначальной загрузки данных
     */
    public function actionCreateCurrency()
    {
        Valutes::deleteAll();
        $client = new \SoapClient('http://www.cbr.ru/DailyInfoWebServ/DailyInfo.asmx?WSDL');
        $res = $client->EnumValutesXML(['Seld' => false]);
        if (empty($res->EnumValutesXMLResult->any))
            return;
        $xml = simplexml_load_string($res->EnumValutesXMLResult->any);
        $builder = \Yii::$app->db->queryBuilder;
        $params = [];
        foreach ($xml as $valutes) {
			
			$params[] = [
                trim((string)$valutes->Vcode),
				trim((string)$valutes->Vname),
                trim((string)$valutes->VEngname),
                trim((string)$valutes->Vnom),
                trim((string)$valutes->VcommonCode),
                trim((string)$valutes->VnumCode),
                trim((string)$valutes->VcharCode)
            ];
        }
        $sql = $builder->batchInsert('valutes',
            ['Vcode', 'Vname', 'VEngname', 'Vnom', 'VcommonCode', 'VnumCode', 'VcharCode'],
            $params);
        $res = \Yii::$app->db->createCommand($sql)->execute();
    }

    public function actionGetKurs($fromnow = '2 weeks'){
        $client = new \SoapClient('http://www.cbr.ru/DailyInfoWebServ/DailyInfo.asmx?WSDL');
        $now = $client->GetLatestDateTime();
        $now = date_create_from_format('!dmY', date('dmY', strtotime($now->GetLatestDateTimeResult)));
        $from = date_create_from_format('!dmY', date('dmY', strtotime($fromnow . ' ago')));
        while($now >= $from){
            $this->GetKursOnDate($from->format('Y-m-d'), $client);
            $from->modify('+1 day');
        }
    }

    public function actiongetKursOnDate($date = 'now'){
        $client = new \SoapClient('http://www.cbr.ru/DailyInfoWebServ/DailyInfo.asmx?WSDL');
        $this->getKursOnDate($date, $client);
    }

    private function getKursOnDate($date='now', &$client){
        $date = date('Y-m-d', strtotime($date));
        $params = ['On_date' => $date];
        $res = $client->GetCursOnDateXML($params);
        if(empty($res->GetCursOnDateXMLResult->any))
            return;
        $xml = simplexml_load_string($res->GetCursOnDateXMLResult->any);
        $d = $xml->attributes()['OnDate'] ?: $date;
        $d = date('Y-m-d', strtotime($d));
        foreach($xml as $valutes) {
            $vd = new ValutesDaily();
            $vd->KursDate = $d;
            $vd->RequestDate = $date;
            $vd->Vname = trim((string)$valutes->Vname);
            $vd->Vnom = trim((string)$valutes->Vnom);
            $vd->Vcurs = trim((string)$valutes->Vcurs);
            $vd->Vcode = trim((string)$valutes->Vcode);
            $vd->VchCode = trim((string)$valutes->VchCode);
            $vd->save();
        }
    }
}
