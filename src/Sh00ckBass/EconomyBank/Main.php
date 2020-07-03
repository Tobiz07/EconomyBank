<?php

declare(strict_types = 1);

namespace Sh00ckBass\EconomyBank;

use Sh00ckBass\EconomyBank\Command\BankCommand;
use Sh00ckBass\EconomyBank\Events\DeathEvent;
use Sh00ckBass\EconomyBank\Events\JoinEvent;
use pocketmine\plugin\PluginBase;

/*******************************************************************************
 Urheberrechtshinweis
 Copyright © Tobias Zechmann 2020
 
 Alle Inhalte dieses Quelltextes sind urheberrechtlich geschützt.
 Das Urheberrecht liegt, soweit nicht ausdrücklich anders gekennzeichnet,
 bei Tobias Zechmann. Alle Rechte vorbehalten.
 Jede Art der Vervielfältigung, Verbreitung, Vermietung, Verleihung,
 öffentlichen Zugänglichmachung oder andere Nutzung
 bedarf der ausdrücklichen, schriftlichen Zustimmung von Tobias Zechmann.
 *******************************************************************************/

class Main extends PluginBase
{
    
    public function onEnable()
    {
        
        @mkdir($this->getDataFolder() . "/Money");
        $this->saveResource("config.yml", false);
        $logger = $this->getLogger();
        $logger->info("Plugin aktiv!");
        $logger->info("Made by Sh00ckBass");
        
        $this->getServer()->getCommandMap()->register("bank", new BankCommand($this));
        $this->getServer()->getPluginManager()->registerEvents(new JoinEvent($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new DeathEvent($this), $this);
    }
    
}
