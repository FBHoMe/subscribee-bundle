<?php 

/**
 * config file
 * 
 * Contao 3.2.4 to ?
 * 
 * @package    subscribee
 * @copyright  HOME - HolsteinMedia
 * @author     Dirk Holstein <dh@holsteinmedia.com>
 *
 */


#-- add subscribee to calendar backend module
#$GLOBALS['BE_MOD']['content']['calendar']['tables'][] = array("tl_subscribee","tl_subscribee_data");
$GLOBALS['BE_MOD']['content']['calendar']['tables'][] = 'tl_subscribee';
$GLOBALS['BE_MOD']['content']['calendar']['tables'][] = 'tl_subscribee_data';

$GLOBALS['BE_MOD']['content']['calendar']['export'] = array('Home\SubscribeeBundle\Resources\contao\Subscribee', 'exportList');

$GLOBALS['subscribee']['export'] = array('Home\SubscribeeBundle\Resources\contao\Subscribee', 'exportList');

/*array_insert($GLOBALS['TL_FFL'], 0, array
(
    'subscribee' => 'Home\SubscribeeBundle\Resources\contao\forms\FormEnableSubscribee'
));*/

/**
 * Front end content elements
 */
array_insert($GLOBALS['TL_CTE'], 2, array
(
    'events' => array
    (
        'event_sign_in_detail_cte' => 'Home\SubscribeeBundle\Resources\contao\elements\SignInDetailElement',
        'event_sign_out_detail_cte' => 'Home\SubscribeeBundle\Resources\contao\elements\SignOutDetailElement',
        'event_personal_cte' => 'Home\SubscribeeBundle\Resources\contao\elements\PersonalSubscribedEventsElement',
    ),
));

/**
 * Front end modules
 */
$GLOBALS['FE_MOD']['events']['subscribee_eventReaderWithSubscription']  = 'Home\SubscribeeBundle\Resources\contao\modules\EventReaderWithSubscription';
#$GLOBALS['FE_MOD']['events']['subscribee_eventListWithSubscription']    = 'Home\SubscribeeBundle\Resources\contao\modules\EventListWithSubscription';

/**
 * models
 */
$GLOBALS['TL_MODELS']['tl_subscribee']  = 'Home\SubscribeeBundle\Resources\contao\models\Subscribee';
/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['compileFormFields'][] = array('Home\SubscribeeBundle\Resources\contao\hooks\CompileFormFields', 'preFillForm');
$GLOBALS['TL_HOOKS']['processFormData'][] = array('Home\SubscribeeBundle\Resources\contao\hooks\ProcessFormData', 'doBeforeSendEMail'); // Hook, der vor dem Versenden eines Kontaktformulars ausgeführt wird.
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('Home\SubscribeeBundle\Resources\contao\hooks\ReplaceInsertTags', 'subscribeeInsertTags');
