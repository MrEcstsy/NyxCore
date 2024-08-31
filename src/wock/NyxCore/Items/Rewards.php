<?php

namespace wock\NyxCore\Items;

use cooldogedev\BedrockEconomy\api\BedrockEconomyAPI;
use cooldogedev\BedrockEconomy\api\legacy\ClosureContext;
use DaPigGuy\PiggyCustomEnchants\CustomEnchantManager;
use onebone\economyapi\EconomyAPI;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\Server;
use wock\NyxCore\Utils\Utils;
use pocketmine\utils\TextFormat as C;

class Rewards {

    public const FRAGMENTGENERATOR = 0;

    public const GEARLOOTBOX = 1;

    public const RANKRANDOMIZER = 2;

    public const MONEYGENERATOR = 3;

    public const XPGENERATOR = 4;

    public static function get(int $id, int $amount = 1): ?Item
    {
        $item = VanillaItems::AIR()->setCount($amount);
        switch ($id) {
            case self::FRAGMENTGENERATOR:
                $item = VanillaItems::NETHER_QUARTZ()->setCount($amount);

                $item->setCustomName("§r§l§f➥ §1Fragment §fGenerator §r§7(Right Click)");
                $item->setLore([
                "",
                "§r§7Right-Click (in your hand) to receive",
                "§r§7one of the fragments listed below.",
                "",
                "§r§f§lRandom Loot (§r§71 Item(s)§l§f)",
                "§r§f§l * 1x §bEnchantment Fragment [§r§7Depth Strider III§l§b]",
                "§r§f§l * 1x §cEnchantment Fragment [§r§7Thorns III§c§l]",
                "§r§f§l * 1x §bEnchantment Fragment [§r§dUnbreaking V§b§l]",
                "§r§f§l * 1x §bEnchantment Fragment [§r§dLooting V§l§b]"
            ]);
                $item->getNamedTag()->setString("fragmentgenerator", "true");
                break;
            case self::GEARLOOTBOX:
                $item = VanillaBlocks::ENDER_CHEST()->asItem()->setCount($amount);

                $item->setCustomName("§r§l§bGear §fLootbox §r§7(Right Click)");
                $item->setLore([
                    "§r§7Right-Click to recieve Prot 4 Gear.",
                    "",
                    "§r§f§lRandom Loot (§r§71 Item§r§f§l)",
                    "§r§f§l * 1x §bHelmet",
                    "§r§f§l * 1x §bChestplate",
                    "§r§f§l * 1x §bLeggings",
                    "§r§f§l * 1x §bBoots"
                ]);
                $item->getNamedTag()->setString("gearlootbox", "true");
                break;
            case self::MONEYGENERATOR:
                $item = VanillaItems::PAPER()->setCount($amount);

                $item->setCustomName("§r§l§f➥ §2Money §fGenerator");
                $item->setLore([
                    "",
                    "§r§7Right-Click (in your hand) to receive",
                    "§r§7one of the money notes listed below",
                    "",
                    "§r§f§lRandom Loot (§r§71 item§l§f)",
                    "§r§f§l * 1x §aFantasy Note §r§7(Right Click)",
                    "§r§f§l * 1x §aFantasy Note §r§7(Right Click)",
                    "§r§f§l * 1x §aFantasy Note §r§7(Right Click)",
                    "§r§f§l * 1x §aFantasy Note §r§7(Right Click)",
                    "§r§f§l * 1x §aFantasy Note §r§7(Right Click)",
                ]);
                $item->getNamedTag()->setString("moneygenerator", "true");
                break;
            case self::RANKRANDOMIZER:
                $item = VanillaItems::PAPER()->setCount($amount);

                $item->setCustomName("§r§l§f➥ §aRANK §fGenerator");
                $item->setLore([
                    "",
                    "§r§7Right-Click (in your hand) to receive",
                    "§r§7one of the ranks listed below",
                    "",
                    "§r§f§lRandom Loot (§r§71 item§l§f)",
                    '§r§f§l * 1x §eRANK "§f§l<§8•Traveler§f>§e§l"',
                    '§r§f§l * 1x §eRANK "§l§f<§e•Explorer§f>§e§l"',
                    '§r§f§l * 1x §eRANK "§l§f<§6•Commander§f>§e§l"',
                    '§r§f§l * 1x §eRANK "§l§f<§a•General§f>§e§l"',
                    '§r§f§l * 1x §eRANK "§l§f<§2✦Fantasy§f>§e§l"',
                ]);
                $item->getNamedTag()->setString("moneygenerator", "true");
                break;
            case self::XPGENERATOR:
                $item = VanillaItems::EXPERIENCE_BOTTLE()->setCount($amount);

                $item->setCustomName("§r§f§l➥ §aXP §fGenerator");
                $item->setLore([
                   "",
                   "§r§7Right-Click (in your hand) to receive",
                   "§r§7one of the xp bottles listed below.",
                   "",
                   "§r§f§lRandom Loot (§r§71 item§f§l)",
                   "§r§f§l * 1x §aExperience Bottle §r§8(Throw)",
                   "§r§f§l * 1x §aExperience Bottle §r§8(Throw)",
                   "§r§f§l * 1x §aExperience Bottle §r§8(Throw)",
                   "§r§f§l * 1x §aExperience Bottle §r§8(Throw)",
                   "§r§f§l * 1x §aExperience Bottle §r§8(Throw)"
                ]);

                $item->getNamedTag()->setString("xpgenerator", "true");
                break;
            default:
                throw new \Exception('No rewards under this ID.');
        }
        return $item;
    }

