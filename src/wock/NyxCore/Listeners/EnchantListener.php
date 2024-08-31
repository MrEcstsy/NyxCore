<?php

namespace wock\NyxCore\Listeners;

use DaPigGuy\PiggyCustomEnchants\CustomEnchantManager;
use DaPigGuy\PiggyCustomEnchants\enchants\CustomEnchant;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Armor;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\StringToItemParser;
use pocketmine\item\Tool;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\world\sound\XpLevelUpSound;
use wock\NyxCore\Items\Rewards;
use wock\NyxCore\Nyx;
use wock\NyxCore\Utils\Utils;
use pocketmine\utils\TextFormat as C;

class EnchantListener implements Listener {

    /**
     * @var Nyx
     */
    private Nyx $plugin;

    /**
     * EventListener constructor.
     * @param Nyx $plugin
     */
    public function __construct(Nyx $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @param PlayerItemUseEvent $event
     */
    public function onTapBook(PlayerItemUseEvent $event)
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $nbt = $item->getNamedTag()->getTag("randomcebook");
        if ($nbt !== null) {
            $rarity = $nbt->getValue();
            $enchants = [];
            switch ($rarity) {
                case 888:
                    $enchants = Utils::getSimpleEnchantments();
                    break;
                case 887:
                    $enchants = Utils::getUniqueEnchantments();
                    break;
                case 886:
                    $enchants = Utils::getEliteEnchantments();
                    break;
                case 885:
                    $enchants = Utils::getUltimateEnchantments();
                    break;
                case 884:
                    $enchants = Utils::getLegendaryEnchantments();
                    break;
            }
            if (!empty($enchants)) {
                $enchant = $enchants[array_rand($enchants)];
                $ce = CustomEnchantManager::getEnchantment($enchant);
                if ($ce instanceof CustomEnchant) {
                    $item->pop();
                    $player->getInventory()->setItemInHand($item);
                    $level = mt_rand(1, $ce->getMaxLevel());
                    $book = Rewards::createCEEnchantmentCrystal($ce->getId(), $level, mt_rand(1, 100));
                    $player->getInventory()->addItem($book);
                }
            }
        }
    }

