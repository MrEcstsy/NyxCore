<?php

declare(strict_types=1);

namespace wock\NyxCore\Utils\Managers;

use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class GiftsManager {

    private string $dataFile;
    private Database $database;

    public function __construct(string $dataFile, Database $database) {
        $this->dataFile = $dataFile;
        $this->database = $database;
        $this->initDataFile();
    }

    public function initDataFile() {
        // Check if the database table exists, create it if necessary
        $this->database->executeChange(DatabaseStmts::INIT_TABLE);
    }

    public function sendGift(Player $sender, Player $recipient) {
        $item = $sender->getInventory()->getItemInHand();

        if ($item->equals(VanillaItems::AIR())) {
            $sender->sendMessage(TextFormat::RED . "You must be holding an item to send it as a gift.");
            return;
        }

        $sender->getInventory()->setItemInHand(VanillaItems::AIR());
        $recipientName = $recipient->getName();

        // Save the gift data to the database
        $this->database->executeChange(DatabaseStmts::INSERT_GIFT, [
            "sender" => $sender->getName(),
            "recipient" => $recipientName,
            "item" => $this->itemToBinary($item)
        ]);

        $sender->sendMessage(TextFormat::GREEN . "Gift sent to $recipientName.");
        $recipient->sendMessage(TextFormat::YELLOW . "You received a gift from " . $sender->getName() . "!");
    }

    public function getGifts(Player $player): array {
        $playerName = $player->getName();

        // Retrieve the gifts for the player from the database
        $result = $this->database->executeSelect(DatabaseStmts::SELECT_GIFTS, [
            "recipient" => $playerName
        ]);

        $gifts = [];
        foreach ($result as $row) {
            $gifts[] = $this->binaryToItem($row["item"]);
        }

        return $gifts;
    }

    public function removeGift(Player $player, Item $gift) {
        $playerName = $player->getName();

        // Remove the gift from the database
        $this->database->executeChange(DatabaseStmts::DELETE_GIFT, [
            "recipient" => $playerName,
            "item" => $this->itemToBinary($gift)
        ]);
    }

    public function clearGifts(Player $player) {
        $playerName = $player->getName();

        // Remove all gifts for the player from the database
        $this->database->executeChange(DatabaseStmts::DELETE_ALL_GIFTS, [
            "recipient" => $playerName
        ]);
    }

    private function itemToBinary(Item $item): string {
        $itemId = $item->getTypeId();
        $itemMeta = $item->getStateId();
        $itemCount = $item->getCount();
        // Add any additional item data you want to store

        // Generate the binary string representation using MySQL-specific encoding
        return pack("C*", $itemId, $itemMeta, $itemCount);
    }

    private function binaryToItem(string $binary): Item {
        // Extract the item data from the binary string
        $itemId = unpack("C1", $binary)[1];
        $itemMeta = unpack("C1", substr($binary, 1, 1))[1];
        $itemCount = unpack("C1", substr($binary, 2, 1))[1];
        // Retrieve any additional item data

        // Create the item object based on the stored data
        $item = Item::legacyJsonDeserialize($itemId);
        // Set any additional item data
        // For example, $item->setCustomName($customName);

        return $item;
    }

}