    public static function getMoneyPouch(int $tier, int $amount = 1): ?Item {
        $item = VanillaItems::AIR()->setCount($amount);
        switch ($tier) {
            case 0:
              $item = VanillaBlocks::ENDER_CHEST()->asItem()->setCount($amount);
              $item->setCustomName("§r§l§2Money Pouch §8<§2I§8> §r§7(Right Click)");
              $item->setLore([
                  "§r§7Open this pouch to receive money!"
              ]);

              $item->getNamedTag()->setString("moneypouch", "tier1");
              break;

            case 1:
                $item = VanillaBlocks::ENDER_CHEST()->asItem()->setCount($amount);
                $item->setCustomName("§r§l§cMoney Pouch §8<§cII§8> §r§7(Right Click)");
                $item->setLore([
                    "§r§7Open this pouch to receive money!"
                ]);

                $item->getNamedTag()->setString("moneypouch", "tier2");
                break;

            case 2:
                $item = VanillaBlocks::ENDER_CHEST()->asItem()->setCount($amount);
                $item->setCustomName("§r§l§6Money Pouch §8<§6III§8> §r§7(Right Click)");
                $item->setLore([
                    "§r§7Open this pouch to receive money!"
                ]);

                $item->getNamedTag()->setString("moneypouch", "tier3");
                break;

            case 3:
                $item = VanillaBlocks::ENDER_CHEST()->asItem()->setCount($amount);
                $item->setCustomName("§r§l§9Money Pouch §8<§9IV§8> §r§7(Right Click)");
                $item->setLore([
                    "§r§7Open this pouch to receive money!"
                ]);

                $item->getNamedTag()->setString("moneypouch", "tier4");
                break;

            case 4:
                $item = VanillaBlocks::ENDER_CHEST()->asItem()->setCount($amount);
                $item->setCustomName("§r§l§4Money Pouch §8<§4V§8> §r§7(Right Click)");
                $item->setLore([
                    "§r§7Open this pouch to receive money!"
                ]);

                $item->getNamedTag()->setString("moneypouch", "tier5");
                break;
        }
        return $item;
    }

