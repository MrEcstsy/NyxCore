<?php

namespace wock\NyxCore\Tasks;

use pocketmine\scheduler\Task;
use wock\NyxCore\Utils\Managers\JackpotManager;

class JackpotRoundTask extends Task {

    private JackpotManager $jackpotManager;

    public function __construct(JackpotManager $jackpotManager) {
        $this->jackpotManager = $jackpotManager;
    }

    public function onRun(): void {
        $this->jackpotManager->startJackpotRoundIfTimeElapsed();
    }
}
