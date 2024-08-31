<?php

namespace wock\NyxCore\Listeners;

use cooldogedev\BedrockEconomy\api\BedrockEconomyAPI;
use cooldogedev\BedrockEconomy\api\legacy\ClosureContext;
use onebone\economyapi\EconomyAPI;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;
use pocketmine\world\sound\AnvilFallSound;
use pocketmine\world\sound\BlockBreakSound;
use pocketmine\world\sound\XpLevelUpSound;
use wock\NyxCore\Items\Rewards;
use wock\NyxCore\Nyx;
use pocketmine\utils\TextFormat as C;
use wock\NyxCore\Tasks\TitleRevealTask;
use wock\NyxCore\Tasks\TitleRevealXpTask;
use wock\NyxCore\Utils\Utils;

class RewardsListener implements Listener {

    public array $pouchCooldown = [];

    public function onBlockPlace(BlockPlaceEvent $event)
    {
        $item = $event->getItem();
        $tag = $item->getNamedTag();

        if ($tag->getTag("moneypouch")) {
            $event->cancel();
        }
        if ($tag->getTag("xppouch")) {
            $event->cancel();
        }
        if ($tag->getTag("gearlootbox")) {
            $event->cancel();
        }
    }

    public function onUseGenerators(PlayerItemUseEvent $event): void
    {
        $player = $event->getPlayer();
        $hand = $player->getInventory()->getItemInHand();
        $item = $event->getItem();
        $tag = $item->getNamedTag();
        if ($tag->getInt("moneynote", 0) !== 0) {
            $event->cancel();
            $value = $tag->getInt("moneynote");
            $formatted = number_format($value, 2);
            $position = new Vector3(0, 64, 0);
            $level = $player->getWorld();
            $level->addSound($position, new XpLevelUpSound(10));
            $hand = $player->getInventory()->getItemInHand();
            BedrockEconomyAPI::legacy()->addToPlayerBalance(
                $player->getName(),
                $value,
                ClosureContext::create(
                    function () use($formatted, $player, $hand): void {
                        $player->sendMessage("§r§a§l+ §r§a$formatted$");
                        $hand->setCount($hand->getCount() - 1);
                        $player->getInventory()->setItemInHand($hand);
                        },
                )
            );
        }
        if ($tag->getInt("xpbottle", 0) !== 0) {
            $event->cancel();
            $value = $tag->getInt("xpbottle");
            $formatted = number_format($value,1);
            $player->sendMessage("§r§a§l+ $formatted xp");
            $player->getWorld()->addSound($player->getLocation(),new BlockBreakSound(VanillaBlocks::GLASS()));
            $player->getXpManager()->addXp($value);
            $hand->setCount($hand->getCount() - 1);
            $player->getInventory()->setItemInHand($hand);
        }
        if ($tag->getTag("fragmentgenerator")) {
            $generatorTag = $tag->getString("fragmentgenerator");
            if ($generatorTag === "true") {
                $item->setCount($item->getCount() - 1);
                $player->getInventory()->setItemInHand($item);
                $randomFragment = mt_rand(0, 3);

                $player->getInventory()->addItem(Rewards::createEnchantFragment($randomFragment, 1));

                Utils::spawnParticleV2($player, "minecraft:endrod");
                Utils::playSound($player, "item.book.page_turn");
            }
        }

        if ($tag->getTag("xpgenerator")) {
            $generatorTag = $tag->getString("xpgenerator");
            if ($generatorTag === "true") {
                $event->cancel();
                $item->setCount($item->getCount() - 1);
                $player->getInventory()->setItemInHand($item);

                $randXP = [
                  5000,
                  10000,
                  15000,
                  20000,
                  25000
                ];

                $getRandXP = $randXP[array_rand($randXP)];

                $player->getInventory()->addItem(Rewards::createXPBottle($player, $getRandXP));

                Utils::spawnParticleV2($player, "minecraft:endrod");
                Utils::playSound($player, "item.book.page_turn");
            }
        }

        if ($tag->getTag("moneygenerator")) {
            $generatorTag = $tag->getString("moneygenerator");
            if ($generatorTag === "true") {
                $event->cancel();
                $item->setCount($item->getCount() - 1);
                $player->getInventory()->setItemInHand($item);

                $randMoney = [
                    5000,
                    10000,
                    15000,
                    20000,
                    25000
                ];

                $getRandMoney = $randMoney[array_rand($randMoney)];

                $player->getInventory()->addItem(Rewards::createMoneyNote($player, $getRandMoney));

                Utils::spawnParticleV2($player, "minecraft:endrod");
                Utils::playSound($player, "item.book.page_turn");
            }
        }
    }

