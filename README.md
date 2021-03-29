# PHP 中文手册 mirror
![Build](https://github.com/php/doc-zh/workflows/Build/badge.svg)

本仓库是官方 docbook 的 git 仓库。

_请注意原来的位于 git.php.net 的上游仓库已被弃用，目前此仓库为正式仓库，而非镜像_

## PHP 中文手册翻译指南

### 在加入翻译小组之前

PHP 文档的翻译工作完全是一项志愿行动，你不会从中得到任何利益。而且要建立起一个适合进行翻译工作的环境也是相当繁琐的，例如申请 git 账号，拥有 Unix/Linux 环境以及 git 工具，订阅相关的邮件列表，翻译完成后还要继续关注英文文档是否有了更新等等。

你能得到的是什么呢？是一种无私奉献并与世界各地的同好们一起工作的乐趣，英文翻译水平的提高（希望这样），对 PHP 更加深入的了解以及在 PHP 文档的中文页面中署上你的大名。

如果你不能满足下面的所有条件，那么你可能不适合加入 PHP 文档的翻译工作：

1. 愿意进行无私奉献加入这个志愿行动；
1. 英文程度较好；有较好的汉语书面表达能力。
1. 对 PHP 本身有相当的了解；
1. 对 Unix/Linux 有相当了解，能够自行建立起 git 的工作环境；
1. 在参与翻译工作时（请注意这一点）愿意为此投入较多时间，并且能够迅速完成自己承担的文件的翻译工作。不要因为一时冲动加入了翻译工作之后，所经手的文件完成一部分之后却由于种种原因而不能继续下去。大家都不希望看到一个文件历时数个星期甚至数月都未能完成翻译工作；
1. 以后能够定期访问并且尽量更新自己所维护的文件。

假如你能够做到以上这几点，那么欢迎你加入 PHP 中文文档翻译小组！

实在难以建立 git 工作环境或者申请不到 git 账号的朋友如果愿意参与，可以参考 [无障碍参与方式](#无障碍参与方式)。

### 有用的链接

- [官方：手册编写指南](http://doc.php.net/tutorial/)
- [官方：手册翻译指南](http://doc.php.net/tutorial/translating.php)
- [中文翻译小组工作状态](http://doc.php.net/revcheck.php?lang=zh)
- [待翻译文档列表](http://doc.php.net/revcheck.php?p=missfiles&lang=zh)
- [旧的 readme.first](readme.first)

### 无障碍参与方式

~~#### 在线编辑~~

~~官方提供一个最简单的编辑文档方式，任何人都可以用你的 GitHub 或 Facebook 账号登录 [edit.php.net](https://edit.php.net)，在线查看文档库，在线提交修改内容，你修改的内容需要经过翻译小组审核后，才会正式合并到官方文档中。~~

> 注意：文档仓库转到 git 后，在线编辑器尚未支持 git 仓库，现阶段请使用提交 PR 方式来参与。

#### 通过 GitHub 提交 PR

你可以直接按照 GitHub 的工作流程：[GitHub Flow](https://docs.github.com/en/github/collaborating-with-issues-and-pull-requests/github-flow)，fork 此仓库完成修改后再提交 Pull Request，你提交的 PR 会在通过翻译小组审核后，合并到官方文档中。

> 如果你不满足于简单的参与，希望对 PHP 文档手册做出更大的贡献，你需要配置对应的环境，并申请自己的 git 账号，请继续往下阅读。 

### 深度参与方式

#### 环境要求

在我们开始之前，我们假设：

- 你运行的是 OS X/Linux 环境
- 你已经安装了 php，并且可以通过命令行来运行它
- 你已经安装了 git 环境

创建一个目录，把我们需要的所有工具和 repos 放在里面，以便开始为文档做贡献：

```bash
mkdir ~/php-docs
cd ~/php-docs
```

#### 获取文档仓库

```bash
mkdir phpdoc
cd phpdoc
git clone git@git.php.net:/doc/en.git
git clone git@git.php.net:/doc/zh.git
git clone git@git.php.net:/doc/base.git doc-base
```

#### 了解 DocBook 格式

仓库里的 .xml 文件是文档的原始文件，我们所看到的 html 和官方在线的 php 文档，都是由这些 xml 文件生成的。XML 的格式为 [DocBook](http://www.docbook.org/)，这也是在 markdown 成为主流之前最流行的文档规范。

#### PhD: 官方的手册生成工具

官方提供了一个构建 DocBook 的工具 [PhD](http://doc.php.net/phd/docs/)，通过以下方式获取：

```bash
cd ~/php-docs
git clone http://git.php.net/repository/phd.git
cd ./phd
```

查看使用帮助：

```bash
php render.php --help
```

#### 翻译流程

git 仓库中的每个文件都有一个版本号，它用来指明当前文件的版本号，我们使用版本号来检查一个文件是否与它的英文版本同步：找出翻译是否是最新的。这就是为什么您的翻译中的每个文件都需要一个 EN-Revision 注释，其语法如下。

```xml
<!-- EN-Revision: [some number] Maintainer: [username] Status: ready -->
```

为便于跟踪翻译工作以及保持与英文文档一致，请用以下四行代替原来英文文档的前两行（以下为示例，请依据实际情况修改）：

```xml
<?xml version="1.0" encoding="utf-8"?>
<!-- $Revision: 350481 $ -->
<!-- $Author: Avenger $ -->
<!-- EN-Revision: 324085 Maintainer: verdana Status: ready -->
```

有时也可以加入这样一行：

```xml
<!-- CREDITS: Gregory,dallas -->
```

各项变量说明如下:
| 变量         | 说明 |
| ----------- | ------------------------------------------------------------ |
| $Revision   | 中文文档的版本号，自动更新                                   |
| $Author     | 最后一次 checkin 的人员的 git 账号，自动更新                 |
| EN-Revision | 与当前中文文档相对应的英文文档的版本号，非常重要！方便后续文档更新检测 |
| Maintainer  | 此中文文档的维护者，一定要与 translation.xml 中的 nick 相同，区分大小写！ |
| Status      | 翻译完成后写 ready，翻译一部分的写 partial                   |
| CREDITS     | 如果你对别人维护的文档做了重大更新或者完成了其中相当大的一部分翻译工作，请在这里添上你的昵称，多个人用逗号隔开 |

#### 翻译新文件的流程

假设你想翻译 `in_array()` 函数的文档，但这个函数在中文文档中还不存在，请打开 `phpdoc/en/reference/array/in-array.xml` 文件，并复制版本号。英文文件的头可能是这样的：

```xml
<?xml version="1.0" encoding="utf-8"?>
<!-- $Revision: 310394 $ -->
```

所以我们的版本号是 310394。让我们看看如果假设你的 git 用户名是 Avenger，你的翻译文件头应该是怎样的。

```xml
<?xml version="1.0" encoding="utf-8"?>
<!-- $Revision$ -->
<!-- EN-Revision: 310394 Maintainer: Avenger Status: ready -->
```

规则很简单：如果你的版本号等于你所翻译的英文文件的版本号，那么你的翻译就是最新的。否则，就需要进行同步更新。

#### 同步更新翻译的流程

假设你想更新 `password_needs_rehash()` 的翻译。有两种简单的方法可以看到哪些文件需要更新，哪些需要修改以与英文版本同步：使用 [在线编辑器](https://edit.php.net/) 或 [doc.php.net 工具](http://doc.php.net/revcheck.php?lang=zh)。下面以第二种方法为例：

点击 [Outdated files](http://doc.php.net/revcheck.php?p=files&lang=zh) 链接，可以通过目录或用户名来过滤文件（用户名来自于上述头注释中的 Maintainer 变量）。我们假设该工具将 `password-needs-rehash.xml` 标记为过时的文件。点击文件名，你会看到 `diff` -- 两个版本的文件之间的变化列表：你的版本（在你的翻译中的 `EN-Revision` 中的当前数字）和英文源树中的最新版本。下面的例子应该是 `diff` 的样子。

```diff
--- phpdoc/en/trunk/reference/password/functions/password-needs-rehash.xml    2013/06/21 12:24:55    330609
+++ phpdoc/en/trunk/reference/password/functions/password-needs-rehash.xml    2014/03/24 20:23:27    333093
@@ -12,8 +12,8 @@
   <methodsynopsis>
    <type>boolean</type><methodname>password_needs_rehash</methodname>
    <methodparam><type>string</type><parameter>hash</parameter></methodparam>
-   <methodparam><type>string</type><parameter>algo</parameter></methodparam>
-   <methodparam choice="opt"><type>string</type><parameter>options</parameter></methodparam>
+   <methodparam><type>integer</type><parameter>algo</parameter></methodparam>
+   <methodparam choice="opt"><type>array</type><parameter>options</parameter></methodparam>
   </methodsynopsis>
   <para>
    This function checks to see if the supplied hash implements the algorithm
```

前两行表示比较过的版本。第一行是取自 `EN-Revision` 编号，第二行是本文件的当前英文版本。

我们可以直观的看到两个文件之间的区别，文档概要中的参数 `options` 和 `algo` 的类型分别从字符串变为了整数和数组。所以我们要在翻译的文档中进行这些对应的修改，以使其成为最新的版本。打开 `phpdoc/zh/reference/password/functions/password-needs-rehash.xml` 修改这两行，使其与英文版本一致。同步完成这些修改后，需要在标题注释中更新 `EN-Revision` 编号。

原文件头：

```xml
<?xml version="1.0" encoding="utf-8"?>
<!-- EN-Revision: 330609 Maintainer: someone Status: ready ->。
<!-- $Revision: 123456$ -->
```

修改后的文件头部应该是这样的：

```xml
<?xml version="1.0" encoding="utf-8"?>
<!-- EN-Revision: 333093 Maintainer: someone Status: ready -->
<!-- $Revision$ -->
<!-- CREDITS: Avenger -->
```

新的 `EN-Revision` 编号来自于上面所示的差异，如果你想把自己添加到已经存在的 `CREDITS` 标签中，用逗号隔开用户名即可：`<!-- CREDITS：George，Avenger ->`。

现在翻译已经是最新的了，我们只需要等待官方文档每天同步更新，即可看到你的最新工作成果。

### 翻译中需要注意的问题  

1. 文件是 UNIX 的文件格式而不是 DOS 的，也就是文件中的换行标记只有换行符（\n），而没有回车符（\r）。并且在文件中不要使用 <TAB>（制表符\t）而要使用空格；
1. 请注意翻译中的标点问题，使用中文标点符号；
1. 中英文字词之间使用空格分开，与中文标点之间就不要有空格了；
1. 不要翻译 TRUE，FALSE，Boolean 之类的词。翻译中请注意！
1. 请翻译的时候注意使用书面用语，翻译完成后检查确认没有错别字和其它错误后再提交！
1. 请参加翻译的人员加入 `doc-zh@lists.php.net` 邮件列表.具体方法是向 `doc-zh-subscribe@lists.php.net` 发一封空白电子邮件，然后回信确认。我们在这个邮件列表里面讨论和协调翻译工作。该邮件列表的内容可以通过如下地址访问 https://news-web.php.net/php.doc.zh
1. 请参加翻译的人员，一定要在提交翻译完成的 XML 文件前运行 `php configure.php --with-lang=zh` 命令，检查文件是否有语法和拼写错误。

#### 有关在翻译后的中文文件中的空格与换行的说明

在原始的英文 XML 文件中不存在此问题，因为英文单词之间本来就有一个空格，而无论是 XML 文件还是编译后的 HTML 文件对待任何连续的空格，回车以及制表符都当成一个空格看待。但是在中文书写习惯中，汉字之间以及与中文标点符号之间都是没有空格的。因此英文文件中类似这样的段落：

```
<para>
    This section applies to Windows 95/98/Me and
    Windows NT/2000/XP. Do not expect PHP to work on
    16 bit platforms such as Windows 3.1. Sometimes
    we refer to the supported Windows platforms as Win32.
</para>
```

在编译后显示出来当然完全正常。可是如果翻译成这样：

```
<para>
    本节内容适用于 Windows 95/98/Me 以及 Windows NT/2000/XP。
    PHP 不能在 16 位平台例如 Windows 3.1 下运行。有时我们把
    支持 PHP 的 Windows 平台称为 Win32。
</para>
```

就会在 HTML 页面显示时在“XP。”和“PHP 不能”以及“我们把”和“支持”之间出现一个不希望的空格，破坏了页面的美观。这种情况下当然可以将翻译的结果全部放到同一行中来解决，例如：

```
<para>
    本节内容适用于 Windows 95/98/Me 以及 Windows NT/2000/XP。PHP 不能在 16 位平台例如 Windows 3.1 下运行。有时我们把支持 PHP 的 Windows 平台称为 Win32。
</para>
```

但这样给翻译时的阅读又带来了不便。可用的解决方法是在翻译后的文件中保留下来的英文单词前后换行 － 既然中英文字词之间本来就是用空格隔开的并且一个空格和多个空格也没什么区别。那么以上这一段在中文文件中可以写成这样：（要注意，不要在中文标点符号前后换行！）

```
<para>
    本节内容适用于 Windows 95/98/Me 以及
    Windows NT/2000/XP。PHP 不能在 16 位平台例如
    Windows 3.1 下运行。有时我们把支持
    PHP 的 Windows 平台称为 Win32。
</para>
```

这样一来显示的时候就没问题了。如果翻译后结果是一大段纯中文的文本，那就没办法了，只能老老实实放在同一行，即使很长很长。

总之原则是在保证显示出来的页面美观无误的情况下兼顾原始 XML 文件的可读性。
