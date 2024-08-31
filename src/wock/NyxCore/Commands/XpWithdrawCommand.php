<?php

namespace wock\NyxCore\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use wock\NyxCore\Items\Rewards;

class XpWithdrawCommand extends Command
{

    public function __construct() {
        parent::__construct("xpbottle", "turns your exp into a physical form for trading or selling");
        $this->setAliases(["xpb"]);
        $this->setPermission("Nyx.xpbottle");
    }

    /** @var array $cooldown */
    public static array $cooldown = [];

    public function execute(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            return;
        }

        if (!isset($args[0])) {
            $sender->sendMessage("§r§c/xpbottle [amount: int|all]");
            return;
        }

        if (strtolower($args[0]) === "all") {
            $xpAmount = $sender->getXpManager()->getCurrentTotalXp();
        } else {
            $xpAmount = (int) $args[0];
        }

        if ($xpAmount < 1) {
            $sender->sendMessage("§r§camount must be > 0 got '" . $args[0] . "'");
            return;
        }

        if (isset(self::$cooldown[$sender->getName()]) && microtime(true) - self::$cooldown[$sender->getName()] <= 90) {
            $delayMessage = round(90 - abs(self::$cooldown[$sender->getName()] - microtime(true)), 2);
            $sender->sendMessage("§r§c§l(!) §r§cYou cannot create another XP Bottle for {$delayMessage} seconds(s).");
            $sender->sendMessage("§r§7Complete a Rank Quest from www.aaa.com to decrease this delay.");
            return;
        }

        if ($xpAmount > $sender->getXpManager()->getCurrentTotalXp()) {
            $sender->sendMessage("§r§c§l(!) §r§cYou don't have the sufficient xp!");
            return;
        }

        $sender->sendMessage("§r§c§l-{$xpAmount} xp");
        $note = Rewards::createXPBottle(null, (float) $xpAmount);
        $sender->getInventory()->addItem($note);

        if (strtolower($args[0]) === "all") {
            $sender->getXpManager()->subtractXp($xpAmount); // Remove all XP
        }

        self::$cooldown[$sender->getName()] = microtime(true);
        $sender->sendMessage("§r§eYou are now afflicted with EXP Exhaustion for 90 seconds(s).");
    }

}