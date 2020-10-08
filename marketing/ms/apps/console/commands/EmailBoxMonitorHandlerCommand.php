<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * EmailBoxMonitorHandlerCommand
 * 
 * @package MailWizz EMA
 * @author Serban George Cristian <cristian.serban@mailwizz.com> 
 * @link https://www.mailwizz.com/
 * @copyright 2013-2018 MailWizz EMA (https://www.mailwizz.com)
 * @license https://www.mailwizz.com/license/
 * @since 1.4.5
 */
 
class EmailBoxMonitorHandlerCommand extends ConsoleCommand 
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // if more than 6 hours then something is def. wrong?
        ini_set('max_execution_time', 6 * 3600);
        set_time_limit(6 * 3600);

        if ($memoryLimit = Yii::app()->options->get('system.cron.process_email_box_monitors.memory_limit')) {
            ini_set('memory_limit', $memoryLimit);
        }

        Yii::import('common.vendors.BounceHandler.*');

        // 1.5.3
        Yii::app()->mutex->shutdownCleanup = false;
    }

    /**
     * @return int
     */
    public function actionIndex() 
    {
        $this->stdout('Start processing...');
        
        // because some cli are not compiled same way with the web module.
        if (!CommonHelper::functionExists('imap_open')) {
            $message = Yii::t('servers', 'The PHP CLI binary is missing the IMAP extension!');
            Yii::log($message, CLogger::LEVEL_ERROR);
            $this->stdout($message);
            return 1;
        }

        // set the lock name
        $lockName = sha1(__METHOD__);
        
        $this->stdout('Acquiring lock...');
        if (!Yii::app()->mutex->acquire($lockName, 5)) {
            $this->stdout('Failed to acquire lock!');
            return 0;
        }

        try {

            // since 1.5.0 
            EmailBoxMonitor::model()->updateAll(array(
                'status' => EmailBoxMonitor::STATUS_ACTIVE,
            ), 'status = :st', array(
                ':st' => EmailBoxMonitor::STATUS_CRON_RUNNING,
            ));
            //
            
            // added in 1.3.4.7
            Yii::app()->hooks->doAction('console_command_email_box_monitor_handler_before_process', $this);

            if ($this->getCanUsePcntl()) {
                $this->stdout('Processing with PCNTL!');
                $this->processWithPcntl();
            } else {
                $this->stdout('Processing without PCNTL!');
                $this->processWithoutPcntl();
            }

            // added in 1.3.4.7
            Yii::app()->hooks->doAction('console_command_email_box_monitor_handler_after_process', $this);

            $this->stdout('Releasing the lock!');
            
        } catch (Exception $e) {

            $this->stdout(__LINE__ . ': ' .  $e->getMessage());
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
        }
        
        Yii::app()->mutex->release($lockName);
        
        return 0;
    }

    /**
     * @return $this
     * @throws CException
     */
    protected function processWithPcntl()
    {
        // get all servers
        $servers = EmailBoxMonitor::model()->findAll(array(
            'condition' => 't.status = :status',
            'params'    => array(':status' => EmailBoxMonitor::STATUS_ACTIVE),
        ));

        // close the external connections
        $this->setExternalConnectionsActive(false);

        // split into x server chuncks
        $chunkSize    = (int)Yii::app()->options->get('system.cron.process_email_box_monitors.pcntl_processes', 10);
        $serverChunks = array_chunk($servers, $chunkSize);
        unset($servers);

        foreach ($serverChunks as $servers) {

            $childs = array();

            foreach ($servers as $server) {
                $pid = pcntl_fork();
                if($pid == -1) {
                    continue;
                }

                // Parent
                if ($pid) {
                    $childs[] = $pid;
                }

                // child 
                if (!$pid) {
                    try {
                        
                        $server->processRemoteContents(array(
                            'logger' => $this->verbose ? array($this, 'stdout') : null,
                        ));
                        
                    } catch (Exception $e) {
                        
                        $this->stdout(__LINE__ . ': ' .  $e->getMessage());
                        Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
                    }
                    Yii::app()->end();
                }
            }

            while (count($childs) > 0) {
                foreach ($childs as $key => $pid) {
                    $res = pcntl_waitpid($pid, $status, WNOHANG);
                    if($res == -1 || $res > 0) {
                        unset($childs[$key]);
                    }
                }
                sleep(1);
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function processWithoutPcntl()
    {
        // get all servers
        $servers = EmailBoxMonitor::model()->findAll(array(
            'condition' => 't.status = :status',
            'params'    => array(':status' => EmailBoxMonitor::STATUS_ACTIVE),
        ));

        foreach ($servers as $server) {
            try {
                
                $server->processRemoteContents(array(
                    'logger' => $this->verbose ? array($this, 'stdout') : null,
                ));
                
            } catch (Exception $e) {
                
                $this->stdout(__LINE__ . ': ' .  $e->getMessage());
                Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
            }
        }

        return $this;
    }

    /**
     * @return bool
     */
    protected function getCanUsePcntl()
    {
        static $canUsePcntl;
        if ($canUsePcntl !== null) {
            return $canUsePcntl;
        }
        if (Yii::app()->options->get('system.cron.process_email_box_monitors.use_pcntl', 'yes') != 'yes') {
            return $canUsePcntl = false;
        }
        if (!CommonHelper::functionExists('pcntl_fork') || !CommonHelper::functionExists('pcntl_waitpid')) {
            return $canUsePcntl = false;
        }
        return $canUsePcntl = true;
    }
}