    public static function getXpPouch(int $tier, int $amount = 1): ?Item {
        $item = VanillaItems::AIR()->setCount($amount);
        switch ($tier) {
            case 0:
                $item = VanillaBlocks::ENDER_CHEST()->asItem()->setCount($amount);
                $item->setCustomName("§r§l§2Xp Pouch §8<§2I§8> §r§7(Right Click)");
                $item->setLore([
                    "§r§7Open this pouch to receive experience!"
                ]);

                $item->getNamedTag()->setString("xppouch", "xptier1");
                break;

            case 1:
                $item = VanillaBlocks::ENDER_CHEST()->asItem()->setCount($amount);
                $item->setCustomName("§r§l§cXp Pouch §8<§cII§8> §r§7(Right Click)");
                $item->setLore([
                    "§r§7Open this pouch to receive experience!"
                ]);

                $item->getNamedTag()->setString("xppouch", "xptier2");
                break;

            case 2:
                $item = VanillaBlocks::ENDER_CHEST()->asItem()->setCount($amount);
                $item->setCustomName("§r§l§6Xp Pouch §8<§6III§8> §r§7(Right Click)");
                $item->setLore([
                    "§r§7Open this pouch to receive experience!"
                ]);

                $item->getNamedTag()->setString("xppouch", "xptier3");
                break;

            case 3:
                $item = VanillaBlocks::ENDER_CHEST()->asItem()->setCount($amount);
                $item->setCustomName("§r§l§9Xp Pouch §8<§9IV§8> §r§7(Right Click)");
                $item->setLore([
                    "§r§7Open this pouch to receive experience!"
                ]);

                $item->getNamedTag()->setString("xppouch", "xptier4");
                break;

            case 4:
                $item = VanillaBlocks::ENDER_CHEST()->asItem()->setCount($amount);
                $item->setCustomName("§r§l§4Xp Pouch §8<§4V§8> §r§7(Right Click)");
                $item->setLore([
                    "§r§7Open this pouch to receive experience!"
                ]);

                $item->getNamedTag()->setString("xppouch", "xptier5");
                break;
        }
        return $item;
    }

    public static function createEnchantFragment(int $tier, int $amount = 1): ?Item {
        $item = VanillaItems::AIR()->setCount($amount);
        switch ($tier) {
            case 0:
                $item = VanillaItems::IRON_INGOT()->setCount($amount);
                $item->setCustomName("§r§l§bEnchantment Fragment [§r§dUnbreaking V§l§b]");
                $item->setLore([
                    "§r§7Drag n' Drop on a pair of",
                    "§r§b'§7armor§b'§7 to enchant §dUnbreaking V§7."
                ]);

                $item->getNamedTag()->setString("enchantmentfragment", "unbreakingv");
                $item->getNamedTag()->setInt("enchantmentfragmenttier", 0);
                break;
            case 1:
                $item = VanillaItems::REDSTONE_DUST()->setCount($amount);
                $item->setCustomName("§r§l§cEnchantment Fragment [§r§7Thorns III§l§c]");
                $item->setLore([
                    "§r§7Drag n' Drop on a pair of",
                    "§r§c'§7armor§c'§7 to enchant §cThorns III§7."
                ]);
                $item->getNamedTag()->setString("enchantmentfragment", "thornsiii");
                $item->getNamedTag()->setInt("enchantmentfragmenttier", 1);
                break;
            case 2:
                $item = VanillaItems::LAPIS_LAZULI()->setCount($amount);
                $item->setCustomName("§r§l§bEnchantment Fragment [§r§7Depth Strider III§l§b]");
                $item->setLore([
                   "§r§7Drag n' Drop on a pair of",
                   "§r§b'§7armor§b'§7 to enchant §bDepth Strider III§7."
                ]);

                $item->getNamedTag()->setString("enchantmentfragment", "depthstrideriii");
                $item->getNamedTag()->setInt("enchantmentfragmenttier", 2);
                break;
            case 3:
                $item = VanillaItems::GOLD_INGOT()->setCount($amount);
                $item->setCustomName("§r§l§bEnchantment Fragment [§r§dLooting V§l§b]");
                $item->setLore([
                    "§r§7Drag n' Drop on a pair of",
                    "§r§b'§7weapon§b'§7 to enchant §dLooting V§7."
                ]);
                $item->getNamedTag()->setString("enchantmentfragment", "lootingv");
                $item->getNamedTag()->setInt("enchantmentfragmenttier", 3);
                break;
        }
        return $item;
    }

