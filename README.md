
# Makeflow ERP/OA

##### ENGLISH


Makeflow is an ERP/OA system framework based on symfony 4 project.

Makeflow is inspired by the GNU tool `make`, and now we introduce the idea to the ERP/OA system where workflow is heavily used.

The weakness of workflow is that it cannot handle the situation well when multiple unordered sibling place appears,
And I found the idea of `makefile` is perfectly suit the situation, So I started this project.



##### 中文


Makeflow ERP 是一个基于PHP Symfony 4的 ERP/OA 系统框架，我在维护老ERP时，发现对于处理没有前后顺序要求的并行的工作流任务，传统的强行转换为顺序工作流很不合理，然后又发现其实`makefile`的思想很适合这种实际场景，于是有了这个项目。




## How to start your own ERP

>I just use the field `directory` of entity `workspace` to simulate the `makefile` directory, And every thing works fine.
 


- run `bin/console makeflow:create` to create a skeleton in `src/Makeflow/`

- refer to the `PaoMianMakeflow` to write your code


## How to install in symfony 4 project


1. inject the `MakeflowManager` to container

2. load configuration from Makeflow by adding the following lines to `\App\Kernel::configureContainer`:

```php
        //load configuration from Makeflow
        $loader->load($this->getProjectDir().'/src/Makeflow/*/configure.yaml', 'glob');
```



## TODO

- hooks and events

- notify stakeholders

- forum in workspace

- add database indexes to entity


## License

[MIT](http://opensource.org/licenses/MIT)

Copyright (c) 2018-present, Wang Chao

