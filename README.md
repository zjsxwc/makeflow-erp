
#### Makeflow ERP/OA


Makeflow is a ERP/OA system framework based on symfony 4 project.

Makeflow is inspired by the GNU tool `make`, and now we introduce the idea to the ERP/OA system where workflow is heavily used.

But the weakness of workflow is that it cannot handle the situation well when multiple unordered sibling place appears.



#### How to install in symfony 4 project


1. inject the `MakeflowManager` to container

2. load configuration from Makeflow by add following lines to `\App\Kernel::configureContainer`:

```php
        //load configuration from Makeflow
        $loader->load($this->getProjectDir().'/src/Makeflow/*/configure.yaml', 'glob');
```



#### TODO

- hooks and events

- notify stakeholders

- forum in workspace

- add database indexes to entity




