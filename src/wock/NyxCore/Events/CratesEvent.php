<?php

# Namespace
namespace wock\NyxCore\Events;

# Pocketmine Packages
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\DeterministicInvMenuTransaction;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\VanillaItems;
use pocketmine\Server;
use wock\NyxCore\Items\Rewards;
use wock\NyxCore\Nyx;

# Listener Class
class CratesEvent implements Listener {

    public Nyx $plugin;
    # Listener Constructor
    public function __construct(Nyx $plugin) {
        $this->plugin = $plugin;
    }

    # Interaction Listener
    public function onInteraction(PlayerInteractEvent $event)
    {

        // Variables
        $block = $event->getBlock();
        $player = $event->getPlayer();
        $x = $block->getPosition()->getFloorX();
        $y = $block->getPosition()->getFloorY();
        $z = $block->getPosition()->getFloorZ();
        $world = $player->getWorld()->getFolderName();
        $keyGrinder = Rewards::createCrateKey(0);
        $keyGodly = Rewards::createCrateKey(1);
        $keyOp = Rewards::createCrateKey(2);
        $keyMutated = Rewards::createCrateKey(3);
        $keyVote = Rewards::createCrateKey(4);

        // Check if Crate
        if ($world === "world") {
            if ($block->getTypeId() === 138) {


                /////////////////////////////// GRINDER CRATE ///////////////////////////////
                if ($x === 20 && $y === 74 && $z === 45) {
                    // Cancel Opening Chests
                    $event->cancel();
                    // Determine Value
                    if ($player->isSneaking()) {
                        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
                        $menu->setName("§r§l§6Grinder Crate");
                        $inv = $menu->getInventory();
                        $basic = "§r§7§l* BASIC *";
                        $meta = "§r§l§c* META *";
                        $iteminhand = $player->getInventory()->getItemInHand();
                        $inv->addItem(VanillaItems::ENDER_PEARL()->setCount(16)->setLore([$basic]));
                        $inv->addItem(Rewards::createXPBottle(null, mt_rand(1000, 1000))->setLore(["§r§7A valuable Experience Bottle that can be redeemed for its value.", "§r§7Simply right-click while holding it to redeem.", " ", "§r§l§aValue§r§f: $ §r§f§kahx:\a", "§r§l§aEnchanter§r§f: Starfall", $meta]));
                        $inv->addItem(Rewards::createMoneyNote(null, mt_rand(10000, 100000))->setLore(["§r§7A valuable Bank Note that can be redeemed for its value.", "§r§7Simply right-click while holding it to redeem.", " ", "§r§l§bValue§r§f: $ §r§f§kahx:\a", "§r§l§bSigner§r§f: Starfall", $meta]));
                        $inv->addItem(VanillaItems::DIAMOND_SWORD()->setCount(1)->setCustomName("§r§l§6Grinder Sword")->setLore(["§r§7A special grinder sword", "§r§7jk sh! t testing", $basic]));
                        $inv->addItem(VanillaItems::GOLD_NUGGET()->setCount(1)->setCustomName("§r§l§eGodly §fCrate Key")->setLore(["§r§7Right-Click on a §eGodly §7Crate to open.", "", "§r§l§e(!) §r§eType §f/warp crates §eto open this crate key.", "", $basic]));


                        // Set a callback function for when an item is clicked in the menu
                        $menu->setListener(InvMenu::readonly(function (DeterministicInvMenuTransaction $transaction) use ($player) {
                        }));

                        // Send the menu to the Player
                        $menu->send($player);
                    }
                    $item = $event->getItem();
                    if ($item instanceof Item && $item->getTypeId() === VanillaItems::GOLD_NUGGET() && $item->getNamedTag()->getTag("cratekey") !== null) {
                        $keyTag = $item->getNamedTag()->getString("cratekey");
                        if ($keyTag === "grinder") {
                            if (!$player->isSneaking()) {
                                $hand = $event->getItem();
                                if ($hand->getCount() > 1) {
                                    $hand->setCount($hand->getCount() - 1);
                                    $player->getInventory()->setItemInHand($hand);
                                } else {
                                    $player->getInventory()->removeItem($hand);
                                }
                                $this->rollGrinder($player);
                                return;
                            }
                            $player->sendActionBarMessage("§l§8(§4!§8) §r§cYou do not have any §l§6Grinder Keys§r§c!");
                        }
                    }
                }

                /////////////////////////////// GODLY CRATE ///////////////////////////////
                if ($x === 20 && $y === 74 && $z === 41) {
                    // Cancel Opening Chests
                    $event->cancel();
                    // Determine Value
                    if ($player->isSneaking()) {
                        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
                        $menu->setName("§r§l§eGodly Crate");
                        $inv = $menu->getInventory();
                        $inv->addItem(Rewards::createXPBottle(null, mt_rand(1000, 1000)));
                        $inv->addItem(Rewards::createXPBottle(null, mt_rand(10000, 100000)));


                        // Set a callback function for when an item is clicked in the menu
                        $menu->setListener(InvMenu::readonly(function (DeterministicInvMenuTransaction $transaction) use ($player) {
                        }));

                        // Send the menu to the Player
                        $menu->send($player);
                    }
                    $item = $event->getItem();
                    if ($item instanceof Item && $item->getTypeId() === VanillaItems::GOLD_NUGGET() && $item->getNamedTag()->getTag("cratekey") !== null) {
                        $keyTag = $item->getNamedTag()->getString("cratekey");
                        if ($keyTag === "godly") {
                            if (!$player->isSneaking()) {
                                $hand = $event->getItem();
                                if ($hand->getCount() > 1) {
                                    $hand->setCount($hand->getCount() - 1);
                                    $player->getInventory()->setItemInHand($hand);
                                } else {
                                    $player->getInventory()->removeItem($hand);
                                }
                                $this->rollGodly($player);
                                return;
                            }
                            $player->sendTip("§l§8(§4!§8) §r§cYou do not have any §l§eGodly Keys§r§c!");
                        }
                    }
                }

                /////////////////////////////// OP CRATE ///////////////////////////////
                if ($x === 23 && $y === 74 && $z === 38) {
                    // Cancel Opening Chests
                    $event->cancel();
                    // Determine Value
                    if ($player->isSneaking()) {
                        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
                        $menu->setName("§r§l§4OP Crate");
                        $inv = $menu->getInventory();
                        $inv->addItem(Rewards::createXPBottle(null, mt_rand(1000, 1000)));
                        $inv->addItem(Rewards::createXPBottle(null, mt_rand(10000, 100000)));
                        // Add more items as needed


                        // Set a callback function for when an item is clicked in the menu
                        $menu->setListener(InvMenu::readonly(function (DeterministicInvMenuTransaction $transaction) use ($player) {
                        }));

                        // Send the menu to the Player
                        $menu->send($player);
                    }
                    $item = $event->getItem();
                    if ($item instanceof Item && $item->getTypeId() === VanillaItems::GOLD_NUGGET() && $item->getNamedTag()->getTag("cratekey") !== null) {
                        $keyTag = $item->getNamedTag()->getString("cratekey");
                        if ($keyTag === "op") {
                            if (!$player->isSneaking()) {
                                $hand = $event->getItem();
                                if ($hand->getCount() > 1) {
                                    $hand->setCount($hand->getCount() - 1);
                                    $player->getInventory()->setItemInHand($hand);
                                } else {
                                    $player->getInventory()->removeItem($hand);
                                }
                                $this->rollOp($player);
                                return;
                            }
                            $player->sendTip("§l§8(§4!§8) §r§cYou do not have any §l§4OP Keys§r§c!");
                        }
                    }
                }

                /////////////////////////////// MUTATED CRATE ///////////////////////////////
                if ($x === 27 && $y === 74 && $z === 38) {
                    // Cancel Opening Chests
                    $event->cancel();
                    // Determine Value
                    if ($player->isSneaking()) {
                        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
                        $menu->setName("§r§l§2Mutated Crate");
                        $inv = $menu->getInventory();
                        $inv->addItem(Rewards::createXPBottle(null, mt_rand(1000, 1000)));
                        $inv->addItem(Rewards::createXPBottle(null, mt_rand(10000, 100000)));

                        // Set a callback function for when an item is clicked in the menu
                        $menu->setListener(InvMenu::readonly(function (DeterministicInvMenuTransaction $transaction) use ($player) {
                        }));

                        // Send the menu to the Player
                        $menu->send($player);
                    }
                    $item = $event->getItem();
                    if ($item instanceof Item && $item->getTypeId() === VanillaItems::GOLD_NUGGET() && $item->getNamedTag()->getTag("cratekey") !== null) {
                        $keyTag = $item->getNamedTag()->getString("cratekey");
                        if ($keyTag === "mutated") {
                            if (!$player->isSneaking()) {
                                $hand = $event->getItem();
                                if ($hand->getCount() > 1) {
                                    $hand->setCount($hand->getCount() - 1);
                                    $player->getInventory()->setItemInHand($hand);
                                } else {
                                    $player->getInventory()->removeItem($hand);
                                }
                                $this->rollMutated($player);
                                return;
                            }
                            $player->sendTip("§l§8(§4!§8) §r§cYou do not have any §l§2Mutated Keys§r§c!");
                        }
                    }
                }

                /////////////////////////////// VOTE CRATE ///////////////////////////////
                if ($x === 30 && $y === 74 && $z === 41) {
                    // Cancel Opening Chests
                    $event->cancel();
                    // Determine Value
                    if ($player->isSneaking()) {
                        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
                        $menu->setName("§r§l§5Vote Crate");
                        $inv = $menu->getInventory();
                        $inv->addItem(Rewards::createXPBottle(null, mt_rand(1000, 1000)));
                        $inv->addItem(Rewards::createXPBottle(null, mt_rand(10000, 100000)));
                        // Add more items as needed


                        // Set a callback function for when an item is clicked in the menu
                        $menu->setListener(InvMenu::readonly(function (DeterministicInvMenuTransaction $transaction) use ($player) {
                        }));

                        // Send the menu to the Player
                        $menu->send($player);
                    }
                    $item = $event->getItem();
                    if ($item instanceof Item && $item->getTypeId() === VanillaItems::GOLD_NUGGET() && $item->getNamedTag()->getTag("cratekey") !== null) {
                        $keyTag = $item->getNamedTag()->getString("cratekey");
                        if ($keyTag === "vote") {
                            if (!$player->isSneaking()) {
                                $hand = $event->getItem();
                                if ($hand->getCount() > 1) {
                                    $hand->setCount($hand->getCount() - 1);
                                    $player->getInventory()->setItemInHand($hand);
                                } else {
                                    $player->getInventory()->removeItem($hand);
                                }
                                $this->rollVote($player);
                                return;
                            }
                            $player->sendTip("§l§8(§4!§8) §r§cYou do not have any §l§5Vote Keys§r§c!");
                        }

                    }
                }
            }
        }
    }

