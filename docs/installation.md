# Netopia Module Installation

1. Add to your application via composer:
    ```bash
    composer require vanilo/netopia 
    ```
2. Add the module to `config/concord.php`:
    ```php
    <?php
    return [
        'modules' => [
             //...
             Vanilo\Netopia\Providers\ModuleServiceProvider::class
             //...
        ]
    ]; 
    ```

## Enum v3 & v4 Compatibility

[Enum v4](https://konekt.dev/enum/4.x/upgrade#return-argument-and-attribute-types)
contains a breaking change, that affects this library.

Vanilo supports Enum v4 (besides v3) beginning with Vanilo 3.1.

Vanilo 2.x series only support Enum v3.

Although this library works very well both with Vanilo 2.2 and 3.x, it's not possible to support
both Enum v3 and v4 at the same time. To satisfy all possible requirements we have added a
gradual upgrade path, according to the version compatibility chart below:

| Netopia Module (this) | PHP       | Vanilo    | Enum | Laravel       |
|-----------------------|-----------|-----------|------|---------------|
| 1.1                   | 7.4 - 8.1 | 2.2       | 3.x  | 6.x - 8.x     |
| 1.2                   | 7.4 - 8.1 | 2.2 - 3.x | 3.x  | 8.22.1* - 9.x |
| 1.3                   | 8.0 - 8.1 | 3.x       | 3.x  | 9.x           |
| 2.0                   | 8.0 - 8.1 | 3.x       | 4.x  | 9.x           |

> *: Minimum Laravel version is 8.22.1, to enforce the [CVE-2021-21263](https://blog.laravel.com/security-laravel-62011-7302-8221-released) security patch

As you can see, Enum is the cornerstone of the backwards compatibility,
thus the [major version bump](https://semver.org/) along with the Enum v4 support.  

Bugfixes and important changes will be backported to the 1.x branch as long as Vanilo
supports Enum v3.

---

**Next**: [Configuration &raquo;](configuration.md)
