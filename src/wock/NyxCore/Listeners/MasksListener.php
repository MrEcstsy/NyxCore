<?php

namespace wock\NyxCore\Listeners;

use pocketmine\block\utils\MobHeadType;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\entity\EntityEffectAddEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\inventory\ArmorInventory;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use pocketmine\item\Item;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\MobArmorEquipmentPacket;
use pocketmine\player\Player;
use pocketmine\world\sound\AnvilFallSound;
use pocketmine\world\sound\XpLevelUpSound;
use wock\NyxCore\Utils\Utils;

class MasksListener implements Listener {

    /**
     * @priority HIGHEST
     */
    public function onPlayerDropCheetahMask(InventoryTransactionEvent $event): void
    {
        $transaction = $event->getTransaction();
        $actions = array_values($transaction->getActions());
        if (count($actions) === 2) {
            foreach ($actions as $i => $action) {
                $items = [ItemTypeIds::DIAMOND_HELMET, ItemTypeIds::NETHERITE_HELMET];
                if ($action instanceof SlotChangeAction && ($otherAction = $actions[($i + 1) % 2]) instanceof SlotChangeAction && ($itemClickedWith = $action->getTargetItem())->getTypeId() === VanillaBlocks::MOB_HEAD()->setMobHeadType(MobHeadType::WITHER_SKELETON())->asItem()->getTypeId() && ($itemClicked = $action->getSourceItem())->getTypeId() !== VanillaItems::AIR()->getTypeId() && in_array($itemClicked->getTypeId(), $items) && $itemClickedWith->getCount() === 1 && $itemClickedWith->getNamedTag()->getString("mask", "") === "cheetah") {
                    if ($itemClicked->getNamedTag()->getTag("mask")) {
                        $event->getTransaction()->getSource()->sendMessage("§r§c§l(!) §r§cYou cannot do that!");
                        $transaction->getSource()->getWorld()->addSound($transaction->getSource()->getLocation(), new AnvilFallSound());
                        return;
                    }
                    $event->cancel();
                    $lore = "§r§7§lATTACHED: §eCheetah Mask";
                    $itemClicked->setLore([$lore]);
                    $itemClicked->getNamedTag()->setString("mask", "cheetah");
                    Utils::spawnParticleV2($event->getTransaction()->getSource(), "minecraft:villager_happy");
                    $action->getInventory()->setItem($action->getSlot(), $itemClicked);
                    $otherAction->getInventory()->setItem($otherAction->getSlot(), VanillaItems::AIR());
                    $transaction->getSource()->getWorld()->addSound($transaction->getSource()->getLocation(), new XpLevelUpSound(100));
                    return;
                }
            }
        }
    }

    /**
     * @priority HIGHEST
     */
    public function onPlayerDropPurgeMask(InventoryTransactionEvent $event): void
    {
        $transaction = $event->getTransaction();
        $actions = array_values($transaction->getActions());
        if (count($actions) === 2) {
            foreach ($actions as $i => $action) {
                $items = [ItemTypeIds::DIAMOND_HELMET, ItemTypeIds::NETHERITE_HELMET];
                if ($action instanceof SlotChangeAction && ($otherAction = $actions[($i + 1) % 2]) instanceof SlotChangeAction && ($itemClickedWith = $action->getTargetItem())->getTypeId() === VanillaBlocks::MOB_HEAD()->setMobHeadType(MobHeadType::WITHER_SKELETON())->asItem()->getTypeId() && ($itemClicked = $action->getSourceItem())->getTypeId() !== VanillaItems::AIR()->getTypeId() && in_array($itemClicked->getTypeId(), $items) && $itemClickedWith->getCount() === 1 && $itemClickedWith->getNamedTag()->getString("mask", "") === "purge") {
                    if ($itemClicked->getNamedTag()->getTag("mask")) {
                        $event->getTransaction()->getSource()->sendMessage("§r§c§l(!) §r§cYou cannot do that!");
                        $transaction->getSource()->getWorld()->addSound($transaction->getSource()->getLocation(), new AnvilFallSound());
                        return;
                    }
                    $event->cancel();
                    $lore = "§r§7§lATTACHED: §cPurge Mask";
                    $itemClicked->setLore([$lore]);
                    $itemClicked->getNamedTag()->setString("mask", "purge");
                    Utils::spawnParticleV2($event->getTransaction()->getSource(), "minecraft:villager_happy");
                    $action->getInventory()->setItem($action->getSlot(), $itemClicked);
                    $otherAction->getInventory()->setItem($otherAction->getSlot(), VanillaItems::AIR());
                    $transaction->getSource()->getWorld()->addSound($transaction->getSource()->getLocation(), new XpLevelUpSound(100));
                    return;
                }
            }
        }
    }