    /**
     * @param InventoryTransactionEvent $event
     * @priority HIGH
     * @ignoreCancelled true
     */
    public function onApplyBook(InventoryTransactionEvent $event): void
    {
        $transaction = $event->getTransaction();
        $actions = $transaction->getActions();
        $oldToNew = isset(array_keys($actions)[0]) ? $actions[array_keys($actions)[0]] : null;
        $newToOld = isset(array_keys($actions)[1]) ? $actions[array_keys($actions)[1]] : null;
        if ($oldToNew instanceof SlotChangeAction && $newToOld instanceof SlotChangeAction) {
            $itemClicked = $newToOld->getSourceItem();
            $itemClickedWith = $oldToNew->getSourceItem();
            if ($itemClickedWith->getTypeId() === ItemTypeIds::NETHER_STAR && $itemClicked->getTypeId() !== VanillaItems::AIR()->getTypeId()) {
                $nbt = $itemClickedWith->getNamedTag()->getTag("enchantbook");
                if ($nbt === null) return;
                $enchantment = $nbt->getValue();

                $enchantment = CustomEnchantManager::getEnchantment($enchantment);
                $success = $itemClickedWith->getNamedTag()->getTag("successbook")->getValue();
                $level = $itemClickedWith->getNamedTag()->getTag("levelbook")->getValue();
                $destroy = $itemClickedWith->getNamedTag()->getTag("destroybook")->getValue();

                $customEnch = [];
                foreach ($itemClicked->getEnchantments() as $enchantmentItem) {
                    if ($enchantmentItem->getType() instanceof CustomEnchant) {
                        $customEnch[] = $enchantmentItem;
                    }
                }

                $currentCe = count($customEnch);
                $limit = (int)$this->plugin->getConfig()->get("max-enchants");
                if (($orb = $itemClicked->getNamedTag()->getTag("orb")) !== null) {
                    $limit = $orb->getValue();
                }
                if ($currentCe >= $limit) {
                    $event->getTransaction()->getSource()->sendMessage(C::RED . "The max number of enchantments you can apply to this item is " . C::AQUA . $limit);
                    return;
                }

                if ($itemClicked->getNamedTag()->getTag("successbook") !== null) {
                    return;
                }
                if (!\DaPigGuy\PiggyCustomEnchants\utils\Utils::canBeEnchanted($itemClicked, $enchantment, $level)) {
                    $event->getTransaction()->getSource()->sendMessage(C::RED . "This item is not compatible with this enchant.");
                    return;
                }

                if (mt_rand(0, 100) > $success) {
                    if (mt_rand(0, 100) < $destroy) {
                        if ($itemClicked->getNamedTag()->getTag("protected") !== null) {
                            $event->cancel();
                            $oldToNew->getInventory()->setItem($oldToNew->getSlot(), VanillaItems::AIR());
                            $event->getTransaction()->getSource()->sendMessage(C::RED . "Enchanting failed. The book has been destroyed and the protection on the item has been removed.");

                            $itemClicked->getNamedTag()->removeTag("protected");
                            $oldLore = $itemClicked->getLore();
                            $newLore = [];
                            foreach ($oldLore as $line) {
                                if (str_contains($line, "PROTECTED")) {
                                    $newLore[] = $line;
                                }
                            }
                            $itemClicked->setLore($newLore);
                            $newToOld->getInventory()->setItem($newToOld->getSlot(), $itemClicked);
                            return;
                        }
                        $event->getTransaction()->getSource()->sendMessage(C::RED . "Enchanting failed. The book and the item have both been destroyed.");
                        $event->cancel();
                        $oldToNew->getInventory()->setItem($oldToNew->getSlot(), VanillaItems::AIR());
                        $newToOld->getInventory()->setItem($newToOld->getSlot(), VanillaItems::AIR());
                        return;
                    }
                    $event->getTransaction()->getSource()->sendMessage(C::RED . "Enchanting failed. The book has been destroyed.");
                    $event->cancel();
                    $oldToNew->getInventory()->setItem($oldToNew->getSlot(), VanillaItems::AIR());
                    return;
                }

                $enchantment = new EnchantmentInstance($enchantment, $level);
                $itemClicked->addEnchantment($enchantment);
                $newToOld->getInventory()->setItem($newToOld->getSlot(), $itemClicked);
                $enchantmentSuccessful = true;
                if ($enchantmentSuccessful) {
                    $event->cancel();
                    $oldToNew->getInventory()->setItem($oldToNew->getSlot(), VanillaItems::AIR());
                    $event->getTransaction()->getSource()->sendMessage(C::GREEN . "Successfully enchanted.");
                }
            }
        }
    }

    /**
     * @param InventoryTransactionEvent $event
     * @priority HIGH
     * @ignoreCancelled true
     */
    public function onWhiteScroll(InventoryTransactionEvent $event): void
    {
        $transaction = $event->getTransaction();
        $actions = $transaction->getActions();
        $item = Utils::getConfigurations("items")->getNested("whitescroll.item");
        $oldToNew = isset(array_keys($actions)[0]) ? $actions[array_keys($actions)[0]] : null;
        $newToOld = isset(array_keys($actions)[1]) ? $actions[array_keys($actions)[1]] : null;
        if ($oldToNew instanceof SlotChangeAction && $newToOld instanceof SlotChangeAction) {
            $itemClicked = $newToOld->getSourceItem();
            $itemClickedWith = $oldToNew->getSourceItem();
            if ($itemClickedWith->getTypeId() === StringToItemParser::getInstance()->parse($item)->getTypeId() && $itemClicked->getTypeId() !== VanillaItems::AIR()) {
                if ($itemClickedWith->getNamedTag()->getTag("whitescroll") !== null) {
                    if ($itemClicked->getNamedTag()->getTag("successbook") === null) {
                        $lore = $itemClicked->getLore();
                        $lore[] = C::RESET . C::BOLD . C::WHITE . "PROTECTED";
                        $itemClicked->setLore($lore);

                        $itemClicked->getNamedTag()->setInt("protected", mt_rand(0, 100000));
                        $newToOld->getInventory()->setItem($newToOld->getSlot(), $itemClicked);

                        $event->cancel();
                        $oldToNew->getInventory()->setItem($oldToNew->getSlot(), VanillaItems::AIR());
                    }
                }
            }
        }
    }

