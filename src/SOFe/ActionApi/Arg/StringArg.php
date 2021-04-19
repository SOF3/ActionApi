<?php

declare(strict_types=1);

namespace SOFe\ActionApi\Arg;

use function implode;
use Closure;
use dktapps\pmforms\element\Input;
use pocketmine\Player;
use pocketmine\command\CommandSender;
use SOFe\ActionApi\Arg;
use SOFe\ActionApi\Util\CustomFormSubset;

/**
 * An argument required for an action.
 *
 * @implements Arg<string>
 */
final class StringArg implements Arg {
	/** @var string */
	private $name;
	/** @var string|null */
	private $value;

	/** @var bool */
	private $implode = false;
	/** @var string */
	private $hint = "";
	/** @var string */
	private $default = "";

	public static function new(string $name, ?string &$value) : self {
		$self = new self;
		$self->name = $name;
		$self->value =& $value;
		return $self;
	}

	public function implode() : self {
		$this->implode = true;
		return $this;
	}

	public function hint(string $hint) : self {
		$this->hint = $hint;
		return $this;
	}

	public function default(string $default) : self {
		$this->default = $default;
		return $this;
	}

	public function setInferredValue($value) : void {
		$this->value = $value;
	}

	public function fromCommandArgs(CommandSender $sender, array &$args) : void {
		if($this->implode) {
			$this->value = implode(" ", $args);
			$args = [];
		} else {
			$this->value = array_shift($args);
			// $this->value is still null if $args is empty
		}
	}

	public function createUi(Player $player, Closure $resolve) : void {
		$resolve(CustomFormSubset::new(function(array $values) : void {
			$this->value = $values[0];
		})->add(fn($name) => new Input($name, $this->name, $this->hint, $this->default)));
	}
}