    /**
     * @priority HIGHEST
     */
    public function onPlayerDropPartyMask(InventoryTransactionEvent $event): void
    {
        $transaction = $event->getTransaction();
        $actions = array_values($transaction->getActions());
        if (count($actions) === 2) {
            foreach ($actions as $i => $action) {
                $items = [ItemTypeIds::DIAMOND_HELMET, ItemTypeIds::NETHERITE_HELMET];
                if ($action instanceof SlotChangeAction && ($otherAction = $actions[($i + 1) % 2]) instanceof SlotChangeAction && ($itemClickedWith = $action->getTargetItem())->getTypeId() === VanillaBlocks::MOB_HEAD()->setMobHeadType(MobHeadType::WITHER_SKELETON())->asItem()->getTypeId() && ($itemClicked = $action->getSourceItem())->getTypeId() !== VanillaItems::AIR()->getTypeId() && in_array($itemClicked->getTypeId(), $items) && $itemClickedWith->getCount() === 1 && $itemClickedWith->getNamedTag()->getString("mask", "") === "party") {
                    if ($itemClicked->getNamedTag()->getTag("mask")) {
                        $event->getTransaction()->getSource()->sendMessage("§r§c§l(!) §r§cYou cannot do that!");
                        $transaction->getSource()->getWorld()->addSound($transaction->getSource()->getLocation(), new AnvilFallSound());
                        return;
                    }
                    $event->cancel();
                    $lore = "§r§7§lATTACHED: §fParty Mask";
                    $itemClicked->setLore([$lore]);
                    $itemClicked->getNamedTag()->setString("mask", "party");
                    Utils::spawnParticleV2($event->getTransaction()->getSource(), "minecraft:villager_happy");
                    $action->getInventory()->setItem($action->getSlot(), $itemClicked);
                    $otherAction->getInventory()->setItem($otherAction->getSlot(), VanillaItems::AIR());
                    $transaction->getSource()->getWorld()->addSound($transaction->getSource()->getLocation(), new XpLevelUpSound(100));
                    return;
                }
            }
        }
    }

    /**
     * @priority HIGHEST
     */
    public function onPlayerDropBuffMask(InventoryTransactionEvent $event): void
    {
        $transaction = $event->getTransaction();
        $actions = array_values($transaction->getActions());
        if (count($actions) === 2) {
            foreach ($actions as $i => $action) {
                $items = [ItemTypeIds::DIAMOND_HELMET, ItemTypeIds::NETHERITE_HELMET];
                if ($action instanceof SlotChangeAction && ($otherAction = $actions[($i + 1) % 2]) instanceof SlotChangeAction && ($itemClickedWith = $action->getTargetItem())->getTypeId() === VanillaBlocks::MOB_HEAD()->setMobHeadType(MobHeadType::WITHER_SKELETON())->asItem()->getTypeId() && ($itemClicked = $action->getSourceItem())->getTypeId() !== VanillaItems::AIR()->getTypeId() && in_array($itemClicked->getTypeId(), $items) && $itemClickedWith->getCount() === 1 && $itemClickedWith->getNamedTag()->getString("mask", "") === "buff") {
                    if ($itemClicked->getNamedTag()->getTag("mask")) {
                        $event->getTransaction()->getSource()->sendMessage("§r§c§l(!) §r§cYou cannot do that!");
                        $transaction->getSource()->getWorld()->addSound($transaction->getSource()->getLocation(), new AnvilFallSound());
                        return;
                    }
                    $event->cancel();
                    $lore = "§r§7§lATTACHED: §9Buff Mask";
                    $itemClicked->setLore([$lore]);
                    $itemClicked->getNamedTag()->setString("mask", "buff");
                    Utils::spawnParticleV2($event->getTransaction()->getSource(), "minecraft:villager_happy");
                    $action->getInventory()->setItem($action->getSlot(), $itemClicked);
                    $otherAction->getInventory()->setItem($otherAction->getSlot(), VanillaItems::AIR());
                    $transaction->getSource()->getWorld()->addSound($transaction->getSource()->getLocation(), new XpLevelUpSound(100));
                    return;
                }
            }
        }
    }

