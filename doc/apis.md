

#### 管理员总览所有makeflow

/makeflow-admin/dashboard GET

#### 管理员获取place下的员工

/makeflow-admin/place-users POST


|参数名|说明|值类型|例子
|-
|makeflowName|makeflow名|String|PaoMian
|placeName|placeName名|String|BuyPaoMian


#### 管理员一次性获取系统下所有员工

/makeflow-admin/all-users POST

>因为一个小企业员工最多也就几百个，所以直接一次性获取就行不分页

#### 管理员绑定员工到place下

/makeflow-admin/place-bind-users POST

|参数名|说明|值类型|例子
|-
|makeflowName|makeflow名|String|PaoMian
|placeName|placeName名|String|BuyPaoMian
|userIds|英文逗号隔开的被绑定员工id|String|3,4,5

#### 管理员移出place下的员工

/makeflow-admin/place-remove-users POST

|参数名|说明|值类型|例子
|-
|makeflowName|makeflow名|String|PaoMian
|placeName|placeName名|String|BuyPaoMian
|userIds|英文逗号隔开的被移出员工id|String|3,4,5


#### 员工总览他的所有workflow

/makeflow-user/dashboard GET

员工查看他所在的place以及其place所在的makeflow位置，同时可以看到他的哪些place有哪些具体待处理workspace

#### 节点员工创建一个workspace

/makeflow-user/makeflow/{makeflowName}/create-workspace POST

|参数名|说明|值类型|例子
|-
|title|workspace名|String|第一次吃泡面


#### 员工进入某个workspace下的place操作

/makeflow-user/workspace/{id}/place/{placeName}  GET|POST

>这里就会鉴权处理后，转发请求到自定义的`Place::processAction`里处理业务了

#### 员工删除某个place下的前置条件，来实现回退功能

/makeflow-user/workspace/{id}/place/{placeName}/delete-prerequisites  POST

|参数名|说明|值类型|例子
|-
|prerequisites|英文逗号隔开的上一级前置要求|String|BuyPaoMian,HeatUpWater

#### 图片上传接口

/user/upload-images POST

>以multipart/form-data编码的POST方式上传文件