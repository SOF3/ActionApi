<?php

declare(strict_types=1);

namespace SOFe\ActionApi\Util;

use Closure;
use dktapps\pmforms\element\CustomFormElement;

/**
 * A subset of elements submitted to a `CustomForm`.
 */
final class CustomFormSubset {
	/**
	 * @var Closure
	 * @phpstan-var Closure(mixed[] $values): void
	 */
	private $resolve;

	/** @var (Closure(string): CustomFormElement)[] */
	private $elements = [];

	/**
	 * Creates a new subset with a listener
	 *
	 * @phpstan-var Closure(mixed[] $values): void
	 */
	public static function new(Closure $resolve) : self {
		$self = new self;
		$self->resolve = $resolve;
		return $self;
	}

	/**
	 * Adds an element to the subset
	 *
	 * @phpstan-param Closure(string): CustomFormElement $element
	 */
	public function add(Closure $element) : self {
		$this->elements[] = $element;
		return $this;
	}
}