    /**
     * @priority HIGHEST
     */
    public function onPlayerDropDragonMask(InventoryTransactionEvent $event): void
    {
        $transaction = $event->getTransaction();
        $actions = array_values($transaction->getActions());
        if (count($actions) === 2) {
            foreach ($actions as $i => $action) {
                $items = [ItemTypeIds::DIAMOND_HELMET, ItemTypeIds::NETHERITE_HELMET];
                if ($action instanceof SlotChangeAction && ($otherAction = $actions[($i + 1) % 2]) instanceof SlotChangeAction && ($itemClickedWith = $action->getTargetItem())->getTypeId() === VanillaBlocks::MOB_HEAD()->setMobHeadType(MobHeadType::WITHER_SKELETON())->asItem()->getTypeId() && ($itemClicked = $action->getSourceItem())->getTypeId() !== VanillaItems::AIR()->getTypeId() && in_array($itemClicked->getTypeId(), $items) && $itemClickedWith->getCount() === 1 && $itemClickedWith->getNamedTag()->getString("mask", "") === "dragon") {
                    if ($itemClicked->getNamedTag()->getTag("mask")) {
                        $event->getTransaction()->getSource()->sendMessage("§r§c§l(!) §r§cYou cannot do that!");
                        $transaction->getSource()->getWorld()->addSound($transaction->getSource()->getLocation(), new AnvilFallSound());
                        return;
                    }
                    $event->cancel();
                    $lore = "§r§7§lATTACHED: §4Dragon Mask";
                    $itemClicked->setLore([$lore]);
                    $itemClicked->getNamedTag()->setString("mask", "dragon");
                    Utils::spawnParticleV2($event->getTransaction()->getSource(), "minecraft:villager_happy");
                    $action->getInventory()->setItem($action->getSlot(), $itemClicked);
                    $otherAction->getInventory()->setItem($otherAction->getSlot(), VanillaItems::AIR());
                    $transaction->getSource()->getWorld()->addSound($transaction->getSource()->getLocation(), new XpLevelUpSound(100));
                    return;
                }
            }
        }
    }

    /**
     * @priority HIGHEST
     */
    public function onPlayerDropXpMask(InventoryTransactionEvent $event): void
    {
        $transaction = $event->getTransaction();
        $actions = array_values($transaction->getActions());
        if (count($actions) === 2) {
            foreach ($actions as $i => $action) {
                $items = [ItemTypeIds::DIAMOND_HELMET, ItemTypeIds::NETHERITE_HELMET];
                if ($action instanceof SlotChangeAction && ($otherAction = $actions[($i + 1) % 2]) instanceof SlotChangeAction && ($itemClickedWith = $action->getTargetItem())->getTypeId() === VanillaBlocks::MOB_HEAD()->setMobHeadType(MobHeadType::WITHER_SKELETON())->asItem()->getTypeId() && ($itemClicked = $action->getSourceItem())->getTypeId() !== VanillaItems::AIR()->getTypeId() && in_array($itemClicked->getTypeId(), $items) && $itemClickedWith->getCount() === 1 && $itemClickedWith->getNamedTag()->getString("mask", "") === "xp") {
                    if ($itemClicked->getNamedTag()->getTag("mask")) {
                        $event->getTransaction()->getSource()->sendMessage("§r§c§l(!) §r§cYou cannot do that!");
                        $transaction->getSource()->getWorld()->addSound($transaction->getSource()->getLocation(), new AnvilFallSound());
                        return;
                    }
                    $event->cancel();
                    $lore = "§r§7§lATTACHED: §6XP Mask";
                    $itemClicked->setLore([$lore]);
                    $itemClicked->getNamedTag()->setString("mask", "xp");
                    Utils::spawnParticleV2($event->getTransaction()->getSource(), "minecraft:villager_happy");
                    $action->getInventory()->setItem($action->getSlot(), $itemClicked);
                    $otherAction->getInventory()->setItem($otherAction->getSlot(), VanillaItems::AIR());
                    $transaction->getSource()->getWorld()->addSound($transaction->getSource()->getLocation(), new XpLevelUpSound(100));
                    return;
                }
            }
        }
    }

