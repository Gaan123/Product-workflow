<?php

namespace App\Workflow\Transition;


final class TicketTransitions
{
    public const START_PROCESS = 'start_process';
    public const COMMENT = 'comment';
    public const SOLVE = 'solve';
    public const UNSOLVE = 'unsolve';
    public const CLOSE = 'close';
}
