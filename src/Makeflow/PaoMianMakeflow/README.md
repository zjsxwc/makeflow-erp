
<p align="center">
![](./makeflow.png)
  </p>


## 一个吃泡面的例子





```text
EatPaoMian: MakePaoMian

MakePaoMian:
    - BuyPaoMian
    - HeatUpWater
```


买泡面`BuyPaoMian`与烧水`HeatUpWater`是同时进行的，然后在这两个条件都满足后才能泡泡面`MakePaoMian`，最后吃泡面`EatPaoMian`