    public function onUsePouches(PlayerItemUseEvent $event): void
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $tag = $item->getNamedTag();
        $config = Nyx::getInstance()->getConfig();
        $currentTime = time();
        $cooldownDuration = 4;

        if (isset($this->pouchCooldown[$player->getName()])) {
            $remainingCooldown = $this->pouchCooldown[$player->getName()] - $currentTime;

            if ($remainingCooldown > 0) {
                $player->sendMessage(C::RED . C::BOLD . "[!]" . C::RESET . C::GRAY . " Please wait $remainingCooldown seconds before opening another pouch.");
                return;
            }
        }
        if ($tag->getTag("moneypouch")) {
            $pouchTag = $tag->getString("moneypouch");
            if ($pouchTag === "tier1") {
                $this->pouchCooldown[$player->getName()] = $currentTime + $cooldownDuration;

                Nyx::getInstance()->getScheduler()->scheduleDelayedTask(
                    new ClosureTask(function () use ($player) {
                        unset($this->pouchCooldown[$player->getName()]);
                    }),
                    $cooldownDuration * 20
                );
                $item->setCount($item->getCount() - 1);
                $player->getInventory()->setItemInHand($item);
                $minAmount = $config->get("moneypouches.$pouchTag.min", 1000);
                $maxAmount = $config->get("moneypouches.$pouchTag.max", 25000);
                $money = mt_rand($minAmount, $maxAmount);
                $obfuscatedTitle = "§k" . str_repeat("#", strlen((string)$money));
                $player->sendTitle($obfuscatedTitle, "§r§6Opening pouch...", 1, 2);

                $revealedDigits = "";
                $task = new TitleRevealTask($player, $money, $revealedDigits);
                Nyx::getInstance()->getScheduler()->scheduleRepeatingTask($task, 10);
            }
        }
        if ($tag->getTag("moneypouch")) {
            $pouchTag = $tag->getString("moneypouch");
            if ($pouchTag === "tier2") {
                $this->pouchCooldown[$player->getName()] = $currentTime + $cooldownDuration;

                Nyx::getInstance()->getScheduler()->scheduleDelayedTask(
                    new ClosureTask(function () use ($player) {
                        unset($this->pouchCooldown[$player->getName()]);
                    }),
                    $cooldownDuration * 20
                );
                $item->setCount($item->getCount() - 1);
                $player->getInventory()->setItemInHand($item);
                $minAmount = $config->get("moneypouches.$pouchTag.min", 25000);
                $maxAmount = $config->get("moneypouches.$pouchTag.max", 150000);
                $money = mt_rand($minAmount, $maxAmount);
                $obfuscatedTitle = "§k" . str_repeat("#", strlen((string)$money));
                $player->sendTitle($obfuscatedTitle, "§r§6Opening pouch...", 1, 2);

                $revealedDigits = "";
                $task = new TitleRevealTask($player, $money, $revealedDigits);
                Nyx::getInstance()->getScheduler()->scheduleRepeatingTask($task, 10);
            }
        }
        if ($tag->getTag("moneypouch")) {
            $pouchTag = $tag->getString("moneypouch");
            if ($pouchTag === "tier3") {
                $this->pouchCooldown[$player->getName()] = $currentTime + $cooldownDuration;

                Nyx::getInstance()->getScheduler()->scheduleDelayedTask(
                    new ClosureTask(function () use ($player) {
                        unset($this->pouchCooldown[$player->getName()]);
                    }),
                    $cooldownDuration * 20
                );
                $item->setCount($item->getCount() - 1);
                $player->getInventory()->setItemInHand($item);
                $minAmount = $config->get("moneypouches.$pouchTag.min", 150000);
                $maxAmount = $config->get("moneypouches.$pouchTag.max", 250000);
                $money = mt_rand($minAmount, $maxAmount);
                $obfuscatedTitle = "§k" . str_repeat("#", strlen((string)$money));
                $player->sendTitle($obfuscatedTitle, "§r§6Opening pouch...", 1, 2);

                $revealedDigits = "";
                $task = new TitleRevealTask($player, $money, $revealedDigits);
                Nyx::getInstance()->getScheduler()->scheduleRepeatingTask($task, 10);
            }
        }
        if ($tag->getTag("moneypouch")) {
            $pouchTag = $tag->getString("moneypouch");
            if ($pouchTag === "tier4") {
                $this->pouchCooldown[$player->getName()] = $currentTime + $cooldownDuration;

                Nyx::getInstance()->getScheduler()->scheduleDelayedTask(
                    new ClosureTask(function () use ($player) {
                        unset($this->pouchCooldown[$player->getName()]);
                    }),
                    $cooldownDuration * 20
                );
                $item->setCount($item->getCount() - 1);
                $player->getInventory()->setItemInHand($item);
                $minAmount = $config->get("moneypouches.$pouchTag.min", 250000);
                $maxAmount = $config->get("moneypouches.$pouchTag.max", 550000);
                $money = mt_rand($minAmount, $maxAmount);
                $obfuscatedTitle = "§k" . str_repeat("#", strlen((string)$money));
                $player->sendTitle($obfuscatedTitle, "§r§6Opening pouch...", 1, 2);

                $revealedDigits = "";
                $task = new TitleRevealTask($player, $money, $revealedDigits);
                Nyx::getInstance()->getScheduler()->scheduleRepeatingTask($task, 10);
            }
        }
        if ($tag->getTag("moneypouch")) {
            $pouchTag = $tag->getString("moneypouch");
            if ($pouchTag === "tier5") {
                $this->pouchCooldown[$player->getName()] = $currentTime + $cooldownDuration;

                Nyx::getInstance()->getScheduler()->scheduleDelayedTask(
                    new ClosureTask(function () use ($player) {
                        unset($this->pouchCooldown[$player->getName()]);
                    }),
                    $cooldownDuration * 20
                );
                $item->setCount($item->getCount() - 1);
                $player->getInventory()->setItemInHand($item);
                $minAmount = $config->get("moneypouches.$pouchTag.min", 1000000);
                $maxAmount = $config->get("moneypouches.$pouchTag.max", 2000000);
                $money = mt_rand($minAmount, $maxAmount);
                $obfuscatedTitle = "§k" . str_repeat("#", strlen((string)$money));
                $player->sendTitle($obfuscatedTitle, "§r§6Opening pouch...", 1, 2);

                $revealedDigits = "";
                $task = new TitleRevealTask($player, $money, $revealedDigits);
                Nyx::getInstance()->getScheduler()->scheduleRepeatingTask($task, 10);
            }
        }

