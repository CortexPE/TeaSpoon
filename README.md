<h1>TeaSpoon<img src="https://raw.githubusercontent.com/CortexPE/stuff/master/TeaSpoonLogo.png" height="64" width="64" align="left"></img></h1>
<br />

[![Poggit](https://poggit.pmmp.io/ci.shield/CortexPE/TeaSpoon/~)](https://poggit.pmmp.io/ci/CortexPE/TeaSpoon/~) [![Donate](https://img.shields.io/badge/donate-PayPal-yellow.svg?style=flat-square)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=MABFZPDR8F5UG) [![HitCount](http://hits.dwyl.io/CortexPE/TeaSpoon.svg)](http://hits.dwyl.io/CortexPE/TeaSpoon) [![Contributions Welcome](https://img.shields.io/badge/contributions-welcome-brightgreen.svg?style=flat-square)](https://github.com/CortexPE/TeaSpoon/pulls) [![License](https://img.shields.io/badge/license-GNU%20AGPL%20v3-brightgreen.svg?style=flat-square)](https://github.com/CortexPE/TeaSpoon/blob/master/LICENSE) [![Discord](https://img.shields.io/discord/350333413737365522.svg?style=flat-square&label=discord%20chat)](https://discord.gg/QA3eSjw)

A Massive PocketMine-MP plugin designed and is aiming to extend PMMP's functionalities (Without completely changing it) to Make it more Vanilla-Like.

A/N: If you don't like it / hate it, Then don't even bother using it. It's that simple... TeaSpoon was made for those who need it. :wink:

Contributions are very welcome :smile:<br />You may contribute by opening a Pull Request and if it has been proven to be correct & working, I'll surely merge it.

__[Discord Group to 'talk-to-a-human-for-support' and for questions](https://discordapp.com/invite/7y8WM4F)__
 
# Installation
Installation is easy, Just download the latest phar from [Poggit](https://poggit.pmmp.io/ci/CortexPE/TeaSpoon/~) then put it to your ```./plugins/``` folder. Restart your server. And you're basically done.

# Issue Reporting
 - ALWAYS use the [LATEST PocketMine-MP Build](https://jenkins.pmmp.io/job/PocketMine-MP/lastSuccessfulBuild/artifact/) to use this plugin
 - Remove any plugins that may interfere with TeaSpoon's current features (if the feature cannot be disabled in the config.yml file)
 - Including the "Steps to Reproduce" in the issue report would be really helpful in fixing bugs.
 - To help me determine why the issue occurs, including the TeaSpoonDump from ```/bugreport``` lets me view all the necessary server information to easily determine incompatibility issues and mis-configuration issues. <sub>Privacy Concerns? You can check the code for yourself <a href="https://github.com/CortexPE/TeaSpoon/blob/master/src/CortexPE/commands/BugReportCommand.php">here</a> ;)</sub>
 - The only supported branch is PMMP's 'master' branch. Issues regarding other branches will be closed.

# FAQs
### MobAI:
&nbsp;&nbsp;&nbsp;&nbsp;For now, you can use [PureEntitiesX](https://github.com/RevivalPMMP/PureEntitiesX) while disabling ```entities.register``` in TeaSpoon's Configuration File.
### Redstone System:
&nbsp;&nbsp;&nbsp;&nbsp;Being worked on... :wink:

***[Read More...](https://github.com/CortexPE/TeaSpoon/wiki#faqs)***

# Finished & Planned Features
 - Worlds
  - [X] Dimensions
    - [X] Nether Dimension
    - [X] End Dimension
    - [X] Fully Functional Nether Portal Frame and Block
    - [X] Funtional END_PORTAL Block (Portal Soon)
  - [X] Weather System
  - [ ] Temperature System
 - Blocks
   <!-- - [X] EnderChests (Already Implemented by PMMP) -->
   - [X] EndPortal
   - [X] Portal (Nether Portal Block)
   - [X] DragonEgg
   - [X] Beacon
   - [X] SlimeBlock
   - [X] Vanilla-Like MobSpawner (Credits: [XenialDan](https://github.com/thebigsmileXD))
   - [X] Working Shulker Boxes
   - [X] Hoppers
 - Items
   - [X] Vanilla Enchants (Progress: 98% | Credits to [TheAz928](https://github.com/TheAz928) for some of the values)
   - [X] Armor Damage (Currently Broken #BlamePMMP :joy:) // TODO
   - [X] Splash Potions
   - [X] FireCharge
   - [X] Totem of Undying
   - [X] Fully Functional Elytra Wings
   - [X] Firework Rocket (as Elytra Booster)
   - [X] Lingering Potions (Credits: [ClearSkyTeam](https://github.com/ClearSkyTeam))
   - [X] Chorus Fruit (with customizable Delay)
   - [X] FishingRod (Fully working Fishing System)
   - [X] Vanilla-Like "Instant-Armor-Equipment"
   - [X] CustomPotions API
   ```php
   // Example:
   \CortexPE\item\Potion::registerPotion(101, "TEST", [[Effect::HEALTH_BOOST, 100 * 20, 100], [Effect::STRENGTH, 100 * 20, 100]]);
   ```
 - Entities & Mobs
   - [X] XP Drops
   - [X] Projectiles
     - [X] EnderPearls
     - [X] Snowballs
     - [X] Eggs
     - [X] Arrows with potion effects
     - [X] EnchantingBottle
     - [X] SplashPotion
     - [X] LingeringPotion
   - [ ] Entities
     - [X] Lightning
     - [X] EndCrystal
   <!--   - [X] XPOrbs -->
   - [X] Mobs
     - [X] Bat
     - [X] Blaze
     - [X] CaveSpider
     - [X] Chicken
     - [X] Cow
     - [X] Creeper
     - [X] Donkey
     - [X] ElderGuardian
     - [X] EnderDragon
     - [X] Enderman
     - [X] Endermite
     - [X] Evoker
     - [X] Ghast
     - [X] Guardian
     - [X] Horse
     - [X] Husk
     - [X] IronGolem
     - [X] Llama
     - [X] MagmaCube
     - [X] Mooshroom
     - [X] Mule
     - [X] Ocelot
     - [X] Parrots
     - [X] Pig
     - [X] PolarBear
     - [X] Rabbit
     - [X] Sheep
     - [X] Shulker
     - [X] Silverfish
     - [X] Skeleton
     - [X] Skeleton Horse
     - [X] Slime
     - [X] SnowGolem
     - [X] Spider
     - [X] Stray
     - [X] Vex
     - [X] Vindicator
     - [X] Witch
     - [X] Wither
     - [X] WitherSkeleton
     - [X] Wolf
     - [X] Zombie Horse
     - [X] Zombie Pigman
     - [X] Zombie Villager
 - Commands
   - [X] More Vanilla-Like /kill command (Not perfect)
   - [X] World Command
   - [X] Clear Command
   - [X] PlaySound Command
 - Utils
   - [X] TextFormat::center like PC or MiNET. (Credits: [Turanic](https://github.com/TuranicTeam/Turanic))
<br />***More to do...***