    public static function creaateSpawnerCase(int $tier, int $amount = 1): ?Item {
        $item = VanillaItems::AIR()->setCount($amount);
        switch ($tier) {
            case 0:
                $item = VanillaBlocks::END_PORTAL_FRAME()->asItem()->setCustomName("§r§l§f* Simple Spawner Case *");

                $item->setLore([
                    "§r§7Right-Click to receive a random",
                    "§r§7spawner from this list:",
                    "§r§f§l * 1x Sheep Spawner",
                    "§r§f§l * 1x Pig Spawner",
                    "§r§f§l * 1x Chicken Spawner",
                    "§r§f§l * 1x Cow Spawner"
                ]);
                $item->getNamedTag()->setString("spawnercase", "simple");
                break;
            case 1:
                $item = VanillaBlocks::END_PORTAL_FRAME()->asItem()->setCustomName("§r§l§a* Unique Spawner Case *");

                $item->setLore([
                    "§r§7Right-Click to receive a random",
                    "§r§7spawner from this list:",
                    "§r§l§a * 1x Wolf Spawner",
                    "§r§l§a * 1x Horse Spawner",
                    "§r§l§a * 1x Ocelot Spawner"
                ]);
                $item->getNamedTag()->setString("spawnercase", "unique");
                break;
            case 2:
                $item = VanillaBlocks::END_PORTAL_FRAME()->asItem()->setCustomName("§r§l§b* Elite Spawner Case *");

                $item->setLore([
                    "§r§7Right-Click to receive a random",
                    "§r§7spawner from this list:",
                    "§r§l§b * 1x Zombie Spawner",
                    "§r§l§b * 1x Skeleton Spawner",
                    "§r§l§b * 1x Villager Spawner",
                    "§r§l§b * 1x Enderman Spawner",
                ]);
                $item->getNamedTag()->setString("spawnercase", "elite");
                break;
            case 3:
                $item = VanillaBlocks::END_PORTAL_FRAME()->asItem()->setCustomName("§r§l§e* Ultimate Spawner Case *");

                $item->setLore([
                    "§r§7Right-Click to receive a random",
                    "§r§7spawner from this list:",
                    "§r§l§e * 1x Blaze Spawner",
                    "§r§l§e * 1x Slime Spawner",
                    "§r§l§e * 1x Witch Spawner",
                    "§r§l§e * 1x Iron Golem Spawner",
                ]);
                $item->getNamedTag()->setString("spawnercase", "ultimaate");
                break;
            case 4:
                $item = VanillaBlocks::END_PORTAL_FRAME()->asItem()->setCustomName("§r§l§6* Legendary Spawner Case *");

                $item->setLore([
                    "§r§7Right-Click to receive a random",
                    "§r§7spawner from this list:",
                    "§r§l§6 * 1x Ghast Spawner",
                    "§r§l§6 * 1x Wither Skeleton Spawner",
                    "§r§l§6 * 1x Vindicator Spawner",
                ]);
                $item->getNamedTag()->setString("spawnercase", "legendary");
                break;
            case 5:
                $item = VanillaBlocks::END_PORTAL_FRAME()->asItem()->setCustomName("§r§l§c* Godly Spawner Case *");

                $item->setLore([
                    "§r§7Right-Click to receive a random",
                    "§r§7spawner from this list:",
                    "§r§l§c * 1x Ghast Spawner",
                    "§r§l§c * 1x Magma Cube Spawner",
                    "§r§l§c * 1x Iron Golem Spawner",
                    "§r§l§c * 1x Blaze Spawner",
                ]);
                $item->getNamedTag()->setString("spawnercase", "godly");
                break;
        }
        return $item;
    }

