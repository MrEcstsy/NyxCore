<?php

namespace wock\NyxCore\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as C;
use wock\NyxCore\Items\Rewards;
use wock\NyxCore\Nyx;
use wock\NyxCore\Utils\Utils;

class ScrollCommand extends Command
{

    /**
     * @var Nyx
     */
    public Nyx $plugin;

    /**
     * ItemsCommand constructor.
     * @param Nyx $plugin
     */
    public function __construct(Nyx $plugin)
    {
        $this->plugin = $plugin;
        parent::__construct("scroll");
        $this->setPermission("Nyx.scroll");
        $this->setUsage("/scroll <black/armor/white/weapon/transmog> <player> <percentage/tier>");
    }

    public function execute(CommandSender $sender, string $label, array $args)
    {
        if(!$this->testPermission($sender)) return;
        if (count($args) < 2) {
            $sender->sendMessage(C::RED . $this->getUsage());
            return;
        }
        if(isset($args[0])) {
            switch ($args[0]) {
                case "black":
                    $item = Rewards::getBlackScroll(isset($args[2]) ? (int) $args[2] : mt_rand(50, 100));
                    break;
                case "white":
                    $item = Rewards::getWhiteScroll();
                    break;
                case "armor":
                    $item = Rewards::getArmorOrb(isset($args[2]) ? (int) $args[2] : mt_rand(10, 15));
                    break;
                case "weapon":
                    $item = Rewards::getWeaponOrb(isset($args[2]) ? (int) $args[2] : mt_rand(10, 15));
                    break;
                case "transmog":
                    $item = Rewards::getTransmogScroll();
                    break;
                default:
                    $sender->sendMessage(C::RED . $this->getUsage());
                    return;
            }
        } else {
            $sender->sendMessage(C::RED . $this->getUsage());
            return;
        }
        if(isset($args[1])) {
            $player = Utils::customGetPlayerByPrefix($args[1]);
            if ($player === null) {
                $sender->sendMessage(C::RED . "Player is offline!");
                return;
            }
            $player->getInventory()->addItem($item);
        }
    }
}