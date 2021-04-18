# ActionApi

Generalized user interface framework for PocketMine plugins.

## How does this work?
ActionApi introduces the following concepts:

### Action
`Action` is an interface implemented by plugins
to represent something that a user can do.

Each action type implements a new subclass of `Action`.
A new instance of `Action` is created for every execution.
An `Action` subclass holds the arguments required for the action.

### Arg
`Arg` is an argument that the user provides to customize the action.
They can be inferred from the `Hook` (action trigger),
or separately requested from the user.

Implementations provided by `ActionApi`:

| Type | Command line support | Form UI support | Inference from `Hook` |
| :---: | :---: | :---: | :---: |
| `StringArg` | Yes (single argument or space implosion) | Yes (`CustomForm` `Input`) | None |
| `StringEnumArg` | Yes (single argument) | Yes (`CustomForm` `Dropdown`, or `CustomForm` `StepSlider`, or `MenuForm`) | None |
| `StringEnumSetArg` | Yes (last argument only, or if delimiter is provided) | Yes (`CustomForm` `Toggle`s) | None |
| `IntArg` | Yes | Yes (`CustomForm` `Slider`, or `CustomForm` `Input`) | None |
| `FloatArg` | Yes | Yes (`CustomForm` `Slider`, or `CustomForm` `Input`) | None |
| `BoolArg` | Yes | Yes (`CustomForm` `Toggle`, or `ModalForm`) | None |
| `GenericListArg` | Yes if element type supports (rearranged as last argument, variadic) | Yes if element type supports (interactive loop) | If element type is inferrable, singleton list |
| `PlayerArg` (online players only) | Yes (by name prefix or full name) | Yes (`CustomForm` `Dropdown`, or `MenuForm`) | If hook is clicking on a player |
| `PlayerSetArg` (online players only) | Yes (comma-separated) | Yes (`CustomForm` `Toggle`s) | None |
| `EntityArg` | No | No | If hook is clicking on an entity |
| `BlockTypeArg` | Yes (by name, with variant colon-separated) | Yes (interactive selector with search button) | If hook is clicking on a block |
| `ItemArg` | Yes (by name, with damage and count colon-separated) | Yes (interactive selector with search button) | If hook is clicking on air, a block or entity (default value is currently held item) |
| `ItemTypeArg` | Yes (by name, with damage colon-separated) | Yes (interactive selector with search button) | If hook is clicking on air, a block or entity (default value is currently held item) |
| `PositionArg` | Yes (by typing x y z world-name) | Yes (`CustomForm` `Dropdown` to select current position, `CustomForm` `Input`, current crosshair target or next-click position) | If hook is clicking on a block or entity (otherwise) |
| `WorldArg` | Yes (world name) | Yes (`CustomForm` `Dropdown`, or `MenuForm`) | None (default value is player current world) |
| `WorldSetArg` | Yes (last argument only) | Yes (`CustomForm` `Toggle`s) | None |
| `YawArg` | Yes (in degrees, not recommended) | Yes (`CustomForm` `Slider`) | Player current orientation |
| `PitchArg` | Yes (in degrees, not recommended) | Yes (`CustomForm` `Slider`) | Player current orientation |
| `DateTimeArg` | Yes (ISO 8601, or `+` + duration) | Yes (`CustomForm` `Slider`s for YMDhmsZ) | None |
| `DurationArg` | Yes (single argument, e.g. `1w2d3h4m5s`) | Yes (interactive loop with `CustomForm` `Input` + `Dropdown` for unit) | None |

Inference from `Hook` may be suppressed as the default value instead of silently selected
by an extra flag from the constructor call in the `Action` implementation.

### Hook
`Hook` is the method that the user used to trigger the initiation of this action.
It stores data related to the trigger.

Implementations provided by `ActionApi`:

- command
- block click
- air click
- entity click
- item selection
- hotbar switch
- action menu (a navigation index for actions defined in config.yml)

### Example `Action` implementation
```php
final class AddWarpAction implements Action {
	private DataProvider $db;

	private ?string $name = null;
	private ?Position $pos = null;
	private ?bool $open = null;

	public function __construct(DataProvider $db) {
		$this->db = $db;
	}

	public function isMissingArg() : bool {
		return $this->name === null || $this->pos === null || $this->open === null;
	}

	public function getArguments() : iterable {
		yield new StringArg("Warp name", $this->name);
		yield new PositionArg("Warp position", $this->pos);
		yield new BoolArg("Open to public?", $this->open, true);
	}

	public function run(CommandSender $sender) : void {
		$this->db->addWarp($this->name, $this->pos->getX(), $this->pos->getY(), $this->pos->getZ(),
				$this->pos->getWorld()->getFolderName(), $this->open);
		$sender->sendMessage("Warp \"$this->name\" created at $this->pos");
	}
}
```

### Implementing `Arg` and `Hook`
If the existing arguments are not sufficient, other plugins can implement additional `Arg` types.
Plugins that want to trigger actions from other methods can also implement their own `Hook` types.

Each `Arg` supports command line and form UI interface.
However, if an `Arg` additionally infers arguments from a `Hook`,
the logic is implemented under a separate `Inferrer` interface.
Plugins may register an `Inferrer` instance that supports a specific type of `Arg` and a specific type of `Hook`.
An example implementation for inferring `EntityArg` from an `EntityClickHook`:

```php
final class EntityClickItemInferrer implements Inferrer {
	public function infer(Hook $hook) {
		assert($hook instanceof EntityClickHook); // this is true if you registered the inferrer for the correct class
		return $hook->getClickedEntity();
	}
}
```