    public static function createXPBottle(?Player $player = null, ?float $amount = null, int $count = 1, bool $subtract = false): ?Item
    {

        $signer = "Nyx";
        $randamt = rand(1, 500000);
        if ($player !== null) {
            $signer = $player->getName();
        }

        if ($amount !== null) {
            $randamt = $amount;
        }
        $item = VanillaItems::EXPERIENCE_BOTTLE()->setCount($count);
        $item->getNamedTag()->setInt("xpbottle", $randamt);
        $tag = $item->getNamedTag()->getInt("xpbottle");
        $item->setCustomName("§r§a§lExperience Bottle §r§7(Throw)");
        $item->setLore(array(
            "§r§dValue §r§f" . number_format($tag) . " XP",
            "§r§dEnchanter §r§f$signer"
        ));
        if($subtract) $player->getXpManager()->subtractXp($amount);
        return $item;
    }

    public static function createCrateKey(int $tier, int $amount = 1): ?Item{
        $item = VanillaItems::GOLD_NUGGET()->setCount($amount);
        switch ($tier){
            case 0:
                $item->setCustomName("§r§l§6Grinder §fCrate Key")->setLore(["§r§7Right-Click on a §6Grinder §7Crate to open.", "", "§r§l§6(!) §r§6Type §f/warp crates §6to open this crate key."])->getNamedTag()->setString("cratekey", "grinder");
                break;
            case 1:
                $item->setCustomName("§r§l§eGodly §fCrate Key")->setLore(["§r§7Right-Click on a §eGodly §7Crate to open.", "", "§r§l§e(!) §r§eType §f/warp crates §eto open this crate key."])->getNamedTag()->setString("cratekey", "godly");
                break;
            case 2:
                $item->setCustomName("§r§l§4OP §fCrate Key")->setLore(["§r§7Right-Click on a §4OP §7Crate to open.", "", "§r§l§4(!) §r§4Type §f/warp crates §4to open this crate key."])->getNamedTag()->setString("cratekey", "op");
                break;
            case 3:
                $item->setCustomName("§r§l§2Mutated §fCrate Key")->setLore(["§r§7Right-Click on a §2Mutated §7Crate to open.", "", "§r§l§2(!) §r§2Type §f/warp crates §2to open this crate key."])->getNamedTag()->setString("cratekey", "mutated");
                break;
            case 4:
                $item->setCustomName("§r§l§5Vote §fCrate Key")->setLore(["§r§7Right-Click on a §5Vote §7Crate to open.", "", "§r§l§5(!) §r§5Type §f/warp crates §5to open this crate key."])->getNamedTag()->setString("cratekey", "vote");
                break;
        }
        return $item;
    }

    /**
     * @param Player|null $player
     * @param int|null $amount
     * @return Item|null
     */
    public static function createMoneyNote(?Player $player = null, ?int $amount = null, bool $subtract = false): ?Item{
        $signer = "Nyx";
        $session = null;
        $amountlol = rand(1,10000000);
        if($player !== null){
            $signer = $player->getName();
        }
        if($amount !== null){
            $amountlol = $amount;
        }
        $item = VanillaItems::PAPER();
        $item->getNamedTag()->setInt("moneynote", $amountlol);
        $tag = $item->getNamedTag()->getInt("moneynote");
        $item->setCustomName("§r§b§l* Bank Note *");
        $item->setLore(array(
            "§r§7A valuable Bank Note that can be redeemed for its value.",
            "§r§7Simply right-click while holding it to redeem.",
            "",
            "§r§l§bValue§r§f: $" . number_format($tag),
            "§r§l§bSigner§r§f: " . $signer
        ));
        if($subtract) BedrockEconomyAPI::legacy()->subtractFromPlayerBalance(
            $player->getName(),
            $amount,
            ClosureContext::create(
                function() use($player): void {
               },
            )
        );
        return $item;

    }

