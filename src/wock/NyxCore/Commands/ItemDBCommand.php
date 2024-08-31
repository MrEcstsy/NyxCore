<?php

declare(strict_types=1);

namespace wock\NyxCore\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\Durable;
use pocketmine\player\Player;
use pocketmine\plugin\PluginOwned;
use pocketmine\utils\TextFormat;
use wock\NyxCore\Nyx;

class ItemDBCommand extends Command implements PluginOwned
{
    /** @var Nyx */
    public Nyx $plugin;

    public function __construct()
    {
        parent::__construct("itemdb", "View the item information in hand", "/itemdb");
        $this->setPermission("Nyx.itemdb");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "This command can only be run by a player.");
            return false;
        }

        $hand = $sender->getInventory()->getItemInHand();
        $sender->sendMessage(TextFormat::GOLD . "Item: " . TextFormat::RED . $hand->getVanillaName());
        $sender->sendMessage(TextFormat::GOLD . "ID: " . TextFormat::RED . $hand->getTypeId());
        if ($hand instanceof Durable) {
            $maxDurability = $hand->getMaxDurability();
            $currentDurability = $hand->getDamage();
            $usesLeft = $maxDurability - $currentDurability;
            $sender->sendMessage(TextFormat::GOLD . "This tool has " . TextFormat::RED . $usesLeft . TextFormat::GOLD . " uses left.");
        }
        return true;
    }

    public function getOwningPlugin(): Nyx
    {
        return $this->plugin;
    }
}