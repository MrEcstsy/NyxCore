<?php

namespace wock\NyxCore\Utils\Managers;

use cooldogedev\BedrockEconomy\api\BedrockEconomyAPI;
use cooldogedev\BedrockEconomy\api\legacy\ClosureContext;
use pocketmine\player\Player;
use wock\NyxCore\Nyx;
use pocketmine\utils\TextFormat;
use wock\NyxCore\Utils\Utils;

class JackpotManager {

    private Nyx $plugin;

    private int $pot = 0;

    private int|float $drawingInterval = 4; //  * 60 * 60

    private int $lastDrawingTime = 0;

    private int $ticketPrice = 10000; // $10,000 per ticket

    private int $maxTicketsPerRound = 1000;

    public array $ticketsSold = [];

    public function __construct(Nyx $plugin) {
        $this->plugin = $plugin;
    }

    public function getJackpotPot(): int {
        return $this->pot;
    }

    public function getLastDrawingTime(): int {
        return $this->lastDrawingTime;
    }

    public function purchaseTickets(Player $player, int $numTickets): bool {
        $totalCost = $this->ticketPrice * $numTickets;
        $playerName = $player->getName();

        if (!isset($this->ticketsSold[$playerName])) {
            $this->ticketsSold[$playerName] = 0;
        }

        BedrockEconomyAPI::legacy()->subtractFromPlayerBalance(
            $playerName,
            $totalCost,
            ClosureContext::create(
                function (bool $wasUpdated) use ($player, $numTickets, $totalCost, $playerName): void {
                    $this->ticketsSold[$playerName] += $numTickets; // Line 43
                    $this->pot += $totalCost;
                    $player->sendMessage(TextFormat::GREEN . "You have purchased $numTickets tickets for a total cost of $" . number_format($totalCost) . ".");
                    $player->sendMessage(TextFormat::GREEN . "Current jackpot pot: $" . number_format($this->pot));
                },
            )
        );

        return true;
    }

    public function drawWinner(): void {
        if (empty($this->ticketsSold)) {
            $this->plugin->getServer()->broadcastMessage(TextFormat::YELLOW . "No winner this time. No participants.");
            return;
        }

        $totalTickets = array_sum($this->ticketsSold);

        $chances = [];
        foreach ($this->ticketsSold as $playerName => $numTickets) {
            $chance = $numTickets / $totalTickets;
            $chances[$playerName] = $chance;
        }

        $winner = $this->selectWinner($chances);

        $prize = $this->pot * (1 - Utils::getConfigurations()->get("tax.jackpot") / 100);

        BedrockEconomyAPI::legacy()->addToPlayerBalance(
            $winner,
            $prize,
            ClosureContext::create(
                function (bool $wasUpdated) use ($winner, $prize) {
                    if ($wasUpdated) {
                        $this->plugin->getServer()->broadcastMessage(TextFormat::YELLOW . "The Jackpot has been won by $winner!");
                        $this->plugin->getServer()->broadcastMessage(TextFormat::GREEN . "Prize: $" . number_format($prize));
                    }
                }
            )
        );

        $this->resetJackpot();
    }

    private function selectWinner(array $chances): string {
        $rand = mt_rand() / mt_getrandmax();
        $accumulatedChances = 0;

        foreach ($chances as $playerName => $chance) {
            $accumulatedChances += $chance;
            if ($rand <= $accumulatedChances) {
                return $playerName;
            }
        }

        return array_key_first($chances);
    }

    private function resetJackpot(): void {
        $this->pot = 0;
        $this->ticketsSold = [];
    }

    public function startJackpotRoundIfTimeElapsed(): void {
        $currentTime = time();
        if ($currentTime - $this->lastDrawingTime >= $this->drawingInterval) {
            $this->drawWinner();
            $this->lastDrawingTime = $currentTime;
        }
    }

    public function getWinner(): ?string {
        $participants = array_keys($this->ticketsSold);
        if (empty($participants)) {
            return null; // No participants, no winner
        }
        $winnerIndex = mt_rand(0, count($participants) - 1);
        return $participants[$winnerIndex];
    }

    public function getDrawingInterval(): int {
        return $this->drawingInterval;
    }

}
