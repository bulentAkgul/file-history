# File History

This is a helper package, and it can't be used independently. Its purpose is to log the changes of files when they are created/deleted/updated through the commands shipped with the packages, which are part of [**Packagified Laravel**](https://github.com/bulentAkgul/packagified-laravel). So this package can't track the changes made manually.

### Commands

This package contains 3 console commands. They are quite self-explanatory and don't expect any argument.
```
sail artisan delete-logs
```
```
sail artisan redo-log
```
```
sail artisan undo-log
```

## Packagified Laravel

The main package that includes this one can be found here: [**Packagified Laravel**](https://github.com/bulentAkgul/packagified-laravel)

## The Packages That Dependent On This One

-   [**Laravel Code Generator**](https://github.com/bulentAkgul/laravel-code-generator)
-   [**Laravel File Creator**](https://github.com/bulentAkgul/laravel-file-creator)
-   [**Laravel Resource Creator**](https://github.com/bulentAkgul/laravel-resource-creator)
-   [**Laravel Package Generator**](https://github.com/bulentAkgul/laravel-package-generator)