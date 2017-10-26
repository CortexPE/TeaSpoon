<?php

declare(strict_types=1);

namespace CortexPE\item\enchantment;

class Enchantment extends \pocketmine\item\enchantment\Enchantment {
	public static function init(){
		self::registerEnchantment(new Enchantment(self::BLAST_PROTECTION, "%enchantment.protect.explosion", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_ARMOR));
		self::registerEnchantment(new Enchantment(self::PROJECTILE_PROTECTION, "%enchantment.protect.projectile", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_ARMOR));
		self::registerEnchantment(new Enchantment(self::THORNS, "%enchantment.protect.thorns", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_SWORD));
		self::registerEnchantment(new Enchantment(self::RESPIRATION, "%enchantment.protect.waterbrething", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_FEET));
		self::registerEnchantment(new Enchantment(self::DEPTH_STRIDER, "%enchantment.waterspeed", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_FEET));
		self::registerEnchantment(new Enchantment(self::AQUA_AFFINITY, "%enchantment.protect.wateraffinity", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_FEET));
		self::registerEnchantment(new Enchantment(self::SHARPNESS, "%enchantment.weapon.sharpness", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_SWORD));
		self::registerEnchantment(new Enchantment(self::SMITE, "%enchantment.weapon.smite", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_SWORD));
		self::registerEnchantment(new Enchantment(self::BANE_OF_ARTHROPODS, "%enchantment.weapon.arthropods", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_SWORD));
		self::registerEnchantment(new Enchantment(self::KNOCKBACK, "%enchantment.weapon.knockback", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_SWORD));
		self::registerEnchantment(new Enchantment(self::FIRE_ASPECT, "%enchantment.weapon.fireaspect", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_SWORD));
		self::registerEnchantment(new Enchantment(self::LOOTING, "%enchantment.weapon.looting", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_SWORD));
		self::registerEnchantment(new Enchantment(self::EFFICIENCY, "%enchantment.mining.efficiency", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_TOOL));
		self::registerEnchantment(new Enchantment(self::SILK_TOUCH, "%enchantment.mining.silktouch", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_TOOL));
		self::registerEnchantment(new Enchantment(self::UNBREAKING, "%enchantment.mining.durability", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_TOOL));
		self::registerEnchantment(new Enchantment(self::FORTUNE, "%enchantment.mining.fortune", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_TOOL));
		self::registerEnchantment(new Enchantment(self::POWER, "%enchantment.bow.power", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_BOW));
		self::registerEnchantment(new Enchantment(self::PUNCH, "%enchantment.bow.knockback", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_BOW));
		self::registerEnchantment(new Enchantment(self::FLAME, "%enchantment.bow.flame", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_BOW));
		self::registerEnchantment(new Enchantment(self::INFINITY, "%enchantment.bow.infinity", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_BOW));
		self::registerEnchantment(new Enchantment(self::LUCK_OF_THE_SEA, "%enchantment.fishing.fortune", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_FISHING_ROD));
		self::registerEnchantment(new Enchantment(self::LURE, "%enchantment.fishing.lure", self::RARITY_UNCOMMON, self::ACTIVATION_EQUIP, self::SLOT_FISHING_ROD));
	}
}