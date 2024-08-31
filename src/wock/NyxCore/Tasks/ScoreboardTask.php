<?php

# Namespace
namespace wock\NyxCore\Tasks;

# Pocketmine API
use pocketmine\scheduler\Task;
use wock\NyxCore\Nyx;
use wock\NyxCore\Utils\ScoreboardManager;

# Task Class
class ScoreboardTask extends Task {

    public Nyx $plugin;

    private ScoreboardManager $ScoreboardManager;

    # Task Constructor
    public function __construct(Nyx $plugin) {
        $this->plugin = $plugin;
        $this->ScoreboardManager = new ScoreboardManager($plugin);
    }

    # Task Execution
    public function onRun(): void {
        $this->ScoreboardManager->scoreboard();
    }
}