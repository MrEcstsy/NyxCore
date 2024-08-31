<?php

namespace wock\NyxCore\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use wock\NyxCore\Utils\Managers\JackpotManager;

class JackpotCommand extends Command
{
    private $jackpotManager;

    public function __construct(JackpotManager $jackpotManager) {
        parent::__construct("jackpot", "Jackpot command");
        $this->jackpotManager = $jackpotManager;
        $this->setPermission("Nyx.jackpot");
    }

    /**
     * @inheritDoc
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::DARK_RED . "You must run this command in-game.");
            return false;
        }

        if (empty($args)) {
            $this->displayJackpotInfo($sender);
            return true;
        }

        $subCommand = strtolower(array_shift($args));

        switch ($subCommand) {
            case "buy":
                $this->handleBuyCommand($sender, $args);
                break;

            case "top":
                // Handle /jackpot top command
                $this->handleTopCommand($sender);
                break;

            default:
                // Display usage information
                $sender->sendMessage(TextFormat::RED . "Usage: /jackpot [buy <x>] | /jackpot top");
                break;
        }

        return true;
    }

    private function displayJackpotInfo(Player $player): void {
        $currentPot = $this->jackpotManager->getJackpotPot();
        $lastDrawingTime = $this->jackpotManager->getLastDrawingTime();
        $timeUntilDrawing = $lastDrawingTime + $this->jackpotManager->getDrawingInterval() - time();

        $player->sendMessage(TextFormat::YELLOW . "Current Jackpot Information:");
        $player->sendMessage(TextFormat::GREEN . "Current Jackpot Pot: $" . number_format($currentPot));
        $player->sendMessage(TextFormat::GREEN . "Time Until Drawing: " . gmdate("H:i:s", $timeUntilDrawing));
    }

    private function handleBuyCommand(Player $player, array $args): void {
        if (empty($args)) {
            $player->sendMessage(TextFormat::RED . "Usage: /jackpot buy <x>");
            return;
        }

        $numTickets = (int) array_shift($args);
        if ($numTickets <= 0) {
            $player->sendMessage(TextFormat::RED . "Please specify a valid number of tickets to purchase.");
            return;
        }

        // Call your JackpotManager's purchaseTickets method here
        $success = $this->jackpotManager->purchaseTickets($player, $numTickets);

        if ($success) {
            // Tickets purchased successfully, display jackpot info
            $this->displayJackpotInfo($player);
        }
    }

    private function handleTopCommand(Player $player): void {
        // Implement logic to display the top jackpot winners here
        $player->sendMessage(TextFormat::YELLOW . "Top Jackpot Winners:");
        // Add your logic to fetch and display the top winners
    }
}
