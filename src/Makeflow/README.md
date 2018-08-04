How to install in symfony project


1. inject the `MakeflowManager` to container

2. load configuration from Makeflow by add following lines to `\App\Kernel::configureContainer`:

```php
        //load configuration from Makeflow
        $loader->load($this->getProjectDir().'/src/Makeflow/*/configure.yaml', 'glob');
```



TODO

- hooks and events

- notify stakeholders

- forum in workspace




