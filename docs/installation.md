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

- Vanilo 4.x only supports Enum v4.
- Vanilo 3.x supports both Enum v4 and v3.
- Vanilo 2.x series only support Enum v3.

Although the various versions of this library work very well with Vanilo 2.2, 3.x and 4.x,
it's not possible to support both Enum v3 and v4 at the same time. To satisfy all possible
requirements we have added a gradual upgrade path, according to the version compatibility chart below:

| Netopia Module (this) | PHP       | Vanilo    | Enum | Laravel       |
|-----------------------|-----------|-----------|------|---------------|
| 1.1                   | 7.4 - 8.1 | 2.2       | 3.x  | 6.x - 8.x     |
| 1.2                   | 7.4 - 8.1 | 2.2 - 3.x | 3.x  | 8.22.1 - 9.x  |
| 1.3                   | 8.0 - 8.1 | 3.x       | 3.x  | 9.x           |
| 2.0                   | 8.0 - 8.1 | 3.x       | 4.x  | 9.x           |
| 2.1                   | 8.0 - 8.3 | 3.x       | 4.x  | 9.2 - 10.x    |
| 3.0                   | 8.2 - 8.3 | 4.x       | 4.2+ | 10.43+ - 11.x |
| 3.1                   | 8.2 - 8.3 | 4.1+      | 4.2+ | 10.43+ - 11.x |

---

**Next**: [Configuration &raquo;](configuration.md)
