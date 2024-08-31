<?php

namespace wock\NyxCore\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\StringTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use Ramsey\Collection\Set;
use wock\NyxCore\Items\Masks;

class RemoveMaskCommand extends Command {

    public function __construct() {
        parent::__construct("removemask", "Remove mask from a diamond helmet", "/removemask", ["rm"]);
        $this->setPermission("Nyx.removemask");
    }

    /**
     * @throws \Exception
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool
    {
        if (!$sender instanceof Player) {
            return false; // Command can only be used by players
        }

        if (!$this->testPermission($sender)) {
            $sender->sendMessage("You don't have permission to use this command.");
            return false;
        }

        $item = $sender->getInventory()->getItemInHand();
        $ids = [VanillaItems::LEATHER_CAP()->getTypeId(), VanillaItems::IRON_HELMET()->getTypeId(), VanillaItems::CHAINMAIL_HELMET()->getTypeId(), VanillaItems::GOLDEN_HELMET()->getTypeId(), VanillaItems::DIAMOND_HELMET()->getTypeId()];
        if (!in_array($item->getTypeId(), $ids, true) || $item->getNamedTag()->getString("mask", "") === "") {
            $sender->sendMessage(TextFormat::RED . "Please hold a helmet with a mask applied to it!");
            return false;
        }

        $mask = $item->getNamedTag()->getString("mask");
        if ($mask === null) {
            $sender->sendMessage(TextFormat::RED . "Undefined Mask!");
            return false;
        }
        $lore = $item->getLore();
        if ($mask === "cheetah") {
            if (isset($lore[array_search("§r§7§lATTACHED: §eCheetah Mask", $lore)])) {
                unset($lore[array_search("§r§7§lATTACHED: §eCheetah Mask", $lore)]);
                $sender->getInventory()->addItem(Masks::get(Masks::CHEETAHMASK));
            }
        } elseif ($mask === "purge") {
            if (isset($lore[array_search("§r§7§lATTACHED: §cPurge Mask", $lore)])) {
                unset($lore[array_search("§r§7§lATTACHED: §cPurge Mask", $lore)]);
                $sender->getInventory()->addItem(Masks::get(Masks::PURGEMASK));

            }
        } elseif ($mask === "party") {
            if (isset($lore[array_search("§r§7§lATTACHED: §fParty Mask", $lore)])) {
                unset($lore[array_search("§r§7§lATTACHED: §fParty Mask", $lore)]);
                $sender->getInventory()->addItem(Masks::get(Masks::PARTYMASK));
            }
        } elseif ($mask === "buff") {
            if (isset($lore[array_search("§r§7§lATTACHED: §9Buff Mask", $lore)])) {
                unset($lore[array_search("§r§7§lATTACHED: §9Buff Mask", $lore)]);
                $sender->getInventory()->addItem(Masks::get(Masks::BUFFMASK));
            }
        }

        $item->getNamedTag()->removeTag("mask");
        $item->setLore($lore);
        $sender->getInventory()->setItemInHand($item);
        $sender->sendMessage(TextFormat::GREEN . "Mask successfully unequipped!");
        return true;
    }

    private function getMaskItem(string $maskValue): ?string {
        $maskItemClasses = [
            "cheetah" => Masks::CHEETAHMASK,
            "purge" => Masks::PURGEMASK,
            "party" => Masks::PARTYMASK,
            "buff" => Masks::BUFFMASK,
            "dragon" => Masks::DRAGONMASK
        ];

        return $maskItemClasses[$maskValue] ?? null;
    }
}