        if ($tag->getTag("xppouch")) {
            $pouchTag = $tag->getString("xppouch");
            if ($pouchTag === "xptier1") {
                $this->pouchCooldown[$player->getName()] = $currentTime + $cooldownDuration;

                Nyx::getInstance()->getScheduler()->scheduleDelayedTask(
                    new ClosureTask(function () use ($player) {
                        unset($this->pouchCooldown[$player->getName()]);
                    }),
                    $cooldownDuration * 20
                );
                $item->setCount($item->getCount() - 1);
                $player->getInventory()->setItemInHand($item);
                $minAmount = $config->get("moneypouches.$pouchTag.min", 1000);
                $maxAmount = $config->get("moneypouches.$pouchTag.max", 25000);
                $experience = mt_rand($minAmount, $maxAmount);
                $obfuscatedTitle = "§k" . str_repeat("#", strlen((string)$experience));
                $player->sendTitle($obfuscatedTitle, "§r§6Opening pouch...", 1, 2);

                $revealedDigits = "";
                $task = new TitleRevealXpTask($player, $experience, $revealedDigits);
                Nyx::getInstance()->getScheduler()->scheduleRepeatingTask($task, 10);
            }
        }

        if ($tag->getTag("xppouch")) {
            $pouchTag = $tag->getString("xppouch");
            if ($pouchTag === "xptier2") {
                $this->pouchCooldown[$player->getName()] = $currentTime + $cooldownDuration;

                Nyx::getInstance()->getScheduler()->scheduleDelayedTask(
                    new ClosureTask(function () use ($player) {
                        unset($this->pouchCooldown[$player->getName()]);
                    }),
                    $cooldownDuration * 20
                );
                $item->setCount($item->getCount() - 1);
                $player->getInventory()->setItemInHand($item);
                $minAmount = $config->get("moneypouches.$pouchTag.min", 25000);
                $maxAmount = $config->get("moneypouches.$pouchTag.max", 35000);
                $experience = mt_rand($minAmount, $maxAmount);
                $obfuscatedTitle = "§k" . str_repeat("#", strlen((string)$experience));
                $player->sendTitle($obfuscatedTitle, "§r§6Opening pouch...", 1, 2);

                $revealedDigits = "";
                $task = new TitleRevealXpTask($player, $experience, $revealedDigits);
                Nyx::getInstance()->getScheduler()->scheduleRepeatingTask($task, 10);
            }
        }

