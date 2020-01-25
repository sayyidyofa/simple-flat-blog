# Simple Flat Blog

> I made this project for college assignment, as well as my first real attempt to learn about MVC pattern
> There is some problems though, because this is part of my learning process.

Notes to anyone that probably going to try running this project:

- The `model.php` is just a bunch of raw MySQL queries wrapped in CRUD-related function, so it's not even a real model
- The `.env` file is exposed to public. You must change the directory structure in order to safely run this in production. Precautions like putting public-related files in a new `/public` just like any PHP framework will do.
- The routes are in `index.php`
- There are a bunch unreadable functions in `Misc.php` that does tag management for each post. Please don't ask me how it works, even myself who made the functions have a hard time reading it now.

I was strongly willing to not include any third-party libraries when started this project. However even for this simple project my lazy self can't afford but to include some libraries (you can look at it in `composer.json` but I will explain each of them anyway):

- `vlucas/phpdotenv` to fetch `.env` variables of course.
- `mustache/mustache` to render templates.
- `ezyang/htmlpurifier` to (supposedly) filter XSS attempts from HTML templates before rendering.