    public static function createCEEnchantmentCrystal(int $enchantId, int $level = 1, int $success = 100, int $destroy = 50): ?Item
    {
        $book = VanillaItems::NETHER_STAR();
        $enchant = CustomEnchantManager::getEnchantment($enchantId);
        if ($enchant !== null) {
            $enchInstance = new EnchantmentInstance($enchant, $level);
            $book->setCustomName(C::RESET . C::BOLD . \DaPigGuy\PiggyCustomEnchants\utils\Utils::getColorFromRarity($enchInstance->getType()->getRarity()) . $enchInstance->getType()->getName() . " " . Server::getInstance()->getPluginManager()->getPlugin("PiggyCustomEnchants")->getConfig()->getNested("enchants.roman-numerals") ? \DaPigGuy\PiggyCustomEnchants\utils\Utils::getRomanNumeral($enchInstance->getLevel()) : $enchInstance->getLevel());

            $description = $enchant->getDescription();
            $pos = strpos($description, "", strlen($description) > 35 ? 35 : strlen($description));
            if($pos !== false) $description = chunk_split($description, $pos, "\n");
            $book->setLore([
                C::RESET . C::GREEN . "$success% Success Rate",
                C::RESET . C::RED . "$destroy% Destroy Rate",
                C::RESET . C::YELLOW . $description,
                C::RESET . C::GRAY . "Drag n' Drop onto item to enchant."
            ]);
        }
        $book->getNamedTag()->setInt("enchantbook", $enchantId);
        $book->getNamedTag()->setInt("levelbook", $level);
        $book->getNamedTag()->setInt("successbook", $success);
        $book->getNamedTag()->setInt("destroybook", $destroy);
        return $book;
    }

    public static function getRarityBook(int $rarity, int $amount): Item
    {
        $item = VanillaItems::BOOK()->setCount($amount);
        $item->setCustomName(C::RESET . C::BOLD . \DaPigGuy\PiggyCustomEnchants\utils\Utils::getColorFromRarity($rarity) . Utils::rarityToName($rarity) . C::WHITE . " Book");
        $item->getNamedTag()->setInt("randomcebook", $rarity);
        $item->setLore([C::RESET . C::GRAY . "Tap this item to redeem a random Custom Enchant"]);
        return $item;
    }

    /**
     * @return Item
     */
    public static function getTransmogScroll(): Item
    {
        $item = StringToItemParser::getInstance()->parse(Utils::getConfigurations("items")->getNested("transmogscroll.item"));
        $item->setCustomName(C::RESET . C::colorize(Utils::getConfigurations("items")->getNested("transmogscroll.name")));

        $lore = Utils::getConfigurations("items")->getNested("transmogscroll.lore");
        $itemLore = [];
        foreach ($lore as $line) {
            $itemLore[] = C::RESET . C::colorize($line);
        }
        $item->setLore($itemLore);

        $item->getNamedTag()->setInt("transmogscroll", mt_rand(0, 100000));
        if (Utils::getConfigurations("items")->getNested("transmogscroll.glint")) $item->getNamedTag()->getListTag("ench");

        return $item;
    }

    /**
     * @return Item
     */
    public static function getWhiteScroll(): Item
    {
        $item = StringToItemParser::getInstance()->parse(Utils::getConfigurations("items")->getNested("whitescroll.item"));
        $item->setCustomName(C::RESET . C::colorize(Utils::getConfigurations("items")->getNested("whitescroll.name")));

        $lore = Utils::getConfigurations("items")->getNested("whitescroll.lore");
        $itemLore = [];
        foreach ($lore as $line) {
            $itemLore[] = C::RESET . C::colorize($line);
        }
        $item->setLore($itemLore);

        $item->getNamedTag()->setInt("whitescroll", mt_rand(0, 100000));
        if (Utils::getConfigurations("items")->getNested("whitescroll.glint")) $item->getNamedTag()->getListTag("ench");

        return $item;
    }