        if ($tag->getTag("xppouch")) {
            $pouchTag = $tag->getString("xppouch");
            if ($pouchTag === "xptier3") {
                $this->pouchCooldown[$player->getName()] = $currentTime + $cooldownDuration;

                Nyx::getInstance()->getScheduler()->scheduleDelayedTask(
                    new ClosureTask(function () use ($player) {
                        unset($this->pouchCooldown[$player->getName()]);
                    }),
                    $cooldownDuration * 20
                );
                $item->setCount($item->getCount() - 1);
                $player->getInventory()->setItemInHand($item);
                $minAmount = $config->get("moneypouches.$pouchTag.min", 35000);
                $maxAmount = $config->get("moneypouches.$pouchTag.max", 45000);
                $experience = mt_rand($minAmount, $maxAmount);
                $obfuscatedTitle = "§k" . str_repeat("#", strlen((string)$experience));
                $player->sendTitle($obfuscatedTitle, "§r§6Opening pouch...", 1, 2);

                $revealedDigits = "";
                $task = new TitleRevealXpTask($player, $experience, $revealedDigits);
                Nyx::getInstance()->getScheduler()->scheduleRepeatingTask($task, 10);
            }
        }

        if ($tag->getTag("xppouch")) {
            $pouchTag = $tag->getString("xppouch");
            if ($pouchTag === "xptier4") {
                $this->pouchCooldown[$player->getName()] = $currentTime + $cooldownDuration;

                Nyx::getInstance()->getScheduler()->scheduleDelayedTask(
                    new ClosureTask(function () use ($player) {
                        unset($this->pouchCooldown[$player->getName()]);
                    }),
                    $cooldownDuration * 20
                );
                $item->setCount($item->getCount() - 1);
                $player->getInventory()->setItemInHand($item);
                $minAmount = $config->get("moneypouches.$pouchTag.min", 45000);
                $maxAmount = $config->get("moneypouches.$pouchTag.max", 55000);
                $experience = mt_rand($minAmount, $maxAmount);
                $obfuscatedTitle = "§k" . str_repeat("#", strlen((string)$experience));
                $player->sendTitle($obfuscatedTitle, "§r§6Opening pouch...", 1, 2);

                $revealedDigits = "";
                $task = new TitleRevealXpTask($player, $experience, $revealedDigits);
                Nyx::getInstance()->getScheduler()->scheduleRepeatingTask($task, 10);
            }
        }