    /**
     * @param InventoryTransactionEvent $event
     * @priority HIGH
     * @ignoreCancelled true
     */
    public function onEnchantOrb(InventoryTransactionEvent $event): void
    {
        $transaction = $event->getTransaction();
        $actions = $transaction->getActions();
        $oldToNew = isset(array_keys($actions)[0]) ? $actions[array_keys($actions)[0]] : null;
        $newToOld = isset(array_keys($actions)[1]) ? $actions[array_keys($actions)[1]] : null;
        if ($oldToNew instanceof SlotChangeAction && $newToOld instanceof SlotChangeAction) {
            $itemClicked = $newToOld->getSourceItem();
            $itemClickedWith = $oldToNew->getSourceItem();
            if (($orb = $itemClickedWith->getNamedTag()->getTag("armororb")) !== null) {
                if (!$itemClicked instanceof Armor) {
                    $event->getTransaction()->getSource()->sendMessage(C::RED . "You can only apply this on Armor");
                    return;
                }
                $lore = $itemClicked->getLore();
                if($itemClicked->getNamedTag()->getTag("orb") !== null) {
                    foreach ($lore as $key => $line) {
                        if(str_contains($line, " Max Enchants")) {
                            unset($lore[$key]);
                            break;
                        }
                    }
                }
                $lore[] = "\n" . C::RESET . C::BOLD . C::GREEN . "+ " . $orb->getValue() . " Max Enchants";
                $itemClicked->setLore($lore);
                $itemClicked->getNamedTag()->setTag("orb", $orb->getValue());
                $newToOld->getInventory()->setItem($newToOld->getSlot(), $itemClicked);

                $event->cancel();
                $oldToNew->getInventory()->setItem($oldToNew->getSlot(), VanillaItems::AIR());

            }
            if (($orb = $itemClickedWith->getNamedTag()->getTag("weaponorb")) !== null) {
                if (!$itemClicked instanceof Tool) {
                    $event->getTransaction()->getSource()->sendMessage(C::RED . "You can only apply this on Tools");
                    return;
                }
                if($itemClicked->getNamedTag()->getTag("orb") !== null) {
                    $lore = $itemClicked->getLore();
                    foreach ($lore as $key => $line) {
                        if(str_contains($line, " Max Enchants")) {
                            unset($lore[$key]);
                            break;
                        }
                    }
                }
                $lore = $itemClicked->getLore();
                $lore[] = "\n" . C::RESET . C::BOLD . C::GREEN . "+ " . $orb->getValue() . " Max Enchants";
                $itemClicked->setLore($lore);

                $itemClicked->getNamedTag()->setTag("orb", $orb->getValue());
                $newToOld->getInventory()->setItem($newToOld->getSlot(), $itemClicked);

                $event->cancel();
                $oldToNew->getInventory()->setItem($oldToNew->getSlot(), VanillaItems::AIR());

            }
        }
    }

