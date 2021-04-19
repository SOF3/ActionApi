<?php

declare(strict_types=1);

namespace SOFe\ActionApi;

use pocketmine\plugin\PluginBase;
use SOFe\AwaitStd\AwaitStd;

final class Main extends PluginBase {
	/** @var AwaitStd */
	private static $std;

	public function onEnable() : void{
		self::$std = AwaitStd::init($this);
	}

	/**
	 * @internal
	 */
	public static function std() : AwaitStd {
		return self::$std;
	}
}
