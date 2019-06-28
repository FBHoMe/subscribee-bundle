<?php

/**
 * Subscribee - the main class
 *
 * @package    subscribee
 * @copyright  HOME - HolsteinMedia
 * @author     Dirk Holstein <dh@holsteinmedia.com>
 */

namespace Home\SubscribeeBundle\Resources\contao;
use Home\SubscribeeBundle\Resources\contao\models as Models;

class Subscribee extends \Frontend
{
    public function exportList()
	{
		if( $this->Input->get('key') === 'export' ) {
			$table = $this->Input->get('table');
			$id = $this->Input->get('id');

			$sql = '
				SELECT *
				FROM tl_calendar_events
				WHERE id = ' . $id . '
			';

			$objResult = \Database::getInstance()
				->prepare($sql)
				->execute()
			;
			$event = $objResult->fetchAllAssoc()[0];

			$sql = '
				SELECT *
				FROM ' . $table . '
				WHERE pid = ' . $id . '
			';
			$objResult = \Database::getInstance()
				->prepare($sql)
				->execute()
			;
			$results = $objResult->fetchAllAssoc();
			$filePath = $_SERVER['DOCUMENT_ROOT']."/files/export.csv";
			$file = fopen($filePath, 'w');
			$header = array(
				'Zeit',
				'Name',
				'Status',
				'Anreise',
				'Abreise',
				'Vegetarisch',
                'Nachricht'
			);
			fputcsv($file, array(utf8_decode($event['title'])));
			fputcsv($file, $header);
			foreach($results as $result){
				$data = unserialize($result['subscribe_data']);
				fputcsv($file, array(
					date("d.m.Y H:i:s",$result['tstamp']),
					utf8_decode($data['name']),
					utf8_decode($data['status'] == 'signOut' ? 'Abgemeldet' : 'Angemeldet'),
					utf8_decode($data['zimmer']),
					utf8_decode($data['anreise']),
                    utf8_decode($data['abreise']),
                    utf8_decode($data['vegetarisch']),
                    utf8_decode($data['nachricht'])
                ));
			}
			fclose($file);

			$path = $_SERVER['DOCUMENT_ROOT']."/files/export.csv";
			header('Content-Description: File Transfer');
			header('Content-Type: Document');
			header('Content-Disposition: attachment; filename=' . $event['alias'] . '.csv');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($path));
			ob_clean();
			flush();
			readfile($path);
			exit;

		}
	}
}