    /**
     * @param InventoryTransactionEvent $event
     * @priority HIGH
     * @ignoreCancelled true
     */
    public function onBlackScroll(InventoryTransactionEvent $event): void
    {
        $transaction = $event->getTransaction();
        $actions = array_values($transaction->getActions());
        if (count($actions) === 2) {
            foreach ($actions as $i => $action) {
                $ids = [VanillaItems::DIAMOND_SWORD()->getTypeId(), VanillaItems::DIAMOND_AXE()->getTypeId()];
                if ($action instanceof SlotChangeAction && ($otherAction = $actions[($i + 1) % 2]) instanceof SlotChangeAction && ($itemClickedWith = $action->getTargetItem())->getTypeId() !== VanillaItems::NETHER_STAR()->getTypeId() && ($itemClicked = $action->getSourceItem())->getTypeId() !== VanillaItems::AIR()->getTypeId() && $itemClicked->getTypeId() !== VanillaItems::NETHER_STAR()->getTypeId() && in_array($itemClicked->getTypeId(), $ids) && $itemClickedWith->getCount() === 1 && Utils::hasMaskTag($itemClickedWith, "blackscroll", StringTag::class) && $itemClickedWith->getNamedTag()->getString("blackscroll") !== "") {
                    $blackscrollTag = $itemClickedWith->getNamedTag()->getString("blackscroll");
                    $tag = $itemClickedWith->getNamedTag()->getInt("percentage", 50); // -1 or a default value of your choice
                    if ($tag !== 50) {
                        $enchantmentSuccessful = false;
                        $enchants = $itemClicked->getEnchantments();
                        if (empty($enchants)) {
                            return;
                        }
                        $removed = $enchants[array_rand($enchants)];
                        if ($removed instanceof EnchantmentInstance) {
                            if (!$removed->getType() instanceof CustomEnchant) {
                                return;
                            }
                            $id = $removed->getType()->getName();
                            $level = $removed->getLevel();
                            $itemClicked->removeEnchantment(CustomEnchantManager::getEnchantmentByName($id));
                            $action->getInventory()->setItem($action->getSlot(), $itemClicked);
                            $enchantmentSuccessful = true;
                        }
                        if ($enchantmentSuccessful) {
                            $event->cancel();
                            $id = $removed->getType()->getName();
                            $otherAction->getInventory()->setItem($otherAction->getSlot(), VanillaItems::AIR());
                            $inv = $otherAction->getInventory();
                            $book = VanillaItems::NETHER_STAR();
                            $book->addEnchantment(new EnchantmentInstance(CustomEnchantManager::getEnchantmentByName($id), $removed->getLevel()));
                            $tag = $itemClickedWith->getNamedTag()->getInt("percentage");
                            $book->getNamedTag()->setInt("success", $tag);
                            $book->getNamedTag()->setInt("destroy", mt_rand(0, 40));
                            $rarity = $removed->getType()->getRarity();
                            $level = $removed->getLevel();
                            $book->setCustomName("§r§l{$rarity}{$removed->getType()->getName()} " . \DaPigGuy\PiggyCustomEnchants\utils\Utils::getRomanNumeral($level));
                            $this->setEnchantmentLore($book);
                            $inv->addItem($book);
                            $transaction->getSource()->getWorld()->addSound($transaction->getSource()->getLocation(), new XpLevelUpSound(100));
                        }
                    }
                }
            }
        }
    }

    /**
     * @param InventoryTransactionEvent $event
     * @priority HIGH
     * @ignoreCancelled true
     */
    public function onTransmogScroll(InventoryTransactionEvent $event): void
    {
        $transaction = $event->getTransaction();
        $actions = $transaction->getActions();
        $oldToNew = isset(array_keys($actions)[0]) ? $actions[array_keys($actions)[0]] : null;
        $newToOld = isset(array_keys($actions)[1]) ? $actions[array_keys($actions)[1]] : null;
        if ($oldToNew instanceof SlotChangeAction && $newToOld instanceof SlotChangeAction) {
            $itemClicked = $newToOld->getSourceItem();
            $itemClickedWith = $oldToNew->getSourceItem();
            if ($itemClickedWith->getNamedTag()->getTag("transmogscroll") !== null) {
                $enchants = $itemClicked->getEnchantments();
                $enchantmentSuccessful = false;

                if (empty($enchants)) {
                    return;
                }
                $enchants = \DaPigGuy\PiggyCustomEnchants\utils\Utils::sortEnchantmentsByRarity($enchants);
                $itemClicked->removeEnchantments();
                foreach ($enchants as $enchant) {
                    $itemClicked->addEnchantment($enchant);
                    $enchantmentSuccessful = true;
                }

                if ($enchantmentSuccessful) {
                    $event->cancel();
                    $newToOld->getInventory()->setItem($newToOld->getSlot(), $itemClicked);
                    $oldToNew->getInventory()->setItem($oldToNew->getSlot(), VanillaItems::AIR());
                }
                $amount = count($enchants);
                if ($itemClicked->hasCustomName()) {
                    $itemClicked->setCustomName($itemClicked->getCustomName() . " §r§d§l[§r§b§l{$amount}§r§d§l]");
                }
                if (!$itemClicked->hasCustomName()) {
                    $itemClicked->setCustomName($itemClicked->getName() . " §r§d§l[§r§b§l{$amount}§r§d§l]");
                }
                $newToOld->getInventory()->setItem($newToOld->getSlot(), $itemClicked);
            }
        }
    }


