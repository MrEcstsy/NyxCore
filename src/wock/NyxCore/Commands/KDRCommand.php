<?php

namespace wock\NyxCore\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use wock\NyxCore\Nyx;
use wock\NyxCore\Utils\Managers\KDRManager;

class KDRCommand extends Command
{

    public function __construct()
    {
        parent::__construct("kdr");
        $this->setDescription("Check your kill/death ratio");
        $this->setUsage("/kdr");
        $this->setPermission("Nyx.kdr");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            $kdr = KDRManager::getKdr($sender->getName());

            $kills = $kdr["kills"] ?? 0;
            $deaths = $kdr["deaths"] ?? 0;

            if ($deaths === 0) {
                $kdrRatio = $kills;
            } else {
                $kdrRatio = round($kills / $deaths, 2);
            }

            $sender->sendMessage("§r§l§cKDR §r§8| §r§7Your KDR stats: §fKills§7: " . $kills . ", §fDeaths§7: " . $deaths . ", §fKDR Ratio§7: $kdrRatio");
        } else {
            $sender->sendMessage("This Command can only be used in-game.");
        }
    }
}