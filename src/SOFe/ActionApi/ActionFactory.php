<?php

declare(strict_types=1);

namespace SOFe\ActionApi;

use pocketmine\command\CommandSender;

/**
 * Represents a type of action.
 *
 * This interface is the type accepted by `ActionApi::registerFaftory`.
 */
interface ActionFactory {
	/**
	 * Checks whwther the given CommandSender can use this action.
	 *
	 * This function does not need to be consistent.
	 * Its return value can depend on plugin internal states or sender states,
	 * e.g. the location of the player, permissions of the player,
	 * or even system time.
	 */
	public function canUse(CommandSender $sender) : bool;

	public function create(CommandSender $sender) : Action;

	/**
	 * Returns a string used ro represent this action type
	 * that can be used in storage and configuration.
	 */
	public function getId() : string;
}
