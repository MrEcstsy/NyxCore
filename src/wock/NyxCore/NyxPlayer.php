<?php

declare(strict_types=1);

namespace wock\NyxCore;

use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use pocketmine\Server;

class NyxPlayer {

    private float $health;
    private float $defense;
    private float $strength;
    private float $speed;
    private float $critChance;
    private float $critDamage;
    private float $intelligence;
    private float $seacreatureChance;
    private float $miningFortune;
    private float $farmingFortune;
    private float $foragingFortune;
    private float $magicDamage;
    private string $username;


    private float $maxHealth;
    private float $maxIntelligence;



    //below here will be stats that do not need to be saved but just cached.
    private float $miningWisdom = 0; //At 20 Mining Wisdom, +120 Mining XP would be gained instead of +100 Mining XP.
    private float $combatWisdom = 0; //At 20 Mining Wisdom, +120 Mining XP would be gained instead of +100 Mining XP.
    private float $foragingWisdom = 0; //At 20 Mining Wisdom, +120 Mining XP would be gained instead of +100 Mining XP.
    private float $fishingSpeed = 0;
    private float $miningSpeed = 0;

    public function __construct(Player|string $username, $dataStore /* replace this with the appropriate data source */) {
        $this->username = strtolower($username instanceof Player ? $username->getName() : $username);

        // Fetch data from the provided data source
        $data = $dataStore->getPlayerData($this->username);

        // Extract the relevant data from the fetched array
        $this->health = (float) ($data['health'] ?? 0) + 100;
        $this->intelligence = (float) ($data['intelligence'] ?? 0) + 100;

        $this->setDefense((float) ($data['defense'] ?? 0) + 1);
        $this->setStrength((float) ($data['strength'] ?? 0) + 1);
        $this->setSpeed((float) ($data['speed'] ?? 0) + 100);
        $this->setCritChance((float) $data['critChance']);
        $this->setCritDamage((float) $data['critDamage']);
        $this->setSeacreatureChance((float) $data['seaCreatureChance']);
        $this->setMiningFortune((float) ($data['miningFortune'] ?? 0));
        $this->setFarmingFortune((float) ($data['farmingFortune'] ?? 0));
        $this->setForagingFortune((float) ($data['foragingFortune'] ?? 0));
        $this->setMagicDamage((float) ($data['magicDamage'] ?? 0));

        $this->setMaxHealth($this->health);
        $this->setHealth($this->getHealth());

        $this->setMaxIntelligence($this->intelligence);
        $this->setIntelligence($this->getIntelligence());
    }

    public function getMaxHealth() : float{
        return $this->maxHealth;
    }

    public function getMaxIntelligence() : float{
        return $this->maxIntelligence;
    }

    public function getMiningWisdom() : float{
        return $this->miningWisdom;
    }

    public function setMiningWisdom(float $miningWisdom) : void{
        $this->miningWisdom = $miningWisdom;
    }

    public function setMaxHealth(float $maxHealth) : void{
        $this->maxHealth = $maxHealth;
    }

    public function setMaxIntelligence(float $maxIntelligence) : void{
        $this->maxIntelligence = $maxIntelligence;
    }


    public function getHealth() : float{
        return $this->health;
    }

    public function setHealth(float $health) : void{
        $this->health = $health = min($this->getMaxHealth(), $health);

        //TODO: add grinding world check, this is for now for testing
        if($p = $this->getPlayer()){
            $p->setMaxHealth((int) min(20, 20 + (floor($health / 50) * 2)));


            $left = 100 / $this->getMaxHealth() * $health;

            $visibleHealth = 20 * ($left / 100);
            if($visibleHealth > 0){
                $p->setHealth($visibleHealth);
            }
        }
    }

    public function getDefense() : float{
        return $this->defense;
    }

    public function setDefense(float $defense) : void{
        $this->defense = $defense;
    }

    public function getMiningSpeed() : float{
        return $this->miningSpeed;
    }

    public function setMiningSpeed(float $v) : void{
        $this->miningSpeed = $v;
    }

    public function getStrength() : float{
        return $this->strength;
    }

    public function setStrength(float $strength) : void{
        $this->strength = $strength;
    }


    public function getSpeed() : float{
        return $this->speed;
    }

    public function setSpeed(float $speed) : void{
        if($speed === 0.0) return;

        $this->speed = $speed;

        //TODO: add grinding world check, this is for now for testing
        if($p = $this->getPlayer()){
            $p->setMovementSpeed($speed / 1000);
        }
    }

    public function setMagicDamage(float $v) : void{
        $this->magicDamage = $v;
    }

    public function getMagicDamage() : float{
        return $this->magicDamage;
    }

    public function getCritChance() : float{
        return $this->critChance;
    }

    public function setCritChance(float $critChance) : void{
        $this->critChance = $critChance;
    }

    public function getCritDamage() : float{
        return $this->critDamage;
    }

    public function setCritDamage(float $critDamage) : void{
        $this->critDamage = $critDamage;
    }

    public function getIntelligence() : float{
        return $this->intelligence;
    }

    public function setIntelligence(float $intelligence) : void{
        $this->intelligence = min($this->getMaxIntelligence(), $intelligence);
    }

    public function getSeacreatureChance() : float{
        return $this->seacreatureChance;
    }

    public function setSeacreatureChance(float $seacreatureChance) : void{
        $this->seacreatureChance = $seacreatureChance;
    }

    public function getMiningFortune() : float{
        return $this->miningFortune;
    }

    public function setMiningFortune(float $miningFortune) : void{
        $this->miningFortune = $miningFortune;
    }

    public function getFarmingFortune() : float{
        return $this->farmingFortune;
    }

    public function setFarmingFortune(float $farmingFortune) : void{
        $this->farmingFortune = $farmingFortune;
    }

    public function getForagingFortune() : float{
        return $this->foragingFortune;
    }

    public function setForagingFortune(float $foragingFortune) : void{
        $this->foragingFortune = $foragingFortune;
    }

    public function getFishingSpeed() : float{
        return $this->fishingSpeed;
    }

    public function setFishingSpeed(float $fishingSpeed) : void{
        $this->fishingSpeed = $fishingSpeed;
    }

    public function getCombatWisdom() : float{
        return $this->combatWisdom;
    }

    public function setCombatWisdom(float $combatWisdom) : void{
        $this->combatWisdom = $combatWisdom;
    }

    public function getForagingWisdom() : float{
        return $this->foragingWisdom;
    }

    public function setForagingWisdom(float $foragingWisdom) : void{
        $this->foragingWisdom = $foragingWisdom;
    }

    public function getUsername() : string{
        return $this->username;
    }

    public function setUsername(string $username) : void{
        $this->username = $username;
    }

    public function getPlayer(): ?Player {
        return Server::getInstance()->getPlayerExact(substr($this->username, 0, strpos($this->username, "-profile-")));
    }

}