    /**
     * @priority HIGHEST
     */
    public function onPlayerDropNyxMask(InventoryTransactionEvent $event): void
    {
        $transaction = $event->getTransaction();
        $actions = array_values($transaction->getActions());
        if (count($actions) === 2) {
            foreach ($actions as $i => $action) {
                $items = [ItemTypeIds::DIAMOND_HELMET, ItemTypeIds::NETHERITE_HELMET];
                if ($action instanceof SlotChangeAction && ($otherAction = $actions[($i + 1) % 2]) instanceof SlotChangeAction && ($itemClickedWith = $action->getTargetItem())->getTypeId() === VanillaBlocks::MOB_HEAD()->setMobHeadType(MobHeadType::WITHER_SKELETON())->asItem()->getTypeId() && ($itemClicked = $action->getSourceItem())->getTypeId() !== VanillaItems::AIR()->getTypeId() && in_array($itemClicked->getTypeId(), $items) && $itemClickedWith->getCount() === 1 && $itemClickedWith->getNamedTag()->getString("mask", "") === "nyx") {
                    if ($itemClicked->getNamedTag()->getTag("mask")) {
                        $event->getTransaction()->getSource()->sendMessage("§r§c§l(!) §r§cYou cannot do that!");
                        $transaction->getSource()->getWorld()->addSound($transaction->getSource()->getLocation(), new AnvilFallSound());
                        return;
                    }
                    $event->cancel();
                    $lore = "§r§7§lATTACHED: §9Nyx Mask";
                    $itemClicked->setLore([$lore]);
                    $itemClicked->getNamedTag()->setString("mask", "nyx");
                    Utils::spawnParticleV2($event->getTransaction()->getSource(), "minecraft:villager_happy");
                    $action->getInventory()->setItem($action->getSlot(), $itemClicked);
                    $otherAction->getInventory()->setItem($otherAction->getSlot(), VanillaItems::AIR());
                    $transaction->getSource()->getWorld()->addSound($transaction->getSource()->getLocation(), new XpLevelUpSound(100));
                    return;
                }
            }
        }
    }

    /**
     * @priority HIGHEST
     */
    public function onPlayerDropScarecrowMask(InventoryTransactionEvent $event): void
    {
        $transaction = $event->getTransaction();
        $actions = array_values($transaction->getActions());
        if (count($actions) === 2) {
            foreach ($actions as $i => $action) {
                $items = [ItemTypeIds::DIAMOND_HELMET, ItemTypeIds::NETHERITE_HELMET];
                if ($action instanceof SlotChangeAction && ($otherAction = $actions[($i + 1) % 2]) instanceof SlotChangeAction && ($itemClickedWith = $action->getTargetItem())->getTypeId() === VanillaBlocks::MOB_HEAD()->setMobHeadType(MobHeadType::WITHER_SKELETON())->asItem()->getTypeId() && ($itemClicked = $action->getSourceItem())->getTypeId() !== VanillaItems::AIR()->getTypeId() && in_array($itemClicked->getTypeId(), $items) && $itemClickedWith->getCount() === 1 && $itemClickedWith->getNamedTag()->getString("mask", "") === "scarecrow") {
                    if ($itemClicked->getNamedTag()->getTag("mask")) {
                        $event->getTransaction()->getSource()->sendMessage("§r§c§l(!) §r§cYou cannot do that!");
                        $transaction->getSource()->getWorld()->addSound($transaction->getSource()->getLocation(), new AnvilFallSound());
                        return;
                    }
                    $event->cancel();
                    $lore = "§r§7§lATTACHED: §eScarecrow Mask";
                    $itemClicked->setLore([$lore]);
                    $itemClicked->getNamedTag()->setString("mask", "scarecrow");
                    Utils::spawnParticleV2($event->getTransaction()->getSource(), "minecraft:villager_happy");
                    $action->getInventory()->setItem($action->getSlot(), $itemClicked);
                    $otherAction->getInventory()->setItem($otherAction->getSlot(), VanillaItems::AIR());
                    $transaction->getSource()->getWorld()->addSound($transaction->getSource()->getLocation(), new XpLevelUpSound(100));
                    return;
                }
            }
        }
    }

    public function onEntityDamage(EntityDamageByEntityEvent $event){
        $entity = $event->getEntity();
        $damager = $event->getDamager();
        if ($damager instanceof Player) {
            $damagerInv = $damager->getArmorInventory();
            $helmet = $damagerInv->getHelmet();

            // Check for the "nyx" mask
            if (Utils::hasMaskTag($helmet, "mask", "nyx")) {
                // Apply a 3% damage boost
                $event->setBaseDamage($event->getBaseDamage() * 1.03);
            }

            // Check for other masks and apply their effects
            if (Utils::hasMaskTag($helmet, "mask", "purge")) {
                $event->setBaseDamage($event->getBaseDamage() + 2.5);
            }
            if (Utils::hasMaskTag($helmet, "mask", "dragon")) {
                $event->setBaseDamage($event->getBaseDamage() * 1.5);
            }
        }

        if ($entity instanceof Player) {
            $entityInv = $entity->getArmorInventory();
            $helmet = $entityInv->getHelmet();

            // Check for the "nyx" mask
            if (Utils::hasMaskTag($helmet, "mask", "nyx")) {
                // Apply a 4% damage reduction
                $event->setBaseDamage($event->getBaseDamage() * 0.96);
            }

            // Check for other masks and apply their effects
            if (Utils::hasMaskTag($helmet, "mask", "party")) {
                $event->setBaseDamage($event->getBaseDamage() * 0.95);
            }
        }
    }