        if ($tag->getTag("xppouch")) {
            $pouchTag = $tag->getString("xppouch");
            if ($pouchTag === "xptier5") {
                $this->pouchCooldown[$player->getName()] = $currentTime + $cooldownDuration;

                Nyx::getInstance()->getScheduler()->scheduleDelayedTask(
                    new ClosureTask(function () use ($player) {
                        unset($this->pouchCooldown[$player->getName()]);
                    }),
                    $cooldownDuration * 20
                );
                $item->setCount($item->getCount() - 1);
                $player->getInventory()->setItemInHand($item);
                $minAmount = $config->get("moneypouches.$pouchTag.min", 55000);
                $maxAmount = $config->get("moneypouches.$pouchTag.max", 65000);
                $experience = mt_rand($minAmount, $maxAmount);
                $obfuscatedTitle = "§k" . str_repeat("#", strlen((string)$experience));
                $player->sendTitle($obfuscatedTitle, "§r§6Opening pouch...", 1, 2);

                $revealedDigits = "";
                $task = new TitleRevealXpTask($player, $experience, $revealedDigits);
                Nyx::getInstance()->getScheduler()->scheduleRepeatingTask($task, 10);
            }
        }
    }

    /**
     * @priority HIGHEST
     */
    public function onPlayerDropUnbreakingFragment(InventoryTransactionEvent $event): void
    {
        $transaction = $event->getTransaction();
        $actions = array_values($transaction->getActions());
        if (count($actions) === 2) {
            foreach ($actions as $i => $action) {
                $ids = [VanillaItems::DIAMOND_HELMET()->getTypeId(), VanillaItems::DIAMOND_CHESTPLATE()->getTypeId(), VanillaItems::DIAMOND_LEGGINGS()->getTypeId(), VanillaItems::DIAMOND_BOOTS()->getTypeId()];
                if ($action instanceof SlotChangeAction && ($otherAction = $actions[($i + 1) % 2]) instanceof SlotChangeAction && ($itemClickedWith = $action->getTargetItem())->getTypeId() === VanillaItems::IRON_INGOT()->getTypeId() && ($itemClicked = $action->getSourceItem())->getTypeId() !== VanillaItems::AIR()->getTypeId() && in_array($itemClicked->getTypeId(), $ids) && $itemClickedWith->getCount() === 1 && $itemClickedWith->getNamedTag()->getString("enchantmentfragment", "") !== "") {
                    if ($itemClicked->getNamedTag()->getString("enchantmentfragment", "") === "unbreakingv") {
                        $event->getTransaction()->getSource()->sendMessage("§r§c§l(!) §r§cYou cannot do that!");
                        $transaction->getSource()->getWorld()->addSound($transaction->getSource()->getLocation(), new AnvilFallSound());
                        return;
                    }
                    $event->cancel();

                    if ($itemClicked->getNamedTag()->getString("enchantmentfragment", "") !== "unbreakingv") {
                        $itemClicked->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 5));
                        $itemClicked->getNamedTag()->setString("enchantmentfragment", "unbreakingv");
                        $itemClicked->getNamedTag()->setInt("enchantmentfragmenttier", $itemClickedWith->getNamedTag()->getInt("enchantmentfragmenttier"));
                    }

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
    public function onPlayerDropThornsFragment(InventoryTransactionEvent $event): void
    {
        $transaction = $event->getTransaction();
        $actions = array_values($transaction->getActions());
        if (count($actions) === 2) {
            foreach ($actions as $i => $action) {
                $ids = [VanillaItems::DIAMOND_HELMET()->getTypeId(), VanillaItems::DIAMOND_CHESTPLATE()->getTypeId(), VanillaItems::DIAMOND_LEGGINGS()->getTypeId(), VanillaItems::DIAMOND_BOOTS()->getTypeId()];
                if ($action instanceof SlotChangeAction && ($otherAction = $actions[($i + 1) % 2]) instanceof SlotChangeAction && ($itemClickedWith = $action->getTargetItem())->getTypeId() === VanillaItems::REDSTONE_DUST()->getTypeId() && ($itemClicked = $action->getSourceItem())->getTypeId() !== VanillaItems::AIR()->getTypeId() && in_array($itemClicked->getTypeId(), $ids) && $itemClickedWith->getCount() === 1 && $itemClickedWith->getNamedTag()->getString("enchantmentfragment", "") !== "") {
                    if ($itemClicked->getNamedTag()->getString("enchantmentfragment", "") === "thornsiii") {
                        $event->getTransaction()->getSource()->sendMessage("§r§c§l(!) §r§cYou cannot do that!");
                        $transaction->getSource()->getWorld()->addSound($transaction->getSource()->getLocation(), new AnvilFallSound());
                        return;
                    }
                    $event->cancel();

                    if ($itemClicked->getNamedTag()->getString("enchantmentfragment", "") !== "thornsiii") {
                        $itemClicked->addEnchantment(new EnchantmentInstance(VanillaEnchantments::THORNS(), 3));
                        $itemClicked->getNamedTag()->setString("enchantmentfragment", "thornsiii");
                        $itemClicked->getNamedTag()->setInt("enchantmentfragmenttier", $itemClickedWith->getNamedTag()->getInt("enchantmentfragmenttier"));
                    }

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
    public function onPlayerDropDepthStriderFragment(InventoryTransactionEvent $event): void
    {
        $transaction = $event->getTransaction();
        $actions = array_values($transaction->getActions());
        if (count($actions) === 2) {
            foreach ($actions as $i => $action) {
                $ids = [VanillaItems::DIAMOND_BOOTS()->getTypeId()];
                if ($action instanceof SlotChangeAction && ($otherAction = $actions[($i + 1) % 2]) instanceof SlotChangeAction && ($itemClickedWith = $action->getTargetItem())->getTypeId() === VanillaItems::LAPIS_LAZULI()->getTypeId() && ($itemClicked = $action->getSourceItem())->getTypeId() !== VanillaItems::AIR()->getTypeId() && in_array($itemClicked->getTypeId(), $ids) && $itemClickedWith->getCount() === 1 && $itemClickedWith->getNamedTag()->getString("enchantmentfragment", "") !== "") {
                    if ($itemClicked->getNamedTag()->getString("enchantmentfragment", "") === "depthstrideriii") {
                        $event->getTransaction()->getSource()->sendMessage("§r§c§l(!) §r§cYou cannot do that!");
                        $transaction->getSource()->getWorld()->addSound($transaction->getSource()->getLocation(), new AnvilFallSound());
                        return;
                    }
                    $event->cancel();

                    if ($itemClicked->getNamedTag()->getString("enchantmentfragment", "") !== "depthstrideriii") {
                        $itemClicked->addEnchantment(new EnchantmentInstance(StringToEnchantmentParser::getInstance()->parse("depth_strider"), 3));
                        $itemClicked->getNamedTag()->setString("enchantmentfragment", "depthstrideriii");
                        $itemClicked->getNamedTag()->setInt("enchantmentfragmenttier", $itemClickedWith->getNamedTag()->getInt("enchantmentfragmenttier"));
                    }

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
    public function onPlayerDropLootingFragment(InventoryTransactionEvent $event): void
    {
        $transaction = $event->getTransaction();
        $actions = array_values($transaction->getActions());
        if (count($actions) === 2) {
            foreach ($actions as $i => $action) {
                $ids = [VanillaItems::DIAMOND_SWORD()->getTypeId()];
                if ($action instanceof SlotChangeAction && ($otherAction = $actions[($i + 1) % 2]) instanceof SlotChangeAction && ($itemClickedWith = $action->getTargetItem())->getTypeId() === VanillaItems::GOLD_INGOT()->getTypeId() && ($itemClicked = $action->getSourceItem())->getTypeId() !== VanillaItems::AIR()->getTypeId() && in_array($itemClicked->getTypeId(), $ids) && $itemClickedWith->getCount() === 1 && $itemClickedWith->getNamedTag()->getString("enchantmentfragment", "") !== "") {
                    if ($itemClicked->getNamedTag()->getString("enchantmentfragment", "") === "lootingv") {
                        $event->getTransaction()->getSource()->sendMessage("§r§c§l(!) §r§cYou cannot do that!");
                        $transaction->getSource()->getWorld()->addSound($transaction->getSource()->getLocation(), new AnvilFallSound());
                        return;
                    }
                    $event->cancel();

                    if ($itemClicked->getNamedTag()->getString("enchantmentfragment", "") !== "lootingv") {
                        $itemClicked->addEnchantment(new EnchantmentInstance(StringToEnchantmentParser::getInstance()->parse("looting"), 5));
                        $itemClicked->getNamedTag()->setString("enchantmentfragment", "lootingv");
                        $itemClicked->getNamedTag()->setInt("enchantmentfragmenttier", $itemClickedWith->getNamedTag()->getInt("enchantmentfragmenttier"));
                    }

                    Utils::spawnParticleV2($event->getTransaction()->getSource(), "minecraft:villager_happy");
                    $action->getInventory()->setItem($action->getSlot(), $itemClicked);
                    $otherAction->getInventory()->setItem($otherAction->getSlot(), VanillaItems::AIR());
                    $transaction->getSource()->getWorld()->addSound($transaction->getSource()->getLocation(), new XpLevelUpSound(100));
                    return;
                }
            }
        }
    }

    public function onUseGearLootbox(PlayerItemUseEvent $event)
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $tag = $item->getNamedTag();

        if ($tag->getTag("gearlootbox")) {
            $generatorTag = $tag->getString("gearlootbox");
            if ($generatorTag === "true") {
                $item->setCount($item->getCount() - 1);
                $player->getInventory()->setItemInHand($item);
                $protection = new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 4);
                $unbreaking = new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3);

                $armorPieces = [
                    VanillaItems::DIAMOND_HELMET()->setCustomName("§r§l§bHelmet")->addEnchantment($protection)->addEnchantment($unbreaking),
                    VanillaItems::DIAMOND_CHESTPLATE()->setCustomName("§r§l§bChestplate")->addEnchantment($protection)->addEnchantment($unbreaking),
                    VanillaItems::DIAMOND_LEGGINGS()->setCustomName("§r§l§bLeggings")->addEnchantment($protection)->addEnchantment($unbreaking),
                    VanillaItems::DIAMOND_BOOTS()->setCustomName("§r§l§bBoots")->addEnchantment($protection)->addEnchantment($unbreaking)
                ];

                $randomArmorPiece = $armorPieces[array_rand($armorPieces)];

                $player->getInventory()->addItem($randomArmorPiece);

                Utils::spawnParticleV2($player, "minecraft:endrod");
                Utils::playSound($player, "item.book.page_turn");
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function onUseLootbox(PlayerItemUseEvent $event) {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $tag = $item->getNamedTag();
        if ($tag->getTag("lootbox")) {
            $lootboxTag = $tag->getString("lootbox");
            if ($lootboxTag === "player") {
                $item->setCount($item->getCount() - 1);
                $player->getInventory()->setItemInHand($item);

                $loot = [
                    Rewards::get(Rewards::XPGENERATOR),
                    Rewards::get(Rewards::MONEYGENERATOR)
                ];

                $randomLoot = $loot[array_rand($loot)];

                $player->getInventory()->addItem($randomLoot);

                Utils::playSound($player, "item.book.page_turn");
            }
        }
    }
}