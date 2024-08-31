<?php

namespace wock\NyxCore\Tasks;

use pocketmine\scheduler\Task;
use wock\NyxCore\Nyx;

class AnnounceTask extends Task {

    /** @var Nyx */
    private Nyx $plugin;

    public function __construct(Nyx $plugin) {
        $this->plugin = $plugin;
    }

    public function onRun(): void {
        $this->plugin->broadcastNextMessage();
    }
}