<?php

namespace wock\NyxCore\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as C;
use wock\NyxCore\Items\Rewards;
use wock\NyxCore\Nyx;
use wock\NyxCore\Utils\Utils;

class DustCommand extends Command
{
    /**
     * @var Nyx
     */
    protected Nyx $plugin;

    /**
     * ItemsCommand constructor.
     * @param Nyx $plugin
     */
    public function __construct(Nyx $plugin)
    {
        $this->plugin = $plugin;
        parent::__construct("dust");
        $this->setPermission("Nyx.dust");
        $this->setUsage("/dust <percentage> <player>");
    }

    public function execute(CommandSender $sender, string $label, array $args)
    {
        if(!$this->testPermission($sender)) return;
        if (count($args) !== 2) {
            $sender->sendMessage(C::RED . $this->getUsage());
            return;
        }
        if (intval($args[0]) > 100) {
            $sender->sendMessage(C::RED . $this->getUsage());
            return;
        }
        $player = Utils::customGetPlayerByPrefix($args[1]);
        if ($player === null) {
            $sender->sendMessage(C::RED . "Player is offline!");
            return;
        }
        $percentage = (int)$args[0];
        $dust = Rewards::getEnchantDust($percentage);
        $player->getInventory()->addItem($dust);
    }
}