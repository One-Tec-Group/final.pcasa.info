<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * HourlyCommand
 * 
 * @package MailWizz EMA
 * @author Serban George Cristian <cristian.serban@mailwizz.com> 
 * @link https://www.mailwizz.com/
 * @copyright 2013-2018 MailWizz EMA (https://www.mailwizz.com)
 * @license https://www.mailwizz.com/license/
 * @since 1.3.7.5
 */
 
class HourlyCommand extends ConsoleCommand 
{
    /**
     * @return int
     */
    public function actionIndex()
    {
        // set the lock name
        $lockName = sha1(__METHOD__);
        
        if (!Yii::app()->mutex->acquire($lockName, 5)) {
            return 0;
        }

        $result = 0;
        
        try {

            Yii::app()->hooks->doAction('console_command_hourly_before_process', $this);

            $result = $this->process();

            Yii::app()->hooks->doAction('console_command_hourly_after_process', $this);
        
        } catch (Exception $e) {

            $this->stdout(__LINE__ . ': ' . $e->getMessage());
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
        }

        Yii::app()->mutex->release($lockName);
        
        return $result;
    }

    /**
     * @return int
     */
    public function process()
    {
        $this
            ->resetProcessingCampaigns()
            ->resetBounceServers()
            ->handleCampaignsMaxAllowedBounceAndComplaintRates();
        
        return 0;
    }

    /**
     * @return $this
     */
    protected function resetProcessingCampaigns()
    {
        try {
            $connection = Yii::app()->getDb();
            $connection->createCommand('UPDATE `{{campaign}}` SET `status` = "sending", last_updated = NOW() WHERE status = "processing" AND last_updated < DATE_SUB(NOW(), INTERVAL 7 HOUR)')->execute();
        } catch (Exception $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
        }
        return $this;
    }

    /**
     * @return $this
     */
    protected function resetBounceServers()
    {
        try {
            $connection = Yii::app()->getDb();
            $connection->createCommand('UPDATE `{{bounce_server}}` SET `status` = "active", last_updated = NOW() WHERE status = "cron-running" AND last_updated < DATE_SUB(NOW(), INTERVAL 7 HOUR)')->execute();
        } catch (Exception $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
        }
        return $this;
    }

	/**
	 * @since 1.6.1
	 * @return $this
	 */
    protected function handleCampaignsMaxAllowedBounceAndComplaintRates()
    {
    	try {

		    $options = Yii::app()->options;
		    
    		$criteria = new CDbCriteria();
    		$criteria->addInCondition('status', array(Campaign::STATUS_SENDING));
    		$campaigns = Campaign::model()->findAll($criteria);
    		
    		foreach ($campaigns as $campaign) {

			    $customer         = $campaign->customer;
			    $maxBounceRate    = (float)$customer->getGroupOption('campaigns.max_bounce_rate', (float)$options->get('system.cron.send_campaigns.max_bounce_rate', -1));
			    $maxComplaintRate = (float)$customer->getGroupOption('campaigns.max_complaint_rate', (float)$options->get('system.cron.send_campaigns.max_complaint_rate', -1));
			    
			    if ($maxBounceRate > -1) {
				    $bouncesRate = $campaign->getStats()->getBouncesRate() - $campaign->getStats()->getInternalBouncesRate();
				    if ((float)$bouncesRate > (float)$maxBounceRate) {
					    $campaign->block("Campaign bounce rate is higher than allowed!");
					    continue;
				    }
			    }
			    
			    if ($maxComplaintRate > -1 && (float)$campaign->getStats()->getComplaintsRate() > (float)$maxComplaintRate) {
				    $campaign->block("Campaign complaint rate is higher than allowed!");
				    continue;
			    }
		    }
    		
	    } catch (Exception $e) {
    		
	    }
	    
	    return $this;
    }
}
