<?php
namespace Sh00ckBass\EconomyBank\Command;

use Sh00ckBass\EconomyBank\Main;
use onebone\economyapi\EconomyAPI;
use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;

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

class BankCommand extends Command
{
    public function __construct(Main $plugin)
    {
        parent::__construct("bank", "Open the Bank", "/bank");
        $this->plugin = $plugin;
    }
    
    public function execute(CommandSender $s, string $commandLabel, array $args) : bool
    {        
        $this->formBank($s);
        return false;
    }

    //hauptmenu
    public function formBank(Player $player)
    {
        $config = new Config($this->plugin->getDataFolder() . "config.yml", Config::YAML);
        $api = $this->plugin->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function(Player $player, int $data = null){
            $result = $data;
            if($result === null) {
                return true;
            }
            switch($result){
                case "0":
                    $this->formBankProfile($player);
                    break;
                case "1":
                    $this->formInfo($player);
                    break;
            }
        });
        $form->setTitle("§6Bank");
        $form->setContent(str_replace(["{player}", "&"], [$player->getPlayer(), "§"], $config->get("welcome-message")));
        $form->addButton("§aBank-Profile", 1, "http://avengetech.me/items/133-0.png");
        $form->addButton("§6Info", 1, "http://avengetech.me/items/339-0.png");
        $form->sendToPlayer($player);
        return $form;
    }
    //info menu
    public function formInfo(Player $player)
    {
        
        $config = new Config($this->plugin->getDataFolder() . "config.yml", Config::YAML);
        $api = $this->plugin->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function(Player $player, int $data = null){
            $result = $data;
            switch($result){
                case "0":
                    $this->formBank($player);
                    break;
            }
        });
        $form->setTitle("§6Info");
        $form->setContent("§bAuthor: §aSh00ckBass \n" . "§bVersion: §a1.0.0 \n" . "§bDiscord: §aSh00ckBass#7301");
        $form->addButton(str_replace("&", "§", $config->get("back-button")));
        $form->sendToPlayer($player);
        return $form;
    }
    //profile menu
    public function formBankProfile(Player $player)
    {
        $msgs = new Config($this->plugin->getDataFolder() . "config.yml", Config::YAML);
        $config = new Config($this->plugin->getDataFolder() . "money/" . "money.yml", Config::YAML);
        $api = $this->plugin->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function(Player $player, int $data = null){
            $result = $data;
            $msgs = new Config($this->plugin->getDataFolder() . "config.yml", Config::YAML);
            switch($result){
                case "0":
                    if(EconomyAPI::getInstance()->myMoney($player) != 0) {
                        $this->formBankEinzahlen($player);
                    } else {
                        $player->sendMessage(str_replace("&", "§", $msgs->get("littlemoneytodeposit")));
                    }
                    break;
                case "1":
                    $config = new Config($this->plugin->getDataFolder() . "money/" . "money.yml", Config::YAML);
                    if($config->get($player->getUniqueId()->toString()) >= 0) {
                        $this->formBankAuszahlen($player);
                    } else {
                        $player->sendMessage(str_replace("&", "§", $msgs->get("littlemoneytopayout")));
                    }
                    break;
                case "2":
                    $this->formBank($player);
                    break;
            }
        });
        $form->setTitle("§6Profile");
        $form->setContent(str_replace(["{money}", "{bank}", "&"], [EconomyAPI::getInstance()->myMoney($player), $config->get($player->getUniqueId()->toString()), "§"], $msgs->get("profile-message")));
        $form->addButton(str_replace("&", "§", $msgs->get("deposit")), 1, "http://avengetech.me/items/388-0.png");
        $form->addButton(str_replace("&", "§", $msgs->get("payout")), 1, "http://avengetech.me/items/266-0.png");
        $form->addButton(str_replace("&", "§", $msgs->get("back-button")));
        $form->sendToPlayer($player);
        return $form;
    }
    //einzahlen menu
    public function formBankEinzahlen(Player $player)
    {
        $msgs = new Config($this->plugin->getDataFolder() . "config.yml", Config::YAML);
        $config = new Config($this->plugin->getDataFolder() . "money/" . "money.yml", Config::YAML);
        $api = $this->plugin->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createCustomForm(function(Player $player, array $data = null){
            $msgs = new Config($this->plugin->getDataFolder() . "config.yml", Config::YAML);
            if($data === null){
                return true;
            }
            $config = new Config($this->plugin->getDataFolder() . "money/" . "money.yml", Config::YAML);
            $anzahl = $data[1];
            
            if((EconomyAPI::getInstance()->myMoney($player) - $anzahl) >= 0) {
                EconomyAPI::getInstance()->reduceMoney($player, $anzahl);
                $config->set($player->getUniqueId()->toString(), $config->get($player->getUniqueId()->toString()) + $anzahl);
                $config->save();
                $player->sendMessage(str_replace("&", "§", $msgs->get("successfullydeposited")));
            } else {
                $player->sendMessage(str_replace("&", "§", $msgs->get("littlemoneytodeposit")));
            }
        });
            $form->setTitle("§6Menu");
            $form->addLabel(str_replace(["{money}", "{bank}", "&"], [EconomyAPI::getInstance()->myMoney($player), $config->get($player->getUniqueId()->toString()), "§"], $msgs->get("profile-message")));
            $form->addInput(str_replace("&", "§", $msgs->get("specifymoneydeposit")));
            $form->sendToPlayer($player);
            return $form;
    }
    //auszahlen menu
    public function formBankAuszahlen(Player $player)
    {
        $msgs = new Config($this->plugin->getDataFolder() . "config.yml", Config::YAML);
        $config = new Config($this->plugin->getDataFolder() . "money/" . "money.yml", Config::YAML);
        $api = $this->plugin->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createCustomForm(function(Player $player, array $data = null){
            $msgs = new Config($this->plugin->getDataFolder() . "config.yml", Config::YAML);
            if($data === null){
                return true;
            }
            $config = new Config($this->plugin->getDataFolder() . "money/" . "money.yml", Config::YAML);
            $anzahl = $data[1];
            
            if(($config->get($player->getUniqueId()->toString()) - $anzahl) >= 0) {
                EconomyAPI::getInstance()->addMoney($player, $anzahl);
                $config->set($player->getUniqueId()->toString(), $config->get($player->getUniqueId()->toString()) - $anzahl);
                $config->save();
                $player->sendMessage(str_replace("&", "§", $msgs->get("successfullypaiedout")));
            } else {
                $player->sendMessage(str_replace("&", "§", $msgs->get("littlemoneytopayout")));
            }
        });
            $form->setTitle("§6Menu");
            $form->addLabel(str_replace(["{money}", "{bank}", "&"], [EconomyAPI::getInstance()->myMoney($player), $config->get($player->getUniqueId()->toString()), "§"], $msgs->get("profile-message")));
            $form->addInput(str_replace("&", "§", $msgs->get("specifymoneypayout")));
            $form->sendToPlayer($player);
            return $form;
    }
}