    /////////////////////////////// GRINDER ROLL ///////////////////////////////
    public function rollGrinder($player) {

        // Variables
        $chance = mt_rand(1, 2);

        // Meta Rewards
        if (mt_rand(1, 150) === mt_rand(1, 150)) {
            // Money Note
            $amount = mt_rand(1, 1);
            $item = Rewards::createMoneyNote(null, mt_rand(10000, 100000));
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§b** Bank Note ** §r§a.");
            return;
        }
        if (mt_rand(1, 150) === mt_rand(1, 150)) {
            // Xp Bottle
            $amount = mt_rand(1, 1);
            $item = Rewards::createXPBottle(null, mt_rand(1000, 1000));
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§a** Experience Bottle **§r§a.");
            return;
        }

        // Basic Rewards
        switch ($chance) {
            case 1:
                // Ender Pearl
                $amount = 16;
                $item = VanillaItems::ENDER_PEARL()->setCount($amount);
                foreach ($player->getInventory()->addItem($item) as $invfull) {
                    $player->getWorld()->dropItem($player->getPosition(), $invfull);
                }
                $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§6Money Pouch§r§a.");
                break;
            case 2:
                // Temp
                $amount = 1;
                $item = VanillaItems::DIAMOND_SWORD()->setCount($amount);
                $item->setCustomName("§r§6Grinder Sword");
                $item->setLore([
                    "§r§7A special grinder sword",
                    "§r§7jk sh! t testing"
                ]);
                foreach ($player->getInventory()->addItem($item) as $invfull) {
                    $player->getWorld()->dropItem($player->getPosition(), $invfull);
                }
                $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§6Grinder Sword§r§a.");
                break;
            case 3:
                // Crate Key
                $amount = 1;
                Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), "key");
                $player->sendTip("§r§l§8(§2!§8) §r§aYou have received " . $amount . "x §r§l§eGodly §fCrate Key§r§a.");
        }

    }

    /////////////////////////////// GODLY ROLL ///////////////////////////////
    public function rollGodly($player) {

        // Variables
        $chance = mt_rand(1, 2);

        // Lucky Rewards
        if (mt_rand(1, 1500) === mt_rand(1, 1500)) {
            // Feed Permissions
            $amount = mt_rand(1, 1);
            $item = VanillaItems::PAPER();
            $item->setCustomName("§r§eFeed Permissions");
            $item->getNamedTag()->setByte("FeedPermissions", 1);
            $item->setLore([
                "§r§7Examine this note to receive permissions",
                "§r§7for the Command /feed.",
                "§r§8 * §7Right-click to examine this note."
            ]);
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§eFeed Permissions§r§a.");
            $this->plugin->getServer()->broadcastMessage("§l§eCrates §8>> §r§7" . $player->getName() . " has won a §l§eFeed Permissions§r§7 from a Uncommon Crate!");
            return;
        }
        if (mt_rand(1, 1500) === mt_rand(1, 1500)) {
            // Heal Permissions
            $amount = mt_rand(1, 1);
            $item = VanillaItems::PAPER();
            $item->setCustomName("§r§eHeal Permissions");
            $item->getNamedTag()->setByte("HealPermissions", 1);
            $item->setLore([
                "§r§7Examine this note to receive permissions",
                "§r§7for the Command /heal.",
                "§r§8 * §7Right-click to examine this note."
            ]);
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§eHeal Permissions§r§a.");
            $this->plugin->getServer()->broadcastMessage("§l§eCrates §8>> §r§7" . $player->getName() . " has won a §l§eHeal Permissions§r§7 from a Uncommon Crate!");
            return;
        }
        if (mt_rand(1, 1500) === mt_rand(1, 1500)) {
            // Fly Permissions
            $amount = mt_rand(1, 1);
            $item = VanillaItems::PAPER();
            $item->setCustomName("§r§eFly Permissions");
            $item->getNamedTag()->setByte("FlyPermissions", 1);
            $item->setLore([
                "§r§7Examine this note to receive permissions",
                "§r§7for the Command /fly.",
                "§r§8 * §7Right-click to examine this note."
            ]);
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§eFly Permissions§r§a.");
            $this->plugin->getServer()->broadcastMessage("§l§eCrates §8>> §r§7" . $player->getName() . " has won a §l§eFly Permissions§r§7 from a Uncommon Crate!");
            return;
        }
        if (mt_rand(1, 750) === mt_rand(1, 750)) {
            // Warlord Crystal
            $amount = mt_rand(1, 1);
            $item = VanillaItems::PAPER();
            $item->setCustomName("§r§3Warlord Crystal");
            $item->getNamedTag()->setByte("WarlordCrystal", 1);
            $item->setLore([
                "§r§7Has a chance to make you rankup to Warlord rank.",
                "§r§7Only works if you have the Warrior rank.",
                "§r§8 * §7Right-click to shatter the crystal.",
                "§r§8 * §7Warrior -> Warlord"
            ]);
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§3Warlord Crystal§r§a.");
            $this->plugin->getServer()->broadcastMessage("§l§eCrates §8>> §r§7" . $player->getName() . " has won a §l§3Warlord Crystal§r§7 from a Uncommon Crate!");
            return;
        }
        if (mt_rand(1, 750) === mt_rand(1, 750)) {
            // Warrior Crystal
            $amount = mt_rand(1, 1);
            $item = VanillaItems::PAPER();
            $item->setCustomName("§r§eWarrior Crystal");
            $item->getNamedTag()->setByte("WarriorCrystal", 1);
            $item->setLore([
                "§r§7Has a chance to make you rankup to Warrior rank.",
                "§r§7Only works if you have the Knight rank.",
                "§r§8 * §7Right-click to shatter the crystal.",
                "§r§8 * §7Knight -> Warrior"
            ]);
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§eWarrior Crystal§r§a.");
            $this->plugin->getServer()->broadcastMessage("§l§eCrates §8>> §r§7" . $player->getName() . " has won a §l§eWarrior Crystal§r§7 from a Uncommon Crate!");
            return;
        }
        if (mt_rand(1, 50) === mt_rand(1, 50)) {
            // Mystery Tag
            $amount = mt_rand(1, 2);
            $item = VanillaItems::PAPER();
            $item->setCustomName("§r§5Mystery Tag");
            $item->getNamedTag()->setByte("MysteryTag", 1);
            $item->setLore([
                "§r§7An item containing a random tag.",
                "§r§8 * §7Right-click to reveal the tag.",
            ]);
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§5Mystery Tag§r§a.");
            return;
        }
        if (mt_rand(1, 50) === mt_rand(1, 50)) {
            // Epic Book
            $amount = mt_rand(1, 2);
            $item = VanillaItems::PAPER();
            $item->setCustomName("§r§eEpic Book");
            $item->getNamedTag()->setByte("EpicBook", 1);
            $item->setLore([
                "§r§7Examine this book to receive a random",
                "§r§eEpic §7enchantment book.",
                "§r§8 * §7Right-click to examine this book.",
                "§r§8 * §7Tier: §aII"
            ]);
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§eElite Book§r§a.");
            return;
        }
        if (mt_rand(1, 30) === mt_rand(1, 30)) {
            // Mystery Orb
            $amount = mt_rand(1, 1);
            $item = VanillaItems::PAPER();
            $item->setCustomName("§r§5Mystery Effect Orb");
            $item->getNamedTag()->setByte("MysteryOrb", 1);
            $item->setLore([
                "§r§7Examine this orb to receive a random effect orb.",
                "§r§8 * §7Right-click to examine this orb.",
            ]);
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§5Mystery Orb§r§a.");
            return;
        }
        if (mt_rand(1, 30) === mt_rand(1, 30)) {
            // Elite Book
            $amount = mt_rand(1, 3);
            $item = VanillaItems::PAPER();
            $item->setCustomName("§r§bElite Book");
            $item->getNamedTag()->setByte("EliteBook", 1);
            $item->setLore([
                "§r§7Examine this book to receive a random",
                "§r§bElite §7enchantment book.",
                "§r§8 * §7Right-click to examine this book.",
                "§r§8 * §7Tier: §aI"
            ]);
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§bElite Book§r§a.");
            return;
        }
        if (mt_rand(1, 8) === mt_rand(1, 8)) {
            // Elixir Pouch
            $amount = mt_rand(1, 3);
            $item = VanillaItems::PAPER();
            $item->setCustomName("§r§5Elixir Pouch");
            $item->getNamedTag()->setByte("ElixirPouch", 1);
            $item->setLore([
                "§r§7Receive a random amount of elixir.",
                "§r§7Right-Click to use."
            ]);
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§5Elixir Pouch§r§a.");
            return;
        }

        // Basic Rewards
        switch ($chance) {
            case 1:
                // Money Pouch
                $amount = mt_rand(1, 3);
                $item = VanillaItems::PAPER();
                $item->setCustomName("§r§6Money Pouch");
                $item->getNamedTag()->setByte("MoneyPouch", 1);
                $item->setLore([
                    "§r§7Receive a random amount of money.",
                    "§r§7Right-Click to use."
                ]);
                foreach ($player->getInventory()->addItem($item) as $invfull) {
                    $player->getWorld()->dropItem($player->getPosition(), $invfull);
                }
                $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§6Money Pouch§r§a.");
                break;
            case 2:
                // EXP Pouch
                $amount = mt_rand(1, 3);
                $item = VanillaItems::PAPER();
                $item->setCustomName("§r§aEXP Pouch");
                $item->getNamedTag()->setByte("EXPPouch", 1);
                $item->setLore([
                    "§r§7Receive a random amount of experience points.",
                    "§r§7Right-Click to use."
                ]);
                foreach ($player->getInventory()->addItem($item) as $invfull) {
                    $player->getWorld()->dropItem($player->getPosition(), $invfull);
                }
                $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§aEXP Pouch§r§a.");
                break;
        }

    }

    /////////////////////////////// OP ROLL ///////////////////////////////
    public function rollOp($player) {

        // Variables
        $chance = mt_rand(1, 3);

        // Lucky Rewards
        if (mt_rand(1, 1200) === mt_rand(1, 1200)) {
            // Feed Permissions
            $amount = mt_rand(1, 1);
            $item = VanillaItems::PAPER();
            $item->setCustomName("§r§eFeed Permissions");
            $item->getNamedTag()->setByte("FeedPermissions", 1);
            $item->setLore([
                "§r§7Examine this note to receive permissions",
                "§r§7for the Command /feed.",
                "§r§8 * §7Right-click to examine this note."
            ]);
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§eFeed Permissions§r§a.");
            $this->plugin->getServer()->broadcastMessage("§l§eCrates §8>> §r§7" . $player->getName() . " has won a §l§eFeed Permissions§r§7 from a Rare Crate!");
            return;
        }
        if (mt_rand(1, 1200) === mt_rand(1, 1200)) {
            // Heal Permissions
            $amount = mt_rand(1, 1);
            $item = VanillaItems::PAPER();
            $item->setCustomName("§r§eHeal Permissions");
            $item->getNamedTag()->setByte("HealPermissions", 1);
            $item->setLore([
                "§r§7Examine this note to receive permissions",
                "§r§7for the Command /heal.",
                "§r§8 * §7Right-click to examine this note."
            ]);
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§eHeal Permissions§r§a.");
            $this->plugin->getServer()->broadcastMessage("§l§eCrates §8>> §r§7" . $player->getName() . " has won a §l§eHeal Permissions§r§7 from a Rare Crate!");
            return;
        }
        if (mt_rand(1, 1200) === mt_rand(1, 1200)) {
            // Fly Permissions
            $amount = mt_rand(1, 1);
            $item = VanillaItems::PAPER();
            $item->setCustomName("§r§eFly Permissions");
            $item->getNamedTag()->setByte("FlyPermissions", 1);
            $item->setLore([
                "§r§7Examine this note to receive permissions",
                "§r§7for the Command /fly.",
                "§r§8 * §7Right-click to examine this note."
            ]);
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§eFly Permissions§r§a.");
            $this->plugin->getServer()->broadcastMessage("§l§eCrates §8>> §r§7" . $player->getName() . " has won a §l§eFly Permissions§r§7 from a Rare Crate!");
            return;
        }
        if (mt_rand(1, 750) === mt_rand(1, 750)) {
            // Twilight Crystal
            $amount = mt_rand(1, 1);
            $item = VanillaItems::PAPER();
            $item->setCustomName("§r§dTwilight Crystal");
            $item->getNamedTag()->setByte("TwilightCrystal", 1);
            $item->setLore([
                "§r§7Has a chance to make you rankup to Twilight rank.",
                "§r§7Only works if you have the Overlord rank.",
                "§r§8 * §7Right-click to shatter the crystal.",
                "§r§8 * §7Overlord -> Twilight"
            ]);
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§dTwilight Crystal§r§a.");
            $this->plugin->getServer()->broadcastMessage("§l§eCrates §8>> §r§7" . $player->getName() . " has won a §l§dTwilight Crystal§r§7 from a Rare Crate!");
            return;
        }
        if (mt_rand(1, 750) === mt_rand(1, 750)) {
            // Overlord Crystal
            $amount = mt_rand(1, 1);
            $item = VanillaItems::PAPER();
            $item->setCustomName("§r§4Overlord Crystal");
            $item->getNamedTag()->setByte("OverlordCrystal", 1);
            $item->setLore([
                "§r§7Has a chance to make you rankup to Overlord rank.",
                "§r§7Only works if you have the Warlord rank.",
                "§r§8 * §7Right-click to shatter the crystal.",
                "§r§8 * §7Warlord -> Overlord"
            ]);
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§4Overlord Crystal§r§a.");
            $this->plugin->getServer()->broadcastMessage("§l§eCrates §8>> §r§7" . $player->getName() . " has won a §l§4Overlord Crystal§r§7 from a Rare Crate!");
            return;
        }
        if (mt_rand(1, 50) === mt_rand(1, 50)) {
            // Sacred Book
            $amount = mt_rand(1, 2);
            $item = VanillaItems::PAPER();
            $item->setCustomName("§r§6Sacred Book");
            $item->getNamedTag()->setByte("SacredBook", 1);
            $item->setLore([
                "§r§7Examine this book to receive a random",
                "§r§6Sacred §7enchantment book.",
                "§r§8 * §7Right-click to examine this book.",
                "§r§8 * §7Tier: §aIII"
            ]);
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§6Sacred Book§r§a.");
            return;
        }
        if (mt_rand(1, 30) === mt_rand(1, 30)) {
            // Mystery Tag
            $amount = mt_rand(1, 2);
            $item = VanillaItems::PAPER();
            $item->setCustomName("§r§5Mystery Tag");
            $item->getNamedTag()->setByte("MysteryTag", 1);
            $item->setLore([
                "§r§7An item containing a random tag.",
                "§r§8 * §7Right-click to reveal the tag.",
            ]);
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§5Mystery Tag§r§a.");
            return;
        }
        if (mt_rand(1, 30) === mt_rand(1, 30)) {
            // Mystery Orb
            $amount = mt_rand(1, 2);
            $item = VanillaItems::PAPER();
            $item->setCustomName("§r§5Mystery Effect Orb");
            $item->getNamedTag()->setByte("MysteryOrb", 1);
            $item->setLore([
                "§r§7Examine this orb to receive a random effect orb.",
                "§r§8 * §7Right-click to examine this orb.",
            ]);
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§5Mystery Orb§r§a.");
            return;
        }
        if (mt_rand(1, 30) === mt_rand(1, 30)) {
            // Epic Book
            $amount = mt_rand(1, 3);
            $item = VanillaItems::PAPER();
            $item->setCustomName("§r§eEpic Book");
            $item->getNamedTag()->setByte("EpicBook", 1);
            $item->setLore([
                "§r§7Examine this book to receive a random",
                "§r§eEpic §7enchantment book.",
                "§r§8 * §7Right-click to examine this book.",
                "§r§8 * §7Tier: §aII"
            ]);
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§eElite Book§r§a.");
            return;
        }

        // Basic Rewards
        switch ($chance) {
            case 1:
                // Money Pouch
                $amount = mt_rand(1, 5);
                $item = VanillaItems::PAPER();
                $item->setCustomName("§r§6Money Pouch");
                $item->getNamedTag()->setByte("MoneyPouch", 1);
                $item->setLore([
                    "§r§7Receive a random amount of money.",
                    "§r§7Right-Click to use."
                ]);
                foreach ($player->getInventory()->addItem($item) as $invfull) {
                    $player->getWorld()->dropItem($player->getPosition(), $invfull);
                }
                $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§6Money Pouch§r§a.");
                break;
            case 2:
                // EXP Pouch
                $amount = mt_rand(1, 5);
                $item = VanillaItems::PAPER();
                $item->setCustomName("§r§aEXP Pouch");
                $item->getNamedTag()->setByte("EXPPouch", 1);
                $item->setLore([
                    "§r§7Receive a random amount of experience points.",
                    "§r§7Right-Click to use."
                ]);
                foreach ($player->getInventory()->addItem($item) as $invfull) {
                    $player->getWorld()->dropItem($player->getPosition(), $invfull);
                }
                $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§aEXP Pouch§r§a.");
                break;
            case 3:
                // Elixir Pouch
                $amount = mt_rand(1, 2);
                $item = VanillaItems::PAPER();
                $item->setCustomName("§r§5Elixir Pouch");
                $item->getNamedTag()->setByte("ElixirPouch", 1);
                $item->setLore([
                    "§r§7Receive a random amount of elixir.",
                    "§r§7Right-Click to use."
                ]);
                foreach ($player->getInventory()->addItem($item) as $invfull) {
                    $player->getWorld()->dropItem($player->getPosition(), $invfull);
                }
                $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§5Elixir Pouch§r§a.");
                break;
        }

    }

    /////////////////////////////// MUTATED ROLL ///////////////////////////////
    public function rollMutated($player) {

        // Variables
        $chance = mt_rand(1, 3);

        // Lucky Rewards
        if (mt_rand(1, 750) === mt_rand(1, 750)) {
            // Eternal Crystal
            $amount = mt_rand(1, 1);
            $item = VanillaItems::PAPER();
            $item->setCustomName("§r§l§bE§3t§be§3r§bn§3a§bl §r§bCrystal");
            $item->getNamedTag()->setByte("EternalCrystal", 1);
            $item->setLore([
                "§r§7Has a chance to make you rankup to Eternal rank.",
                "§r§7Only works if you have the Nightmare rank.",
                "§r§8 * §7Right-click to shatter the crystal.",
                "§r§8 * §7Nightmare -> Eternal"
            ]);
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§bE§3t§be§3r§bn§3a§bl Crystal§r§a.");
            $this->plugin->getServer()->broadcastMessage("§l§eCrates §8>> §r§7" . $player->getName() . " has won a §l§bE§3t§be§3r§bn§3a§bl Crystal§r§7 from a Legend Crate!");
            return;
        }
        if (mt_rand(1, 750) === mt_rand(1, 750)) {
            // Nightmare Crystal
            $amount = mt_rand(1, 1);
            $item = VanillaItems::PAPER();
            $item->setCustomName("§r§l§4N§ci§4g§ch§4t§cm§4a§cr§4e §r§4Crystal");
            $item->getNamedTag()->setByte("NightmareCrystal", 1);
            $item->setLore([
                "§r§7Has a chance to make you rankup to Nightmare rank.",
                "§r§7Only works if you have the Twilight rank.",
                "§r§8 * §7Right-click to shatter the crystal.",
                "§r§8 * §7Twilight -> Nightmare"
            ]);
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§4N§ci§4g§ch§4t§cm§4a§cr§4e Crystal§r§a.");
            $this->plugin->getServer()->broadcastMessage("§l§eCrates §8>> §r§7" . $player->getName() . " has won a §l§4N§ci§4g§ch§4t§cm§4a§cr§4e Crystal§r§7 from a Legend Crate!");
            return;
        }
        if (mt_rand(1, 100) === mt_rand(1, 100)) {
            // Heroic Book
            $amount = mt_rand(1, 1);
            $item = VanillaItems::PAPER();
            $item->setCustomName("§r§dHeroic Book");
            $item->getNamedTag()->setByte("HeroicBook", 1);
            $item->setLore([
                "§r§7Examine this book to receive a random",
                "§r§dHeroic §7enchantment book.",
                "§r§8 * §7Right-click to examine this book.",
                "§r§8 * §7Tier: §aV"
            ]);
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§dHeroic Book§r§a.");
            return;
        }
        if (mt_rand(1, 75) === mt_rand(1, 75)) {
            // Mystery Tablet
            $amount = mt_rand(1, 3);
            $item = VanillaItems::PAPER();
            $item->setCustomName("§r§8[§7*§8] §l§5Mystery Tablet §8[§7*§8]");
            $item->getNamedTag()->setByte("MysteryTablet", 1);
            $item->setLore([
                "§r§7Examine this mysterious stone tablet for a",
                "§r§7chance to receive a random shard tablet.",
                "§r§8 * §7Right-click to examine the tablet."
            ]);
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§5Mystery Tablet§r§a.");
            return;
        }
        if (mt_rand(1, 50) === mt_rand(1, 50)) {
            // Mythical Book
            $amount = mt_rand(1, 2);
            $item = VanillaItems::PAPER();
            $item->setCustomName("§r§cMythical Book");
            $item->getNamedTag()->setByte("MythicalBook", 1);
            $item->setLore([
                "§r§7Examine this book to receive a random",
                "§r§cMythical §7enchantment book.",
                "§r§8 * §7Right-click to examine this book.",
                "§r§8 * §7Tier: §aIV"
            ]);
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§cMythical Book§r§a.");
            return;
        }
        if (mt_rand(1, 30) === mt_rand(1, 30)) {
            // Sacred Book
            $amount = mt_rand(1, 3);
            $item = VanillaItems::PAPER();
            $item->setCustomName("§r§6Sacred Book");
            $item->getNamedTag()->setByte("SacredBook", 1);
            $item->setLore([
                "§r§7Examine this book to receive a random",
                "§r§6Sacred §7enchantment book.",
                "§r§8 * §7Right-click to examine this book.",
                "§r§8 * §7Tier: §aIII"
            ]);
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§6Sacred Book§r§a.");
            return;
        }

        // Basic Rewards
        switch ($chance) {
            case 1:
                // Money Pouch
                $amount = mt_rand(3, 5);
                $item = VanillaItems::PAPER();
                $item->setCustomName("§r§6Money Pouch");
                $item->getNamedTag()->setByte("MoneyPouch", 1);
                $item->setLore([
                    "§r§7Receive a random amount of money.",
                    "§r§7Right-Click to use."
                ]);
                foreach ($player->getInventory()->addItem($item) as $invfull) {
                    $player->getWorld()->dropItem($player->getPosition(), $invfull);
                }
                $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§6Money Pouch§r§a.");
                break;
            case 2:
                // EXP Pouch
                $amount = mt_rand(3, 5);
                $item = VanillaItems::PAPER();
                $item->setCustomName("§r§aEXP Pouch");
                $item->getNamedTag()->setByte("EXPPouch", 1);
                $item->setLore([
                    "§r§7Receive a random amount of experience points.",
                    "§r§7Right-Click to use."
                ]);
                foreach ($player->getInventory()->addItem($item) as $invfull) {
                    $player->getWorld()->dropItem($player->getPosition(), $invfull);
                }
                $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§aEXP Pouch§r§a.");
                break;
            case 3:
                // Elixir Pouch
                $amount = mt_rand(2, 4);
                $item = VanillaItems::PAPER();
                $item->setCustomName("§r§5Elixir Pouch");
                $item->getNamedTag()->setByte("ElixirPouch", 1);
                $item->setLore([
                    "§r§7Receive a random amount of elixir.",
                    "§r§7Right-Click to use."
                ]);
                foreach ($player->getInventory()->addItem($item) as $invfull) {
                    $player->getWorld()->dropItem($player->getPosition(), $invfull);
                }
                $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§5Elixir Pouch§r§a.");
                break;
        }

    }

    /////////////////////////////// VOTE ROLL ///////////////////////////////
    public function rollVote($player) {

        // Variables
        $chance = mt_rand(1, 6);

        // Lucky Rewards
        if (mt_rand(1, 5000) === mt_rand(1, 5000)) {
            // Zeus Shard
            $amount = mt_rand(1, 1);
            $item = VanillaItems::PAPER();
            $item->setCustomName("§r§8[§l§k§6|§e|§r§8] §l§6Zeus §r§6Shard §8[§l§k§e|§6|§r§8]");
            $item->getNamedTag()->setByte("ZeusShard", 1);
            $item->setLore([
                "§r§7A magical shard from the gods.",
                "§r§7This shard contains the power of the god Zeus.",
                "§r§7Obtain 9x Zeus Shards to gain Zeus God Kit Permissions.",
                "§r§8 * §7Craft the Zeus Permissions with /blacksmith.",
                "§r§8 * §7Tier: §aIII"
            ]);
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§6Zeus Shard§r§a.");
            $this->plugin->getServer()->broadcastMessage("§l§eCrates §8>> §r§7" . $player->getName() . " has won a §l§6Zeus Shard§r§7 from a Ultimate Crate!");
            return;
        }
        if (mt_rand(1, 3000) === mt_rand(1, 3000)) {
            // Athena Shard
            $amount = mt_rand(1, 1);
            $item = VanillaItems::PAPER();
            $item->setCustomName("§r§8[§l§k§d|§5|§r§8] §l§dAthena §r§dShard §8[§l§k§5|§d|§r§8]");
            $item->getNamedTag()->setByte("AthenaShard", 1);
            $item->setLore([
                "§r§7A magical shard from the gods.",
                "§r§7This shard contains the power of the god Athena.",
                "§r§7Obtain 9x Athena Shards to gain Athena God Kit Permissions.",
                "§r§8 * §7Craft the Athena Permissions with /blacksmith.",
                "§r§8 * §7Tier: §aII"
            ]);
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§dAthena Shard§r§a.");
            $this->plugin->getServer()->broadcastMessage("§l§eCrates §8>> §r§7" . $player->getName() . " has won a §l§dAthena Shard§r§7 from a Ultimate Crate!");
            return;
        }
        if (mt_rand(1, 1500) === mt_rand(1, 1500)) {
            // Hermes Shard
            $amount = mt_rand(1, 1);
            $item = VanillaItems::PAPER();
            $item->setCustomName("§r§8[§l§k§9|§b|§r§8] §l§9Hermes §r§9Shard §8[§l§k§b|§9|§r§8]");
            $item->getNamedTag()->setByte("HermesShard", 1);
            $item->setLore([
                "§r§7A magical shard from the gods.",
                "§r§7This shard contains the power of the god Hermes.",
                "§r§7Obtain 9x Hermes Shards to gain Hermes God Kit Permissions.",
                "§r§8 * §7Craft the Hermes Permissions with /blacksmith.",
                "§r§8 * §7Tier: §aI"
            ]);
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§9Hermes Shard§r§a.");
            $this->plugin->getServer()->broadcastMessage("§l§eCrates §8>> §r§7" . $player->getName() . " has won a §l§9Hermes Shard§r§7 from a Ultimate Crate!");
            return;
        }
        if (mt_rand(1, 500) === mt_rand(1, 500)) {
            // Seraph Crystal
            $amount = mt_rand(1, 1);
            $item = VanillaItems::PAPER();
            $item->setCustomName("§r§l§6S§ee§6r§ea§6p§eh §r§6Crystal");
            $item->getNamedTag()->setByte("SeraphCrystal", 1);
            $item->setLore([
                "§r§7Has a chance to make you rankup to Seraph rank.",
                "§r§7Only works if you have the Eternal rank.",
                "§r§8 * §7Right-click to shatter the crystal.",
                "§r§8 * §7Eternal -> Seraph"
            ]);
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§6S§ee§6r§ea§6p§eh Crystal§r§a.");
            $this->plugin->getServer()->broadcastMessage("§l§eCrates §8>> §r§7" . $player->getName() . " has won a §l§6S§ee§6r§ea§6p§eh Crystal§r§7 from a Ultimate Crate!");
            return;
        }
        if (mt_rand(1, 500) === mt_rand(1, 500)) {
            // Global Relic Booster
            $amount = mt_rand(1, 1);
            $item = VanillaItems::PAPER();
            $item->setCustomName("§r§l§cGlobal Relic Booster");
            $item->getNamedTag()->setByte("GlobalRelicBooster", 1);
            $item->setLore([
                "§r§7Boost everyone online with more relics received",
                "§r§7from mining in /wilderness, /mine, /pvpmine",
                "§r§7as well as the VIP Mine and the Prestige Mine",
                "§r§7for a total of 30 Minutes.",
                "§r",
                "§r§8 * §7Right-click to activate this booster."
            ]);
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§cGlobal Relic Booster§r§a.");
            $this->plugin->getServer()->broadcastMessage("§l§eCrates §8>> §r§7" . $player->getName() . " has won a §l§cGlobal Relic Booster§r§7 from a Ultimate Crate!");
            return;
        }
        if (mt_rand(1, 500) === mt_rand(1, 500)) {
            // Global Money Booster
            $amount = mt_rand(1, 1);
            $item = VanillaItems::PAPER();
            $item->setCustomName("§r§l§6Global Money Booster");
            $item->getNamedTag()->setByte("GlobalMoneyBooster", 1);
            $item->setLore([
                "§r§7Boost everyone online with 2x Money gained from",
                "§r§7mines in /mine, /pvpmine as well as the VIP Mine",
                "§r§7and the Prestige Mine for a total of 30 Minutes.",
                "§r",
                "§r§8 * §7Right-click to activate this booster."
            ]);
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§6Global Money Booster§r§a.");
            $this->plugin->getServer()->broadcastMessage("§l§eCrates §8>> §r§7" . $player->getName() . " has won a §l§6Global Money Booster§r§7 from a Ultimate Crate!");
            return;
        }
        if (mt_rand(1, 300) === mt_rand(1, 300)) {
            // Personal Relic Booster
            $amount = mt_rand(1, 1);
            $item = VanillaItems::PAPER();
            $item->setCustomName("§r§l§cPersonal Relic Booster");
            $item->getNamedTag()->setByte("RelicBooster", 1);
            $item->setLore([
                "§r§7Boost yourself with more relics received",
                "§r§7from mining in /wilderness, /mine, /pvpmine",
                "§r§7as well as the VIP Mine and the Prestige Mine",
                "§r§7for a total of 30 Minutes.",
                "§r",
                "§r§8 * §7Right-click to activate this booster."
            ]);
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§cPersonal Relic Booster§r§a.");
            $this->plugin->getServer()->broadcastMessage("§l§eCrates §8>> §r§7" . $player->getName() . " has won a §l§cPersonal Relic Booster§r§7 from a Ultimate Crate!");
            return;
        }
        if (mt_rand(1, 300) === mt_rand(1, 300)) {
            // Personal Money Booster
            $amount = mt_rand(1, 1);
            $item = VanillaItems::PAPER();
            $item->setCustomName("§r§l§6Personal Money Booster");
            $item->getNamedTag()->setByte("MoneyBooster", 1);
            $item->setLore([
                "§r§7Boost yourself with 2x Money gained from mines",
                "§r§7in /mine, /pvpmine as well as the VIP Mine and",
                "§r§7the Prestige Mine for a total of 30 Minutes.",
                "§r",
                "§r§8 * §7Right-click to activate this booster."
            ]);
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§6Personal Money Booster§r§a.");
            $this->plugin->getServer()->broadcastMessage("§l§eCrates §8>> §r§7" . $player->getName() . " has won a §l§6Personal Money Booster§r§7 from a Ultimate Crate!");
            return;
        }
        if (mt_rand(1, 15) === mt_rand(1, 15)) {
            // Mystery Tablet
            $amount = mt_rand(1, 5);
            $item = VanillaItems::PAPER();
            $item->setCustomName("§r§8[§7*§8] §l§5Mystery Tablet §8[§7*§8]");
            $item->getNamedTag()->setByte("MysteryTablet", 1);
            $item->setLore([
                "§r§7Examine this mysterious stone tablet for a",
                "§r§7chance to receive a random shard tablet.",
                "§r§8 * §7Right-click to examine the tablet."
            ]);
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§5Mystery Tablet§r§a.");
            return;
        }
        if (mt_rand(1, 15) === mt_rand(1, 15)) {
            // Heroic Book
            $amount = mt_rand(1, 2);
            $item = VanillaItems::PAPER();
            $item->setCustomName("§r§dHeroic Book");
            $item->getNamedTag()->setByte("HeroicBook", 1);
            $item->setLore([
                "§r§7Examine this book to receive a random",
                "§r§dHeroic §7enchantment book.",
                "§r§8 * §7Right-click to examine this book.",
                "§r§8 * §7Tier: §aV"
            ]);
            foreach ($player->getInventory()->addItem($item) as $invfull) {
                $player->getWorld()->dropItem($player->getPosition(), $invfull);
            }
            $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§dHeroic Book§r§a.");
            return;
        }

        // Basic Rewards
        switch ($chance) {
            case 1:
                // Money Pouch
                $amount = mt_rand(6, 16);
                $item = VanillaItems::PAPER();
                $item->setCustomName("§r§6Money Pouch");
                $item->getNamedTag()->setByte("MoneyPouch", 1);
                $item->setLore([
                    "§r§7Receive a random amount of money.",
                    "§r§7Right-Click to use."
                ]);
                foreach ($player->getInventory()->addItem($item) as $invfull) {
                    $player->getWorld()->dropItem($player->getPosition(), $invfull);
                }
                $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§6Money Pouch§r§a.");
                break;
            case 2:
                // EXP Pouch
                $amount = mt_rand(6, 16);
                $item = VanillaItems::PAPER();
                $item->setCustomName("§r§aEXP Pouch");
                $item->getNamedTag()->setByte("EXPPouch", 1);
                $item->setLore([
                    "§r§7Receive a random amount of experience points.",
                    "§r§7Right-Click to use."
                ]);
                foreach ($player->getInventory()->addItem($item) as $invfull) {
                    $player->getWorld()->dropItem($player->getPosition(), $invfull);
                }
                $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§aEXP Pouch§r§a.");
                break;
            case 3:
                // Elixir Pouch
                $amount = mt_rand(5, 12);
                $item = VanillaItems::PAPER();
                $item->setCustomName("§r§5Elixir Pouch");
                $item->getNamedTag()->setByte("ElixirPouch", 1);
                $item->setLore([
                    "§r§7Receive a random amount of elixir.",
                    "§r§7Right-Click to use."
                ]);
                foreach ($player->getInventory()->addItem($item) as $invfull) {
                    $player->getWorld()->dropItem($player->getPosition(), $invfull);
                }
                $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§5Elixir Pouch§r§a.");
                break;
            case 4:
                // Mythical Book
                $amount = mt_rand(1, 3);
                $item = VanillaItems::PAPER();
                $item->setCustomName("§r§cMythical Book");
                $item->getNamedTag()->setByte("MythicalBook", 1);
                $item->setLore([
                    "§r§7Examine this book to receive a random",
                    "§r§cMythical §7enchantment book.",
                    "§r§8 * §7Right-click to examine this book.",
                    "§r§8 * §7Tier: §aIV"
                ]);
                foreach ($player->getInventory()->addItem($item) as $invfull) {
                    $player->getWorld()->dropItem($player->getPosition(), $invfull);
                }
                $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§cMythical Book§r§a.");
                break;
            case 5:
                // Kit Pouch
                $amount = mt_rand(1, 2);
                $item = VanillaItems::PAPER();
                $item->setCustomName("§r§l§b*§7*§b* Random Kit §3Lootbox §b*§7*§b*");
                $item->getNamedTag()->setByte("KitPouch", 1);
                $item->setLore([
                    "§r§7Right-Click to Claim this Lootbox",
                    "§r§7and get an random Kit!",
                    "§r§7This Lootbox can be gotten by buying",
                    "§r§7or from crates!",
                    "§r",
                    "§r§l§bKits:",
                    "§r§l§b| *§r§2 Warrior Kit",
                    "§r§l§b| *§r§3 Warlord Kit",
                    "§r§l§b| *§r§4 Overlord Kit",
                    "§r§l§b| *§r§d Twilight Kit",
                    "§r§l§b| *§r§c Nightmare Kit",
                    "§r§l§b| *§r§b Eternal Kit",
                    "§r",
                    "§r§7Right-Click to use."

                ]);
                foreach ($player->getInventory()->addItem($item) as $invfull) {
                    $player->getWorld()->dropItem($player->getPosition(), $invfull);
                }
                $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§9Kit Pouch§r§a.");
                break;
            case 6:
                // Crystal Pouch
                $amount = mt_rand(1, 2);
                $item = VanillaItems::PAPER();
                $item->setCustomName("§r§dCrystal Pouch");
                $item->getNamedTag()->setByte("CrystalPouch", 1);
                $item->setLore([
                    "§r§7Receive a random amount of crystals.",
                    "§r§7Right-Click to use."
                ]);
                foreach ($player->getInventory()->addItem($item) as $invfull) {
                    $player->getWorld()->dropItem($player->getPosition(), $invfull);
                }
                $player->sendTip("§l§8(§2!§8) §r§aYou have received " . $amount . "x §l§dCrystal Pouch§r§a.");
                break;
        }

    }

}