    /**
     * @param InventoryTransactionEvent $event
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function onApplyDust(InventoryTransactionEvent $event): void
    {
        $transaction = $event->getTransaction();
        $actions = $transaction->getActions();
        $item = Utils::getConfigurations("items")->getNested("enchantdust.item");
        $oldToNew = isset(array_keys($actions)[0]) ? $actions[array_keys($actions)[0]] : null;
        $newToOld = isset(array_keys($actions)[1]) ? $actions[array_keys($actions)[1]] : null;
        if ($oldToNew instanceof SlotChangeAction && $newToOld instanceof SlotChangeAction) {
            $itemClicked = $newToOld->getSourceItem();
            $itemClickedWith = $oldToNew->getSourceItem();
            if ($itemClickedWith->getTypeId() === StringToItemParser::getInstance()->parse($item)->getTypeId() && $itemClicked->getTypeId() !== VanillaItems::AIR()->getTypeId()) {
                if ($itemClickedWith->getNamedTag()->getTag("enchantdust") !== null) {
                    if ($itemClicked->getNamedTag()->getTag("successbook") !== null) {
                        $oldLore = $itemClicked->getLore();
                        $countLine = 10000;

                        $new = (int)$itemClicked->getNamedTag()->getTag("successbook")->getValue() + $itemClickedWith->getNamedTag()->getTag("enchantdust")->getValue();

                        foreach ($oldLore as $key => $value) {
                            if (str_contains($value, "Success")) {
                                $countLine = $key;
                            }
                        }
                        if ($countLine !== 10000 && isset($oldLore[$countLine])) {
                            unset($oldLore[$countLine]);
                        }
                        if ($new > 100) {
                            $new = 100;
                        }
                        $oldLore[$countLine] = C::RESET . C::GREEN . "$new% Success Rate";

                        $itemClicked->setLore($oldLore);
                        $newTag = new IntTag($new);
                        $itemClicked->getNamedTag()->setTag("successbook", $newTag);
                        $newToOld->getInventory()->setItem($newToOld->getSlot(), $itemClicked);
                        $enchantmentSuccessful = true;
                        if ($enchantmentSuccessful) {
                            $event->cancel();
                            $oldToNew->getInventory()->setItem($oldToNew->getSlot(), VanillaItems::AIR());
                        }
                    }
                }
            }
        }
    }

    /**
     * @param Item $book
     */
    public static function setEnchantmentLore(Item $book): void
    {
        if ($book->hasEnchantments()) {
            foreach ($book->getEnchantments() as $enchants) {
                $successrate = $book->getNamedTag()->getInt("success");
                $destroyrate = $book->getNamedTag()->getInt("destroy");
                $lore = $book->getLore();
                $lore[] = "§r§a$successrate% Success Rate";
                $lore[] = "§r§c$destroyrate% Destroy Rate";
                $lore[] = "";
                if($enchants->getType() instanceof CustomEnchant) {
                    $lore[] = C::RESET . C::YELLOW . CustomEnchantManager::getEnchantment($enchants->getType()->getName())->getDescription();
                    $lore[] = C::RESET . C::WHITE . "Drag 'n Drop on a item to enchant.";
                }
                $book->setLore($lore);
            }
        }
    }
}