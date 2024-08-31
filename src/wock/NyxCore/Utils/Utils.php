<?php

namespace wock\NyxCore\Utils;

use DaPigGuy\PiggyCustomEnchants\CustomEnchantManager;
use DaPigGuy\PiggyCustomEnchants\enchants\CustomEnchant;
use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\SpawnParticleEffectPacket;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\world\Position;
use wock\NyxCore\Nyx;

class Utils {

    /**
     * ===============================
     *   ____  _   _
     *  / __ \| | | |
     * | |  | | |_| |__   ___ _ __
     * | |  | | __| '_ \ / _ \ '__|
     * | |__| | |_| | | |  __/ |
     *  \____/ \__|_| |_|\___|_|
     *
     * ===============================
     *
     * This is the things like toggleable, sound & effect stuff too.
     * along with other base utilities.
     */

    public static function getConfigurations(string $type = "default") : Config {
        return match ($type) {
            "messages" => new Config(Nyx::getInstance()->getDataFolder() . "configurations/messages.yml", Config::YAML),
            "settings" => new Config(Nyx::getInstance()->getDataFolder() . "data/settings.json", Config::JSON),
            "starterkit" => new Config(Nyx::getInstance()->getDataFolder() . "configurations/kits/starterKit.yml", Config::YAML),
            "bounty" => new Config(Nyx::getInstance()->getDataFolder() . "data/bounties.json", Config::JSON),
            "quests" => new Config(Nyx::getInstance()->getDataFolder() . "configurations/quests/quests.yml", Config::YAML),
            "items" => new Config(Nyx::getInstance()->getDataFolder() . "configurations/items/items.yml", Config::YAML),
            "kdr" => new Config(Nyx::getInstance()->getDataFolder() . "data/kdr.json", Config::JSON),
            default => new Config(Nyx::getInstance()->getDataFolder() . "config.yml", Config::YAML),
        };
    }

    public static function toggleFlight(Player $player, bool $forceOff = false): void
    {
        if ($forceOff) {
            $player->setAllowFlight(false);
            $player->setFlying(false);
            $player->resetFallDistance(); // Reset fall distance when flight is turned off
            $player->sendMessage("§cYou can no longer fly.");
        } else {
            if (!$player->getAllowFlight()) {
                $player->setAllowFlight(true);
                $player->sendMessage("§aYou can now fly!");
            } else {
                $player->setAllowFlight(false);
                $player->setFlying(false);
                $player->resetFallDistance(); // Reset fall distance when flight is turned off
                $player->sendMessage("§cYou can no longer fly.");
            }
        }
    }

    /**
     * @param Item $item
     * @return bool
     */
    public static function hasMaskTag(Item $item, string $name, string $value = "true"): bool {
        $namedTag = $item->getNamedTag();
        if ($namedTag instanceof CompoundTag) {
            $tag = $namedTag->getTag($name);
            return $tag instanceof StringTag && $tag->getValue() === $value;
        }
        return false;
    }

    /**
     * Returns an online player whose name begins with or equals the given string (case insensitive).
     * The closest match will be returned, or null if there are no online matches.
     *
     * @param string $name The prefix or name to match.
     * @return Player|null The matched player or null if no match is found.
     */
    public static function customGetPlayerByPrefix(string $name): ?Player {
        $found = null;
        $name = strtolower($name);
        $delta = PHP_INT_MAX;

        /** @var Player[] $onlinePlayers */
        $onlinePlayers = Server::getInstance()->getOnlinePlayers();

        foreach ($onlinePlayers as $player) {
            if (stripos($player->getName(), $name) === 0) {
                $curDelta = strlen($player->getName()) - strlen($name);

                if ($curDelta < $delta) {
                    $found = $player;
                    $delta = $curDelta;
                }

                if ($curDelta === 0) {
                    break;
                }
            }
        }

        return $found;
    }

    /**
     * @param Entity $player
     * @param string $sound
     * @param int $volume
     * @param int $pitch
     * @param int $radius
     */
    public static function playSound(Entity $player, string $sound, $volume = 1, $pitch = 1, int $radius = 5): void
    {
        foreach ($player->getWorld()->getNearbyEntities($player->getBoundingBox()->expandedCopy($radius, $radius, $radius)) as $p) {
            if ($p instanceof Player) {
                if ($p->isOnline()) {
                    $spk = new PlaySoundPacket();
                    $spk->soundName = $sound;
                    $spk->x = $p->getLocation()->getX();
                    $spk->y = $p->getLocation()->getY();
                    $spk->z = $p->getLocation()->getZ();
                    $spk->volume = $volume;
                    $spk->pitch = $pitch;
                    $p->getNetworkSession()->sendDataPacket($spk);
                }
            }
        }
    }

