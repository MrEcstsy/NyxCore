<?php

declare(strict_types=1);

namespace wock\NyxCore\Entity\Bosses;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use wock\NyxCore\NyxPlayer;
use wock\NyxCore\Utils\EntityUtils;

class LunarSerpent extends Living {

    protected function getInitialSizeInfo() : EntitySizeInfo{
        return new EntitySizeInfo(0.4, 1.2);
    }

    public static function getNetworkTypeId() : string{
        return EntityIds::STRAY;
    }

    protected function initEntity(CompoundTag $nbt) : void{
        parent::initEntity($nbt);
        $this->setScale(2.5);

        $this->setNameTag($this->getName());
        $this->setNameTagAlwaysVisible();

        $this->setCanSaveWithChunk(false);
    }


    public function useAbility(Player $player): void {
        $player->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 20 * 10, 3));

        $player->sendMessage("§r");
        $player->sendMessage("§r§l§a» Lunar Serpent «§r§7 Do you feel a bit §dslow??");
        $player->sendMessage("§r§l§a» Lunar Serpent «§r§7 Feel my power!");
        $player->sendMessage("§r");

        for($i = 0; $i <= 3; $i++){
            EntityUtils::spawnLightning($player, $player->getLocation());
        }
    }


    public function getName() : string{
        return "§r§a§lLunar Serpent";
    }
}