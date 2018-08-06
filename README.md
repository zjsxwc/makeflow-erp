
# Makeflow ERP/OA

##### ENGLISH


Makeflow is an ERP/OA system framework based on symfony 4 project.

Makeflow is inspired by the GNU tool `make`, and now we introduce the idea to the ERP/OA system where workflow is heavily used.

The weakness of workflow is that it cannot handle the situation well when multiple unordered sibling places appear,
And I found the idea of `makefile` perfectly suits the situation, So I started this project.



<br>


##### 中文


Makeflow ERP 是一个基于PHP Symfony 4的 ERP/OA 系统框架，在一般ERP开发时，我们会发现对于处理没有前后顺序要求的并行的工作流任务，传统的强行转换为顺序工作流方式很不合理，然后又发现其实`makefile`的思想很适合这种实际场景，而且和写makefile一样搭ERP(在本项目里是写[`makeflow.yaml`](https://github.com/zjsxwc/makeflow-erp/blob/master/src/Makeflow/PaoMianMakeflow/makeflow.yaml))确实可以方便开发提高效率，于是有了这个项目。


因为用户是基于工作流place节点鉴权，于是也就没有了传统的ACL角色菜单等鉴权方式，算是省了代码写权限管理。


<br>

## How to start your own ERP

> I just use the field `directory` of entity `workspace` to simulate the `makefile` directory, And every thing works fine.
 
> [具体的实现思路看API文档应该就可以了解了](https://github.com/zjsxwc/makeflow-erp/blob/master/doc/apis.md)

<br>

- run `bin/console makeflow:create` to create a skeleton in `src/Makeflow/`

- refer to the [`PaoMianMakeflow`](https://github.com/zjsxwc/makeflow-erp/tree/master/src/Makeflow/PaoMianMakeflow) to write your code


- to enable your new makeflow by add the makeflow class name to `/src/Makeflow/makeflows.yaml`


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

- add database indexes to entities

- could assign/limit next place users by previous place user in workspace when there is only one previous place


## License

[MIT](http://opensource.org/licenses/MIT)

Copyright (c) 2018-present, Wang Chao