    public function onTransaction(InventoryTransactionEvent $event): void {
        $transaction = $event->getTransaction();
        $player = $transaction->getSource();

        foreach ($transaction->getActions() as $action) {
            if ($action instanceof SlotChangeAction) {
                $inventory = $action->getInventory();
                if ($inventory instanceof ArmorInventory) {
                    $oldArmorPiece = $action->getSourceItem();
                    $newArmorPiece = $action->getTargetItem();

                    // Cheetah Tag
                    $oldHasCheetahTag = Utils::hasMaskTag($oldArmorPiece, "mask", "cheetah");
                    $newHasCheetahTag = Utils::hasMaskTag($newArmorPiece, "mask", "cheetah");
                    if ($oldHasCheetahTag && !$newHasCheetahTag && $player->getEffects()->has(VanillaEffects::SPEED())) {
                        $player->getEffects()->remove(VanillaEffects::SPEED());
                    } elseif (!$oldHasCheetahTag && $newHasCheetahTag && !$player->getEffects()->has(VanillaEffects::SPEED())) {
                        $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 20 * 9999, 3, false));
                    }

                    // Buff Tag
                    $oldHasBuffTag = Utils::hasMaskTag($oldArmorPiece, "mask", "buff");
                    $newHasBuffTag = Utils::hasMaskTag($newArmorPiece, "mask", "buff");
                    if ($oldHasBuffTag && !$newHasBuffTag && $player->getEffects()->has(VanillaEffects::REGENERATION())) {
                        $player->getEffects()->remove(VanillaEffects::REGENERATION());
                    } elseif (!$oldHasBuffTag && $newHasBuffTag && !$player->getEffects()->has(VanillaEffects::REGENERATION())) {
                        $player->getEffects()->add(new EffectInstance(VanillaEffects::REGENERATION(), 20 * 9999, 0, false));
                    }

                    // Dragon Tag
                    $oldHasDragonTag = Utils::hasMaskTag($oldArmorPiece, "mask", "dragon");
                    $newHasDragonTag = Utils::hasMaskTag($newArmorPiece, "mask", "dragon");
                    if ($oldHasDragonTag && !$newHasDragonTag && $player->getEffects()->has(VanillaEffects::FIRE_RESISTANCE())) {
                        $player->getEffects()->remove(VanillaEffects::FIRE_RESISTANCE());
                    } elseif (!$oldHasDragonTag && $newHasDragonTag && !$player->getEffects()->has(VanillaEffects::FIRE_RESISTANCE())) {
                        $player->getEffects()->add(new EffectInstance(VanillaEffects::FIRE_RESISTANCE(), 20 * 9999, 1, false));
                    }

                    // Scarecrow Tag
                    $oldHasScarecrowTag = Utils::hasMaskTag($oldArmorPiece, "mask", "scarecrow");
                    $newHasScarecrowTag = Utils::hasMaskTag($newArmorPiece, "mask", "scarecrow");
                    if ($oldHasScarecrowTag && !$newHasScarecrowTag && $player->getEffects()->has(VanillaEffects::SATURATION())) {
                        $player->getEffects()->remove(VanillaEffects::SATURATION());
                    } elseif (!$oldHasScarecrowTag && $newHasScarecrowTag && !$player->getEffects()->has(VanillaEffects::SATURATION())) {
                        $player->getEffects()->add(new EffectInstance(VanillaEffects::SATURATION(), 20 * 9999, 0, false));
                    }
                }
            }
        }
    }

    public function onEntityDeath(EntityDeathEvent $event) {
        $entity = $event->getEntity();
        $cause = $entity->getLastDamageCause();

        if ($cause instanceof EntityDamageByEntityEvent) {
            $damager = $cause->getDamager();

            if ($damager instanceof Player) {
                $damagerInv = $damager->getArmorInventory();
                $helmet = $damagerInv->getHelmet();

                if (Utils::hasMaskTag($helmet, "mask", "xp")) {
                    if ($entity instanceof Entity) {
                        $xpDrops = $entity->getXpDropAmount();
                        $newXP = ceil($xpDrops * 1.1); // Round up to ensure a 10% increase
                        $event->setXpDropAmount($newXP);
                    }
                }
            }
        }
    }

}