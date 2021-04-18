<?php

declare(strict_types=1);

namespace SOFe\ActionApi;

use Closure;
use SOFe\ActionApi\Util\CustomFormSubset;

/**
 * An argument required for an action.
 *
 * @template T the underlying value type
 */
interface Arg {
	/**
	 * Sets the argument to the inferred value.
	 *
	 * @phpstan-param T $value
	 */
	public function setInferredValue($value) : void;

	/**
	 * Parses command arguments for this argument.
	 *
	 * Arguments are passed by reference and used arguments shall be removed.
	 *
	 * @param string[] $args
	 */
	public function fromCommandArgs(array &$args) : void;

	/**
	 * Requests user to fill this argument using form UI.
	 *
	 * If the argument creates its own forms,
	 * call `$resolve` with `null` after receiving results.
	 * If it shares a `CustomForm` with other arguments,
	 * pass a `CustomFormSubset` to the closure,
	 * which includes the list of elements to provide
	 * and a callback to invoke when the `CustomForm` is returned.
	 *
	 * @phpstan-param Closure(?CustomFormSubset): void $resolve
	 */
	public function createUi(Closure $resolve) : void;
}
