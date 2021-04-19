<?php

declare(strict_types=1);

namespace SOFe\ActionApi\Arg;

use function implode;
use Closure;
use InvalidArgumentException;
use dktapps\pmforms\MenuOption;
use dktapps\pmforms\element;
use pocketmine\Player;
use pocketmine\block\{Block, BlockFactory};
use pocketmine\command\CommandSender;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\LegacyStringToItemParser;
use SOFe\ActionApi\{Arg, Main};
use SOFe\ActionApi\Util\{CustomFormSubset, Util};
use SOFe\AwaitGenerator\Await;

/**
 * @implements Arg<Block>
 */
final class BlockTypeArg implements Arg {
	/** @var string */
	private $name;
	/** @var Block|null */
	private $value;

	public static function new(string $name, ?Block &$value) : self {
		$self = new self;
		$self->name = $name;
		$self->value =& $value;
		return $self;
	}

	public function setInferredValue($value) : void {
		$this->value = $value;
	}

	public function fromCommandArgs(CommandSender $sender, array &$args) : void {
		$value = array_shift($args);
		if($value !== null) {
			try {
				$item = LegacyStringToItemParser::parse($value);
				$block = $item->getBlock();
				$this->value = $block;
			} catch(InvalidArgumentException $e) {
				$sender->sendMessage($e->getMessage());
			}
		}
	}

	public function createUi(Player $player, Closure $resolve) : void {
		Await::g2c($this->makeUi($player), static function(?Block $block) use($resolve) : void {
			if($block !== null) {
				$this->value = $value;
				$resolve();
			}
		});
	}

	private function makeUi(Player $player) : Generator {
		while(true) {
			$option = Util::asyncMenuForm($player, $this->name, "How do you want to select a block?", [
				new MenuOption("Search by name"),
				new MenuOption("Choose from list"),
				new MenuOption("Pick from world"),
				new MenuOption("Use currently holding block"),
				new MenuOption("Select from inventory"),
			]);

			if($option === null) {
				return null;
			}

			if($option === 0) {
				$block = yield $this->searchByName($player);
				if($block !== null) {
					return $block;
				}
			}
			if($option === 1) {
				$block = yield $this->chooseFromList($player);
				if($block !== null) {
					return $block;
				}
			}
			if($option === 2) {
				$block = yield $this->pickFromWorld($player);
				if($block !== null) {
					return $block;
				}
			}
			if($option === 3) {
				$item = $player->getInventory()->getItemInHand();
				return $item->getBlock();
			}
			if($option === 4) {
				$block = yield $this->selectFromInventory($player);
				if($block !== null) {
					return $block;
				}
			}
		}
	}

	private function searchByName(Player $player) : Generator {
		["name" => $name] = yield Util::asyncCustomForm($player, "Search a block type", [
			new element\Input("name", "Block type"),
		]);

		$factory = BlockFactory::getInstance();
		// TODO
	}

	private function chooseFromList(Player $player) : Generator {
		false && yield;
		// TODO
	}

	private function pickFromWorld(Player $player) : Generator {
		$std = Main::std();
		$event = yield $std->nextInteract($player);
		if($event->getAction() === PlayerInteractEvent::LEFT_CLICK_BLOCK) {
			return null;
		}
		return $event->getBlock();
	}

	private function selectFromInventory(Player $player) : Generator {
		false && yield;
		// TODO
	}
}
