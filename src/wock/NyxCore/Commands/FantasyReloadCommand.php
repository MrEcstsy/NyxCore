<?php

namespace wock\NyxCore\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;
use pocketmine\utils\TextFormat;
use wock\NyxCore\Nyx;

class FantasyReloadCommand extends Command implements PluginOwned {

    /** @var Nyx */
    private Nyx $plugin;

    public function __construct(Nyx $plugin) {
        parent::__construct("nyxreload", "Reload the NyxCore configuration", "/NyxCore", ["fc"]);
        $this->setPermission("Nyx.reload");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {
        if (!$sender->hasPermission("NyxCore.reload")) {
            $sender->sendMessage(TextFormat::RED . "You do not have sufficient permission to use this command!");
            return false;
        }

        $this->plugin->reloadConfig();
        $sender->sendMessage(TextFormat::GREEN . "Successfully reloaded configuration.");
        return true;
    }

    public function getOwningPlugin(): Nyx
    {
        return $this->plugin;
    }
}

