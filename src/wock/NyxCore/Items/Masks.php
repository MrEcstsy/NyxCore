<?php

namespace wock\NyxCore\Items;

use Exception;
use pocketmine\block\utils\MobHeadType;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;

class Masks {

    public const CHEETAHMASK = 0;

    public const PURGEMASK = 1;

    public const PARTYMASK = 2;

    public const BUFFMASK = 3;

    public const DRAGONMASK = 4;

    public const XPMASK = 5;

    public const NYXMASK = 6;

    public const SCARECROW = 7;

    /**
     * @throws Exception
     */
    public static function get(int $id, int $amount = 1): ?Item
    {
        $item = VanillaItems::AIR()->setCount($amount);
        switch ($id) {
            case self::CHEETAHMASK:
                $item = VanillaBlocks::MOB_HEAD()->setMobHeadType(MobHeadType::WITHER_SKELETON())->asItem()->setCount($amount);

                $item->setCustomName("§r§l§eCheetah Mask");
                $item->setLore([
                    "§r§8▌§o Gives the agility of one",
                    "§r§8▌§o of the fastest predators",
                    "",
                    "§r§e§lCheetah Bonus",
                    "§r§7 ➥ §fReceive §eSpeed IV §fEnchantment",
                    "",
                    "§r§7§oAttach this mask to any helmet",
                    "§r§7§oto apply the bonuses.",
                    "",
                    "§r§7To equip, place this mask on a helmet"
                ]);

                $item->getNamedTag()->setString("mask", "cheetah");
                break;
            case self::PURGEMASK:
                $item = VanillaBlocks::MOB_HEAD()->setMobHeadType(MobHeadType::WITHER_SKELETON())->asItem()->setCount($amount);

                $item->setCustomName("§r§c§lPurge Mask");
                $item->setLore([
                    "§r§8§l▌§o A great evil is contained within this",
                    "§r§8§l▌§o horrifying mask. Who knows what inner",
                    "§r§8§l▌§o demons it will unleash...",
                    "",
                    "§r§c§lPurge Bonus",
                    "§r§7 ➥ §fReceive §c+2.5 §fDMG",
                    "",
                    "§r§7§oAttach this mask to any helmet",
                    "§r§7§oto apply the bonuses.",
                    "",
                    "§r§7To equip, place this mask on a helmet"
                ]);

                $item->getNamedTag()->setString("mask", "purge");
                break;
            case self::PARTYMASK:
                $item = VanillaBlocks::MOB_HEAD()->setMobHeadType(MobHeadType::WITHER_SKELETON())->asItem()->setCount($amount);

                $item->setCustomName("§r§f§lParty Mask");
                $item->setLore([
                    "§r§8§l▌§o Everywhere you are is a party.",
                    "",
                    "§r§f§lParty Bonus",
                    "§r§7 ➥ §fReceive §f-5% §fDMG",
                    "",
                    "§r§7§oAttach this mask to any helmet",
                    "§r§7§oto apply the bonuses.",
                    "",
                    "§r§7To equip, place this mask on a helmet"
                ]);

                $item->getNamedTag()->setString("mask", "party");
                break;
            case self::BUFFMASK:
                $item = VanillaBlocks::MOB_HEAD()->setMobHeadType(MobHeadType::WITHER_SKELETON())->asItem()->setCount($amount);

                $item->setCustomName("§r§9§lBuff Mask");
                $item->setLore([
                    "§r§8§l▌§o The result of consistent and dedicated physical",
                    "§r§8§l▌§o training pushing one's body to its limits.",
                    "",
                    "§r§9§lBuff Bonus",
                    "§r§7 ➥ §fReceive permanent §9Regeneration I",
                    "",
                    "§r§7§oAttach this mask to any helmet",
                    "§r§7§oto apply the bonuses.",
                    "",
                    "§r§7To equip, place this mask on a helmet"
                ]);

                $item->getNamedTag()->setString("mask", "buff");
                break;
            case self::DRAGONMASK:
                $item = VanillaBlocks::MOB_HEAD()->setMobHeadType(MobHeadType::WITHER_SKELETON())->asItem()->setCount($amount);

                $item->setCustomName("§r§4§lDragon Mask");
                $item->setLore([
                    "§r§8§l▌§o Dragons are powerful, mythical creatures with",
                    "§r§8§l▌§o fierce claws and the ability to breathe fire.",
                    "",
                    "§r§4§lDragon Bonus",
                    "§r§7 ➥ §fReceive §45% Extra Outgoing Damage",
                    "§r§7 ➥ §fReceive permanent Fire Resistance",
                    "",
                    "§r§7§oAttach this mask to any helmet",
                    "§r§7§oto apply the bonuses.",
                    "",
                    "§r§7To equip, place this mask on a helmet"
                ]);

                $item->getNamedTag()->setString("mask", "dragon");
                break;
            case self::XPMASK:
                $item = VanillaBlocks::MOB_HEAD()->setMobHeadType(MobHeadType::WITHER_SKELETON())->asItem()->setCount($amount);

                $item->setCustomName("§r§l§6XP Mask");
                $item->setLore(array(
                    "§r§l§8▌ §oIncrease your XP using this mask while loosing no hunger.",
                    " ",
                    "§r§l§6XP Bonus",
                    "§r§7 ➥ §fReceive §610% §fXP Buff.",
                    "",
                    "§r§7§oAttach this mask to any helmet",
                    "§r§7§oto apply the bonuses.",
                    "",
                    "§r§7To equip, place this mask on a helmet"
                ));

                $item->getNamedTag()->setString("mask", "xp");
                break;
            case self::NYXMASK:
                $item = VanillaBlocks::MOB_HEAD()->setMobHeadType(MobHeadType::WITHER_SKELETON())->asItem()->setCount($amount);

                $item->setCustomName("§r§l§9Nyx Mask");
                $item->setLore([
                    "§r§l§8▌ §oThe purest creature of all, Truly",
                    "§r§l§8▌ §oconfident, independent, and very powerful",
                    "",
                    "§r§l§9Nyx Bonus",
                    "§r§7 ➥ §fReceive §9+3% DMG",
                    "§r§7 ➥ §fReceive §9-4% Enemy DMG",
                    "§r§7 ➥ §fReceive §9+3 Max Hearts",
                    "",
                    "§r§7§oAttach this mask to any helmet",
                    "§r§7§oto apply the bonuses.",
                    "",
                    "§r§7To equip, place this mask on a helmet"
                ]);

                $item->getNamedTag()->setString("mask", "nyx");
                break;
            case self::SCARECROW:
                $item = VanillaBlocks::MOB_HEAD()->setMobHeadType(MobHeadType::WITHER_SKELETON())->asItem()->setCount($amount);

                $item->setCustomName("§r§l§eScarecrow Mask");
                $item->setLore([
                    "§r§l§8▌ §oAn empty husk of a",
                    "§r§l§8▌ §oonce tortured soul left to rot.",
                    "",
                    "§r§l§eScarecrow Bonus",
                    "§r§7 ➥ §fReceive Infinite §eSaturation",
                    "",
                    "§r§7§oAttach this mask to any helmet",
                    "§r§7§oto apply the bonuses.",
                    "",
                    "§r§7To equip, place this mask on a helmet"
                ]);

                $item->getNamedTag()->setString("mask", "scarecrow");
                break;
            default:
                throw new Exception('No rewards under this ID.');
        }
        return $item;
    }
}