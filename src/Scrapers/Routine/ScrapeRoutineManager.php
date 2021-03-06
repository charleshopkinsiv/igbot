<?php

namespace IgBot\Scrapers\Routine;

use \IgBot\Task\TaskManager;
use \IgBot\Queue\QueueManager;
use \CharlesHopkinsIV\Core\Registry;

class ScrapeRoutineManager
{

    private $mapper;

    public static $TYPES = [
        "User Followers"    => "\\igbot\\scrapers\\UserFollowersScraper",
        "Location"          => "\\igbot\\scrapers\\LocationScraper",
        "Location2L"        => "\\igbot\\scrapers\\LocationScraperTwoL"
    ];

    public static $FREQUENCIES = [
        'Daily' => 0,
        'Weekly' => 7,
        'Thirty Days' => 30
    ];


    public function __construct()
    {

        $this->mapper = new ScrapeRoutineMapper();
    }


    public static function getTypes()
    {

        return self::$TYPES;
    }


    public static function getFrequencies()
    {

        return self::$FREQUENCIES;
    }


    public function getAllScrapeRoutines()
    {

        return $this->mapper->fetchAll();
    }


    public function editRoutineHttp()
    {

        $REQ_KEYS = ['id', 'account', 'type', 'details', 'frequency', 'sequence', 'status'];

        $DATA = [];
        
        foreach($REQ_KEYS as $key) {

            if(!isset($_POST[$key]))
                throw new \Exception("Missing required input '" . $key . "'. . . ");

            $DATA[$key] = $_POST[$key];
        }


        if(empty($DATA['id'])) {
                
            $this->mapper->insert(new ScrapeRoutine(
                $DATA['id'],
                $DATA['account'],
                $DATA['type'],
                $DATA['details'],
                $DATA['frequency'],
                $DATA['sequence'],
                $DATA['status']
            ));
        }
        else {

            $this->mapper->update(new ScrapeRoutine(
                $DATA['id'],
                $DATA['account'],
                $DATA['type'],
                $DATA['details'],
                $DATA['frequency'],
                $DATA['sequence'],
                $DATA['status']
            ));
        } 
    }


    public function deleteRoutineHttp()
    {
        
        if(!empty(\core\Registry::instance()->getRequest()->getProperty("patharg")[0]))
            $this->mapper->deleteById(\core\Registry::instance()->getRequest()->getProperty("patharg")[0]);
    }


    /**
     * Populate Queue
     * Will load all of the routines, and add them for the account if it hasn't been sent yet
     * 
     */
    public function populateQueue(QueueManager $Queue_Manager) 
    {

        // Load all routines that are due
        $ROUTINES = $this->mapper->fetchAll();

        // Add the scrapes/tasks from the routines to the queue
        foreach($ROUTINES as $Routine) {

            // If already or inactive added skip
            if($Queue_Manager->alreadyAdded($Routine->getTask())
            || TaskManager::taskOnLog($Routine->getTask(), self::$FREQUENCIES[$Routine->getFrequency()])
            || $Routine->getStatus() == "Inactive")
                continue;

            $Queue_Manager->addTask($Routine->getTask());
            if(!empty(Registry::getDebug())) printf("%-'.32s\032[32mAdding %s to %s's queue\033[39m\n", date("Y-m-d H:i:s"), get_class($Routine->getTask()), $Routine->getTask()->getAccount()->getUsername());
        }
    }
}