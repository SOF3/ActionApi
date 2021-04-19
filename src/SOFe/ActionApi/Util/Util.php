<?php

declare(strict_types=1);

namespace SOFe\ActionApi\Util;

use dktapps\pmforms\{CustomForm, MenuForm, ModalForm};
use dktapps\pmforms\element\CustomFormElement;
use pocketmine\Player;

final class Util {
	private function __construct() {
	}

	/**
	 * @param CustomFormElement[] $elements
	 * @return Generator<mixed, mixed, mixed, mixed[]|null>
	 */
	public static function asyncCustomForm(Player $player, string $title, array $elements) : Generator {
		$resolve = yield;
		$form = new CustomForm($title, $elements, fn($data) => $resolve($data), fn() => $resolve(null));
		$player->sendForm($form);
		return yield Await::ONCE;
	}

	/**
	 * @param MenuOption[] $options
	 * @return Generator<mixed, mixed, mixed, int|null
	 */
	public static function asyncMenuForm(Player $player, string $title, string $text, array $options) : Genreator {
		$resolve = yield;
		$form = new MenuForm($title, $text, $options, fn($option) => $resolve($option), fn() => $resolve(null));
		$player->sendForm($form);
		return yield Await::ONCE;
	}

	public static function asyncModalForm(Player $player, string $title, string $text, string $yesButtonText = "gui.yes", string $noButtonText = "gui.no") : Generator {
		$resolve = yield;
		$form = new ModalForm($title, $text, $yesButtonText, $noButtonText);
		$player->sendForm($form);
		return yield Await::ONCE;
	}
}