    /**
     * @param Entity $player
     * @param string $particleName
     * @param int $radius
     */
    public static function spawnParticle(Entity $player, string $particleName, int $radius = 5): void {
        $packet = new SpawnParticleEffectPacket();
        $packet->particleName = $particleName;
        $packet->position = $player->getPosition()->asVector3();

        foreach ($player->getWorld()->getNearbyEntities($player->getBoundingBox()->expandedCopy($radius, $radius, $radius)) as $p) {
            if ($p instanceof Player) {
                if ($p->isOnline()) {
                    $p->getNetworkSession()->sendDataPacket($packet);
                }
            }
        }
    }

    public static function spawnParticleV2(Entity $entity, string $particleName): void
    {
        $particleCount = 10;
        $radius = 0.5;

        $position = $entity->getEyePos();

        for ($i = 0; $i < $particleCount; $i++) {
            $offsetX = mt_rand(-$radius * 100, $radius * 100) / 100;
            $offsetY = mt_rand(-$radius * 100, $radius * 100) / 100;
            $offsetZ = mt_rand(-$radius * 100, $radius * 100) / 100;

            $particleX = $position->getX() + $offsetX;
            $particleY = $position->getY() + $offsetY;
            $particleZ = $position->getZ() + $offsetZ;

            $particlePosition = new Vector3($particleX, $particleY, $particleZ);
            self::spawnParticleNear($entity, $particleName, $particlePosition);
        }
    }

    public static function spawnParticleNear(Entity $entity, string $particleName, Vector3 $position, int $radius = 5): void
    {
        $packet = new SpawnParticleEffectPacket();
        $packet->particleName = $particleName;
        $packet->position = $position;

        foreach ($entity->getWorld()->getNearbyEntities($entity->getBoundingBox()->expandedCopy($radius, $radius, $radius)) as $player) {
            if ($player instanceof Player && $player->isOnline()) {
                $player->getNetworkSession()->sendDataPacket($packet);
            }
        }
    }

    /**
     * ==============================================
     *   _____                              _
     *  / ____|                            (_)
     * | |     ___  _ ____   _____ _ __ ___ _  ___  _ __  ___
     * | |    / _ \| '_ \ \ / / _ \ '__/ __| |/ _ \| '_ \/ __|
     * | |___| (_) | | | \ V /  __/ |  \__ \ | (_) | | | \__ \
     *  \_____\___/|_| |_|\_/ \___|_|  |___/_|\___/|_| |_|___/
     *
     * ==============================================
     *
     * Under this are the conversions
     */

    public static function translateTime(int $seconds): string
    {
        $timeUnits = [
            'week' => 60 * 60 * 24 * 7,
            'day' => 60 * 60 * 24,
            'hour' => 60 * 60,
            'minute' => 60,
            'second' => 1,
        ];

        $parts = [];

        foreach ($timeUnits as $unit => $value) {
            if ($seconds >= $value) {
                $amount = floor($seconds / $value);
                $seconds %= $value;
                $parts[] = $amount . ' ' . ($amount === 1 ? $unit : $unit . 's');
            }
        }

        return implode(', ', $parts);
    }

    /**
     * @param int $level
     * @return int
     */
    public static function getExpToLevelUp(int $level): int
    {
        if ($level <= 15) {
            return 2 * $level + 7;
        } else if ($level <= 30) {
            return 5 * $level - 38;
        } else {
            return 9 * $level - 158;
        }
    }

    public static function parseShorthandAmount($shorthand): float|int
    {
        $multipliers = [
            'k' => 1000,   // Thousand
            'm' => 1000000, // Million
            'b' => 1000000000, // Billion
        ];
        $lastChar = strtolower(substr($shorthand, -1));
        if (isset($multipliers[$lastChar])) {
            $multiplier = $multipliers[$lastChar];
            $shorthand = substr($shorthand, 0, -1); // Remove the multiplier character
        } else {
            $multiplier = 1; // Default multiplier for no shorthand
        }

        $amount = intval($shorthand) * $multiplier;

        return $amount;
    }

    public static function positionToString(Position $position): string{
        return "$position->x:$position->y:$position->z";
    }

    public static function stringToPosition(string $pos): Position{
        $ex = explode(":", $pos);
        return new Position($ex[0],$ex[1],$ex[2],Server::getInstance()->getWorldManager()->getDefaultWorld());
    }

    public static function sendConfirmation(Player $player, Item $item, int $price): void
    {
        $form = new CustomForm(function (Player $player, array $data = null) use ($item, $price) {
            if ($data === null || $data[1] === true) {
                $player->sendMessage("§r§cPurchase canceled.");
            } elseif ($data[1] === false) {
                $playerXP = $player->getXpManager()->getCurrentTotalXp();
                if ($playerXP < $price) {
                    $player->sendMessage("§r§cYou don't have enough XP to purchase this item.");
                } else {
                    $quantity = (int)$data[0];

                    $totalPrice = $price * $quantity;
                    $player->getXpManager()->subtractXp($totalPrice);

                    $items = [];
                    for ($i = 0; $i < $quantity; $i++) {
                        $items[] = clone $item;
                    }
                    foreach ($items as $item) {
                        $player->getInventory()->addItem($item);
                    }
                    $player->sendMessage("§r§aPurchase successful! You've spent " . number_format($totalPrice) . " XP for $quantity item(s).");
                }
            }
        });

        $form->setTitle($item->getCustomName());
        $form->addLabel("Cost per item: " . number_format($price) . " XP.");
        $form->addSlider("Amount", 1, 64, 1);

        $player->sendForm($form);
    }

