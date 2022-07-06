# Trellis Hooks
Trellis exposes a few hooks that can execute bash commands. Add a hook by placing a bash script in the corresponding hook directory. Hooks are run from the CWD of the hook directory so relative paths should be relative to the script's location. Hooks are run in lexicographic order.

## Hooks
### PreSnapshot
Runs before the snapshot is executed.

### PostSnapshot
Runs after the snapshot has been created.