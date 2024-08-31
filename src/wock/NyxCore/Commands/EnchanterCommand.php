<?php

namespace wock\NyxCore\Commands;

use DaPigGuy\PiggyCustomEnchants\enchants\CustomEnchant;
use DaPigGuy\PiggyCustomEnchants\utils\Utils;
use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as C;
use wock\NyxCore\Items\Rewards;
use wock\NyxCore\Nyx;

class EnchanterCommand extends Command
{
    /**
     * @var Nyx
     */
    private Nyx $plugin;

    /**
     * ItemsCommand constructor.
     * @param Nyx $plugin
     */
    public function __construct(Nyx $plugin)
    {
        $this->plugin = $plugin;
        parent::__construct("enchanter");
        $this->setUsage("/enchanter");
        $this->setPermission("Nyx.enchanter");
    }

    public function execute(CommandSender $sender, string $label, array $args)
    {
        if ($sender instanceof Player) {
            $form = new SimpleForm(function ($sender, $data) {
                if (!is_null($data)) $this->confirm($sender, $data);
            });
            $form->setTitle("Custom Enchants Shop");
            $form->addButton(EnchanterCommand . phpUtils::getColorFromRarity(CustomEnchant::SIMPLE) . "Simple");
            $form->addButton(EnchanterCommand . phpUtils::getColorFromRarity(CustomEnchant::UNIQUE) . "Unique");
            $form->addButton(EnchanterCommand . phpUtils::getColorFromRarity(CustomEnchant::ELITE) . "Elite");
            $form->addButton(EnchanterCommand . phpUtils::getColorFromRarity(CustomEnchant::ULTIMATE) . "Ultimate");
            $form->addButton(EnchanterCommand . phpUtils::getColorFromRarity(CustomEnchant::LEGENDARY) . "Legendary");
            $sender->sendForm($form);
            return;
        }
    }

    public function confirm(Player $player, int $dataid): void
    {
        $rarity = $this->dataIdToRarity($dataid);
        if ($rarity === null) {
            return;
        }
        $cost = $this->getCost($rarity);
        $form = new CustomForm(function (Player $player, $data) use ($rarity, $cost) {
            if ($data !== null) {
                $amount = $data[2];
                $xpPrice = $cost * $amount;
                if($player->getXpManager()->getCurrentTotalXp() >= $xpPrice) {
                    $item = Rewards::getRarityBook($rarity, $amount);
                    if($player->getInventory()->canAddItem($item)) {
                        $player->getInventory()->addItem($item);
                        $player->getXpManager()->subtractXp($xpPrice);
                    } else {
                        $player->sendMessage(C::RED . "You do not have enough Inventory Space to buy " . C::AQUA . $amount . C::RED . " books");
                        return;
                    }
                    $player->sendMessage(C::GREEN . "Successfully purchased " . C::AQUA . $amount . C::GREEN . " books for " . C::AQUA . $xpPrice . "XP");
                } else {
                    $player->sendMessage(C::RED . "You do not have enough XP to buy $amount Books.\n" . C::AQUA . "Required XP: " . C::GREEN . $xpPrice);
                }
            }
        });
        $form->setTitle(EnchanterCommand . phpUtils::getColorFromRarity($rarity) . \wock\NyxCore\Utils\Utils::rarityToName($rarity));
        $form->addLabel(C::GREEN . "How many books do you want to purchase?");
        $form->addLabel(C::AQUA . "Cost Per Book: $cost XP");
        $form->addSlider(C::GREEN . "Amount", 1, 64, 1);
        $player->sendForm($form);
    }

    public function dataIdToRarity(int $dataid): ?int
    {
        switch ($dataid) {
            case 0:
                return 888;
            case 1:
                return 887;
            case 2:
                return 886;
            case 3:
                return 885;
            case 4:
                return 884;
        }
        return null;
    }

    public function getCost(int $rarity): ?int
    {
        switch ($rarity) {
            case 888:
                return $this->plugin->getConfig()->get("simple");
            case 887:
                return $this->plugin->getConfig()->get("unique");
            case 886:
                return $this->plugin->getConfig()->get("elite");
            case 885:
                return $this->plugin->getConfig()->get("ultimate");
            case 884:
                return  $this->plugin->getConfig()->get("legendary");
        }
        return null;
    }
}