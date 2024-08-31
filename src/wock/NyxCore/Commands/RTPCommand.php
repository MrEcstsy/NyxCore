<?php

namespace wock\NyxCore\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\world\format\Chunk;
use wock\NyxCore\Nyx;

class RTPCommand extends Command
{

    public Nyx $plugin;

    public function __construct(Nyx $plugin)
    {
        parent::__construct("rtp", "Teleport to a random location in the world", "/rtp");
        $this->plugin = $plugin;
        $this->setPermission("Nyx.rtp");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "This command can only be used by players.");
            return true;
        }

        $world = $sender->getWorld();
        $x = mt_rand(-3000, 3000);
        $z = mt_rand(-3000, 3000);

        // Calculate chunk coordinates using Chunk::COORD_BIT_SIZE
        $chunkX = $x >> Chunk::COORD_BIT_SIZE;
        $chunkZ = $z >> Chunk::COORD_BIT_SIZE;

        // Check if the chunk at the selected coordinates has been generated
        if (!$world->isChunkLoaded($chunkX, $chunkZ)) {
            $world->loadChunk($chunkX, $chunkZ);
        }

        // Get the entire chunk
        $chunk = $world->getChunk($chunkX, $chunkZ);

        $y = $chunk->getHighestBlockAt($x, $z);

        $sender->teleport(new Vector3($x, $y, $z));
        $sender->sendMessage(TextFormat::GREEN . "You have been teleported to a random location!");

        return true;
    }
}