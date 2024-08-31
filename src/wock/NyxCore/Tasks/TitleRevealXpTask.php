<?php

namespace wock\NyxCore\Tasks;

use cooldogedev\BedrockEconomy\api\BedrockEconomyAPI;
use cooldogedev\BedrockEconomy\api\legacy\ClosureContext;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;
use wock\NyxCore\Nyx;

class TitleRevealXpTask extends Task {

    private Player $player;
    private int $xp;
    private int $revealedPosition = 0;
    public string $obfuscatedTitle = '';

    public function __construct(Player $player, int $xp) {
        $this->player = $player;
        $this->xp = $xp;

        $this->obfuscatedTitle = "§r§5§k" . implode("", array_fill(0, strlen((string) $this->xp), "#")) . "§r§a XP";
    }

    public function onRun() : void {
        if ($this->revealedPosition >= strlen((string) $this->xp)) {
            $this->getHandler()->cancel();
            $revealedTitle = "§r§a" . TextFormat::GREEN . number_format($this->xp) . " XP";
            $this->player->sendTitle($revealedTitle, "§r§6Opening pouch...", 1, 2);
            $this->player->getXpManager()->addXp($this->xp);
            return;
        }

        $this->revealedPosition++;
        $revealedTitle = "§r§a" . TextFormat::GREEN . number_format(substr((string) $this->xp, 0, $this->revealedPosition)) . "§5§k" . implode("", array_fill(0, strlen((string) $this->xp) - $this->revealedPosition, "#")) . "§r§a XP";
        $this->player->sendTitle($revealedTitle, "§r§6Opening pouch...", 0, 1);
    }
}
