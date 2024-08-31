<?php

namespace wock\NyxCore\Commands;

use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use pocketmine\utils\TextFormat;
use wock\NyxCore\Nyx;

class CoinFlipCommand extends Command implements PluginOwned
{
    public Nyx $plugin;

    public function __construct(Nyx $plugin) {
        parent::__construct("coinflip", "Open the coinflip menu");
        $this->setAliases([
            "cf"
        ]);
        $this->setPermission("Nyx.coinflip");
        $this->plugin = $plugin;
    }

    /**
     * @inheritDoc
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::DARK_RED . "You must run this command in-game.");
            return true;
        }

        if (empty($args)) {
            // Show the main coinflip menu/form
            $this->sendCoinFlipMenu($sender);
            return true;
        }

        $subcommand = array_shift($args);

        switch (strtolower($subcommand)) {
            case "add":
                // Implement /coinflip add <amount> <heads/tails> logic here
                // You can use a custom form to get the input from the player
                $this->sendAddCoinFlipForm($sender);
                return true;

            case "remove":
                // Implement /coinflip remove logic here
                return true;

            default:
                $sender->sendMessage(TextFormat::RED . "Usage: /coinflip [add|remove]");
                return true;
        }
    }

    private function sendCoinFlipMenu(Player $player) {
        $form = new SimpleForm(function (Player $player, $data) {
            if ($data === null) {
                // Player closed the form
                return;
            }

            switch ($data) {
                case 0:
                    // Implement the logic for starting a new coinflip here
                    $this->sendAddCoinFlipForm($player);
                    break;
                case 1:
                    // Implement any other menu options here
                    // For example, view active coinflips, etc.
                    break;
            }
        });

        $form->setTitle("CoinFlip Menu");
        $form->setContent("Choose an option:");
        $form->addButton("Start a New CoinFlip");
        $form->addButton("View Active CoinFlips");
        $form->sendToPlayer($player);
    }

    private function sendAddCoinFlipForm(Player $player) {
        $form = new CustomForm(function (Player $player, $data) {
            if ($data === null) {
                // Player closed the form
                return;
            }

            // Extract input from the form
            $amount = $data[0];
            $choice = $data[1];

            // Implement the logic for starting a new coinflip
            // You can use $amount and $choice to determine the bet and choice

            // Example: Start a new coinflip with the extracted data
            // $this->startCoinflip($player, $amount, $choice);
        });

        $form->setTitle("Start a New CoinFlip");
        $form->addInput("Enter the bet amount:", "10"); // Default value
        $form->addDropdown("Choose heads or tails:", ["heads", "tails"]);
        $form->sendToPlayer($player);
    }

    public function getOwningPlugin(): Nyx
    {
        return $this->plugin;
    }
}