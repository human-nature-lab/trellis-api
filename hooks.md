# Trellis Hooks
Trellis exposes a few hooks that can execute bash commands. Hooks are configured by creating a `hooks.yaml` file in the root directory of the server.

## Hooks config
```yaml

# Runs before the snapshot is executed. No inputs are sent to the hook.
preSnapshot:
  - name: pre snapshot hook
    bin: bash
    env:
    args: [/path/to/hook.sh]

# Runs after the snapshot has been created. No inputs are sent to the hook.
postSnapshot:
  - name: post snapshot hook
    bin: bash
    env:
    args: [/path/to/hook.sh]

# Runs whenever the geo hook is executed from the in the UI
geo:
  - id: sample_village          # Unique identifier
    name: Village hook          # Name that shows up in the UI
    icon: mdi-eyedropper        # Add an icon to the UI
    # geoTypeId:                # Optionally limit to a specific geo type
    description: your desc here # A description of what the hook does
    bin: bash                   # The command to run
    args: [/path/to/hook.sh]    # Args to pass to the command
    # once: true                # Indicate that the hook can't run multiple times for the same geo


```