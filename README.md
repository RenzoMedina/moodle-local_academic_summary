# Academic Summary #

This plugin adds a page to the Reports menu that allows administrators and managers to quickly and centrally view the academic summary of any user.
The tool facilitates the management and monitoring of student progress without the need to navigate course by course.

## Features ##

- Search by user's full name or email address.
- View academic profile with:
- Full name and last time logged into the platform.
- List of courses enrolled in with:
    - Course name
    - Start and end dates
    - Percentage of progress (when completion tracking is enabled)
    - Status: In progress, Completed, or Not started
- Total number of courses enrolled in.
- Overall progress average (only courses with completion tracking).
- Upcoming assignments in the next 7 days in all courses.

## Installing via uploaded ZIP file ##

1. Log in to your Moodle site as an admin and go to _Site administration >
   Plugins > Install plugins_.
2. Upload the ZIP file with the plugin code. You should only be prompted to add
   extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

## Installing manually ##

The plugin can be also installed by putting the contents of this directory to

    {your/moodle/dirroot}/local/academic_summary

Afterwards, log in to your Moodle site as an admin and go to _Site administration >
Notifications_ to complete the installation.

Alternatively, you can run

    $ php admin/cli/upgrade.php

to complete the installation from the command line.

## License ##

2026 Renzo Medina <medinast30@gmail.com>

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <https://www.gnu.org/licenses/>.
