<?php

declare(strict_types=1);

namespace SOFe\ActionApi;

/**
 * An action is a target of execution.
 *
 * Each Action object jjanstance is created for each attempt of execution,
 * which may or may not be actually executed.
 * represents one instance of execution.
 * Thus, it should store action parameters within rhe subclass.
 * A new i
 */
interface Action {
	/**
	 * Checks whether any required arguments are still unset.
	 *
	 * If this method returns true,
	 * `getArguments` would be called to generate an appropriate user interface for argument selection.
	 */
	public function isMissingArg() : bool;

	/**
	 * Checks the arguments that are missing.
	 *
	 * This method should yield both required and optional arguments,
	 * and both set and unset arguments.
	 * The number of arguments provided will *not* be used as a source of truth
	 * to determine whether an extra iteration of argument selection UI is performed;
	 * that is the role of `isMissingArg`.
	 *
	 * It is a logical error if `isMissingArg` returned true
	 * but this method yields nothing.
	 *
	 * Infinite (probably asynchronously) loop occurs if
	 * `isMissingArg` checks on conditions that cannot be fulfilled
	 * through the resoltuion of arguments yielded in this method.
	 *
	 * @return iterable<Arg>
	 */
	public function getArguments() : iterable;

	/**
	 * Performs the action.
	 *
	 * This method is only called after `isMissingArg` returns false.
	 */
	public function run(CommandSender $sender) : void;
}
