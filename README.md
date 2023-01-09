## tperCMS

##  1.简介

- 是一个类似于wordpress的博客管理系统CMS, 但是更简单

- 前端使用bootsrtrap4.3.1 后台使用thinkphp5.1
- 需要一个服务器，并且装有mysql5.7以上，php能支持tp5.1就行
- 可能有点bug，有问题欢迎找我
- 如果感兴趣，可以找。。建议摆烂。。。



##  2.安装与初始化

- 想整一个玩玩，可以直接下载一个类似wamp的集成环境，或者phpstudy

![image-20220223200347841](https://github.com/fight-fritten/tperCMS/blob/master/SCREENSHOT/1.png)

- 1.然后把下载好的项目放进其phpstudy的工作目录下：

在这之前，一定要把 **工作目录设置为 tperCMS/public**  ，这是tp5的基本操作

- 2.输入路由 **http://你的服务器网址/tperCMS** 来进行访问

```
如果出现了，什么open_dir()。。。这种报错，请关掉网站的防跨站保护。或者去百度
```

就是部署tp5.1的基本操作



- 3.如果能正常访问，应该会有一个报错，因为你没有初始化数据库

- 4.访问路由 **http://你的服务器网址/install/install/step1**，tperCMS/public被设置成了工作目录，所以整体路由相当于 **http://你的服务器网址/tperCMS/public/install/install/step1**

- 然后，输入你的数据库的各种信息，什么用户名，密码，端口，数据库名，网址。

  ![image-20220223205444733](https://github.com/fight-fritten/tperCMS/blob/master/SCREENSHOT/2.png)

- 点击确认

- 还要输入你的登录用户名和密码

![image-20220223205518781](https://github.com/fight-fritten/tperCMS/blob/master/SCREENSHOT/3.png)

- 用作登录后台用。

- 一切顺利的话，就可以点后台登录了：

  ![image-20220223205623912](https://github.com/fight-fritten/tperCMS/blob/master/SCREENSHOT/4.png)

## 3.博客操作



### 3.1写博客

- 登录后台，直接选项都有，包括写博客，编辑博客，回收站等。

- 写博客之后，可以选择博客的类型，如果选为“个人”，则需要登录后，才能查看：

- 这个密码查看功能可以取消，下图中有个标签，点那个

  ![image-20220223210546563](https://github.com/fight-fritten/tperCMS/blob/master/SCREENSHOT/5.png)

- 这个富文本编辑器很有意思，功能很多，然后发表情会报错，不知道为什么
- 点击”保存并发布“

### 3.2编辑博客，删除博客

进入后台，可以看到左边有一个  “编辑博客” ，点击可以编辑

- 点击之后，可以选择点击 “修改按钮”
- 也可以点击 ”删除” 按钮，删除后在后台的”回收箱“中可以查看。

### 3.3还原博客

- 后台，有一个“回收箱” ，在里面点击”还原“

- 回收箱可以点击“彻底删除博客” ，彻底删除之后，博客被压缩成字符串，存在tperCMS/common/rush.txt下。当然，是序列化之后的，可以复原，但你要找到他才行。

### 3.4设置网站备案号

- 网页最下面的备案号，在tperCMS/common/common.xml中改，有提示。

  

  **网站的前端可以自己改改，或者加链接什么的。html文件在application下各个模块的view文件夹中**

  

## 4.。。。

可能有bug ,  但是基本功能就差不多那样，很简单。写着玩。

有大佬觉得可以，可以加入一起写，或者帮我测测有什么bug
