<?php

namespace wock\NyxCore\Tasks;

use cooldogedev\BedrockEconomy\api\BedrockEconomyAPI;
use cooldogedev\BedrockEconomy\api\legacy\ClosureContext;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;
use wock\NyxCore\Nyx;

class TitleRevealTask extends Task {

    private Player $player;
    private int $money;
    private int $revealedPosition = 0;
    public string $obfuscatedTitle = '';

    public function __construct(Player $player, int $money) {
        $this->player = $player;
        $this->money = $money;

        // Create the initial obfuscated title with all digits as placeholders
        $this->obfuscatedTitle = "§r§5§k" . implode("", array_fill(0, strlen((string) $this->money), "#"));
    }

    public function onRun() : void {
        if ($this->revealedPosition >= strlen((string) $this->money)) {
            $this->getHandler()->cancel();
            $revealedTitle = "§r§a$" . TextFormat::GREEN . number_format($this->money);
            $this->player->sendTitle($revealedTitle, "§r§6Opening pouch...", 1, 2);
            BedrockEconomyAPI::legacy()->addToPlayerBalance(
                $this->player->getName(),
                $this->money,
                ClosureContext::create(
                    function (bool $wasUpdated) {
                        // Handle callback if needed
                    }
                )
            );
            return;
        }

        // Reveal the next digit
        $this->revealedPosition++;
        $revealedTitle = "§r§a$" . TextFormat::GREEN . number_format(substr((string) $this->money, 0, $this->revealedPosition)) . "§5§k" . implode("", array_fill(0, strlen((string) $this->money) - $this->revealedPosition, "#"));
        $this->player->sendTitle($revealedTitle, "§r§6Opening pouch...", 0, 1);
    }
}
