<?php
///////////////////
//
//  


namespace IgBot\Sequence;

use \IgBot\Task\Task;

class SequenceAction 
{

    private Task $Task;
    private int $days_after_signup;

    public function __construct(Task $Task, int $days_after_signup)
    {

        $this->Task                 = $Task;  
        $this->days_after_signup    = $days_after_signup;
    }
}
