<?php

declare(strict_types=1);

namespace SOFe\ActionApi\Arg;

use function array_keys;
use function array_values;
use function implode;
use Closure;
use InvalidArgumentException;
use RuntimeException;
use dktapps\pmforms\{FormIcon, MenuForm, MenuOption};
use dktapps\pmforms\element\{Dropdown, StepSlider, CustomFormElement};
use pocketmine\Player;
use pocketmine\command\CommandSender;
use SOFe\ActionApi\Arg;
use SOFe\ActionApi\Util\CustomFormSubset;

/**
 * An argument required for an action.
 *
 * @implements Arg<string>
 */
final class StringEnumArg implements Arg {
	public const STYLE_DROPDOWN = 0;
	public const STYLE_STEP_SLIDER = 1;
	public const STYLE_MENU = 2;

	/** @var string */
	private $name;
	/** @var string|null */
	private $value;

	/** @var string|null */
	private $text = null;
	/** @var array<string, MenuOption> */
	private $options = [];
	/** @var int */
	private $default = 0;
	/** @var int */
	private $style = self::STYLE_DROPDOWN;

	/**
	 * Construct a new `StringEnumArg`
	 */
	public static function new(string $name, ?string &$value) : self {
		$self = new self;
		$self->name = $name;
		$self->value =& $value;
		return $self;
	}

	/**
	 * Set the display style
	 */
	public function style(int $style) : self {
		$this->style = $style;
		return $this;
	}

	/**
	 * Add an enum option
	 */
	public function option(string $key, ?string $display = null, bool $default = false, ?FormIcon $icon = null) : self {
		if(isset($this->options[$key])) {
			throw new InvalidArgumentException("Key duplicate: $key");
		}
		$this->options[$key] = new MenuOption($display ?? $key, $icon);
		if($default) {
			$this->default = count($this->options) - 1;
		}
		return $this;
	}

	public function setInferredValue($value) : void {
		$this->value = $value;
	}

	public function fromCommandArgs(CommandSender $sender, array &$args) : void {
		$value = array_shift($args);
		if($value !== null && isset($this->options[$value])) {
			$this->value = $value;
		} else {
			$options = implode(",", array_keys($this->options));
			$sender->sendMessage("Invalid value \"$value\". Allowed values: $options");
		}
	}

	public function createUi(Player $player, Closure $resolve) : void {
		if($this->style === self::STYLE_DROPDOWN || $this->style === self::STYLE_STEP_SLIDER) {
			$resolve(CustomFormSubset::new(function(array $values) : void {
				$this->value = array_keys($this->options)[$values[0]];
			})->add(fn($name) => $this->makeElement($name)));
		} elseif($this->style === self::STYLE_MENU) {
			$options = array_values($this->options);
			$form = new MenuForm($this->name, $this->text ?? $this->name, $options, function(int $option) use($resolve) : void {
				$this->value = array_keys($this->options)[$option];
				$resolve(null);
			});
		} else {
			throw new RuntimeException("Invalid style");
		}
	}

	private function makeElement(string $name) : CustomFormElement {
		$options = array_values($this->options);
		$options = array_map(fn($option) => $option->getText(), array_values($options));
		if($this->style === self::STYLE_DROPDOWN) {
			return new Dropdown($name, $this->name, $options, $this->default);
		} elseif($this->style === self::STYLE_STEP_SLIDER) {
			return new StepSlider($name, $this->name, $options, $this->default);
		} else {
			throw new RuntimeException("Unreachable case");
		}
	}
}
