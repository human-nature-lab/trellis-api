# Trellis API
The Trellis API survey is built on an API optimized version of Laravel called Lumen.

## Setup

### Create storage directories
The following directories must be created before starting.
- storage/app
- storage/exports
- storage/logs
- storage/framework/cache
- storage/framework/sessions
- storage/framework/views
- storage/respondent-photos
- storage/snapshot
- storage/temp
- storage/uploads
- storage/uploads-failed
- storage/uploads-pending
- storage/uploads-running
- storage/uploads-success

## Development

### Debugging

#### Command line
Use the `-dxdebug.remote_autostart` option from the command line to start the debugger.

Ex.

    php7.1 -dxdebug.remote_autostart artisan trellis:make:reports {study_id}

### Profiling
From the command line you can start the profiling using the `-dxdebug.profiler_enable` option. 

Ex

    php7.1 -dxdebug.profiler_enable -dxdebug.extended_info=0 artisan trellis:make:reports {study_id}
       
The `-dxdebug.extended_info=0` command limits the amount of data written to the profile dump and reduces the performance overhead caused by profiling.