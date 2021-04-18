<?php

declare(strict_types=1);

namespace SOFe\ActionApi;

/**
 * Infers an argument from a hook.
 *
 * The hook and argument types are specified when the inferrer is registered.
 *
 * @template H of Hook the hook type
 * @template T the object to return
 */
interface Inferrer {
	/**
	 * @phpstan-param H $hook
	 * @phpstan-return T|null
	 */
	public function infer(Hook $hook);
}
