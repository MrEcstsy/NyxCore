<?php

namespace wock\NyxCore\Items;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;

class Lootboxes {

    public static function createLootbox(string $tier, int $amount = 1): ?Item {
        $item = VanillaItems::AIR()->setCount($amount);
        switch ($tier) {
            case "player":
                $item = VanillaBlocks::BEACON()->asItem()->setCount($amount);
                $item->setCustomName("§r§f§k::: §r§8§lPlayer §fLootbox §r§7(Right Click) §f§k::: §r");
                $item->setLore([
                    "",
                    "§r§7Right-Click (in your hand) to receive",
                    "§r§7the rewards listed below, this lootbox",
                    "§r§7is given to every player when they",
                    "§r§7first connect to §l§aFANTASY§fCLOUD §r§7factions.",
                    "",
                    "§r§f§lContains:",
                    "§r§f§l * 1x ➥ §4Tag §fGenerator",
                    "§r§f§l * 1x ➥ §6Tracker §fRandomizer §r§7(Right Click)",
                    "§r§f§l * 5x ➥ §fDust §dRandomizer §r§7(Right Click)",
                    "§r§f§l * 3x ➥ §jBlackscroll §fGenerator §r§7(Right Click)",
                    "§r§f§l * 5x ➥ §bCrate Key §fGenerator §r§7(x1)",
                    "§r§f§l * 2x ➥ §3Enchantment Book §fGenerator §r§7(Right Click)",
                    "§r§f§l * 1x ➥ §aXP §fGenerator",
                    "§r§f§l * 1x ➥ §2Money §fGenerator",
                    "§r§f§l * 2x ➥ §6White Scroll §fOR §eTransmog §r§7(Right Click)",
                    "§r§f§l * 10x §r§fSimple Enchantment Book §r§7(Right Click)",
                    "§r§f§l * 6x §r§fUnique Enchantment Book §r§7(Right Click)",
                    "§r§f§l * 2x §r§bElite Enchantment BOok §r§7(Righ Click)",
                    "§r§f§l * 2x §aMystery Mob Spawner §r§7(Right Click)"
                ]);

                $item->getNamedTag()->setString("lootbox", "player");
                break;
        }
        return $item;
    }
}