    /**
     * ==============================================
     *   _____          _                  ______            _                 _
     *  / ____|        | |                |  ____|          | |               | |
     * | |    _   _ ___| |_ ___  _ __ ___ | |__   _ __   ___| |__   __ _ _ __ | |_ ___
     * | |   | | | / __| __/ _ \| '_ ` _ \|  __| | '_ \ / __| '_ \ / _` | '_ \| __/ __|
     * | |___| |_| \__ \ || (_) | | | | | | |____| | | | (__| | | | (_| | | | | |_\__ \
     * \_____\__,_|___/\__\___/|_| |_| |_|______|_| |_|\___|_| |_|\__,_|_| |_|\__|___/
     *
     * ==============================================
     *
     * Custom Enchantment related stuff
     */

    public static function getAllEnchantments(): array
    {
        $ids = [];
        foreach (CustomEnchantManager::getEnchantments() as $enchantment) {
            $ids[] = $enchantment->getId();
        }
        return $ids;
    }

    public static function rarityToName(int $rarity): ?string
    {
        switch ($rarity) {
            case 888:
                return "Simple";
            case 887:
                return "Unique";
            case 886:
                return "Elite";
            case 885:
                return "Ultimate";
            case 884:
                return "Legendary";
            case 883:
                return "Soul";
            case 882:
                return "Heroic";
            case 881:
                return "Mastery";
        }
        return null;
    }

    public static function getSimpleEnchantments(): array
    {
        $array = [];
        foreach (self::getAllEnchantments() as $enchantment) {
            $enchant = CustomEnchantManager::getEnchantment($enchantment);
            if($enchant instanceof CustomEnchant) {
                if($enchant->getRarity() === CustomEnchant::SIMPLE) {
                    $array[] = $enchantment;
                }
            }
        }
        return $array;
    }

    public static function getUniqueEnchantments(): array
    {
        $array = [];
        foreach (self::getAllEnchantments() as $enchantment) {
            $enchant = CustomEnchantManager::getEnchantment($enchantment);
            if($enchant instanceof CustomEnchant) {
                if($enchant->getRarity() === 887) {
                    $array[] = $enchantment;
                }
            }
        }
        return $array;
    }

    public static function getEliteEnchantments(): array
    {
        $array = [];
        foreach (self::getAllEnchantments() as $enchantment) {
            $enchant = CustomEnchantManager::getEnchantment($enchantment);
            if($enchant instanceof CustomEnchant) {
                if($enchant->getRarity() === 886) {
                    $array[] = $enchantment;
                }
            }
        }
        return $array;
    }

    public static function getUltimateEnchantments(): array
    {
        $array = [];
        foreach (self::getAllEnchantments() as $enchantment) {
            $enchant = CustomEnchantManager::getEnchantment($enchantment);
            if($enchant instanceof CustomEnchant) {
                if($enchant->getRarity() === 885) {
                    $array[] = $enchantment;
                }
            }
        }
        return $array;
    }

    public static function getLegendaryEnchantments(): array
    {
        $array = [];
        foreach (self::getAllEnchantments() as $enchantment) {
            $enchant = CustomEnchantManager::getEnchantment($enchantment);
            if($enchant instanceof CustomEnchant) {
                if($enchant->getRarity() === 884) {
                    $array[] = $enchantment;
                }
            }
        }
        return $array;
    }

    public static function getSoulEnchantments(): array
    {
        $array = [];
        foreach (self::getAllEnchantments() as $enchantment) {
            $enchant = CustomEnchantManager::getEnchantment($enchantment);
            if($enchant instanceof CustomEnchant) {
                if($enchant->getRarity() === 883) {
                    $array[] = $enchantment;
                }
            }
        }
        return $array;
    }

    public static function getHeroicEnchantments(): array
    {
        $array = [];
        foreach (self::getAllEnchantments() as $enchantment) {
            $enchant = CustomEnchantManager::getEnchantment($enchantment);
            if($enchant instanceof CustomEnchant) {
                if($enchant->getRarity() === 882) {
                    $array[] = $enchantment;
                }
            }
        }
        return $array;
    }

    public static function getMasteryEnchantments(): array
    {
        $array = [];
        foreach (self::getAllEnchantments() as $enchantment) {
            $enchant = CustomEnchantManager::getEnchantment($enchantment);
            if($enchant instanceof CustomEnchant) {
                if($enchant->getRarity() === 881) {
                    $array[] = $enchantment;
                }
            }
        }
        return $array;
    }


}