    /**
     * @param int $percent
     * @return Item
     */
    public static function getBlackScroll($percent = 100): Item
    {
        $item = StringToItemParser::getInstance()->parse(Utils::getConfigurations("items")->getNested("blackscroll.item"));
        $item->setCustomName(C::RESET . C::colorize(Utils::getConfigurations("items")->getNested("blackscroll.name")));

        $lore = Utils::getConfigurations("items")->getNested("blackscroll.lore");
        $itemLore = [];
        foreach ($lore as $line) {
            $line = str_replace("{SUCCESS}", $percent, $line);
            $itemLore[] = C::RESET . C::colorize($line);
        }
        $item->setLore($itemLore);

        $item->getNamedTag()->setInt("blackscroll", $percent);
        if (Utils::getConfigurations("items")->getNested("blackscroll.glint")) $item->getNamedTag()->getListTag("ench");

        return $item;
    }

    /**
     * @param int $percent
     * @return Item
     */
    public static function getEnchantDust($percent = 100): Item
    {
        $item = StringToItemParser::getInstance()->parse(Utils::getConfigurations("items")->getNested("enchantdust.item"));
        $item->setCustomName(C::RESET . C::colorize(Utils::getConfigurations("items")->getNested("enchantdust.name")));

        $lore = Utils::getConfigurations("items")->getNested("enchantdust.lore");
        $itemLore = [];
        foreach ($lore as $line) {
            $line = str_replace("{PERCENT}", $percent, $line);
            $itemLore[] = C::RESET . C::colorize($line);
        }
        $item->setLore($itemLore);

        $item->getNamedTag()->setInt("enchantdust", $percent);
        if (Utils::getConfigurations("items")->getNested("enchantdust.glint")) $item->getNamedTag()->getListTag("ench");

        return $item;
    }

    /**
     * @param int $tier
     * @return Item
     */
    public static function getWeaponOrb($tier = 10): Item
    {
        $item = StringToItemParser::getInstance()->parse(Utils::getConfigurations("items")->getNested("weaponorb.item"));
        $item->setCustomName("§r§l§6Weapon Enchantment Orb [§r§a". $tier . "§r§l§6]");

        $lore = Utils::getConfigurations("items")->getNested("weaponorb.lore");
        $itemLore = [];
        foreach ($lore as $line) {
            $line = str_replace("{TIER}", $tier, $line);
            $itemLore[] = C::RESET . C::colorize($line);
        }
        $item->setLore($itemLore);

        $item->getNamedTag()->setInt("weaponorb", $tier);
        if (Utils::getConfigurations("items")->getNested("weaponorb.glint")) $item->getNamedTag()->getListTag("ench");

        return $item;
    }

    /**
     * @param int $tier
     * @return Item
     */
    public static function getArmorOrb($tier = 10): Item
    {
        $item = StringToItemParser::getInstance()->parse(Utils::getConfigurations("items")->getNested("armororb.item"));
        $item->setCustomName("§r§l§6Armor Enchantment Orb [§r§a" . $tier . "§r§l§6]");

        $lore = Utils::getConfigurations("items")->getNested("armororb.lore");
        $itemLore = [];
        foreach ($lore as $line) {
            $line = str_replace("{TIER}", $tier, $line);
            $itemLore[] = C::RESET . C::colorize($line);
        }
        $item->setLore($itemLore);

        $item->getNamedTag()->setInt("armororb", $tier);
        if (Utils::getConfigurations("items")->getNested("armororb.glint")) $item->getNamedTag()->getListTag("ench");

        return $item;
    }
}