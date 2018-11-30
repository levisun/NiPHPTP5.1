# NiPHP2.x

> NiPHP2.x的运行环境要求PHP5.6以上。

> NiPHP2.x基于ThinkPHP5.1框架开发，使用时请先下载[ThinkPHP5.1框架](http://www.thinkphp.cn/)

> 安装ThinkPHP5.1框架后，替换application和public目录。

> 业务层支持API版本分层

## 目录结构
~~~
www  WEB部署目录（或者子目录）
├─application                               应用目录
│   ├─admin                                 后台模块目录
│   │   ├─config                            配置目录
│   │   ├─controller                        控制层目录
│   │   ├─lang                              语言包目录
│   │   ├─logic                             业务层目录
│   │   ├─middleware                        中间层目录
│   │   ├─validate                          验证层目录
│   │   ├─common.php                        函数文件
│   ├─cms                                   CMS模块目录
│   │   ├─behavior                          行为目录
│   │   ├─config                            配置目录
│   │   ├─controller                        控制层目录
│   │   ├─lang                              语言包目录
│   │   ├─logic                             业务层目录
│   │   ├─taglib                            模板标签目录
│   │   ├─common.php                        函数文件
│   ├─common                                公共模块目录
│   │   ├─behavior                          行为目录
│   │   │   ├─Concurrent.php
│   │   │   ├─CreateApiToken.php            生成API请求令牌方法
│   │   │   ├─RemoveRunGarbage.php          清理运行垃圾方法
│   │   │   ├─Visit.php                     访问记录方法
│   │   ├─logic                             业务层目录
│   │   │   ├─Async.php                     异步请求方法
│   │   │   ├─IpInfo.php                    IP归属地方法
│   │   │   ├─Rbac.php                      帐户权限验证方法
│   │   │   ├─RequestLog.php                请求日志方法
│   │   │   ├─SafeFilter.php                数据安全过滤方法
│   │   │   ├─Upload.php                    上传文件文件
│   │   ├─model                             模型目录
│   ├─mall                                  商城模块目录
│   ├─member                                会员模块目录
│   ├─wechat                                微信模块目录
│   ├─command.php                           命令行定义文件
│   ├─common.php                            公共函数文件
│   ├─provider.php                          应用容器定义文件
│   ├─tags.php                              应用行为扩展定义文件
├─backup                                    备份文件目录
├─config                                    应用配置目录
│   ├─app.php                               应用配置文件
│   ├─cache.php                             缓存配置文件
│   ├─captcha.php                           验证码配置文件
│   ├─console.php                           控制台配置文件
│   ├─cookie.php                            Cookie配置文件
│   ├─database.php                          数据库配置文件
│   ├─log.php                               日志配置文件
│   ├─middleware.php                        中间件配置文件
│   ├─paginate.php                          分页配置文件
│   ├─session.php                           会话配置文件
│   ├─template.php                          模板配置文件
│   ├─trace.php                             Trace配置文件
├─extend                                    扩展类库目录
├─public                                    WEB目录（对外访问目录）
├─route                                     路由定义目录
├─runtime                                   应用的运行时目录
├─thinkphp                                  框架系统目录
├─vendor                                    第三方类库目录（Composer依赖库）






## 版权信息

版权所有Copyright © 2006-2018 by niphp.com (http://niphp.com)

All rights reserved。
